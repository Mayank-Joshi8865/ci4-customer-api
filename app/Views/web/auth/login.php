<section class="panel">
    <div class="page-head">
        <div>
            <h1>Sign In</h1>
            <p class="muted">Use the seeded admin or user account to open the application.</p>
        </div>
    </div>

    <form class="form-grid" action="/login" method="post">
        <label>
            <span>Email</span>
            <input type="email" name="email" value="<?= esc(old('email') ?? '') ?>" required autofocus>
        </label>

        <label>
            <span>Password</span>
            <input type="password" name="password" required>
        </label>

        <button class="primary" type="submit">Sign In</button>
    </form>
</section>

<section class="panel" style="margin-top: 16px;">
    <h2>Seeded Accounts</h2>
    <table>
        <thead>
            <tr>
                <th>Role</th>
                <th>Email</th>
                <th>Password</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Admin</td>
                <td><code>admin@example.com</code></td>
                <td><code>password</code></td>
            </tr>
            <tr>
                <td>User</td>
                <td><code>user@example.com</code></td>
                <td><code>password</code></td>
            </tr>
        </tbody>
    </table>
</section>
