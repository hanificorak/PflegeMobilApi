<?php

namespace App\Http\Controllers\Api;

use App\Http\Classes\AuthClass;
use App\Http\Classes\CareClass;
use App\Http\Classes\PatientClass;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;

class CaresController extends Controller
{
    protected $cares;

    // Dependency Injection
    public function __construct(CareClass $cares)
    {
        $this->cares = $cares;
    }


    public function getData(Request $request)
    {
        try {
            return $this->cares->getData($request);
        } catch (\Throwable $th) {
            return ApiResponse::error("System Error: ".$th->getMessage(), 500);
        }
    }
    public function statusChange(Request $request)
    {
        try {
            return $this->cares->statusChange();
        } catch (\Throwable $th) {
            return ApiResponse::error("System Error: ".$th->getMessage(), 500);
        }
    }


}
