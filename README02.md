## 224 Laravel Passport Authentication : Forgot Password Part1

+ `$ php artisan make:controller ForgetPasswordController`を実行<br>

+ `routes/api.php`を編集<br>

```
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgetPasswordController;
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
```

+ `$ php artisan make:request ForgetPasswordRequest`を実行<br>

+ `ForgetPasswordRequest.php`を編集<br>

```
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgetPasswordRequest extends FormRequest
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
            //
        ];
    }
}
```

+ `ForgetPasswordController.php`を編集<br>

```
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
```
