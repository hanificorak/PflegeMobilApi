<?php

namespace App\Http\Classes;

use App\Models\CareMedicines;
use App\Models\CareProccess;
use App\Models\Cares;
use App\Models\MedicineLists;
use App\Models\Patients;
use App\Models\ProccessList;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\DB;

class CareClass
{
    public function getData($request)
    {
        try {
            $date = request()->get('date');
            if ($date == null) {
                $date = Carbon::now();
            } else {
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
            return ApiResponse::success($query, 'OK');
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

            if ($mdl->save()) {
                return ApiResponse::success($mdl, 'OK');
            } else {
                return ApiResponse::error('İşlem başarısız.', 500);
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
                return ApiResponse::error('-1', 200);
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
                return ApiResponse::success($mdl, $mdl->id);
            } else {
                return ApiResponse::error("Hata oluştu", 500);
            }
        } catch (\Exception $exception) {
            return ApiResponse::error($exception->getMessage(), $exception->getCode());
        }
    }

    public function careDetails()
    {
        try {

            $care_id = request()->get('care_id');
            $lang = "tr";

            $care = Cares::where('id', $care_id)->first();
            $patient = Patients::where('id', $care->patient_id)->first();

            $proc_data = CareProccess::query()
                ->join('proccess_list', 'proccess_list.param_id', '=', 'care_proccess.proccess_id')
                ->join('users', 'users.id', '=', 'care_proccess.create_user_id')
                ->where('care_proccess.care_id', $care_id)
                ->where('proccess_list.lang_code', 'tr')
                ->select(
                    'care_proccess.created_at',
                    'care_proccess.id',
                    'care_proccess.care_id',
                    'care_proccess.proccess_id',
                    'care_proccess.description',
                    'care_proccess.file_path',
                    'proccess_list.name',
                    'users.name as user_name_cr',
                )
                ->distinct()
                ->get();

            $medicines = CareMedicines::query()
                ->where('care_id', request()->get('care_id'))
                ->join('medicine_lists', 'medicine_lists.id', '=', 'care_medicines.medicine_id')
                ->select(
                    'care_medicines.*',
                    'medicine_lists.name',
                    DB::raw("(SELECT name FROM params WHERE type_id = 12 and param_id = care_medicines.usage_type_id and lang_code = '$lang') as usage_type_name"),
                    DB::raw("(SELECT name FROM params WHERE type_id = 12 and param_id = care_medicines.period_type and lang_code = '$lang') as period_type_name"),
                    DB::raw("(SELECT name FROM params WHERE type_id = 13 and param_id = care_medicines.dose_info_detail_type and lang_code = '$lang') as dose_detail_type_name")
                )
                ->get();


            $datas = [
                'care' => $care,
                'patient' => $patient,
                'proc_data' => $proc_data,
                'medicines' => $medicines,
            ];

            return ApiResponse::success($datas, 'OK');
        } catch (\Exception $exception) {
            return ApiResponse::error($exception->getMessage(), $exception->getCode());
        }
    }

    public function getParam()
    {
        try {


            $cares_param = ProccessList::where('lang_code', 'tr')->get();

            $data = [
                'cares' => $cares_param,
            ];
            return ApiResponse::success($data, 'OK');
        } catch (\Exception $exception) {
            return ApiResponse::error($exception->getMessage(), $exception->getCode());
        }
    }

    public function careProcAdd()
    {
        try {

            $care_id = request()->get('care_id');
            $proc_id = request()->get('proc_id');
            $desc = request()->get('desc');

            $mdl = new CareProccess();
            $mdl->created_at = Carbon::now();
            $mdl->create_user_id = Auth::user()->id;
            $mdl->care_id = $care_id;
            $mdl->proccess_id = $proc_id;
            $mdl->description = $desc;

            if ($mdl->save()) {
                return ApiResponse::success(null, 'OK');
            } else {
                return ApiResponse::error('İşlem başarısız', 500);
            }
        } catch (\Exception $exception) {
            return ApiResponse::error($exception->getMessage(), 500);
        }
    }

    public function medicineList()
    {
        try {

            $query = MedicineLists::where('lang', "tr")->limit(1000)->get();

            return ApiResponse::success($query, 'OK');
        } catch (\Exception $exception) {
            return ApiResponse::error($exception->getMessage(), 500);
        }
    }

    public function medicineAdd()
    {
        try {

            $care_id = request()->get('care_id');
            $medicine_id = request()->get('medicine_id');
            $desc = request()->get('desc');

            $careMedicine = new CareMedicines();
            $careMedicine->create_user_id = Auth::user()->id;
            $careMedicine->created_at = Carbon::now();
            $careMedicine->care_id = $care_id;
            $careMedicine->medicine_id = $medicine_id;
            $careMedicine->usage_type_id = null;
            $careMedicine->period_type = null;
            $careMedicine->dose_info_detail_type = null;
            $careMedicine->dose_info = null;
            $careMedicine->dose_info_detail = null;
            $careMedicine->period = null;
            $careMedicine->usage_amount = null;
            $careMedicine->amount = 1;

            if ($careMedicine->save()) {
                return ApiResponse::success(null, 'OK');
            } else {
                return ApiResponse::error('İşlem başarısız', 500);
            }
        } catch (\Exception $exception) {
            return ApiResponse::error($exception->getMessage(), 500);
        }
    }

    public function getCareDetailData()
    {
        try {

            $care_id = request()->get('care_id');

            $proc_data = CareProccess::query()
                ->join('proccess_list', 'proccess_list.param_id', '=', 'care_proccess.proccess_id')
                ->join('users', 'users.id', '=', 'care_proccess.create_user_id')
                ->where('care_proccess.care_id', $care_id)
                ->where('proccess_list.lang_code', 'tr')
                ->select(
                    'care_proccess.created_at',
                    'care_proccess.id',
                    'care_proccess.care_id',
                    'care_proccess.proccess_id',
                    'care_proccess.description',
                    'care_proccess.file_path',
                    'proccess_list.name',
                    'users.name as user_name_cr',
                )
                ->distinct()
                ->get();

            $medicines = CareMedicines::query()
                ->where('care_id', request()->get('care_id'))
                ->join('medicine_lists', 'medicine_lists.id', '=', 'care_medicines.medicine_id')
                ->select(
                    'care_medicines.*',
                    'medicine_lists.name',
                    DB::raw("(SELECT name FROM params WHERE type_id = 12 and param_id = care_medicines.usage_type_id and lang_code = '$lang') as usage_type_name"),
                    DB::raw("(SELECT name FROM params WHERE type_id = 12 and param_id = care_medicines.period_type and lang_code = '$lang') as period_type_name"),
                    DB::raw("(SELECT name FROM params WHERE type_id = 13 and param_id = care_medicines.dose_info_detail_type and lang_code = '$lang') as dose_detail_type_name")
                )
                ->get();

            $obj = [
                "medicines" => $medicines,
                "proc_data" => $proc_data,
            ];

            return ApiResponse::success($obj, 'OK');
        } catch (\Exception $exception) {
            return ApiResponse::error($exception->getMessage(), 500);
        }
    }

    public function updateDesc()
    {
        try {

            $care_id = request()->get('care_id');
            $description = request()->get('description');

            $mdl = Cares::find($care_id);
            $mdl->updated_at = Carbon::now();
            $mdl->update_user_id = Auth::user()->id;
            $mdl->desc_detail = $description;

            if ($mdl->save()) {
                return ApiResponse::success(null, 'OK');
            } else {
                return ApiResponse::error('İşlem başarısız.');
            }

        } catch (\Exception $exception) {
            return ApiResponse::error($exception->getMessage(), 500);
        }
    }

    public function medicineDelete()
    {
        try {

            $id = request()->get('id');

            if(CareMedicines::where('id',$id)->delete()){
                return ApiResponse::success(null, 'OK');
            }else{
                return ApiResponse::error('İşlem başarısız.');
            }

        } catch (\Exception $exception) {
            return ApiResponse::error($exception->getMessage(), 500);
        }
    }

    public function procDelete()
    {
        try {

            $id = request()->get('id');

            if(CareProccess::where('id',$id)->delete()){
                return ApiResponse::success(null, 'OK');
            }else{
                return ApiResponse::error('İşlem başarısız.');
            }

        } catch (\Exception $exception) {
            return ApiResponse::error($exception->getMessage(), 500);
        }
    }
}
