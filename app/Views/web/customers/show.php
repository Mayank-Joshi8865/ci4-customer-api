<div class="page-head">
    <div>
        <h1><?= esc($customer['name']) ?></h1>
        <p class="muted"><?= esc($customer['email']) ?> / <?= esc($customer['phone_number']) ?></p>
    </div>
    <a class="button" href="/customers">Back to Customers</a>
</div>

<div class="grid two">
    <section class="panel">
        <h2>Customer Details</h2>
        <table>
            <tbody>
                <tr>
                    <th>Name</th>
                    <td><?= esc($customer['name']) ?></td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td><?= esc($customer['phone_number']) ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?= esc($customer['email']) ?></td>
                </tr>
                <tr>
                    <th>Payment Amount</th>
                    <td><?= esc(number_format((float) $customer['payment_amount'], 2)) ?></td>
                </tr>
                <tr>
                    <th>Payment Status</th>
                    <td>
                        <span class="badge <?= $customer['payment_status'] === 'Paid' ? 'paid' : 'pending' ?>">
                            <?= esc($customer['payment_status']) ?>
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </section>

    <section class="panel">
        <h2>Actions</h2>

        <?php if (($currentUser['role'] ?? '') === 'user'): ?>
            <form class="form-grid" action="/customers/<?= esc($customer['id']) ?>/payment-status" method="post">
                <label>
                    <span>Payment Status</span>
                    <select name="payment_status">
                        <option value="Pending" <?= $customer['payment_status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Paid" <?= $customer['payment_status'] === 'Paid' ? 'selected' : '' ?>>Paid</option>
                    </select>
                </label>
                <button class="primary" type="submit">Update Status</button>
            </form>

            <?php if ($customer['payment_status'] === 'Pending'): ?>
                <div class="toolbar" style="margin-top: 18px;">
                    <form action="/customers/<?= esc($customer['id']) ?>/send-notification" method="post">
                        <input type="hidden" name="type" value="email">
                        <button type="submit">Send Email</button>
                    </form>
                    <form action="/customers/<?= esc($customer['id']) ?>/send-notification" method="post">
                        <input type="hidden" name="type" value="whatsapp">
                        <button type="submit">Send WhatsApp</button>
                    </form>
                </div>
            <?php else: ?>
                <p class="muted" style="margin-bottom: 0;">Notifications are available only for pending payments.</p>
            <?php endif; ?>
        <?php else: ?>
            <p class="muted" style="margin-bottom: 0;">Admin accounts can upload customers and view reports. User accounts update payments and send notifications.</p>
        <?php endif; ?>
    </section>
</div>

<section class="panel" style="margin-top: 18px;">
    <h2>Communication Logs</h2>
    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Sent By</th>
                <th>Sent At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= esc(ucfirst($log['type'])) ?></td>
                    <td><?= esc($log['user_name']) ?></td>
                    <td><?= esc($log['sent_at']) ?></td>
                </tr>
            <?php endforeach; ?>

            <?php if ($logs === []): ?>
                <tr>
                    <td colspan="3" class="muted">No notifications have been logged for this customer.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>
