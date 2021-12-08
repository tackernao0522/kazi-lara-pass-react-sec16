## 227 Laravel Passport Authentication Authorization User

+ `php artisan make:controller UserController`を実行<br>

+ `routes/api.php`を編集<br>

```
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgetPasswordController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\UserController;
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

// Current User Route
Route::get('/user', [UserController::class, 'user'])->middleware('auth:api');
```

+ `UserController.php`を編集<br>

```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function user()
    {

        return Auth::user();
    }
}
```

+ `Postman(GET) localhost/api/user`を入力<br>

+ `HeadersタブのKeyにAuthorizationを追加記入し Valueに Bearer "ここにloginしたtokenをコピペする(""は除く)" を追加記入する`<br>

+ `Bodyタブを選択してform-dataを選択する`<br>

+ `KEYにemailとpasswordを記入`<br>

+ `Sendする`<br>

```
{
    "id": 2,
    "name": "Pepenao",
    "email": "takaproject777@gmail.com",
    "email_verified_at": null,
    "created_at": "2021-12-08T07:57:20.000000Z",
    "updated_at": "2021-12-08T07:57:20.000000Z"
}
```

