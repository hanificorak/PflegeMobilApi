<?php

namespace App\Http\Controllers\Api;

use App\Http\Classes\PatientClass;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;

class PatientController extends Controller
{
    protected $patient;

    // Dependency Injection
    public function __construct(PatientClass $patient)
    {
        $this->patient = $patient;
    }

    public function getData(Request $request)
    {
        try {
            return $this->patient->getData($request);
        } catch (\Throwable $th) {
            return ApiResponse::error("System Error: ".$th->getMessage(), 500);
        }
    }
}
