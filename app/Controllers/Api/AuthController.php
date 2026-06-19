<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Libraries\AuthTokenService;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class AuthController extends BaseController
{
    use ResponseTrait;

    public function login()
    {
        $input = $this->requestInput();
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required',
        ];

        if (! $this->validateInput($input, $rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $user = (new UserModel())->where('email', $input['email'])->first();

        if (! $user || ! password_verify((string) $input['password'], $user['password'])) {
            return $this->failValidationErrors(['email' => 'Invalid email or password.']);
        }

        $token = (new AuthTokenService())->issueToken((int) $user['id']);

        return $this->respond([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => (int) $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
            ],
        ]);
    }
}
