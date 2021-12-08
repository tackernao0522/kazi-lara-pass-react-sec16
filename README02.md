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

## 225 Laravel Passport Authentication : Forgot Password Part2

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
            'email' => 'required',
        ];
    }
}
```

+ `$ php artisan make:mail ForgetPasswordMail`を実行<br>

+ `ForgetPasswordController.php`を編集<br>

```
<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgetPasswordRequest;
use App\Mail\ForgetPasswordMail;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ForgetPasswordController extends Controller
{
    public function forgetPassword(ForgetPasswordRequest $request)
    {
        $email = $request->email;

        if (User::where('email', $email)->doesntExist()) {
            return response([
                'message' => 'Email Invalid',
            ], 401);
        }

        // generate Random Token
        $token = rand(10, 100000);

        try {
            DB::table('password_resets')->insert([
                'email' => $email,
                'token' => $token,
            ]);

            // Mail Send to User
            Mail::to($email)->send(new ForgetPasswordMail($token));

            return response([
                'message' => 'Reset Password Mail send on your email',
            ], 200);
        } catch (Exception $exception) {
            return response([
                'message' => $exception->getMessage()
            ], 400);
        }
    }
}
```

`app/Mail/ForgetPasswordController.php`を編集<br>

```
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->data = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = $this->data;

        return $this->from('takaki_5573031@yahoo.co.jp')
            ->view('mail.forget_password', compact('data'))
            ->subject('Password Reset Link');
    }
}
```

+ `resources/views/mail`ディレクトリを作成<br>

+ `redources/views/mail/forget_password.blade.php`を作成<br>

```
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forget Password</title>
</head>

<body>
  HI <br>
  Change Your Password <a href="https://localhost/reset/{{ $data }}">Click Here</a>
  Pincode : {{ $data }}
</body>

</html>
```

+ `Postman(POST) localhost/api/forgetpassword`を入力<br>

+ `BODYタグを選択してform-dataを選択`<br>

+ `KEYにemailを入力 VALUEにメールアドレスを入力`<br>

+ `Sendしてメールが届くか確認する`<br>
