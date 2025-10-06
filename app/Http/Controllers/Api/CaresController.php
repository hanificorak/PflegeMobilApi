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

    public function addCares(Request $request)
    {
        try {
            return $this->cares->addCares();
        } catch (\Throwable $th) {
            return ApiResponse::error("System Error: ".$th->getMessage(), 500);
        }
    }

    public function careDetails(Request $request)
    {
        try {
            return $this->cares->careDetails();
        } catch (\Throwable $th) {
            return ApiResponse::error("System Error: ".$th->getMessage(), 500);
        }
    }

    public function getParam(Request $request)
    {
        try {
            return $this->cares->getParam();
        } catch (\Throwable $th) {
            return ApiResponse::error("System Error: ".$th->getMessage(), 500);
        }
    }

    public function careProcAdd(Request $request)
    {
        try {
            return $this->cares->careProcAdd();
        } catch (\Throwable $th) {
            return ApiResponse::error("System Error: ".$th->getMessage(), 500);
        }
    }

    public function medicineList(Request $request)
    {
        try {
            return $this->cares->medicineList();
        } catch (\Throwable $th) {
            return ApiResponse::error("System Error: ".$th->getMessage(), 500);
        }
    }

    public function medicineAdd(Request $request)
    {
        try {
            return $this->cares->medicineAdd();
        } catch (\Throwable $th) {
            return ApiResponse::error("System Error: ".$th->getMessage(), 500);
        }
    }

    public function getCareDetailData(Request $request)
    {
        try {
            return $this->cares->getCareDetailData();
        } catch (\Throwable $th) {
            return ApiResponse::error("System Error: ".$th->getMessage(), 500);
        }
    }

    public function updateDesc(Request $request)
    {
        try {
            return $this->cares->updateDesc();
        } catch (\Throwable $th) {
            return ApiResponse::error("System Error: ".$th->getMessage(), 500);
        }
    }

    public function medicineDelete(Request $request)
    {
        try {
            return $this->cares->medicineDelete();
        } catch (\Throwable $th) {
            return ApiResponse::error("System Error: ".$th->getMessage(), 500);
        }
    }

    public function procDelete(Request $request)
    {
        try {
            return $this->cares->procDelete();
        } catch (\Throwable $th) {
            return ApiResponse::error("System Error: ".$th->getMessage(), 500);
        }
    }


}
