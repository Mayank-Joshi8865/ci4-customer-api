<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;

abstract class BaseWebController extends BaseController
{
    /**
     * @return array<string, mixed>|null
     */
    protected function currentUser(): ?array
    {
        $user = service('session')->get('user');

        return is_array($user) ? $user : null;
    }

    protected function requireAuth(): ?RedirectResponse
    {
        if ($this->currentUser() !== null) {
            return null;
        }

        return redirect()->to('/login')->with('error', 'Please sign in to continue.');
    }

    protected function requireRole(string $role): ?RedirectResponse
    {
        $authRedirect = $this->requireAuth();

        if ($authRedirect !== null) {
            return $authRedirect;
        }

        $user = $this->currentUser();

        if ($user !== null && $user['role'] === $role) {
            return null;
        }

        return redirect()->to('/dashboard')->with('error', 'You are not authorized to access that page.');
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function render(string $view, array $data = []): string
    {
        $data['currentUser'] = $this->currentUser();
        $data['title'] = $data['title'] ?? 'CI4 Customer API';

        return view('web/layout/header', $data)
            . view($view, $data)
            . view('web/layout/footer', $data);
    }
}
