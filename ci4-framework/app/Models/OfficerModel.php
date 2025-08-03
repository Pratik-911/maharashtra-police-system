<?php

namespace App\Models;

use CodeIgniter\Model;

class OfficerModel extends Model
{
    protected $table = 'officers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name', 'badge_no', 'rank', 'police_station', 'mobile', 'password'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[255]',
        'badge_no' => 'required|is_unique[officers.badge_no,id,{id}]',
        'rank' => 'required|max_length[100]',
        'police_station' => 'required|max_length[255]',
        'mobile' => 'required|regex_match[/^[0-9]{10}$/]',
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'अधिकाऱ्याचे नाव आवश्यक आहे',
            'min_length' => 'नाव किमान 2 अक्षरांचे असावे',
            'max_length' => 'नाव 255 अक्षरांपेक्षा जास्त असू शकत नाही'
        ],
        'badge_no' => [
            'required' => 'बॅज नंबर आवश्यक आहे',
            'is_unique' => 'हा बॅज नंबर आधीच वापरला गेला आहे'
        ],
        'rank' => [
            'required' => 'पद आवश्यक आहे'
        ],
        'police_station' => [
            'required' => 'पोलीस स्टेशन आवश्यक आहे'
        ],
        'mobile' => [
            'required' => 'मोबाइल नंबर आवश्यक आहे',
            'regex_match' => 'वैध मोबाइल नंबर प्रविष्ट करा'
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

    public function getOfficerByBadge($badge_no)
    {
        return $this->where('badge_no', $badge_no)->first();
    }

    public function getOfficersForDuty($duty_id)
    {
        return $this->select('officers.*, duty_officers.duty_id')
                    ->join('duty_officers', 'duty_officers.officer_id = officers.id')
                    ->where('duty_officers.duty_id', $duty_id)
                    ->findAll();
    }

    public function getAvailableOfficers()
    {
        return $this->select('id, name, badge_no, rank, police_station, mobile')
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }
}
