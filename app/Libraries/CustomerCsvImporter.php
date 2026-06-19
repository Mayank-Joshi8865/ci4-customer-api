<?php

namespace App\Libraries;

use App\Models\CustomerModel;
use InvalidArgumentException;
use RuntimeException;

class CustomerCsvImporter
{
    /**
     * @return array<string, bool|int>
     */
    public function import(string $path): array
    {
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new RuntimeException('Unable to read uploaded CSV file.');
        }

        $headers = $this->normalizeHeaders(fgetcsv($handle) ?: []);

        if (! $this->hasRequiredCsvHeaders($headers)) {
            fclose($handle);

            throw new InvalidArgumentException('The CSV must include Name, Phone Number, Email, and Payment Amount columns.');
        }

        $customers = new CustomerModel();
        $seenEmails = [];
        $total = 0;
        $inserted = 0;
        $duplicates = 0;
        $invalid = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if ($this->isEmptyCsvRow($row)) {
                continue;
            }

            $total++;
            $record = $this->rowToCustomerData($headers, $row);

            if ($record === null) {
                $invalid++;
                continue;
            }

            $email = strtolower($record['email']);

            if (isset($seenEmails[$email]) || $customers->where('email', $record['email'])->first()) {
                $duplicates++;
                continue;
            }

            $seenEmails[$email] = true;
            $customers->insert($record);
            $inserted++;
        }

        fclose($handle);

        return [
            'success' => true,
            'total_records' => $total,
            'inserted_records' => $inserted,
            'duplicate_records' => $duplicates,
            'invalid_records' => $invalid,
        ];
    }

    /**
     * @param array<int, string|null> $headers
     * @return array<int, string>
     */
    private function normalizeHeaders(array $headers): array
    {
        return array_map(
            static fn ($header) => str_replace(' ', '_', strtolower(trim((string) $header, "\xEF\xBB\xBF \t\n\r\0\x0B"))),
            $headers
        );
    }

    /**
     * @param array<int, string> $headers
     */
    private function hasRequiredCsvHeaders(array $headers): bool
    {
        foreach (['name', 'phone_number', 'email', 'payment_amount'] as $requiredHeader) {
            if (! in_array($requiredHeader, $headers, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array<int, string|null> $row
     */
    private function isEmptyCsvRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array<int, string> $headers
     * @param array<int, string|null> $row
     * @return array<string, string>|null
     */
    private function rowToCustomerData(array $headers, array $row): ?array
    {
        $data = [];

        foreach ($headers as $index => $header) {
            $data[$header] = trim((string) ($row[$index] ?? ''));
        }

        $email = $data['email'] ?? '';

        if (
            ($data['name'] ?? '') === ''
            || ($data['phone_number'] ?? '') === ''
            || $email === ''
            || ! filter_var($email, FILTER_VALIDATE_EMAIL)
            || ! is_numeric($data['payment_amount'] ?? null)
        ) {
            return null;
        }

        return [
            'name' => $data['name'],
            'phone_number' => $data['phone_number'],
            'email' => $email,
            'payment_amount' => (string) ((float) $data['payment_amount']),
            'payment_status' => 'Pending',
        ];
    }
}
