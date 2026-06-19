<?php
$session = service('session');
$success = $session->getFlashdata('success');
$error = $session->getFlashdata('error');
$role = $currentUser['role'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> | CI4 Customer API</title>
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">
    <style>
        :root {
            --bg: #f5f7fa;
            --surface: #ffffff;
            --surface-muted: #eef2f5;
            --text: #17202a;
            --muted: #657282;
            --line: #dce3ea;
            --primary: #0f766e;
            --primary-dark: #0b5e58;
            --danger: #b42318;
            --warning: #b45309;
            --success: #0f766e;
            --shadow: 0 10px 25px rgba(15, 23, 42, .07);
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            min-height: 100%;
            background: var(--bg);
            color: var(--text);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 15px;
            line-height: 1.5;
        }

        a {
            color: var(--primary);
            text-decoration: none;
        }

        a:hover {
            color: var(--primary-dark);
        }

        .app-shell {
            min-height: 100vh;
        }

        .topbar {
            background: var(--surface);
            border-bottom: 1px solid var(--line);
        }

        .topbar-inner {
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 14px 0;
        }

        .brand {
            display: flex;
            flex-direction: column;
            color: var(--text);
        }

        .brand strong {
            font-size: 18px;
        }

        .brand span {
            color: var(--muted);
            font-size: 13px;
        }

        .nav {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .nav a,
        .button,
        button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 38px;
            border: 1px solid var(--line);
            border-radius: 6px;
            padding: 8px 12px;
            background: var(--surface);
            color: var(--text);
            font: inherit;
            cursor: pointer;
        }

        .nav a:hover,
        .button:hover,
        button:hover {
            border-color: var(--primary);
            color: var(--primary-dark);
        }

        .button.primary,
        button.primary {
            border-color: var(--primary);
            background: var(--primary);
            color: #ffffff;
        }

        .button.primary:hover,
        button.primary:hover {
            background: var(--primary-dark);
            color: #ffffff;
        }

        .button.danger,
        button.danger {
            border-color: #f3c2bd;
            color: var(--danger);
        }

        .main {
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
            padding: 26px 0 40px;
        }

        .page-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 18px;
        }

        h1 {
            margin: 0;
            font-size: 28px;
            letter-spacing: 0;
        }

        h2 {
            margin: 0 0 14px;
            font-size: 19px;
            letter-spacing: 0;
        }

        .muted {
            color: var(--muted);
        }

        .panel {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 8px;
            box-shadow: var(--shadow);
            padding: 18px;
        }

        .grid {
            display: grid;
            gap: 18px;
        }

        .grid.two {
            grid-template-columns: 1fr 1fr;
        }

        .metrics {
            display: grid;
            grid-template-columns: repeat(5, minmax(120px, 1fr));
            gap: 12px;
        }

        .metric {
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 14px;
            background: #fbfcfd;
        }

        .metric span {
            display: block;
            color: var(--muted);
            font-size: 13px;
        }

        .metric strong {
            display: block;
            margin-top: 4px;
            font-size: 24px;
        }

        .alert {
            margin-bottom: 16px;
            border-radius: 8px;
            padding: 12px 14px;
            border: 1px solid;
            background: var(--surface);
        }

        .alert.success {
            border-color: #9fd5cf;
            color: var(--success);
            background: #eefaf8;
        }

        .alert.error {
            border-color: #f1bbb5;
            color: var(--danger);
            background: #fff1f0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 11px 10px;
            border-bottom: 1px solid var(--line);
            text-align: left;
            vertical-align: middle;
        }

        th {
            color: var(--muted);
            font-size: 12px;
            text-transform: uppercase;
        }

        tr:last-child td {
            border-bottom: 0;
        }

        .toolbar,
        .inline-form {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        input,
        select {
            min-height: 38px;
            border: 1px solid var(--line);
            border-radius: 6px;
            padding: 8px 10px;
            background: var(--surface);
            color: var(--text);
            font: inherit;
        }

        input[type="file"] {
            padding: 7px;
        }

        label {
            display: grid;
            gap: 6px;
            color: var(--muted);
            font-size: 13px;
            font-weight: 700;
        }

        label span {
            color: var(--muted);
        }

        .form-grid {
            display: grid;
            gap: 14px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            min-height: 26px;
            border-radius: 999px;
            padding: 4px 9px;
            font-size: 13px;
            font-weight: 700;
            background: var(--surface-muted);
        }

        .badge.paid {
            background: #e7f8ed;
            color: #166534;
        }

        .badge.pending {
            background: #fff7ed;
            color: var(--warning);
        }

        .auth-wrap {
            width: min(440px, calc(100% - 32px));
            margin: 56px auto;
        }

        .auth-wrap .panel {
            padding: 24px;
        }

        .pager {
            margin-top: 16px;
        }

        .pager a,
        .pager strong {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            min-height: 34px;
            margin-right: 4px;
            border: 1px solid var(--line);
            border-radius: 6px;
            background: var(--surface);
            color: var(--text);
        }

        .pager strong {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }

        @media (max-width: 900px) {
            .topbar-inner,
            .page-head {
                align-items: stretch;
                flex-direction: column;
            }

            .grid.two,
            .metrics {
                grid-template-columns: 1fr;
            }

            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>
    <div class="app-shell">
        <?php if ($currentUser): ?>
            <header class="topbar">
                <div class="topbar-inner">
                    <a class="brand" href="/dashboard">
                        <strong>CI4 Customer API</strong>
                        <span><?= esc($currentUser['name']) ?> / <?= esc($role) ?></span>
                    </a>
                    <nav class="nav" aria-label="Primary">
                        <a href="/dashboard">Dashboard</a>
                        <a href="/customers">Customers</a>
                        <?php if ($role === 'admin'): ?>
                            <a href="/admin/upload-csv">Upload CSV</a>
                        <?php endif; ?>
                        <a href="/reports">Reports</a>
                        <a href="/logout">Logout</a>
                    </nav>
                </div>
            </header>
        <?php endif; ?>

        <main class="<?= $currentUser ? 'main' : 'auth-wrap' ?>">
            <?php if ($success): ?>
                <div class="alert success"><?= esc($success) ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert error"><?= esc($error) ?></div>
            <?php endif; ?>
