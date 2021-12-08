<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function resetPassword(ResetPasswordRequest $request)
    {
        $email = $request->email;
        $token = $request->token;
        $password = Hash::make($request->password);

        $emailcheck = DB::table('password_resets')
            ->where('email', $email)->first();
        $pincheck = DB::table('password_resets')
            ->where('token', $token)->first();

        if (!$emailcheck) {
            return response([
                'message' => "Email Not Found",
            ], 401);
        }
        if (!$pincheck) {
            return response([
                'message' => "Pin Code Invalid",
            ], 401);
        }

        DB::table('users')
            ->where('email', $email)->update(['password' => $password]);
        DB::table('password_resets')
            ->where('email', $email)->delete();

        return response([
            'message' => 'Password Change Successfully',
        ], 200);
    }
}
