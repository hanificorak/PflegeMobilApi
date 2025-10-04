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
}
