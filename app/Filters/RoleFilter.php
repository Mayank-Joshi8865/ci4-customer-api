<?php

namespace App\Filters;

use App\Libraries\AuthTokenService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $requiredRole = $arguments[0] ?? null;
        $user = (new AuthTokenService())->userFromRequest($request);

        if (! $user) {
            return Services::response()
                ->setStatusCode(401)
                ->setJSON(['message' => 'Unauthenticated.']);
        }

        if ($requiredRole !== null && $user['role'] !== $requiredRole) {
            return Services::response()
                ->setStatusCode(403)
                ->setJSON(['message' => 'You are not authorized to perform this action.']);
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
