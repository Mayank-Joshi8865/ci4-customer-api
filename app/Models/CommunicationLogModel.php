<?php

namespace App\Models;

use CodeIgniter\Model;

class CommunicationLogModel extends Model
{
    protected $table = 'communication_logs';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['customer_id', 'user_id', 'type', 'sent_at'];
}
