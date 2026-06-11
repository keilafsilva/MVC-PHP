<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Usuários - Tasks Colaborativas</title>
    <link rel="stylesheet" href="/mvc/app/views/users/style.css">
</head>

<body>
    <div class="page-shell">
        <header class="navbar">
            <div class="brand-block">
                <div>
                    <div class="logo">Tasks Colaborativas</div>
                    <p class="subtitle">Painel administrativo de usuários</p>
                </div>
            </div>

            <div class="nav-actions">
                <a class="btn-tasks" href="/mvc/home/tasks">Tarefas</a>
                <a class="btn-users" href="/mvc/home/users">Usuários</a>
                <button class="btn-logout" onclick="logout()">Sair</button>
            </div>
        </header>

        <main class="container">
            <section class="hero-card">
                <div>
                    <span class="eyebrow">Administração</span>
                    <h1>Gestão de usuários</h1>
                    <p id="users-summary" class="summary-text">Carregando usuários...</p>
                </div>
            </section>

            <section class="content-grid">
                <div class="panel-card">
                    <div class="panel-header">
                        <div>
                            <span class="eyebrow">Cadastro</span>
                            <h2 id="form-title">Novo usuário</h2>
                        </div>
                        <button type="button" id="btn-clear" class="btn-secondary">Limpar</button>
                    </div>

                    <form id="user-form" class="user-form">
                        <input type="hidden" id="user-id">

                        <label>Nome</label>
                        <input id="nome" type="text" placeholder="Nome completo" required>

                        <label>E-mail</label>
                        <input id="email" type="email" placeholder="usuario@email.com" required>

                        <!-- Campos de senha para criação -->
                        <div id="senha-create-section">
                            <label>Senha</label>
                            <input id="senha-create" type="password" placeholder="Senha do novo usuário">
                        </div>

                        <!-- Campos de senha para edição (oculto por padrão) -->
                        <div id="senha-edit-section" style="display:none">
                            <label>Senha atual <span style="color:var(--danger)">*</span></label>
                            <input id="senha-atual" type="password" placeholder="Obrigatória para salvar alterações">

                            <label style="margin-top:0.7rem">
                                Nova senha <span style="color:var(--muted-2);font-weight:400">(opcional)</span>
                            </label>
                            <input id="senha-nova" type="password" placeholder="Deixe em branco para manter a atual">
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-save" id="btn-save-user">Salvar usuário</button>
                        </div>
                    </form>
                </div>

                <div class="panel-card list-panel">
                    <div class="panel-header">
                        <div>
                            <span class="eyebrow">Lista</span>
                            <h2>Usuários cadastrados</h2>
                        </div>
                        <input id="user-search" class="search-input" type="search" placeholder="Pesquisar por nome ou e-mail">
                    </div>

                    <div id="users-list" class="users-list"></div>
                </div>
            </section>
        </main>
    </div>

    <div id="toast" class="toast" hidden></div>

    <script>
        const token = localStorage.getItem('token');
        if (!token) {
            window.location.href = '/mvc/home/login';
        }

        let users = [];
        let filteredUsers = [];

        function showToast(message, timeout = 3000) {
            const t = document.getElementById('toast');
            t.textContent = message;
            t.hidden = false;
            t.classList.add('show');
            setTimeout(() => {
                t.classList.remove('show');
                t.hidden = true;
            }, timeout);
        }

        function logout() {
            localStorage.removeItem('token');
            localStorage.removeItem('nome');
            window.location.href = '/mvc/';
        }

        async function loadUsers() {
            const response = await fetch('/mvc/users', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const data = await response.json();

            if (!response.ok) {
                showToast(data.error || 'Erro ao carregar usuários');
                return;
            }

            users = Array.isArray(data.data) ? data.data : [];
            applySearchFilter();
            updateStats();
            renderUsers();
        }

        function updateStats() {
            const total = users.length;
            document.getElementById('users-summary').textContent = total
                ? `${total} usuário(s) encontrados no painel.`
                : 'Nenhum usuário cadastrado no momento.';
        }

        function applySearchFilter() {
            const query = document.getElementById('user-search').value.trim().toLowerCase();
            filteredUsers = users.filter((user) => {
                if (!query) return true;
                return user.nome.toLowerCase().includes(query) || user.email.toLowerCase().includes(query);
            });
        }

        function renderUsers() {
            const list = document.getElementById('users-list');
            list.innerHTML = '';

            if (!filteredUsers.length) {
                list.innerHTML = '<div class="empty-state">Nenhum usuário encontrado.</div>';
                return;
            }

            filteredUsers.forEach((user) => {
                const card = document.createElement('article');
                card.className = 'user-card';
                card.innerHTML = `
                    <div class="user-card-top">
                        <div>
                            <h3>${user.nome}</h3>
                            <p>${user.email}</p>
                        </div>
                        <span class="user-pill">ID ${user.id}</span>
                    </div>
                    <div class="user-card-meta">
                        <span>Criado em ${user.created_at || '-'}</span>
                    </div>
                    <div class="user-card-actions">
                        <button class="btn-mini btn-edit-user" type="button">Editar</button>
                        <button class="btn-mini btn-delete-user" type="button">Excluir</button>
                    </div>
                `;

                card.querySelector('.btn-edit-user').addEventListener('click', () => fillForm(user));
                card.querySelector('.btn-delete-user').addEventListener('click', () => deleteUser(user.id));
                list.appendChild(card);
            });
        }

        function fillForm(user) {
            document.getElementById('form-title').textContent = 'Editar usuário';
            document.getElementById('user-id').value = user.id;
            document.getElementById('nome').value = user.nome;
            document.getElementById('email').value = user.email;
            document.getElementById('senha-atual').value = '';
            document.getElementById('senha-nova').value = '';

            // Mostra campos de edição, oculta criação
            document.getElementById('senha-create-section').style.display = 'none';
            document.getElementById('senha-edit-section').style.display = 'block';

            showToast(`Editando ${user.nome}`);
        }

        function resetForm() {
            document.getElementById('form-title').textContent = 'Novo usuário';
            document.getElementById('user-id').value = '';
            document.getElementById('user-form').reset();

            // Volta para modo criação
            document.getElementById('senha-create-section').style.display = 'block';
            document.getElementById('senha-edit-section').style.display = 'none';
        }

        async function deleteUser(id) {
            const confirmed = confirm('Tem certeza que deseja excluir este usuário? As tarefas atribuídas a ele ficarão sem responsável.');
            if (!confirmed) return;

            const response = await fetch(`/mvc/users/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json'
                }
            });
            const data = await response.json();

            if (!response.ok) {
                showToast(data.error || 'Erro ao excluir usuário');
                return;
            }

            showToast('Usuário removido');
            resetForm();
            loadUsers();
        }

        document.getElementById('btn-clear').addEventListener('click', resetForm);

        document.getElementById('user-search').addEventListener('input', () => {
            applySearchFilter();
            renderUsers();
        });

        document.getElementById('user-form').addEventListener('submit', async (event) => {
            event.preventDefault();

            const id    = document.getElementById('user-id').value;
            const nome  = document.getElementById('nome').value.trim();
            const email = document.getElementById('email').value.trim();

            // --- CRIAÇÃO ---
            if (!id) {
                const senha = document.getElementById('senha-create').value;
                if (!senha) {
                    showToast('Informe uma senha para o novo usuário');
                    return;
                }

                const response = await fetch('/mvc/users', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ nome, email, senha })
                });
                const data = await response.json();
                if (!response.ok) {
                    showToast(data.error || 'Erro ao criar usuário');
                    return;
                }

                showToast('Usuário criado');
                resetForm();
                loadUsers();
                return;
            }

            // --- EDIÇÃO ---
            const senhaAtual = document.getElementById('senha-atual').value;
            const senhaNova  = document.getElementById('senha-nova').value;

            if (!senhaAtual) {
                showToast('Informe a senha atual para salvar alterações');
                return;
            }

            const payload = { nome, email, senha_atual: senhaAtual };
            if (senhaNova) payload.senha = senhaNova;

            const response = await fetch(`/mvc/users/${id}`, {
                method: 'PUT',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            const data = await response.json();
            if (!response.ok) {
                showToast(data.error || 'Erro ao salvar usuário');
                return;
            }

            showToast('Usuário atualizado');
            resetForm();
            loadUsers();
        });

        loadUsers();
    </script>
</body>

</html>