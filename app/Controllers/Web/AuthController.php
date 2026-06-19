<?php

namespace App\Controllers\Web;

use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class AuthController extends BaseWebController
{
    public function showLogin(): string|RedirectResponse
    {
        if ($this->currentUser() !== null) {
            return redirect()->to('/dashboard');
        }

        return $this->render('web/auth/login', [
            'title' => 'Sign In',
        ]);
    }

    public function login(): RedirectResponse
    {
        $input = $this->requestInput();
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required',
        ];

        if (! $this->validateInput($input, $rules)) {
            return redirect()->back()->withInput()->with('error', 'Enter a valid email and password.');
        }

        $user = (new UserModel())->where('email', $input['email'])->first();

        if (! $user || ! password_verify((string) $input['password'], $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Invalid email or password.');
        }

        service('session')->regenerate(true);
        service('session')->set('user', [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ]);

        return redirect()->to('/dashboard')->with('success', 'Signed in successfully.');
    }

    public function logout(): RedirectResponse
    {
        service('session')->destroy();

        return redirect()->to('/login');
    }
}
