<?php

namespace App\Libraries;

use App\Models\CommunicationLogModel;
use App\Models\CustomerModel;

class ReportService
{
    /**
     * @return array<string, int>
     */
    public function summary(): array
    {
        $customers = new CustomerModel();
        $logs = new CommunicationLogModel();

        return [
            'total_customers' => $customers->countAllResults(),
            'paid_customers' => $customers->where('payment_status', 'Paid')->countAllResults(),
            'pending_customers' => $customers->where('payment_status', 'Pending')->countAllResults(),
            'emails_sent' => $logs->where('type', 'email')->countAllResults(),
            'whatsapp_sent' => $logs->where('type', 'whatsapp')->countAllResults(),
        ];
    }
}
