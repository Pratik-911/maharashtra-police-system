<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PointModel;

class PointController extends BaseController
{
    protected $pointModel;

    public function __construct()
    {
        $this->pointModel = new PointModel();
    }

    public function index()
    {
        $data = [
            'title' => 'ड्यूटी पॉइंट व्यवस्थापन',
            'points' => $this->pointModel->getAllPoints()
        ];

        return view('admin/points/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'नवा ड्यूटी पॉइंट जोडा'
        ];

        return view('admin/points/create', $data);
    }

    public function store()
    {
        $rules = [
            'zone_id' => 'required|max_length[50]',
            'police_station_id' => 'required|max_length[50]',
            'name' => 'required|min_length[2]|max_length[255]',
            'latitude' => 'required|decimal|greater_than[-90]|less_than[90]',
            'longitude' => 'required|decimal|greater_than[-180]|less_than[180]',
            'radius' => 'required|integer|greater_than[0]|less_than_equal_to[5000]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'zone_id' => $this->request->getPost('zone_id'),
            'police_station_id' => $this->request->getPost('police_station_id'),
            'name' => $this->request->getPost('name'),
            'latitude' => $this->request->getPost('latitude'),
            'longitude' => $this->request->getPost('longitude'),
            'radius' => $this->request->getPost('radius')
        ];

        if ($this->pointModel->insert($data)) {
            return redirect()->to('/admin/points')->with('success', 'ड्यूटी पॉइंट यशस्वीरित्या जोडला गेला');
        } else {
            return redirect()->back()->withInput()->with('error', 'ड्यूटी पॉइंट जोडण्यात त्रुटी');
        }
    }

    public function edit($id)
    {
        $point = $this->pointModel->find($id);
        
        if (!$point) {
            return redirect()->to('/admin/points')->with('error', 'ड्यूटी पॉइंट सापडला नाही');
        }

        $data = [
            'title' => 'ड्यूटी पॉइंट संपादित करा',
            'point' => $point
        ];

        return view('admin/points/edit', $data);
    }

    public function update($id)
    {
        $point = $this->pointModel->find($id);
        
        if (!$point) {
            return redirect()->to('/admin/points')->with('error', 'ड्यूटी पॉइंट सापडला नाही');
        }

        $rules = [
            'zone_id' => 'required|max_length[50]',
            'police_station_id' => 'required|max_length[50]',
            'name' => 'required|min_length[2]|max_length[255]',
            'latitude' => 'required|decimal|greater_than[-90]|less_than[90]',
            'longitude' => 'required|decimal|greater_than[-180]|less_than[180]',
            'radius' => 'required|integer|greater_than[0]|less_than_equal_to[5000]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'zone_id' => $this->request->getPost('zone_id'),
            'police_station_id' => $this->request->getPost('police_station_id'),
            'name' => $this->request->getPost('name'),
            'latitude' => $this->request->getPost('latitude'),
            'longitude' => $this->request->getPost('longitude'),
            'radius' => $this->request->getPost('radius')
        ];

        if ($this->pointModel->update($id, $data)) {
            return redirect()->to('/admin/points')->with('success', 'ड्यूटी पॉइंट यशस्वीरित्या अपडेट केला गेला');
        } else {
            return redirect()->back()->withInput()->with('error', 'ड्यूटी पॉइंट अपडेट करण्यात त्रुटी');
        }
    }

    public function delete($id)
    {
        $point = $this->pointModel->find($id);
        
        if (!$point) {
            return redirect()->to('/admin/points')->with('error', 'ड्यूटी पॉइंट सापडला नाही');
        }

        if ($this->pointModel->delete($id)) {
            return redirect()->to('/admin/points')->with('success', 'ड्यूटी पॉइंट यशस्वीरित्या डिलीट केला गेला');
        } else {
            return redirect()->to('/admin/points')->with('error', 'ड्यूटी पॉइंट डिलीट करण्यात त्रुटी');
        }
    }
}
