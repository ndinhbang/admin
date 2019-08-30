<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\Http\Controllers\HandlesOAuthErrors;
use Laravel\Passport\TokenRepository;
use Lcobucci\JWT\Parser as JwtParser;
use League\OAuth2\Server\AuthorizationServer;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Zend\Diactoros\Response as Psr7Response;


class LoginController extends Controller
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
     * @var \League\OAuth2\Server\AuthorizationServer
     */
    protected $server;

    /**
     * The token repository instance.
     *
     * @var \Laravel\Passport\TokenRepository
     */
    protected $tokens;

    /**
     * The JWT parser instance.
     *
     * @var \Lcobucci\JWT\Parser
     */
    protected $jwt;

    /**
     * Create a new controller instance.
     *
     * @param  \League\OAuth2\Server\AuthorizationServer $server
     * @param  \Laravel\Passport\TokenRepository         $tokens
     * @param  \Lcobucci\JWT\Parser                      $jwt
     * @return void
     */
    public function __construct(
        AuthorizationServer $server,
        TokenRepository $tokens,
        JwtParser $jwt
    ) {
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
     * @throws \Illuminate\Validation\ValidationException
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

        if ($this->attemptLogin($request)) {
//            $request->session()->regenerate();
            $this->clearLoginAttempts($request);
            // authenticated user
//            $user = $this->guard()->user();

            // TODO: get all user 's abilities for using as scope for access token

            // create PSR-7 request from current request object
            // See: symfony/psr-http-message-bridge v1.2
            $psr7Request = (new DiactorosFactory())->createRequest($request);

            // issue the access token
            return $this->issueToken($psr7Request->withParsedBody([
                'grant_type' => 'password',
                'username' => $request->input($this->username()),
                'password' => $request->input('password'),
                'client_id' => config('passport.password_client_id'),
                'client_secret' => config('passport.password_client_secret'),
            ]));
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        // Get the failed login response instance.
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt($request->only([$this->username(), 'password']), $request->filled('remember'));
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

    /**
     * Authorize a client to access the user's account.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request
     * @return \Illuminate\Http\Response
     */
    public function issueToken(ServerRequestInterface $request)
    {
        return $this->withErrorHandling(function () use ($request) {
            return $this->convertResponse($this->server->respondToAccessTokenRequest($request, new Psr7Response));
        });
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

//        $request->session()->invalidate();

//        return $this->loggedOut($request) ?: redirect('/');
    }
}
