<?php

namespace App\Models;

use CodeIgniter\Model;

class AccessModel extends Model
{
    protected $table = 'access';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'users', 'access'];
}
