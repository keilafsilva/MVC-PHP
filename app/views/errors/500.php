<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Erro interno</title>
    <style>
        :root {
            --bg: #100b16;
            --text: #faf5ff;
            --muted: #c4b5fd;
            --accent: #a855f7;
            --accent-strong: #d8b4fe;
            --card: rgba(24, 13, 37, 0.86);
            --border: rgba(216, 180, 254, 0.16);
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
                radial-gradient(circle at 15% 20%, rgba(168, 85, 247, 0.24), transparent 26%),
                radial-gradient(circle at 85% 80%, rgba(236, 72, 153, 0.16), transparent 24%),
                linear-gradient(160deg, #09040f 0%, var(--bg) 50%, #09040f 100%);
            overflow: hidden;
        }

        body::before,
        body::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            pointer-events: none;
            filter: blur(22px);
            opacity: 0.5;
        }

        body::before {
            width: 300px;
            height: 300px;
            background: rgba(168, 85, 247, 0.2);
            top: -70px;
            left: -80px;
        }

        body::after {
            width: 260px;
            height: 260px;
            background: rgba(236, 72, 153, 0.16);
            right: -80px;
            bottom: -90px;
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
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.4);
            overflow: hidden;
        }

        .error-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(168, 85, 247, 0.12), transparent 46%);
            pointer-events: none;
        }

        .code {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid rgba(216, 180, 254, 0.2);
            background: rgba(168, 85, 247, 0.1);
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
            box-shadow: 0 0 0 6px rgba(168, 85, 247, 0.14);
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
            color: #1e1b4b;
            background: linear-gradient(135deg, #d8b4fe, #a855f7);
            box-shadow: 0 16px 32px rgba(168, 85, 247, 0.24);
        }

        .button-secondary {
            color: var(--text);
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.03);
        }

        .status-panel {
            margin-top: 28px;
            display: grid;
            gap: 12px;
        }

        .pulse-row {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .pulse {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--accent);
            box-shadow: 0 0 0 8px rgba(168, 85, 247, 0.12);
        }

        .line {
            flex: 1;
            height: 10px;
            border-radius: 999px;
            background: linear-gradient(90deg, rgba(168, 85, 247, 0.2), rgba(216, 180, 254, 0.95), rgba(236, 72, 153, 0.22));
        }

        .meta {
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
            <span class="code">Erro 500</span>
            <h1>Algo falhou por aqui</h1>
            <p>O servidor encontrou um problema inesperado ao processar sua solicitação. A página está indisponível no momento, mas a equipe pode corrigir isso em breve.</p>
            <div class="actions">
                <a class="button button-primary" href="/">Tentar novamente</a>
                <a class="button button-secondary" href="javascript:history.back()">Voltar</a>
            </div>
            <div class="status-panel" aria-hidden="true">
                <div class="pulse-row">
                    <span class="pulse"></span>
                    <span class="line"></span>
                </div>
                <div class="meta">
                    <span><strong>Status:</strong> falha interna temporária.</span>
                </div>
            </div>
        </section>
    </main>
</body>
</html>