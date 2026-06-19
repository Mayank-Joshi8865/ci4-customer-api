<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $this->upsert('users', 'email', 'admin@example.com', [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'role' => 'admin',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->upsert('users', 'email', 'user@example.com', [
            'name' => 'API User',
            'email' => 'user@example.com',
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'role' => 'user',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->upsert('customers', 'email', 'ravi.customer@example.com', [
            'name' => 'Ravi Customer',
            'phone_number' => '9876543210',
            'email' => 'ravi.customer@example.com',
            'payment_amount' => 2500,
            'payment_status' => 'Pending',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->upsert('customers', 'email', 'meera.customer@example.com', [
            'name' => 'Meera Customer',
            'phone_number' => '9876501234',
            'email' => 'meera.customer@example.com',
            'payment_amount' => 1800,
            'payment_status' => 'Paid',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function upsert(string $table, string $key, string $value, array $data): void
    {
        $builder = $this->db->table($table);
        $existing = $builder->where($key, $value)->get()->getRowArray();

        if ($existing) {
            $this->db->table($table)->where($key, $value)->update($data);
            return;
        }

        $this->db->table($table)->insert($data);
    }
}
