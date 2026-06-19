<?php

namespace App\Controllers\Web;

use App\Libraries\CustomerCsvImporter;
use App\Libraries\NotificationService;
use App\Libraries\ReportService;
use App\Models\CommunicationLogModel;
use App\Models\CustomerModel;
use CodeIgniter\HTTP\RedirectResponse;
use InvalidArgumentException;
use RuntimeException;

class CustomerController extends BaseWebController
{
    public function index(): string|RedirectResponse
    {
        $redirect = $this->requireAuth();

        if ($redirect !== null) {
            return $redirect;
        }

        $customers = new CustomerModel();

        foreach (['name', 'email', 'phone_number', 'payment_status'] as $field) {
            $value = $this->request->getGet($field);

            if ($value !== null && $value !== '') {
                $customers->like($field, $value);
            }
        }

        $search = $this->request->getGet('search');

        if ($search !== null && $search !== '') {
            $customers->groupStart()
                ->like('name', $search)
                ->orLike('email', $search)
                ->orLike('phone_number', $search)
                ->groupEnd();
        }

        $perPage = 10;
        $data = $customers->orderBy('id', 'DESC')->paginate($perPage);

        return $this->render('web/customers/index', [
            'title' => 'Customers',
            'customers' => $data,
            'pager' => $customers->pager,
            'filters' => [
                'search' => $search ?? '',
                'payment_status' => $this->request->getGet('payment_status') ?? '',
            ],
        ]);
    }

    public function show(int $id): string|RedirectResponse
    {
        $redirect = $this->requireAuth();

        if ($redirect !== null) {
            return $redirect;
        }

        $customer = (new CustomerModel())->find($id);

        if (! $customer) {
            return redirect()->to('/customers')->with('error', 'Customer not found.');
        }

        $logs = (new CommunicationLogModel())
            ->select('communication_logs.*, users.name as user_name')
            ->join('users', 'users.id = communication_logs.user_id')
            ->where('communication_logs.customer_id', $id)
            ->orderBy('communication_logs.id', 'DESC')
            ->findAll();

        return $this->render('web/customers/show', [
            'title' => $customer['name'],
            'customer' => $customer,
            'logs' => $logs,
        ]);
    }

    public function uploadForm(): string|RedirectResponse
    {
        $redirect = $this->requireRole('admin');

        if ($redirect !== null) {
            return $redirect;
        }

        return $this->render('web/customers/upload', [
            'title' => 'Upload Customer CSV',
        ]);
    }

    public function uploadCsv(): RedirectResponse
    {
        $redirect = $this->requireRole('admin');

        if ($redirect !== null) {
            return $redirect;
        }

        $rules = [
            'file' => [
                'rules' => 'uploaded[file]|max_size[file,5120]|ext_in[file,csv,txt]',
                'errors' => [
                    'uploaded' => 'A CSV file is required.',
                    'ext_in' => 'The file must be a CSV or TXT file.',
                ],
            ],
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', implode(' ', $this->validator->getErrors()));
        }

        try {
            $result = (new CustomerCsvImporter())->import($this->request->getFile('file')->getTempName());
        } catch (InvalidArgumentException | RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->to('/customers')->with(
            'success',
            sprintf(
                'CSV processed: %d total, %d inserted, %d duplicates, %d invalid.',
                $result['total_records'],
                $result['inserted_records'],
                $result['duplicate_records'],
                $result['invalid_records']
            )
        );
    }

    public function updatePaymentStatus(int $id): RedirectResponse
    {
        $redirect = $this->requireRole('user');

        if ($redirect !== null) {
            return $redirect;
        }

        $input = $this->requestInput();

        if (! $this->validateInput($input, ['payment_status' => 'required|in_list[Pending,Paid]'])) {
            return redirect()->back()->with('error', 'Choose Pending or Paid.');
        }

        $customers = new CustomerModel();
        $customer = $customers->find($id);

        if (! $customer) {
            return redirect()->to('/customers')->with('error', 'Customer not found.');
        }

        $customers->update($id, ['payment_status' => $input['payment_status']]);

        return redirect()->back()->with('success', 'Payment status updated.');
    }

    public function sendNotification(int $id): RedirectResponse
    {
        $redirect = $this->requireRole('user');

        if ($redirect !== null) {
            return $redirect;
        }

        $input = $this->requestInput();

        if (! $this->validateInput($input, ['type' => 'required|in_list[email,whatsapp]'])) {
            return redirect()->back()->with('error', 'Choose Email or WhatsApp.');
        }

        $customers = new CustomerModel();
        $customer = $customers->find($id);

        if (! $customer) {
            return redirect()->to('/customers')->with('error', 'Customer not found.');
        }

        if ($customer['payment_status'] !== 'Pending') {
            return redirect()->back()->with('error', 'Notifications can only be sent to pending customers.');
        }

        $user = $this->currentUser();
        $type = (string) $input['type'];
        (new NotificationService())->send($customer, $type);

        (new CommunicationLogModel())->insert([
            'customer_id' => $id,
            'user_id' => $user['id'],
            'type' => $type,
            'sent_at' => date('Y-m-d H:i:s'),
        ]);

        $report = (new ReportService())->summary();

        return redirect()->back()->with(
            'success',
            sprintf(
                '%s notification logged. Report: %d customers, %d paid, %d pending, %d emails, %d WhatsApp.',
                ucfirst($type),
                $report['total_customers'],
                $report['paid_customers'],
                $report['pending_customers'],
                $report['emails_sent'],
                $report['whatsapp_sent']
            )
        );
    }
}
