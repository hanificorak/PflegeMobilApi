<?php

namespace App\Http\Classes;

use App\Models\Patients;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Helpers\ApiResponse;

class PatientClass
{
    public function getData($request)
    {
        try {
            $query = Patients::get();
            return ApiResponse::success($query, 'success');
        } catch (\Exception $exception) {
            return ApiResponse::error($exception->getMessage(), $exception->getCode());
        }
    }
}
