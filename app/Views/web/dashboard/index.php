<div class="page-head">
    <div>
        <h1>Dashboard</h1>
        <p class="muted">Customer payment and communication summary.</p>
    </div>
    <div class="toolbar">
        <a class="button" href="/customers">Customers</a>
        <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
            <a class="button primary" href="/admin/upload-csv">Upload CSV</a>
        <?php endif; ?>
    </div>
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

<div class="grid two" style="margin-top: 18px;">
    <section class="panel">
        <h2>Recent Customers</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentCustomers as $customer): ?>
                    <tr>
                        <td><a href="/customers/<?= esc($customer['id']) ?>"><?= esc($customer['name']) ?></a></td>
                        <td>
                            <span class="badge <?= $customer['payment_status'] === 'Paid' ? 'paid' : 'pending' ?>">
                                <?= esc($customer['payment_status']) ?>
                            </span>
                        </td>
                        <td><?= esc(number_format((float) $customer['payment_amount'], 2)) ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php if ($recentCustomers === []): ?>
                    <tr>
                        <td colspan="3" class="muted">No customers found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

    <section class="panel">
        <h2>Recent Communication</h2>
        <table>
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Type</th>
                    <th>Sent At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentLogs as $log): ?>
                    <tr>
                        <td><?= esc($log['customer_name']) ?></td>
                        <td><?= esc(ucfirst($log['type'])) ?></td>
                        <td><?= esc($log['sent_at']) ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php if ($recentLogs === []): ?>
                    <tr>
                        <td colspan="3" class="muted">No communication logs yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</div>
