<?php

namespace App\Http\Classes;

use App\Models\Cares;
use App\Models\Patients;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Helpers\ApiResponse;

class CareClass
{
    public function getData($request)
    {
        try {
            $date = request()->get('date');
            if($date == null){
                $date = Carbon::now();
            }else{
                $date = Carbon::parse($date);
            }


            $status = request()->get('status');

            $query = Cares::query()
                ->join('patients', 'patients.id', '=', 'cares.patient_id')
                ->join('users', 'users.id', '=', 'cares.create_user_id')
                ->whereDate('cares.created_at', $date)
                ->when($status != 0, function ($query) use ($status) {
                    return $query->where('cares.status', $status);
                })
                ->select(
                    'cares.id',
                    'patients.gender_id',
                    'cares.type_id',
                    'cares.patient_id',
                    'patients.name',
                    'patients.surname',
                    'patients.birth_date',
                    'cares.created_at',
                    'cares.status',
                    'users.name as user_name_cr',
                )
                ->get();
            return ApiResponse::success($query,'OK');

        } catch (\Exception $exception) {
            return ApiResponse::error($exception->getMessage(), $exception->getCode());
        }
    }

    public function statusChange()
    {
        try {

            $id = request()->get('id');
            $status = request()->get('status');

            $st_id = null;


            switch ($status) {
                case 'Beklemede':
                    $st_id = 1;
                    break;
                case 'Tamamlandı':
                    $st_id = 2;
                    break;
                case 'İptal Edildi':
                    $st_id = 3;
                    break;
            }

            $mdl = Cares::find($id);
            $mdl->status = $st_id;
            $mdl->updated_at = Carbon::now();
            $mdl->update_user_id = Auth::id();

            if($mdl->save()){
                return ApiResponse::success($mdl,'OK');
            }else{
                return ApiResponse::error('İşlem başarısız.',500);
            }

        } catch (\Exception $exception) {
            return ApiResponse::error($exception->getMessage(), $exception->getCode());
        }
    }

    public function addCares()
    {
        try {

            $patient_id = request()->get('patient_id');
            $type_id = 1;
            $description = '';

            $existingCare = Cares::where('patient_id', $patient_id)
                ->where('type_id', $type_id)
                ->where('status', 1)
                ->whereDate('created_at', Carbon::now())
                ->exists();

            if ($existingCare) {
                return ApiResponse::error('-1',200);
            }

            $mdl = new Cares();
            $mdl->created_at = Carbon::now();
            $mdl->create_user_id = Auth::user()->id;
            $mdl->updated_at = null;
            $mdl->patient_id = $patient_id;
            $mdl->type_id = $type_id;
            $mdl->status = 1;
            $mdl->description = $description;

            if ($mdl->save()) {
                return ApiResponse::success($mdl,$mdl->id);
            } else {
                return ApiResponse::error("Hata oluştu",500);
            }
        } catch (\Exception $exception) {
            return ApiResponse::error($exception->getMessage(), $exception->getCode());
        }
    }

    public function PatientDetails()
    {
        try {

            $care_id = request()->get('care_id');

            $care = Cares::where('id', $care_id)->first();
            $patient = Patients::where('id', $care->patient_id)->first();

            $datas = [
                'care' => $care,
                'patient' => $patient,
            ];

            return ApiResponse::success($datas,'OK');
        } catch (\Exception $exception) {
            return ApiResponse::error($exception->getMessage(), $exception->getCode());
        }
    }
}
