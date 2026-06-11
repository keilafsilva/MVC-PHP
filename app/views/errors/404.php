<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página não encontrada</title>
    <style>
        :root {
            --bg: #07111d;
            --text: #f8fafc;
            --muted: #9fb0c9;
            --accent: #38bdf8;
            --accent-strong: #7dd3fc;
            --card: rgba(10, 18, 33, 0.88);
            --border: rgba(125, 211, 252, 0.14);
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
                radial-gradient(circle at top right, rgba(56, 189, 248, 0.2), transparent 30%),
                radial-gradient(circle at bottom left, rgba(14, 165, 233, 0.18), transparent 26%),
                linear-gradient(160deg, #030712 0%, var(--bg) 52%, #020817 100%);
            overflow: hidden;
        }

        body::before,
        body::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            pointer-events: none;
            filter: blur(24px);
            opacity: 0.45;
        }

        body::before {
            width: 320px;
            height: 320px;
            background: rgba(56, 189, 248, 0.18);
            top: -100px;
            right: -80px;
        }

        body::after {
            width: 280px;
            height: 280px;
            background: rgba(14, 165, 233, 0.12);
            left: -90px;
            bottom: -80px;
        }

        .error-shell {
            position: relative;
            width: min(92vw, 760px);
            padding: 28px;
        }

        .error-card {
            position: relative;
            padding: 42px;
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
            background: linear-gradient(135deg, rgba(56, 189, 248, 0.12), transparent 50%);
            pointer-events: none;
        }

        .code {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid rgba(125, 211, 252, 0.2);
            background: rgba(56, 189, 248, 0.08);
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
            box-shadow: 0 0 0 6px rgba(56, 189, 248, 0.14);
        }

        h1 {
            margin: 20px 0 14px;
            font-size: clamp(2.6rem, 7vw, 5rem);
            line-height: 0.95;
            letter-spacing: -0.06em;
        }

        p {
            margin: 0;
            max-width: 60ch;
            color: var(--muted);
            font-size: 1.05rem;
            line-height: 1.7;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 30px;
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
            color: #082f49;
            background: linear-gradient(135deg, #7dd3fc, #38bdf8);
            box-shadow: 0 16px 32px rgba(56, 189, 248, 0.22);
        }

        .button-secondary {
            color: var(--text);
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.03);
        }

        .art {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-top: 28px;
        }

        .tile {
            height: 12px;
            border-radius: 999px;
            background: linear-gradient(90deg, rgba(125, 211, 252, 0.2), rgba(56, 189, 248, 0.85), rgba(14, 165, 233, 0.25));
            box-shadow: 0 0 24px rgba(56, 189, 248, 0.2);
        }

        .meta {
            margin-top: 22px;
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

            .art {
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <main class="error-shell">
        <section class="error-card">
            <span class="code">Erro 404</span>
            <h1>Página fora do mapa</h1>
            <p>O endereço solicitado não existe, foi movido ou nunca esteve aqui. Confira o link, ou retorne para a área principal e continue navegando.</p>
            <div class="actions">
                <a class="button button-primary" href="/">Ir para o início</a>
                <a class="button button-secondary" href="javascript:history.back()">Voltar</a>
            </div>
            <div class="art" aria-hidden="true">
                <span class="tile"></span>
                <span class="tile"></span>
                <span class="tile"></span>
            </div>
            <div class="meta">
                <span><strong>Dica:</strong> se veio de um link, ele pode estar desatualizado.</span>
            </div>
        </section>
    </main>
</body>
</html>