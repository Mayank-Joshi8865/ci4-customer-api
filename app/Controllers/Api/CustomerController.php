<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Libraries\AuthTokenService;
use App\Libraries\CustomerCsvImporter;
use App\Libraries\NotificationService;
use App\Libraries\ReportService;
use App\Models\CommunicationLogModel;
use App\Models\CustomerModel;
use CodeIgniter\API\ResponseTrait;
use InvalidArgumentException;
use RuntimeException;

class CustomerController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $customers = new CustomerModel();

        foreach (['name', 'email', 'phone_number'] as $field) {
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

        $perPage = min(max((int) ($this->request->getGet('per_page') ?? 15), 1), 100);
        $data = $customers->orderBy('id', 'DESC')->paginate($perPage);
        $pager = $customers->pager;

        return $this->respond([
            'data' => $data,
            'pagination' => [
                'current_page' => $pager->getCurrentPage(),
                'per_page' => $pager->getPerPage(),
                'total' => $pager->getTotal(),
                'last_page' => $pager->getPageCount(),
            ],
        ]);
    }

    public function uploadCsv()
    {
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
            return $this->failValidationErrors($this->validator->getErrors());
        }

        try {
            $result = (new CustomerCsvImporter())->import($this->request->getFile('file')->getTempName());
        } catch (InvalidArgumentException $exception) {
            return $this->failValidationErrors(['file' => $exception->getMessage()]);
        } catch (RuntimeException $exception) {
            return $this->fail($exception->getMessage(), 422);
        }

        return $this->respond($result);
    }

    public function updatePaymentStatus(int $id)
    {
        $input = $this->requestInput();
        $rules = [
            'payment_status' => 'required|in_list[Pending,Paid]',
        ];

        if (! $this->validateInput($input, $rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $customers = new CustomerModel();
        $customer = $customers->find($id);

        if (! $customer) {
            return $this->failNotFound('Customer not found.');
        }

        $customers->update($id, [
            'payment_status' => $input['payment_status'],
        ]);

        return $this->respond([
            'message' => 'Payment status updated successfully',
            'customer' => $customers->find($id),
        ]);
    }

    public function sendNotification(int $id)
    {
        $input = $this->requestInput();
        $rules = [
            'type' => 'required|in_list[email,whatsapp]',
        ];

        if (! $this->validateInput($input, $rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $customers = new CustomerModel();
        $customer = $customers->find($id);

        if (! $customer) {
            return $this->failNotFound('Customer not found.');
        }

        if ($customer['payment_status'] !== 'Pending') {
            return $this->fail('Notifications can only be sent to customers with pending payments.', 422);
        }

        $user = (new AuthTokenService())->userFromRequest($this->request);

        if (! $user) {
            return $this->failUnauthorized('Unauthenticated.');
        }

        $type = (string) $input['type'];
        (new NotificationService())->send($customer, $type);

        (new CommunicationLogModel())->insert([
            'customer_id' => $id,
            'user_id' => $user['id'],
            'type' => $type,
            'sent_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->respond([
            'message' => 'Notification sent successfully',
            'report' => (new ReportService())->summary(),
        ]);
    }

}
