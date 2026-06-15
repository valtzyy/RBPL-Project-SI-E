<?php
require_once ROOT_PATH . '/app/models/Vehicle.php';

class VehicleController extends Controller
{
    // GET /vehicles/available — daftar kendaraan tersedia
    public function available(): void
    {
        $vehicles = (new Vehicle())->getAvailable();
        $this->view('vehicles/available', ['vehicles' => $vehicles]);
    }
}