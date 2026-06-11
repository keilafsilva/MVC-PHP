<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Acesso negado</title>
    <style>
        :root {
            --bg: #0b1020;
            --bg-soft: #111935;
            --text: #eef2ff;
            --muted: #a8b3d6;
            --accent: #f59e0b;
            --accent-strong: #fbbf24;
            --card: rgba(17, 25, 53, 0.86);
            --border: rgba(255, 255, 255, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: Inter, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(245, 158, 11, 0.22), transparent 30%),
                radial-gradient(circle at bottom right, rgba(99, 102, 241, 0.18), transparent 28%),
                linear-gradient(160deg, #070b16 0%, var(--bg) 50%, #070b16 100%);
            overflow: hidden;
        }

        body::before,
        body::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            filter: blur(20px);
            opacity: 0.45;
            pointer-events: none;
        }

        body::before {
            width: 260px;
            height: 260px;
            background: rgba(245, 158, 11, 0.2);
            top: -60px;
            left: -70px;
        }

        body::after {
            width: 300px;
            height: 300px;
            background: rgba(99, 102, 241, 0.18);
            right: -90px;
            bottom: -80px;
        }

        .error-shell {
            position: relative;
            width: min(92vw, 720px);
            padding: 28px;
        }

        .error-card {
            position: relative;
            padding: 40px;
            border: 1px solid var(--border);
            border-radius: 28px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0.02)), var(--card);
            backdrop-filter: blur(18px);
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.38);
            overflow: hidden;
        }

        .error-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.12), transparent 48%);
            pointer-events: none;
        }

        .code {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid rgba(245, 158, 11, 0.25);
            background: rgba(245, 158, 11, 0.1);
            color: var(--accent-strong);
            font-size: 0.92rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .code::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent);
            box-shadow: 0 0 0 6px rgba(245, 158, 11, 0.15);
        }

        h1 {
            margin: 20px 0 14px;
            font-size: clamp(2.4rem, 7vw, 4.8rem);
            line-height: 0.95;
            letter-spacing: -0.06em;
        }

        p {
            margin: 0;
            max-width: 58ch;
            color: var(--muted);
            font-size: 1.05rem;
            line-height: 1.7;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 28px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            padding: 0 18px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 700;
            transition: transform 160ms ease, box-shadow 160ms ease, border-color 160ms ease;
        }

        .button:hover {
            transform: translateY(-1px);
        }

        .button-primary {
            color: #111827;
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            box-shadow: 0 16px 32px rgba(245, 158, 11, 0.25);
        }

        .button-secondary {
            color: var(--text);
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.03);
        }

        .meta {
            margin-top: 24px;
            display: grid;
            gap: 8px;
            color: var(--muted);
            font-size: 0.94rem;
        }

        .meta strong {
            color: var(--text);
            font-weight: 600;
        }

        @media (max-width: 640px) {
            .error-shell {
                width: 100%;
                padding: 16px;
            }

            .error-card {
                padding: 28px 22px;
                border-radius: 22px;
            }

            .actions {
                flex-direction: column;
            }

            .button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <main class="error-shell">
        <section class="error-card">
            <span class="code">Erro 403</span>
            <h1>Acesso negado</h1>
            <p>Você chegou até aqui, mas não tem permissão para abrir este recurso. Se isso parecer um engano, volte para a página inicial ou tente novamente com uma conta autorizada.</p>
            <div class="actions">
                <a class="button button-primary" href="/">Voltar para o início</a>
                <a class="button button-secondary" href="javascript:history.back()">Voltar à página anterior</a>
            </div>
            <div class="meta">
                <span><strong>Estado:</strong> solicitação bloqueada por permissão insuficiente.</span>
            </div>
        </section>
    </main>
</body>
</html>