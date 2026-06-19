<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Libraries\ReportService;
use CodeIgniter\API\ResponseTrait;

class ReportController extends BaseController
{
    use ResponseTrait;

    public function summary()
    {
        return $this->respond([
            'report' => (new ReportService())->summary(),
        ]);
    }
}
