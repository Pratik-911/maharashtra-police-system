<?php

namespace App\Controllers\Station;

use App\Controllers\BaseController;
use App\Models\OfficerModel;
use App\Models\DutyModel;
use App\Models\PointModel;
use App\Models\ComplianceModel;

class DashboardController extends BaseController
{
    protected $officerModel;
    protected $dutyModel;
    protected $pointModel;
    protected $complianceModel;

    public function __construct()
    {
        $this->officerModel = new OfficerModel();
        $this->dutyModel = new DutyModel();
        $this->pointModel = new PointModel();
        $this->complianceModel = new ComplianceModel();
    }

    public function index()
    {
        $stationCode = session()->get('station_code');
        
        // Get station-scoped data
        $data = [
            'total_officers' => $this->officerModel->where('police_station', $stationCode)->countAllResults(),
            'active_duties' => $this->dutyModel->getActiveDutiesForStation($stationCode),
            'total_points' => $this->pointModel->where('police_station_id', $stationCode)->countAllResults(),
            'recent_duties' => $this->dutyModel->getRecentDutiesForStation($stationCode, 5),
            'station_name' => session()->get('station_name'),
            'station_code' => $stationCode
        ];

        return view('station/dashboard/index', $data);
    }
}
