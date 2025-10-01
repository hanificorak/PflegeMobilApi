<?php

namespace App\Http\Classes;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Helpers\ApiResponse;

class AuthClass
{
    public function login($request)
    {

        $email = $request->email;
        $password = $request->password;

        if (empty($email)) {
            return ApiResponse::error('E-Mail alanı boş geçilemez.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ApiResponse::error('Geçerli bir e-posta giriniz.');
        }

        if (empty($password)) {
            return ApiResponse::error('Şifre alanı boş geçilemez.');
        }

        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return ApiResponse::error("E-posta veya şifre hatalı", 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        return ApiResponse::success([
            'user' => $user,
            'token' => $token
        ], "Giriş başarılı");
    }
}
