<?php

namespace App\Controllers\Web;

use App\Libraries\ReportService;
use App\Models\CommunicationLogModel;
use CodeIgniter\HTTP\RedirectResponse;

class ReportController extends BaseWebController
{
    public function index(): string|RedirectResponse
    {
        $redirect = $this->requireAuth();

        if ($redirect !== null) {
            return $redirect;
        }

        $logs = (new CommunicationLogModel())
            ->select('communication_logs.*, customers.name as customer_name, users.name as user_name')
            ->join('customers', 'customers.id = communication_logs.customer_id')
            ->join('users', 'users.id = communication_logs.user_id')
            ->orderBy('communication_logs.id', 'DESC')
            ->findAll(25);

        return $this->render('web/reports/index', [
            'title' => 'Reports',
            'report' => (new ReportService())->summary(),
            'logs' => $logs,
        ]);
    }
}
