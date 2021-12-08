<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgetPasswordRequest;
use Illuminate\Http\Request;
use app\Models\User;
use Exception;

class ForgetPasswordController extends Controller
{
    public function forgetPassword(ForgetPasswordRequest $request)
    {
        $email = $request->email;

        if (User::where('email', $email)->dosentExist()) {
            return response([
                'message' => 'Email Invalid',
            ], 401);
        }

        // generate Random Token
        $token = rand(10, 100000);

        try {
            
        } catch (Exception $exception) {
            return response([
                'message' => $exception->getMessage()
            ], 400);
        }
    }
}
