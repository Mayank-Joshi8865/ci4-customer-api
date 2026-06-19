<div class="page-head">
    <div>
        <h1>Customers</h1>
        <p class="muted">Search customers, update payments, and log pending-payment notifications.</p>
    </div>
    <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
        <a class="button primary" href="/admin/upload-csv">Upload CSV</a>
    <?php endif; ?>
</div>

<section class="panel" style="margin-bottom: 18px;">
    <form class="toolbar" action="/customers" method="get">
        <label>
            <span>Search</span>
            <input type="search" name="search" value="<?= esc($filters['search']) ?>" placeholder="Name, email, phone">
        </label>

        <label>
            <span>Status</span>
            <select name="payment_status">
                <option value="">All</option>
                <option value="Pending" <?= $filters['payment_status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Paid" <?= $filters['payment_status'] === 'Paid' ? 'selected' : '' ?>>Paid</option>
            </select>
        </label>

        <button type="submit">Filter</button>
        <a class="button" href="/customers">Reset</a>
    </form>
</section>

<section class="panel">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $customer): ?>
                <tr>
                    <td><a href="/customers/<?= esc($customer['id']) ?>"><?= esc($customer['name']) ?></a></td>
                    <td><?= esc($customer['phone_number']) ?></td>
                    <td><?= esc($customer['email']) ?></td>
                    <td><?= esc(number_format((float) $customer['payment_amount'], 2)) ?></td>
                    <td>
                        <span class="badge <?= $customer['payment_status'] === 'Paid' ? 'paid' : 'pending' ?>">
                            <?= esc($customer['payment_status']) ?>
                        </span>
                    </td>
                    <td>
                        <div class="inline-form">
                            <a class="button" href="/customers/<?= esc($customer['id']) ?>">Open</a>

                            <?php if (($currentUser['role'] ?? '') === 'user'): ?>
                                <form class="inline-form" action="/customers/<?= esc($customer['id']) ?>/payment-status" method="post">
                                    <select name="payment_status" aria-label="Payment status">
                                        <option value="Pending" <?= $customer['payment_status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="Paid" <?= $customer['payment_status'] === 'Paid' ? 'selected' : '' ?>>Paid</option>
                                    </select>
                                    <button type="submit">Save</button>
                                </form>

                                <?php if ($customer['payment_status'] === 'Pending'): ?>
                                    <form action="/customers/<?= esc($customer['id']) ?>/send-notification" method="post">
                                        <input type="hidden" name="type" value="email">
                                        <button type="submit">Email</button>
                                    </form>
                                    <form action="/customers/<?= esc($customer['id']) ?>/send-notification" method="post">
                                        <input type="hidden" name="type" value="whatsapp">
                                        <button type="submit">WhatsApp</button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if ($customers === []): ?>
                <tr>
                    <td colspan="6" class="muted">No customers match your filters.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="pager">
        <?= $pager->links() ?>
    </div>
</section>
