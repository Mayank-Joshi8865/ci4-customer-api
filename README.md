# CodeIgniter 4 Customer API

REST API implementation for the PHP Developer Assessment using CodeIgniter 4, MySQL, JSON APIs, CSV upload, role-based access, and hashed bearer-token authentication.

## Assignment Coverage

- Admin and User roles
- `POST /api/login` returns a bearer token
- Admin-only CSV upload for customer records
- Duplicate customer emails are skipped during CSV upload
- Authenticated customer listing with search and pagination
- User-only payment status updates
- User-only Email/WhatsApp notification logging for pending payments
- Summary reporting for total customers, payment status counts, and sent notification counts
- Migrations, seed data, Postman collection, and sample CSV included

## Requirements

- PHP 8.2 or newer
- Composer
- MySQL
- PHP extensions normally required by CodeIgniter 4, including `intl`, `mbstring`, `json`, and `mysqli`

## Local Setup

Create a MySQL database:

```bash
mysql -u root -p -e "CREATE DATABASE ci4_customer_api CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
```

Install dependencies:

```bash
composer install
```

Create your local environment file:

```bash
cp env .env
```

Update `.env` with your database credentials:

```ini
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080/'

database.default.hostname = localhost
database.default.database = ci4_customer_api
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306
```

Run migrations and seed data:

```bash
php spark migrate
php spark db:seed DatabaseSeeder
```

Start the API:

```bash
php spark serve
```

The API will be available at `http://127.0.0.1:8080`.

## Seeded Users

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@example.com` | `password` |
| User | `user@example.com` | `password` |

## API Endpoints

All protected endpoints require this header:

```http
Authorization: Bearer <access_token>
Accept: application/json
```

| Method | Endpoint | Access | Description |
| --- | --- | --- | --- |
| POST | `/api/login` | Public | Login and receive an access token |
| POST | `/api/admin/upload-csv` | Admin | Upload customer CSV file |
| GET | `/api/customers` | Admin/User | List customers with filters and pagination |
| PUT | `/api/customer/{id}/payment-status` | User | Update payment status to `Pending` or `Paid` |
| POST | `/api/customer/{id}/send-notification` | User | Log an `email` or `whatsapp` notification for a pending customer |
| GET | `/api/reports/summary` | Admin/User | Return customer and communication summary |

## Request Examples

Login:

```bash
curl -X POST http://127.0.0.1:8080/api/login ^
  -H "Accept: application/json" ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"user@example.com\",\"password\":\"password\"}"
```

List customers:

```bash
curl "http://127.0.0.1:8080/api/customers?search=ravi&per_page=10" ^
  -H "Accept: application/json" ^
  -H "Authorization: Bearer YOUR_TOKEN"
```

Update payment status:

```bash
curl -X PUT http://127.0.0.1:8080/api/customer/1/payment-status ^
  -H "Accept: application/json" ^
  -H "Authorization: Bearer YOUR_TOKEN" ^
  -H "Content-Type: application/json" ^
  -d "{\"payment_status\":\"Paid\"}"
```

Send notification:

```bash
curl -X POST http://127.0.0.1:8080/api/customer/1/send-notification ^
  -H "Accept: application/json" ^
  -H "Authorization: Bearer YOUR_TOKEN" ^
  -H "Content-Type: application/json" ^
  -d "{\"type\":\"email\"}"
```

## CSV Upload

Upload uses a `multipart/form-data` field named `file`.

Required CSV headers:

```csv
Name,Phone Number,Email,Payment Amount
Asha Patel,9876541111,asha.patel@example.com,1500
Rahul Shah,9876542222,rahul.shah@example.com,2200
```

Sample response:

```json
{
  "success": true,
  "total_records": 3,
  "inserted_records": 2,
  "duplicate_records": 1,
  "invalid_records": 0
}
```

Use `sample-customers.csv` for a quick upload test.

## Reports

Notification responses and `GET /api/reports/summary` include:

```json
{
  "report": {
    "total_customers": 2,
    "paid_customers": 1,
    "pending_customers": 1,
    "emails_sent": 1,
    "whatsapp_sent": 0
  }
}
```

## Postman

Import `postman_collection.json` into Postman.

1. Run the Admin Login request to set `admin_token`.
2. Run the User Login request to set `user_token`.
3. Use Upload Customer CSV with `sample-customers.csv`.
4. Use the Customers and Reports requests to test the rest of the flow.

## Tests

Run the test suite:

```bash
composer test
```

The added feature tests cover JSON login, customer search/pagination, payment status updates, notification logging/reporting, and admin-only CSV upload protection. The test database uses the SQLite in-memory `tests` connection from `app/Config/Database.php`.

## Authentication Note

The original assessment mentions Laravel Sanctum/JWT because Laravel was preferred. This CodeIgniter 4 version implements the same API behavior with hashed bearer tokens stored in the `api_tokens` table.

## Push To GitHub

Initialize Git from this project folder:

```bash
git init
git add .
git commit -m "Complete CI4 customer API assignment"
```

Create a new empty GitHub repository, then connect and push it:

```bash
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/ci4-customer-api.git
git push -u origin main
```

After pushing, submit the repository URL, for example:

```text
https://github.com/YOUR_USERNAME/ci4-customer-api
```

Do not commit `.env` or `vendor/`; both are ignored by `.gitignore`.
