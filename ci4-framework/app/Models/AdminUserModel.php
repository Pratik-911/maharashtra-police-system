<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminUserModel extends Model
{
    protected $table = 'admin_users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['username', 'password', 'full_name', 'email'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[100]|is_unique[admin_users.username,id,{id}]',
        'password' => 'required|min_length[6]',
        'full_name' => 'required|min_length[2]|max_length[255]',
        'email' => 'required|valid_email|is_unique[admin_users.email,id,{id}]'
    ];

    protected $validationMessages = [
        'username' => [
            'required' => 'वापरकर्ता नाव आवश्यक आहे',
            'min_length' => 'वापरकर्ता नाव किमान 3 अक्षरांचे असावे',
            'is_unique' => 'हे वापरकर्ता नाव आधीच वापरले गेले आहे'
        ],
        'password' => [
            'required' => 'पासवर्ड आवश्यक आहे',
            'min_length' => 'पासवर्ड किमान 6 अक्षरांचा असावा'
        ],
        'full_name' => [
            'required' => 'पूर्ण नाव आवश्यक आहे',
            'min_length' => 'नाव किमान 2 अक्षरांचे असावे'
        ],
        'email' => [
            'required' => 'ईमेल आवश्यक आहे',
            'valid_email' => 'वैध ईमेल प्रविष्ट करा',
            'is_unique' => 'हा ईमेल आधीच वापरला गेला आहे'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    public function getAdminByUsername($username)
    {
        return $this->where('username', $username)->first();
    }

    public function authenticate($username, $password)
    {
        $admin = $this->getAdminByUsername($username);
        
        if ($admin && $this->verifyPassword($password, $admin['password'])) {
            return $admin;
        }
        
        return false;
    }
}
