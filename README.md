## 220 Laravel passport Authentication Passport Install

+ `$ composer require laravel/passport`を実行<br>

+ `$ php artisan migrate`を実行<br>

+ `$ php artisan passport:install`を実行<br>

+ server/.envにCLIENT_1とCLIENT_2のシークレットキーを設定する<br>

+ `app/Models/User.php`を編集<br>

```
<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens; // 追記

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
```

+ `app/Providers/AuthServiceProvider.php`を編集<br>

```
<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport; // 追記

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Models\Model' => 'App\Policies\ModelPolicy', // コメントアウト解除
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot() // 編集
    {
        $this->registerPolicies();

        if (!$this->app->routesAreCached()) {
            Passport::routes();
        }
    }
}
```

+ `config/auth.php`を編集<br>

```
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses session storage and the Eloquent user provider.
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | Supported: "session", "token"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'passport', // 編集
            'provider' => 'users',
            'hash' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | If you have multiple user tables or models you may configure multiple
    | sources which represent each model / table. These sources may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | You may specify multiple password reset configurations if you have more
    | than one user table or model in the application and you want to have
    | separate password reset settings based on the specific user types.
    |
    | The expire time is the number of minutes that the reset token should be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the amount of seconds before a password confirmation
    | times out and the user is prompted to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    */

    'password_timeout' => 10800,

];
```

+ `$ php artisan make:controller AuthController.php`を実行<br>

## Laravel Passport Authentication Login

+ `routes/api.php`を編集<br>

```
<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Login Routes
Route::post('/login', [AuthController::class, 'login']);
```

+ `AuthController.php`を編集<br>

```
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            if (Auth::attempt($request->only('email', 'password'))) {
                $user = Auth::user();
                $token = $user->createToken('app')->accessToken;

                return response([
                    'message' => "Successfully Login",
                    'token' => $token,
                    'user' => $user
                ], 200); // Status Code
            }
        } catch (Exception $exception) {
            return response([
                'message' => $exception->getMessage(),
            ], 400);
        }

        return response([
            'message' => 'Invalid Email Or Password',
        ], 401);
    }
}
```

## Laravel Passport Authentication : Registration Part1

+ `Postman(POST) localhost/api/login`を入力<br>

+ `BODYタブのform-data`を選択<br>

+ `KEYにemail と passwordと入れる`<br>

+ `emailのVALUEとpasswordのVALUE`を入れる<br>

+ Sendしてみると(まだユーザー登録していない為) `Invalid Email Or Password`とエラーが出力される<br>

+ `routes/api.php`を編集<br>

```
<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Login Routes
Route::post('/login', [AuthController::class, 'login']);

// Register Routes
Route::post('/register', [AuthController::class, 'register']);
```

+ `$ php artisan make:request RegisterRequest`を実行<br>

+ `RegisterRequest.php`を編集<br>

```
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required|max:55',
            'email' => 'required|unique:users|min:5|max:60',
            'password' => 'required|min:6|confirmed',
        ];
    }
}
```

## 223 Laravel Passport Authentication : Registration Part2

+ `AuthController.php`を編集<br>

```
<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            if (Auth::attempt($request->only('email', 'password'))) {
                $user = Auth::user();
                $token = $user->createToken('app')->accessToken;

                return response([
                    'message' => "Successfully Login",
                    'token' => $token,
                    'user' => $user
                ], 200); // Status Code
            }
        } catch (Exception $exception) {
            return response([
                'message' => $exception->getMessage(),
            ], 400);
        }

        return response([
            'message' => 'Invalid Email Or Password',
        ], 401);
    }

    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            $token = $user->createToken('app')->accessToken;

            return response([
                'message' => 'Registration Successfull',
                'token' => $token,
                'user' => $user,
            ], 200);
        } catch (Exception $exception) {
            return response([
                'message' => $exception->getMessage(),
            ], 400);
        }
    }
}
```

+ `Postman(POST) localhost/api/register`を入力<br>

+ `BODYタブを選択 form-dataを選択`<br>

+ `KEYに name email password password_confirmation`を入力<br>

+ `各KEYのVALUEを入力`<br>

+ `Send`する<br>

+ 登録完了する<br>
```
{
    "message": "Registration Successfull",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiYzhhMThiZjMwNjVhN2ZhNmJjMDQxNzIxMjQ1ZTNjOGIwNGFiM2EzYjczNzZmZmM3MDM5OTcyNThlMTE5Y2UzZmNlMTY3ZTUyYWUwMjk3ZTIiLCJpYXQiOjE2Mzg5MzQ0MjcuNTk3NTc2LCJuYmYiOjE2Mzg5MzQ0MjcuNTk3NTg3LCJleHAiOjE2NzA0NzA0MjcuNTI5MjgxLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.U-9JRe5dRQNDqrCMPXvC2YyuYNZUtvAXfiSHqFY9OLIfpbn8SOicFf5zksZB7HKzTH7_MlIh-fofQT_EAM01LDyGu-EOwx5JVdpyect1S7w7Hj-ZNkVnF_k_5ya1uRNWFAmdmNlgZ7_AEXtxdLVfrvRBcP7sYuC7JZaORT5aqXk5CYuSBLaIx94zoIVJ_u64wEdKdMpLZbRLEbzmkaBeCNk05g2fJJdWEwEgB2ycRTVPLvhe-7FRgUbA8g_N7NhifqMccmW1eWb8rvwShro6EsibmAQaMVq2igqVWOyqCwk-lPRQvWpf4N9KK0gUsWt4FYNH9poYYFUEMw5xnscpXb4Zo70q0uGb7bY2rFW16asXmxC1Bz5arhXOfqQnZvZG_oDT9MuaFYBYpBZ4R1ezvecaCrLo0MPWjdN0NxjNnWzNp37s7qXZObFZUnUzSQ-IBGrbD3huo03MNASm3Z3aPQ7hvMegLJdRAiaLh7QxSIWEh5uQek5sziyQuY4n_3WF7IkTGYEJYrtpZPyhVua368pknn2s3jZjC-hAApMn1fPs7sbMBs_Pcd2M9YSAP_NPrdM70cNYe8pMCYtwUUCEpEnaD7bb3DvXBFIYQlvAsIBXzmjlyDkAsOS3jpsybA-CKi8FU3FLEDkHC-g1WaM7u5otxUYGJqWG89zw8erXPcI",
    "user": {
        "name": "Takaki Nakamura",
        "email": "takaki55730317@gmail.com",
        "updated_at": "2021-12-08T03:33:47.000000Z",
        "created_at": "2021-12-08T03:33:47.000000Z",
        "id": 1
    }
}
```

+ `ログインが成功するか確認する`<br>

```
{
    "message": "Successfully Login",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiZTJjM2RlYzAxMTVlNWY2Yjk0OTdiMjkzMTk3YWRjNGViM2MxOTViYzcwMmEyNDI3YmRkODliMDk0MzVlNzA4OWE5ZWI0ZjA1N2QwMGNlYzgiLCJpYXQiOjE2Mzg5MzQ2ODAuMzg3NjQ4LCJuYmYiOjE2Mzg5MzQ2ODAuMzg3NjgzLCJleHAiOjE2NzA0NzA2ODAuMzA4MDY0LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.aCtel8yJNLk99g0B9P6wsfkiU1hUlpUZnil4xrmWkYjiS5XTBz_aOLzhonL98BnW51TIX7X3QAA8o-4zxvfsi0U0mQw6EC8v3RyENX7L9h5W2A52ho71rfWf3eGJ6JiuzQeBfinM22GDimBxgdPTafT38vkhWTt0Lphzf7jPat4dTGnTJXDj_99Mw4PXRCqWnGPbFy1fI5VvAI83WhrYFT5ClPr528nfab6PGbLpD8C4wePQQF5N4gIkF6hl-HShp1eHH-ESpkwlydi87TJdrmNG1AW4FHqwTNKcoYlxMGR8_paqmpBSqpYolY2eLJCo-dpBvJT199mxW81altQbeiE741JB2EacketWZmYn9YoWRIDUpDgJmhjL2Aq3EnLDa-lp6siVewfjx5FU0Gmg95jCpB3Kpm4AajNrwvtDVDY92MZXEY0fJ1fblJkULKu9oikMF4cKeS3BTwAvzCY24ZcLluOd2pxfekafBrSIkXkU3sZ9b2tA6GEYLLbOpzMicxOdsIgjkHkKsMEMoRrfdjnipDPkzsf5hodVSriNBElssNP9Xyjq4j27_7o4Rul7YKAOTi-tfYFQW0knnRzNcgQDKCFgLAvf2IvaF-jcqeh2CPTr_vTFsbwv1o7Co7IQJr4mcW0hH1wcYTy52Gngj15C3mWbpwGqcazVe4hhJ6E",
    "user": {
        "id": 1,
        "name": "Takaki Nakamura",
        "email": "takaki55730317@gmail.com",
        "email_verified_at": null,
        "created_at": "2021-12-08T03:33:47.000000Z",
        "updated_at": "2021-12-08T03:33:47.000000Z"
    }
}
```
