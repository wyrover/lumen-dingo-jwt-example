<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\JWTAuth;

class AuthController extends Controller
{
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    // 登录 JWT 验证
    public function loginPost(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email|max:255',
            'password' => 'required',
        ]);
        try {
            if (! $token = $this->jwt->attempt($request->only('email', 'password'))) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (JWTException $e) {
            return response()->json(['token_absent' => $e->getMessage()], $e->getStatusCode());
        }
        return response()->json(compact('token'));
    }

    // 注册用户
    public function createUser(Request $request) 
    {
        $this->validate($request, [
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required',
            'name'     => 'required'
        ]);

        $data = $request->only('email', 'password', 'name');

        $user = new User();
        $user->password = password_hash($data['password'], PASSWORD_DEFAULT);
        $user->email    = $data['email'];
        $user->name     = $data['name'];
        $user->save();
        return $user;
    }

    // 刷新 token
    public function refresh()
    {
        $token = $this->jwt->getToken();

        if (!$token) {
            throw new UnauthorizedHttpException('Token not provided');
        }

        try {
            $token = $this->jwt->refresh();
        } catch (TokenInvalidException $e) {
            //throw new AccessDeniedHttpException('The token is invalid');
        }
        return response()->json(compact('token'));
    }

}