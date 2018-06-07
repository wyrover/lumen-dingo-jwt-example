# lumen-dingo-jwt-example

test


## 1. 生成工程

```
composer global require "laravel/lumen-installer"
composer create-project --prefer-dist laravel/lumen backend
cd backend
php -S localhost:8000 -t public
```

http://localhost:8000


### 安装 dingo
```
composer require dingo/api:2.0.0-alpha2@dev
composer update
```

`bootstrap/app.php` 添加

``` php
$app->register(Dingo\Api\Provider\LumenServiceProvider::class);
```


.env 添加
```
API_STANDARDS_TREE=vnd
API_PREFIX=api
API_STRICT=false
API_DEBUG=true
API_VERSION=v1
API_SUBTYPE=lumen
```


### 安装 jwt

`composer.json` 添加
```
"tymon/jwt-auth": "1.0.0-rc.2@dev"
```

然后
```
composer update 
```


修改 `bootstrap/app.php`

``` php
// 去掉注释
$app->withFacades(); 
$app->withEloquent();
 
// jwt
$app->register(Tymon\JWTAuth\Providers\LumenServiceProvider::class);
```

```
php artisan jwt:secret
```

在项目根目录(backend)下新建一个 config 目录，复制 `vendor/laravel/lumen-framework/config/auth.php` 到 `config` 目录下, 修改内容如下：

``` php
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
        'guard' => env('AUTH_GUARD', 'api'),
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
    | Supported: "token"
    |
    */
 
    'guards' => [
        'api' => ['driver' => 'jwt', 'provider' => 'users'],
 
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
            'model' => \App\User::class,
        ],
    ],
 
    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | Here you may set the options for resetting passwords including the view
    | that is your password reset e-mail. You may also set the name of the
    | table that maintains all of the reset tokens for your application.
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
        //
    ],
 
];

```


修改 app/User.php

``` php
<?php
 
namespace App;
 
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;
 
class User extends Model implements JWTSubject, AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;
 
    protected $table = 'users';
 
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email',
    ];
 
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];
 
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
 
    public function getJWTCustomClaims()
    {
        return [];
    }
}
```


## Postman 测试

### 注册用户

POST http://localhost:8000/api/auth/register

JSON(application/json)

``` js
{
	"name": "wyrover",
	"email": "wyrover@gmail.com",
	"password": "123456"
	
}

```


### 用户登录

POST http://localhost:8000/api/auth/login

JSON(application/json)
``` js
{
    "email": "wyrover@gmail.com",
    "password": "123456"
}
```


### 登录后用户信息

POST http://localhost:8000/api/auth/me

Bearer Token

返回

``` javascript
{
    "data": {
        "id": 1,
        "name": "Shaina Carroll",
        "email": "user1@example.com",
        "realname": "",
        "birthday": "",
        "phone": "",
        "address": "",
        "city": "",
        "province": "",
        "country": "",
        "created_at": "2018-06-07 02:57:09",
        "updated_at": "2018-06-07 02:57:09"
    }
}
```


### 添加 task

POST http://localhost:8000/api/tasks/new

Bearer Token

``` js
{
    "title" : "test1",
    "description" : "hello world!"
}

```

返回
``` js
{
    "task": {
        "title": "test1",
        "description": "hello world!",
        "user_id": 5,
        "updated_at": "2018-06-07 06:46:33",
        "created_at": "2018-06-07 06:46:33",
        "id": 4
    }
}
```