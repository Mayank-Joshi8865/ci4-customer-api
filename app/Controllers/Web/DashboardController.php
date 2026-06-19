<?php

namespace App\Controllers\Web;

use App\Libraries\ReportService;
use App\Models\CommunicationLogModel;
use App\Models\CustomerModel;
use CodeIgniter\HTTP\RedirectResponse;

class DashboardController extends BaseWebController
{
    public function index(): string|RedirectResponse
    {
        $redirect = $this->requireAuth();

        if ($redirect !== null) {
            return $redirect;
        }

        $recentCustomers = (new CustomerModel())
            ->orderBy('id', 'DESC')
            ->findAll(5);

        $recentLogs = (new CommunicationLogModel())
            ->select('communication_logs.*, customers.name as customer_name, users.name as user_name')
            ->join('customers', 'customers.id = communication_logs.customer_id')
            ->join('users', 'users.id = communication_logs.user_id')
            ->orderBy('communication_logs.id', 'DESC')
            ->findAll(5);

        return $this->render('web/dashboard/index', [
            'title' => 'Dashboard',
            'report' => (new ReportService())->summary(),
            'recentCustomers' => $recentCustomers,
            'recentLogs' => $recentLogs,
        ]);
    }
}
