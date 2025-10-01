<?php

namespace App\Http\Controllers\Api;

use App\Http\Classes\AuthClass;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;

class AuthController extends Controller
{
    protected $auth;

    // Dependency Injection
    public function __construct(AuthClass $auth)
    {
        $this->auth = $auth;
    }

    public function login(Request $request)
    {
        try {
            return $this->auth->login($request);
        } catch (\Throwable $th) {
            return ApiResponse::error("System Error: ".$th->getMessage(), 500);
        }
    }
}
