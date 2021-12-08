## 226 Laravel Passport Authentication Reset Password

+ `$ php artisan make:controller ResetPasswordController`を実行<br>

+ `routes/api.php`を編集<br>

```
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgetPasswordController;
use App\Http\Controllers\ResetPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Login Routes
Route::post('/login', [AuthController::class, 'login']);

// Register Routes
Route::post('/register', [AuthController::class, 'register']);

// Forget Password Routes
Route::post('/forgetpassword', [ForgetPasswordController::class, 'forgetPassword']);

// Reset Password Routes
Route::post('/resetpassword', [ResetPasswordController::class, 'resetPassword']);
```

+ `$ php artisan make:request ResetPasswordRequest`を実行<br>

+ `ResetPasswordRequest.php`を編集<br>

```
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ];
    }
}
```


+ `ResetPasswordController.php`を編集<br>

```
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
```

+ `Postman(POST) localhost/api/resetpassword`を入力<br>

+ `Bodyタブを選択してform-dataを選択する`<br>

+ `KEYにtoken email password password_confirmation`を入力<br>

+ `各VALUEに入力する`<br>

+ `Sendして確認する`<br>

