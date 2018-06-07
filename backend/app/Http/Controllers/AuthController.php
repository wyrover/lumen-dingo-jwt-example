<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\JWTAuth;
use App\User;

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

    //第三方登录
    public function login_thrid(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email|max:255',
            'password' => 'required',
        ]);

        //Check is the email have registered
        if (User::where('email', '=',  $request->input('email'))->exists()) {
            $credentials = [
                'email' => $request->input('email'),
                'password' => $request->input('password'),
                'confirm' => 1
            ];
        } else {
            //Create the user first
            $user = array(
                'email'=> $request->input('email'),
                'name'=>$request->input('name'),
                'password'=> $request->input('password'),
                'password_confirmation' => $request->input('password'),
            );
            $response = $this->api->post('register',$user);
            //If success create user, create credentials
            if($response['success']){
                $credentials = [
                    'email' => $request->input('email'),
                    'password' => $request->input('password'),
                    'confirm' => 1
                ];
            }
        }

        if (! $token = $this->jwt->attempt($credentials)) {
            var_dump($token);
            //return response()->json(['Maaf Anda tidak bisa login'], 404);
        }

        return response()->json(compact('token'));
    }


    // 退出登录
    public function logout(Request $request)
    {
        if ($this->jwt->parseToken()->invalidate()) {
            return response()->json(['logout' => 'Logout'], 200);
        }

        $token = $this->jwt->getToken();
        $this->jwt->invalidate($token);
        return response()->json(['logout' => 'Logout'], 200);
    }




    // 注册用户
    public function createUser(Request $request) 
    {
        $this->validate($request, [
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
            'name'     => 'required|min:4|max:255'
        ]);

        $data = $request->only('email', 'password', 'name');

        $user = new User();
        $user->password = password_hash($data['password'], PASSWORD_DEFAULT);
        $user->email    = $data['email'];
        $user->name     = $data['name'];
        $user->save();

        $token = $this->jwt->attempt($request->only('email', 'password'));


        return response()->json([
            'data' => $user,
            'meta' => [
                'token' => $token
            ],
        ], 200);
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