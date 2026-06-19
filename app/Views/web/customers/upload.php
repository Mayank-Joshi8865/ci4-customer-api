<div class="page-head">
    <div>
        <h1>Upload Customer CSV</h1>
        <p class="muted">Admin upload for customer records with duplicate email handling.</p>
    </div>
    <a class="button" href="/customers">Customers</a>
</div>

<div class="grid two">
    <section class="panel">
        <h2>CSV File</h2>
        <form class="form-grid" action="/admin/upload-csv" method="post" enctype="multipart/form-data">
            <label>
                <span>Customer CSV</span>
                <input type="file" name="file" accept=".csv,.txt" required>
            </label>

            <button class="primary" type="submit">Upload</button>
        </form>
    </section>

    <section class="panel">
        <h2>Required Columns</h2>
        <table>
            <tbody>
                <tr>
                    <th>Name</th>
                    <td>Customer full name</td>
                </tr>
                <tr>
                    <th>Phone Number</th>
                    <td>Customer phone number</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>Unique customer email</td>
                </tr>
                <tr>
                    <th>Payment Amount</th>
                    <td>Numeric payment amount</td>
                </tr>
            </tbody>
        </table>
    </section>
</div>

<section class="panel" style="margin-top: 18px;">
    <h2>Sample</h2>
    <pre style="margin:0; overflow-x:auto;"><code>Name,Phone Number,Email,Payment Amount
Asha Patel,9876541111,asha.patel@example.com,1500
Rahul Shah,9876542222,rahul.shah@example.com,2200</code></pre>
</section>
