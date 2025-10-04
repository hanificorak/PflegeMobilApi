<?php

namespace App\Http\Classes;

use App\Models\Patients;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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

    public  function addPatient()
    {
        try {

            $name = request()->get('firstName');
            $surname = request()->get('lastName');
            $kno = request()->get('ident_no');
            $phone = request()->get('phone');
            $gender = request()->get('gender');
            $birthday = request()->get('birthDate');

            $id = request()->get('id');

            if($id == null){
                $mdl = new Patients();
                $mdl->created_at = Carbon::now();
                $mdl->updated_at = null;
                $mdl->create_user_id = Auth::user()->id;
            }else{
                $mdl = Patients::find($id);
                $mdl->updated_at = Carbon::now();
                $mdl->update_user_id = Auth::user()->id;
            }

            $mdl->name = $name;
            $mdl->surname = $surname;
            $mdl->no = $kno;
            $mdl->phone = $phone;
            $mdl->gender_id = $gender == "male" ? 1 : 2;
            $mdl->birth_date = Carbon::parse($birthday)->format('Y-m-d');

            if($mdl->save()){
                return ApiResponse::success($mdl, 'OK');
            }else{
                return ApiResponse::error('İşlem başarısız.', 500);
            }


        } catch (\Exception $exception) {
            return ApiResponse::error($exception->getMessage(), 500);
        }
    }
}
