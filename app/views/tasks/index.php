<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Tarefas - Tasks Colaborativas</title>
    <link rel="stylesheet" href="/mvc/app/views/tasks/style.css">
</head>

<body>
    <div class="page-shell">

        <header class="navbar">
            <div class="brand-block">
                <div>
                    <div class="logo">Tasks Colaborativas</div>
                    <p class="subtitle">Dashbord de tarefas colaborativas</p>
                </div>
            </div>

            <div class="nav-actions">
                <a class="btn-tasks" href="/mvc/home/tasks">Tarefas</a>
                <a class="btn-users" href="/mvc/home/users">Usuários</a>
                <button class="btn-logout" onclick="logout()">Sair</button>
            </div>
        </header>

        <main class="container">
            <section class="dashboard-header">
                <div>
                    <span class="eyebrow">Dashboard</span>
                    <h1>Bem-vindo, <span id="user-greeting"></span> !</h1>
                    <p id="task-summary" class="summary-text">Carregando as tarefas...</p>
                </div>

                <div class="stats-grid">
                    <button class="stat-card stat-filter" data-filter="">
                        <span>Total</span>
                        <strong id="stat-total">0</strong>
                    </button>
                    <button class="stat-card stat-filter" data-filter="pendente">
                        <span>Pendentes</span>
                        <strong id="stat-pendentes">0</strong>
                    </button>
                    <button class="stat-card stat-filter" data-filter="em_andamento">
                        <span>Em andamento</span>
                        <strong id="stat-andamento">0</strong>
                    </button>
                    <button class="stat-card stat-filter" data-filter="concluida">
                        <span>Concluídas</span>
                        <strong id="stat-concluidas">0</strong>
                    </button>
                </div>

                <div class="filters-row">
                    <label class="filter-label" for="filter-atribuido">Filtrar por responsável</label>
                    <select id="filter-atribuido" class="filter-select">
                        <option value="">Todos os responsáveis</option>
                    </select>
                </div>
            </section>

            <div class="actions-row">
                <button id="btn-new-task" class="btn-new">+ Nova tarefa</button>
            </div>

            <!-- Modal de criação -->
            <div id="create-modal" class="modal" hidden>
                <div class="modal-content">
                    <h3>Criar nova tarefa</h3>
                    <form id="create-form">
                        <label>Título</label>
                        <input id="create-titulo" type="text" required />
                        <label>Descrição</label>
                        <textarea id="create-descricao"></textarea>
                        <label>Responsável (opcional)</label>
                        <select id="create-atribuido">
                            <option value="">-- Selecionar usuário --</option>
                        </select>
                        <div class="modal-actions">
                            <button type="button" class="btn-cancel" onclick="closeCreateModal()">Cancelar</button>
                            <button type="submit" class="btn-save">Criar</button>
                        </div>
                    </form>
                </div>
            </div>

            <section class="task-section">
                <div id="lista-tarefas" class="task-grid"></div>

                <div id="empty-state" class="empty-state" hidden>
                    <h2>Nenhuma tarefa encontrada</h2>
                    <p>Não há tarefas para exibir neste momento.</p>
                </div>
            </section>

            <!-- Modal de edição -->
            <div id="edit-modal" class="modal" hidden>
                <div class="modal-content">
                    <h3>Editar tarefa</h3>
                    <form id="edit-form">
                        <input type="hidden" id="edit-id">
                        <label>Título</label>
                        <input id="edit-titulo" type="text" required />
                        <label>Descrição</label>
                        <textarea id="edit-descricao"></textarea>
                        <label>Responsável</label>
                        <select id="edit-atribuido">
                            <option value="">-- Selecionar usuário --</option>
                        </select>
                        <label>Status</label>
                        <input type="hidden" id="edit-status" value="pendente">
                        <div class="status-picker" id="status-picker">
                            <button type="button" class="status-option is-pendente" data-status="pendente">Pendente</button>
                            <button type="button" class="status-option is-andamento" data-status="em_andamento">Em andamento</button>
                            <button type="button" class="status-option is-concluida" data-status="concluida">Concluída</button>
                        </div>
                        <div class="modal-actions">
                            <button type="button" class="btn-cancel" onclick="closeModal()">Cancelar</button>
                            <button type="submit" class="btn-save">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <div id="toast" class="toast" hidden></div>

    <script>
        const token = localStorage.getItem('token');
        if (!token) {
            window.location.href = '/mvc/home/login';
        }

        const usuarioNome = localStorage.getItem('nome') ?? 'Usuário';

        let usersCache            = [];
        let allTasks              = [];
        let currentAssigneeFilter = '';
        let currentStatusFilter   = '';

        document.getElementById('user-greeting').textContent = usuarioNome;

        document.querySelectorAll('.stat-filter').forEach((btn) => {
            btn.addEventListener('click', () => {
                currentStatusFilter = btn.dataset.filter;
                document.querySelectorAll('.stat-filter').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const filtradas = getFilteredTasks();
                renderLista('lista-tarefas', filtradas);
                document.getElementById('empty-state').hidden = filtradas.length !== 0;
                document.getElementById('task-summary').textContent = filtradas.length
                    ? `${filtradas.length} tarefa(s) encontrada(s).`
                    : 'Nenhuma tarefa encontrada.';
            });
        });

        async function loadUsers() {
            try {
                const res  = await fetch('/mvc/users', { headers: { 'Authorization': 'Bearer ' + token } });
                const body = await res.json();
                if (!res.ok) return [];
                usersCache = Array.isArray(body.data) ? body.data : [];
                populateAssigneeFilter(usersCache);
                populateUserSelect('create-atribuido', usersCache);
                populateUserSelect('edit-atribuido', usersCache);
                return usersCache;
            } catch (err) {
                console.error('Erro ao carregar usuarios', err);
                return [];
            }
        }

        function populateUserSelect(selectId, users) {
            const sel = document.getElementById(selectId);
            if (!sel) return;
            sel.innerHTML = '<option value="">-- Selecionar usuário --</option>' +
                users.map(u => `<option value="${u.id}">${u.nome}</option>`).join('');
        }
function populateAssigneeFilter(users) {
    const select = document.getElementById('filter-atribuido');
    if (!select) return;
    const currentValue = select.value;
    select.innerHTML = '<option value="">Todos os responsáveis</option>' +
        '<option value="null">Não atribuído</option>' +
        users.map(u => `<option value="${u.id}">${u.nome}</option>`).join('');
    select.value = currentValue;
}
        function setEditStatus(status) {
            document.getElementById('edit-status').value = status;
            document.querySelectorAll('#status-picker .status-option').forEach((btn) => {
                const isActive = btn.dataset.status === status;
                btn.classList.toggle('active', isActive);
                btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            });
        }

        document.getElementById('status-picker').addEventListener('click', (event) => {
            const button = event.target.closest('.status-option');
            if (!button) return;
            setEditStatus(button.dataset.status);
        });

        async function carregarTarefas() {
            const response = await fetch('/mvc/tasks', {
                headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' }
            });
            const data = await response.json();
            if (!response.ok) { alert(data.error); return; }

            allTasks = Array.isArray(data.data) ? data.data : [];

            document.getElementById('stat-total').textContent     = allTasks.length;
            document.getElementById('stat-pendentes').textContent = allTasks.filter(t => t.status === 'pendente').length;
            document.getElementById('stat-andamento').textContent = allTasks.filter(t => t.status === 'em_andamento').length;
            document.getElementById('stat-concluidas').textContent = allTasks.filter(t => t.status === 'concluida').length;

            const filtradas = getFilteredTasks();
            renderLista('lista-tarefas', filtradas);

            document.getElementById('task-summary').textContent = allTasks.length
                ? `${filtradas.length} tarefa(s) encontrada(s).`
                : 'Nenhuma tarefa cadastrada no momento.';

            document.getElementById('empty-state').hidden = filtradas.length !== 0;
        }

      function getFilteredTasks() {
    return allTasks.filter((tarefa) => {
        let passaResponsavel;
        if (!currentAssigneeFilter) {
            passaResponsavel = true;
        } else if (currentAssigneeFilter === 'null') {
            passaResponsavel = !tarefa.atribuido_a_id;
        } else {
            passaResponsavel = String(tarefa.atribuido_a_id) === currentAssigneeFilter;
        }
        const passaStatus = !currentStatusFilter || tarefa.status === currentStatusFilter;
        return passaResponsavel && passaStatus;
    });
}
        document.getElementById('filter-atribuido').addEventListener('change', (event) => {
            currentAssigneeFilter = event.target.value;
            const filtradas = getFilteredTasks();
            renderLista('lista-tarefas', filtradas);
            document.getElementById('task-summary').textContent = filtradas.length
                ? `${filtradas.length} tarefa(s) encontrada(s).`
                : 'Nenhuma tarefa encontrada.';
            document.getElementById('empty-state').hidden = filtradas.length !== 0;
        });

        document.getElementById('btn-new-task').addEventListener('click', async () => {
            if (!usersCache.length) await loadUsers();
            document.getElementById('create-modal').hidden = false;
        });

        function closeCreateModal() {
            document.getElementById('create-modal').hidden = true;
            document.getElementById('create-form').reset();
        }

        document.getElementById('create-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const titulo    = document.getElementById('create-titulo').value;
            const descricao = document.getElementById('create-descricao').value;
            const atribuido = document.getElementById('create-atribuido').value || null;

            const payload = { titulo, descricao };
            if (atribuido) payload.atribuido_a_id = Number(atribuido);

            const res  = await fetch('/mvc/tasks', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const body = await res.json();
            if (!res.ok) { showToast(body.error || 'Erro ao criar tarefa'); return; }

            closeCreateModal();
            showToast('Tarefa criada');
            carregarTarefas();
        });

        function renderLista(containerId, tarefas) {
            const container = document.getElementById(containerId);
            container.innerHTML = '';

            const statusLabels = {
                'pendente':     'Pendente',
                'em_andamento': 'Em Andamento',
                'concluida':    'Concluída'
            };

            const ul = document.createElement('ul');
            ul.className = 'task-list';

            tarefas.forEach(tarefa => {
                const li = document.createElement('li');
                li.className = 'task-item';

                const atribuidoPara = tarefa.atribuido_nome || 'Não atribuído';
                const tarefaEncoded = encodeURIComponent(JSON.stringify(tarefa));
                const statusClass   = `is-${tarefa.status}`;

                li.innerHTML = `
                    <div class="task-line">
                        <div class="task-left">
                            <div class="status-dropdown" data-task-id="${tarefa.id}">
                                <button type="button" class="status-toggle ${statusClass}" aria-haspopup="true" aria-expanded="false">
                                    Status
                                    <span class="status-toggle-label">${statusLabels[tarefa.status]}</span>
                                </button>
                                <div class="status-menu" hidden>
                                    <button type="button" class="status-menu-item is-pendente"  data-task="${tarefaEncoded}" data-status="pendente">Pendente</button>
                                    <button type="button" class="status-menu-item is-andamento" data-task="${tarefaEncoded}" data-status="em_andamento">Em andamento</button>
                                    <button type="button" class="status-menu-item is-concluida" data-task="${tarefaEncoded}" data-status="concluida">Concluída</button>
                                </div>
                            </div>
                            <div class="task-info">
                                <strong class="task-title">${tarefa.titulo}</strong>
                                <div class="task-meta-inline">
                                    <span><strong>Criado por:</strong> ${tarefa.criador_nome}</span>
                                    <span>•</span>
                                    <span><strong>Atribuído para:</strong> ${atribuidoPara}</span>
                                </div>
                            </div>
                        </div>
                        <div class="task-right">
                            <span class="task-date">${tarefa.updated_at || tarefa.created_at}</span>
                            <div class="task-actions">
                                <button class="btn-edit" type="button" data-task="${tarefaEncoded}" data-id="${tarefa.id}">Editar</button>
                                <button class="btn-delete" type="button" data-id="${tarefa.id}">Excluir</button>
                            </div>
                        </div>
                    </div>
                    <p class="task-desc">${tarefa.descricao || 'Sem descrição.'}</p>
                `;

                li.querySelector('.btn-edit').addEventListener('click', (e) => {
                    const tarefaObj = JSON.parse(decodeURIComponent(e.currentTarget.getAttribute('data-task')));
                    openEditModal(e.currentTarget.getAttribute('data-id'), tarefaObj);
                });

                li.querySelector('.btn-delete').addEventListener('click', (e) => {
                    deletarTarefa(e.currentTarget.getAttribute('data-id'));
                });

                const toggle = li.querySelector('.status-toggle');
                const menu   = li.querySelector('.status-menu');

                toggle.addEventListener('click', (event) => {
                    event.stopPropagation();
                    const isOpen = !menu.hasAttribute('hidden');
                    closeAllStatusMenus();
                    if (!isOpen) {
                        menu.removeAttribute('hidden');
                        toggle.setAttribute('aria-expanded', 'true');
                    }
                });

                li.querySelectorAll('.status-menu-item').forEach((btnStatus) => {
                    btnStatus.addEventListener('click', async (event) => {
                        event.stopPropagation();
                        const tarefaObj  = JSON.parse(decodeURIComponent(btnStatus.getAttribute('data-task')));
                        const novoStatus = btnStatus.getAttribute('data-status');
                        await atualizarStatusNaLista(tarefaObj, novoStatus);
                        closeAllStatusMenus();
                    });
                });

                ul.appendChild(li);
            });

            container.appendChild(ul);
        }

        function closeAllStatusMenus() {
            document.querySelectorAll('.status-dropdown').forEach((dropdown) => {
                const menu   = dropdown.querySelector('.status-menu');
                const toggle = dropdown.querySelector('.status-toggle');
                if (menu)   menu.setAttribute('hidden', 'hidden');
                if (toggle) toggle.setAttribute('aria-expanded', 'false');
            });
        }

        document.addEventListener('click', () => closeAllStatusMenus());

        async function atualizarStatusNaLista(tarefa, novoStatus) {
            const response = await fetch(`/mvc/tasks/${tarefa.id}`, {
                method: 'PUT',
                headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' },
                body: JSON.stringify({ status: novoStatus })
            });
            const data = await response.json();
            if (!response.ok) { showToast(data.error || 'Erro ao alterar status'); return; }
            showToast('Status atualizado');
            carregarTarefas();
        }

        function openEditModal(id, tarefa) {
            document.getElementById('edit-id').value          = id;
            document.getElementById('edit-titulo').value      = tarefa.titulo || '';
            document.getElementById('edit-descricao').value   = tarefa.descricao || '';
            setEditStatus(tarefa.status || 'pendente');

            loadUsers().then(() => {
                const atribSel = document.getElementById('edit-atribuido');
                if (atribSel) atribSel.value = tarefa.atribuido_a_id || '';
            });

            document.getElementById('edit-modal').hidden = false;
        }

        function closeModal() {
            document.getElementById('edit-modal').hidden = true;
            document.getElementById('edit-id').value        = '';
            document.getElementById('edit-titulo').value    = '';
            document.getElementById('edit-descricao').value = '';
            setEditStatus('pendente');
            const atribSel = document.getElementById('edit-atribuido');
            if (atribSel) atribSel.value = '';
        }

        document.getElementById('edit-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id        = document.getElementById('edit-id').value;
            const titulo    = document.getElementById('edit-titulo').value;
            const descricao = document.getElementById('edit-descricao').value;
            const status    = document.getElementById('edit-status').value;
            const atribSel  = document.getElementById('edit-atribuido');

            const payload = { titulo, descricao, status };
            if (atribSel) payload.atribuido_a_id = atribSel.value ? Number(atribSel.value) : null;

            const response = await fetch(`/mvc/tasks/${id}`, {
                method: 'PUT',
                headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await response.json();
            if (!response.ok) { showToast(data.error || 'Erro ao atualizar tarefa'); return; }

            showToast('Tarefa atualizada');
            closeModal();
            carregarTarefas();
        });

        document.getElementById('edit-modal').addEventListener('click', (e) => {
            if (!document.querySelector('#edit-modal .modal-content').contains(e.target)) closeModal();
        });

        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !document.getElementById('edit-modal').hidden) closeModal();
        });

        async function deletarTarefa(id) {
            if (!confirm('Tem certeza que deseja excluir esta tarefa?')) return;

            const response = await fetch(`/mvc/tasks/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' }
            });
            const data = await response.json();
            if (!response.ok) { showToast(data.error || 'Erro ao excluir tarefa'); return; }

            showToast('Tarefa removida');
            carregarTarefas();
        }

        function showToast(message, timeout = 3000) {
            const t = document.getElementById('toast');
            if (!t) return;
            t.textContent = message;
            t.hidden = false;
            t.classList.add('show');
            setTimeout(() => { t.classList.remove('show'); t.hidden = true; }, timeout);
        }

        function logout() {
            localStorage.removeItem('token');
            localStorage.removeItem('nome');
            window.location.href = '/mvc/';
        }

        (async function initPage() {
            await loadUsers();
            await carregarTarefas();
        })();
    </script>
</body>

</html>