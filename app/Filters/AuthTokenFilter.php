<?php

namespace App\Filters;

use App\Libraries\AuthTokenService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class AuthTokenFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! (new AuthTokenService())->userFromRequest($request)) {
            return Services::response()
                ->setStatusCode(401)
                ->setJSON(['message' => 'Unauthenticated.']);
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
