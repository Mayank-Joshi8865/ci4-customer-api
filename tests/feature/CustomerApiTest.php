<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\TestResponse;

/**
 * @internal
 */
final class CustomerApiTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate = true;
    protected $migrateOnce = false;
    protected $refresh = true;
    protected $namespace = 'App';
    protected $seed = 'DatabaseSeeder';
    protected $basePath = APPPATH . 'Database';

    public function testLoginAcceptsJsonAndReturnsBearerToken(): void
    {
        $result = $this->withBodyFormat('json')->post('api/login', [
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        $result->assertOK();

        $body = $this->json($result);

        $this->assertNotEmpty($body['access_token']);
        $this->assertSame('Bearer', $body['token_type']);
        $this->assertSame('user', $body['user']['role']);
    }

    public function testCustomerListSupportsSearchAndPagination(): void
    {
        $token = $this->loginToken('user@example.com');

        $result = $this->withHeaders($this->authHeaders($token))
            ->get('api/customers?search=ravi&per_page=5');

        $result->assertOK();

        $body = $this->json($result);

        $this->assertSame('Ravi Customer', $body['data'][0]['name']);
        $this->assertSame(5, $body['pagination']['per_page']);
        $this->assertSame(1, $body['pagination']['total']);
    }

    public function testUserCanUpdatePaymentStatus(): void
    {
        $token = $this->loginToken('user@example.com');
        $customerId = (int) $this->grabFromDatabase('customers', 'id', [
            'email' => 'ravi.customer@example.com',
        ]);

        $result = $this->withHeaders($this->authHeaders($token))
            ->withBodyFormat('json')
            ->put("api/customer/{$customerId}/payment-status", [
                'payment_status' => 'Paid',
            ]);

        $result->assertOK();

        $this->seeInDatabase('customers', [
            'id' => $customerId,
            'payment_status' => 'Paid',
        ]);
    }

    public function testUserCanSendPendingPaymentNotificationAndReceiveReport(): void
    {
        $token = $this->loginToken('user@example.com');
        $customerId = (int) $this->grabFromDatabase('customers', 'id', [
            'email' => 'ravi.customer@example.com',
        ]);

        $result = $this->withHeaders($this->authHeaders($token))
            ->withBodyFormat('json')
            ->post("api/customer/{$customerId}/send-notification", [
                'type' => 'email',
            ]);

        $result->assertOK();

        $body = $this->json($result);

        $this->assertSame('Notification sent successfully', $body['message']);
        $this->assertSame(2, $body['report']['total_customers']);
        $this->assertSame(1, $body['report']['emails_sent']);
        $this->seeInDatabase('communication_logs', [
            'customer_id' => $customerId,
            'type' => 'email',
        ]);
    }

    public function testAdminOnlyCsvUploadIsRoleProtected(): void
    {
        $token = $this->loginToken('user@example.com');

        $result = $this->withHeaders($this->authHeaders($token))
            ->post('api/admin/upload-csv');

        $result->assertStatus(403);
    }

    /**
     * @return array<string, mixed>
     */
    private function json(TestResponse $result): array
    {
        return json_decode($result->getJSON(), true, 512, JSON_THROW_ON_ERROR);
    }

    private function loginToken(string $email): string
    {
        $result = $this->withBodyFormat('json')->post('api/login', [
            'email' => $email,
            'password' => 'password',
        ]);

        $result->assertOK();

        return (string) $this->json($result)['access_token'];
    }

    /**
     * @return array<string, string>
     */
    private function authHeaders(string $token): array
    {
        return [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ];
    }
}
