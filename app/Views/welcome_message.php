<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CI4 Customer API</title>
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">
    <style>
        :root {
            color-scheme: light;
            --bg: #f6f7f9;
            --panel: #ffffff;
            --text: #17202a;
            --muted: #5f6b7a;
            --line: #dfe4ea;
            --brand: #0f766e;
            --accent: #b45309;
            --code: #111827;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 16px;
            line-height: 1.5;
        }

        .shell {
            width: min(1120px, calc(100% - 32px));
            margin: 0 auto;
            padding: 36px 0;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--line);
        }

        .title {
            margin: 0;
            font-size: 30px;
            font-weight: 700;
            letter-spacing: 0;
        }

        .subtitle {
            margin: 6px 0 0;
            color: var(--muted);
        }

        .status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border: 1px solid #b8ded8;
            border-radius: 6px;
            background: #e9f7f5;
            color: #0f5c56;
            font-weight: 700;
            white-space: nowrap;
        }

        .dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: var(--brand);
        }

        main {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 24px;
            padding-top: 24px;
        }

        section,
        aside {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 22px;
        }

        h2 {
            margin: 0 0 14px;
            font-size: 20px;
            letter-spacing: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            overflow-wrap: anywhere;
        }

        th,
        td {
            padding: 12px 10px;
            border-bottom: 1px solid var(--line);
            text-align: left;
            vertical-align: top;
        }

        th {
            color: var(--muted);
            font-size: 13px;
            text-transform: uppercase;
        }

        tr:last-child td {
            border-bottom: 0;
        }

        code {
            color: var(--code);
            background: #f0f2f5;
            border: 1px solid #e3e7ed;
            border-radius: 5px;
            padding: 2px 5px;
            font-family: Consolas, Monaco, monospace;
            font-size: 14px;
        }

        .stack {
            display: grid;
            gap: 18px;
        }

        .login {
            display: grid;
            gap: 10px;
            margin: 0;
        }

        .login div {
            padding: 12px;
            border: 1px solid var(--line);
            border-radius: 6px;
            background: #fbfcfd;
        }

        .label {
            display: block;
            color: var(--muted);
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .note {
            margin: 0;
            color: var(--muted);
        }

        .command {
            display: block;
            margin-top: 10px;
            padding: 12px;
            color: #f9fafb;
            background: #111827;
            border-radius: 6px;
            overflow-x: auto;
            white-space: nowrap;
        }

        .pill {
            display: inline-block;
            min-width: 54px;
            padding: 3px 8px;
            border-radius: 999px;
            background: #fff4e5;
            color: var(--accent);
            font-size: 13px;
            font-weight: 700;
            text-align: center;
        }

        @media (max-width: 860px) {
            header,
            main {
                grid-template-columns: 1fr;
            }

            header {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <header>
            <div>
                <h1 class="title">CI4 Customer API</h1>
                <p class="subtitle">PHP assignment REST API for customers, payments, notifications, and reports.</p>
            </div>
            <div class="status"><span class="dot"></span> Running</div>
        </header>

        <main>
            <section>
                <h2>API Endpoints</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Method</th>
                            <th>Endpoint</th>
                            <th>Access</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="pill">POST</span></td>
                            <td><code>/api/login</code></td>
                            <td>Public</td>
                        </tr>
                        <tr>
                            <td><span class="pill">POST</span></td>
                            <td><code>/api/admin/upload-csv</code></td>
                            <td>Admin token</td>
                        </tr>
                        <tr>
                            <td><span class="pill">GET</span></td>
                            <td><code>/api/customers?search=ravi&amp;per_page=10</code></td>
                            <td>Admin/User token</td>
                        </tr>
                        <tr>
                            <td><span class="pill">PUT</span></td>
                            <td><code>/api/customer/{id}/payment-status</code></td>
                            <td>User token</td>
                        </tr>
                        <tr>
                            <td><span class="pill">POST</span></td>
                            <td><code>/api/customer/{id}/send-notification</code></td>
                            <td>User token</td>
                        </tr>
                        <tr>
                            <td><span class="pill">GET</span></td>
                            <td><code>/api/reports/summary</code></td>
                            <td>Admin/User token</td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <aside class="stack">
                <section>
                    <h2>Seeded Logins</h2>
                    <div class="login">
                        <div>
                            <span class="label">Admin</span>
                            <code>admin@example.com</code><br>
                            <code>password</code>
                        </div>
                        <div>
                            <span class="label">User</span>
                            <code>user@example.com</code><br>
                            <code>password</code>
                        </div>
                    </div>
                </section>

                <section>
                    <h2>Postman Base URL</h2>
                    <p class="note">Import <code>postman_collection.json</code> and use:</p>
                    <code class="command">http://127.0.0.1:8080</code>
                </section>
            </aside>
        </main>
    </div>
</body>
</html>
