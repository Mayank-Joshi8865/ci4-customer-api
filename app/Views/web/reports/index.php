<div class="page-head">
    <div>
        <h1>Reports</h1>
        <p class="muted">Summary counts from customers and communication logs.</p>
    </div>
    <a class="button" href="/dashboard">Dashboard</a>
</div>

<section class="metrics">
    <div class="metric">
        <span>Total Customers</span>
        <strong><?= esc($report['total_customers']) ?></strong>
    </div>
    <div class="metric">
        <span>Paid Customers</span>
        <strong><?= esc($report['paid_customers']) ?></strong>
    </div>
    <div class="metric">
        <span>Pending Customers</span>
        <strong><?= esc($report['pending_customers']) ?></strong>
    </div>
    <div class="metric">
        <span>Emails Sent</span>
        <strong><?= esc($report['emails_sent']) ?></strong>
    </div>
    <div class="metric">
        <span>WhatsApp Sent</span>
        <strong><?= esc($report['whatsapp_sent']) ?></strong>
    </div>
</section>

<section class="panel" style="margin-top: 18px;">
    <h2>Communication Logs</h2>
    <table>
        <thead>
            <tr>
                <th>Customer</th>
                <th>User</th>
                <th>Type</th>
                <th>Sent At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= esc($log['customer_name']) ?></td>
                    <td><?= esc($log['user_name']) ?></td>
                    <td><?= esc(ucfirst($log['type'])) ?></td>
                    <td><?= esc($log['sent_at']) ?></td>
                </tr>
            <?php endforeach; ?>

            <?php if ($logs === []): ?>
                <tr>
                    <td colspan="4" class="muted">No communication logs yet.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>
