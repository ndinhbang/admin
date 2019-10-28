<?php

namespace App\Http\Controllers;

use App\Notifications\Activated;
use App\Notifications\Activation;
use App\Notifications\PasswordReset;
use App\Notifications\PasswordResetted;
use Illuminate\Support\Facades\Hash;

use App\Http\Requests\AuthRequest;
use App\Http\Requests\PasswordRequest;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\Http\Controllers\HandlesOAuthErrors;
use Laravel\Passport\Passport;
use Laravel\Passport\TokenRepository;
use Lcobucci\JWT\Parser as JwtParser;
use League\OAuth2\Server\AuthorizationServer;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Zend\Diactoros\Response as Psr7Response;


class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application using laravel passport,
    | implements props and methods from Laravel\Passport\Http\Controllers\AccessTokenController
    | and uses some traits to conveniently provide its functionality to your applications.
    |
    */

    use ThrottlesLogins, HandlesOAuthErrors;

    /**
     * The authorization server.
     *
     * @var AuthorizationServer
     */
    protected $server;

    /**
     * The token repository instance.
     *
     * @var TokenRepository
     */
    protected $tokens;

    /**
     * The JWT parser instance.
     *
     * @var JwtParser
     */
    protected $jwt;

    /**
     * Create a new controller instance.
     *
     * @param AuthorizationServer $server
     * @param TokenRepository     $tokens
     * @param JwtParser           $jwt
     * @return void
     */
    public function __construct(AuthorizationServer $server, TokenRepository $tokens, JwtParser $jwt)
    {
        $this->jwt = $jwt;
        $this->server = $server;
        $this->tokens = $tokens;
    }

    /**
     * Handle a login request to the application.
     *
     * @param AuthRequest $request
     * @return mixed
     *
     * @throws ValidationException
     */
    public function login(AuthRequest $request)
    {
        $request->validated();
        
        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            // @throws \Illuminate\Validation\ValidationException
            $this->sendLockoutResponse($request);
        }

        $this->clearLoginAttempts($request);

        // create PSR-7 request from current request object
        // See: symfony/psr-http-message-bridge v1.2
        $psr7Request = (new DiactorosFactory())->createRequest($request);

        // issue the access token
        return $this->issueToken($psr7Request->withParsedBody([
            'grant_type'    => 'password',
            'username'      => $request->input($this->username()),
            'password'      => $request->input('password'),
            'client_id'     => config('passport.password_client_id'),
            'client_secret' => config('passport.password_client_secret'),
            'scope'         => ''
        ]));

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        // Get the failed login response instance.
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    public function refreshToken(AuthRequest $request)
    {
        $request->validated();
        $psr7Request = (new DiactorosFactory())->createRequest($request);

        // issue the access token
        return $this->issueToken($psr7Request->withParsedBody([
            'grant_type'    => 'refresh_token',
            'refresh_token' => $request->input('refresh_token'),
            'client_id'     => config('passport.password_client_id'),
            'client_secret' => config('passport.password_client_secret'),
            'scope'         => ''
        ]));
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param Request $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt($request->only([$this->username(), 'password']), $request->filled('remember'));
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'phone';
    }

    /**
     * Authorize a client to access the user's account.
     *
     * @param ServerRequestInterface $request
     * @return Response
     */
    public function issueToken(ServerRequestInterface $request)
    {
        return $this->withErrorHandling(function () use ($request) {
            return $this->convertResponse($this->server->respondToAccessTokenRequest($request, new Psr7Response));
        });
    }

    /**
     * Log the user out of the application.
     *
     * @param AuthRequest $request
     * @return Response
     */
    public function logout(AuthRequest $request)
    {
        $request->validated();
        // revoke the current access token being used by the user
        $request->user()->token()->revoke();
        // delete passport cookies on logout
        if ($request->hasCookie(Passport::cookie())) {
            Cookie::queue(Cookie::forget(Passport::cookie()));
        }
        // invalidate session if present
        if ($request->hasSession()) {
            $request->session()->invalidate();
        }

        return response()->json([
            'message' => 'Đăng xuất thành công!'
        ]);
    }


    public function register(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'first_name'            => 'required',
            'last_name'             => 'required',
            'email'                 => 'required|email|unique:users',
            'password'              => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ]);

        if ($validation->fails()) {
            return response()->json(['message' => $validation->messages()->first()], 422);
        }

        $user = \App\User::create([
            'email'    => request('email'),
            'status'   => 'pending_activation',
            'password' => bcrypt(request('password')),
        ]);

        $user->activation_token = generateUuid();
        $user->save();
        $profile             = new \App\Profile;
        $profile->first_name = request('first_name');
        $profile->last_name  = request('last_name');
        $user->profile()->save($profile);

        $user->notify(new Activation($user));

        return response()->json(['message' => 'Bạn đã đăng ký thành công. Vui lòng kiểm tra email của bạn để kích hoạt!']);
    }

    public function activate($activation_token)
    {
        $user = \App\User::whereActivationToken($activation_token)->first();

        if (!$user) {
            return response()->json(['message' => 'Mã thông báo kích hoạt không hợp lệ!'], 422);
        }

        if ($user->status == 'activated') {
            return response()->json(['message' => 'Tài khoản của bạn đã được kích hoạt!'], 422);
        }

        if ($user->status != 'pending_activation') {
            return response()->json(['message' => 'Mã thông báo kích hoạt không hợp lệ!'], 422);
        }

        $user->status = 'activated';
        $user->save();
        $user->notify(new Activated($user));

        return response()->json(['message' => 'Tài khoản của bạn đã được kích hoạt!']);
    }

    public function password(AuthRequest $request)
    {

        $user = \App\User::wherePhone(request('phone'))->first();

        if (!$user) {
            return response()->json(['errors' => ['phone' => ['Số điện thoại không hợp lệ!']]], 422);
        }

        $token = generateUuid();
        \DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        $user->notify(new PasswordReset($user, $token));

        return response()->json(['message' => 'Chúng tôi đã gửi thông tin lấy lại mật khẩu vào địa chỉ email `'.$user->email.'` của bạn!']);
    }

    public function validatePasswordReset(AuthRequest $request)
    {
        $validate_password_request = \DB::table('password_resets')->where('token', '=', request('token'))->first();

        if (!$validate_password_request) {
            return response()->json(['message' => 'Mã thông báo đặt lại mật khẩu không hợp lệ!'], 422);
        }

        if (date("Y-m-d H:i:s", strtotime($validate_password_request->created_at . "+30 minutes")) < date('Y-m-d H:i:s')) {
            return response()->json(['message' => 'Mã thông báo đặt lại mật khẩu đã hết hạn. Vui lòng yêu cầu đặt lại mật khẩu!'], 422);
        }

        return response()->json(['status' => true, 'message' => 'ok', 'email' => $validate_password_request->email]);
    }

    public function reset(Request $request)
    {

        $user = \App\User::whereEmail(request('email'))->first();

        if (!$user) {
            return response()->json(['message' => 'Chúng tôi không thể tìm thấy bất kỳ người dùng nào với email này. Vui lòng thử lại!'], 422);
        }

        $validate_password_request = \DB::table('password_resets')->where('email', '=', request('email'))->where('token', '=', request('token'))->first();

        if (!$validate_password_request) {
            return response()->json(['message' => 'Mã thông báo đặt lại mật khẩu không hợp lệ!'], 422);
        }

        if (date("Y-m-d H:i:s", strtotime($validate_password_request->created_at . "+30 minutes")) < date('Y-m-d H:i:s')) {
            return response()->json(['message' => 'Mã thông báo đặt lại mật khẩu đã hết hạn. Vui lòng yêu cầu đặt lại mật khẩu!'], 422);
        }

        $user->password = Hash::make(request('new_password'));
        $user->save();

        $user->notify(new PasswordResetted($user));

        return response()->json(['message' => 'Mật khẩu của bạn đã được thiết lập lại. Xin vui lòng đăng nhập lại!']);
    }
}
