<?php

namespace App\Libraries;

use App\Models\ApiTokenModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\IncomingRequest;

class AuthTokenService
{
    public function issueToken(int $userId): string
    {
        $plainToken = bin2hex(random_bytes(40));

        (new ApiTokenModel())->insert([
            'user_id' => $userId,
            'name' => 'api-token',
            'token_hash' => hash('sha256', $plainToken),
            'last_used_at' => null,
            'expires_at' => null,
        ]);

        return $plainToken;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function userFromRequest(IncomingRequest $request): ?array
    {
        $token = $this->bearerTokenFromRequest($request);

        if ($token === null) {
            return null;
        }

        $tokenModel = new ApiTokenModel();
        $record = $tokenModel->where('token_hash', hash('sha256', $token))->first();

        if (! $record) {
            return null;
        }

        if (! empty($record['expires_at']) && strtotime($record['expires_at']) < time()) {
            return null;
        }

        $tokenModel->update($record['id'], ['last_used_at' => date('Y-m-d H:i:s')]);

        return (new UserModel())->find($record['user_id']);
    }

    private function bearerTokenFromRequest(IncomingRequest $request): ?string
    {
        $header = $request->getHeaderLine('Authorization');

        if (! preg_match('/^Bearer\s+(.+)$/i', $header, $matches)) {
            return null;
        }

        return trim($matches[1]);
    }
}
