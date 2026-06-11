<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tasks Colaborativas</title>
    <link rel="stylesheet" href="/mvc/app/views/home/style.css">
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo">Tasks Colaborativas</div>
            <p>Gerencie suas tarefas colaborativas com eficiência</p>
        </div>
        <form id="form-login" class="login-form">
            <div class="input-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" placeholder="seu@email.com" required>
            </div>
            <div class="input-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" placeholder="••••••••" required>
            </div>
            <button type="button" class="btn-login" onclick="login()">Entrar</button>
        </form>
    </div>
    <script>
        async function login() {
            const email = document.getElementById('email').value;
            const senha = document.getElementById('senha').value;
            if (!email || !senha) {
                alert('Por favor, preencha todos os campos.');
                return;
            }
            const response = await fetch('/mvc/auth/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    email,
                    senha
                })
            });
            const data = await response.json();
            if (response.ok) {
                localStorage.setItem('token', data.token);
                localStorage.setItem('nome', data.usuario.nome); 

                window.location.href = '/mvc/home/tasks';
            } else {
                alert(data.error);
            }
        }
    </script>
</body>

</html>