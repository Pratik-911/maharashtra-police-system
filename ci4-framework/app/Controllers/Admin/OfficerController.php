<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OfficerModel;

class OfficerController extends BaseController
{
    protected $officerModel;

    public function __construct()
    {
        $this->officerModel = new OfficerModel();
    }

    public function index()
    {
        $officers = $this->officerModel->findAll();
        
        $data = [
            'title' => 'अधिकारी व्यवस्थापन',
            'officers' => $officers
        ];

        return view('admin/officers/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'नवीन अधिकारी जोडा'
        ];

        return view('admin/officers/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'name' => 'required|min_length[2]|max_length[255]',
            'badge_no' => 'required|is_unique[officers.badge_no]|max_length[50]',
            'rank' => 'required|max_length[100]',
            'police_station' => 'required|max_length[255]',
            'mobile' => 'required|exact_length[10]|numeric',
            'password' => 'required|min_length[6]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'badge_no' => $this->request->getPost('badge_no'),
            'rank' => $this->request->getPost('rank'),
            'police_station' => $this->request->getPost('police_station'),
            'mobile' => $this->request->getPost('mobile'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT)
        ];

        if ($this->officerModel->insert($data)) {
            return redirect()->to('/admin/officers')->with('success', 'अधिकारी यशस्वीरित्या जोडला गेला');
        } else {
            return redirect()->back()->withInput()->with('error', 'अधिकारी जोडण्यात त्रुटी');
        }
    }

    public function edit($id)
    {
        $officer = $this->officerModel->find($id);
        
        if (!$officer) {
            return redirect()->to('/admin/officers')->with('error', 'अधिकारी सापडला नाही');
        }

        $data = [
            'title' => 'अधिकारी संपादित करा',
            'officer' => $officer
        ];

        return view('admin/officers/edit', $data);
    }

    public function update($id)
    {
        $officer = $this->officerModel->find($id);
        
        if (!$officer) {
            return redirect()->to('/admin/officers')->with('error', 'अधिकारी सापडला नाही');
        }

        $validation = \Config\Services::validation();
        
        $rules = [
            'name' => 'required|min_length[2]|max_length[255]',
            'rank' => 'required|max_length[100]',
            'police_station' => 'required|max_length[255]',
            'mobile' => 'required|exact_length[10]|numeric'
        ];

        // Only validate badge_no uniqueness if it's being changed
        if ($this->request->getPost('badge_no') !== $officer['badge_no']) {
            $rules['badge_no'] = 'required|is_unique[officers.badge_no]|max_length[50]';
        } else {
            $rules['badge_no'] = 'required|max_length[50]';
        }

        // Password is optional for updates
        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[6]';
        }

        $validation->setRules($rules);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'badge_no' => $this->request->getPost('badge_no'),
            'rank' => $this->request->getPost('rank'),
            'police_station' => $this->request->getPost('police_station'),
            'mobile' => $this->request->getPost('mobile')
        ];

        // Only update password if provided
        if ($this->request->getPost('password')) {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        if ($this->officerModel->update($id, $data)) {
            return redirect()->to('/admin/officers')->with('success', 'अधिकारी माहिती अपडेट केली गेली');
        } else {
            return redirect()->back()->withInput()->with('error', 'अधिकारी अपडेट करण्यात त्रुटी');
        }
    }

    public function delete($id)
    {
        $officer = $this->officerModel->find($id);
        
        if (!$officer) {
            return redirect()->to('/admin/officers')->with('error', 'अधिकारी सापडला नाही');
        }

        if ($this->officerModel->delete($id)) {
            return redirect()->to('/admin/officers')->with('success', 'अधिकारी यशस्वीरित्या काढला गेला');
        } else {
            return redirect()->to('/admin/officers')->with('error', 'अधिकारी काढण्यात त्रुटी');
        }
    }
}
