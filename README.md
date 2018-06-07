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


### 删除 task

DELETE http://localhost:8000/api/tasks/1

Bearer Token

返回
``` js
{
    "id": 1,
    "title": "test1",
    "description": "hello world!",
    "completed": 0,
    "user_id": 5,
    "created_at": "2018-06-07 06:42:48",
    "updated_at": "2018-06-07 06:42:48"
}
```


### 修改 task

PUT http://localhost:8000/api/tasks/2

Bearer Token

``` js
{
    "title" : "test11111111111111111",
    "description" : "hello world!!!!!!!!!!!!!!"
}
```

返回
``` js
{
    "id": 2,
    "title": "test11111111111111111",
    "description": "hello world!!!!!!!!!!!!!!",
    "completed": 0,
    "user_id": 5,
    "created_at": "2018-06-07 06:45:11",
    "updated_at": "2018-06-07 07:40:30"
}
```


### 查单条 task

GET http://localhost:8000/api/tasks/2

Bearer Token

返回

```
{
    "task": {
        "id": 2,
        "title": "test11111111111111111",
        "description": "hello world!!!!!!!!!!!!!!",
        "completed": 0,
        "user_id": 5,
        "created_at": "2018-06-07 06:45:11",
        "updated_at": "2018-06-07 07:40:30"
    }
}
```


### 查所有 task

GET http://localhost:8000/api/tasks

Bearer Token

返回
``` js
{
    "current_page": 1,
    "data": [
        {
            "id": 6,
            "title": "test1",
            "description": "hello world!",
            "completed": 0,
            "user_id": 5,
            "created_at": "2018-06-07 07:21:30",
            "updated_at": "2018-06-07 07:21:30"
        },
        {
            "id": 5,
            "title": "test1",
            "description": "hello world!",
            "completed": 0,
            "user_id": 5,
            "created_at": "2018-06-07 07:03:52",
            "updated_at": "2018-06-07 07:03:52"
        },
        {
            "id": 4,
            "title": "test1",
            "description": "hello world!",
            "completed": 0,
            "user_id": 5,
            "created_at": "2018-06-07 06:46:33",
            "updated_at": "2018-06-07 06:46:33"
        },
        {
            "id": 3,
            "title": "test1",
            "description": "hello world!",
            "completed": 0,
            "user_id": 5,
            "created_at": "2018-06-07 06:46:21",
            "updated_at": "2018-06-07 06:46:21"
        },
        {
            "id": 2,
            "title": "test1",
            "description": "hello world!",
            "completed": 0,
            "user_id": 5,
            "created_at": "2018-06-07 06:45:11",
            "updated_at": "2018-06-07 06:45:11"
        }
    ],
    "first_page_url": "http://localhost:8000/api/tasks?page=1",
    "from": 1,
    "last_page": 2,
    "last_page_url": "http://localhost:8000/api/tasks?page=2",
    "next_page_url": "http://localhost:8000/api/tasks?page=2",
    "path": "http://localhost:8000/api/tasks",
    "per_page": 5,
    "prev_page_url": null,
    "to": 5,
    "total": 6
}
```


## 数据查询的方式

ORM 映射后的查询

``` php
// 所有的分类按名字升序
$categories = Category::orderBy('name', 'asc')->get();

// 所有的 feed 按名字升序
$feeds = Feed::orderBy('feed_name', 'asc')->get();


$Articles = Article::where('status', 'unread')->orderBy('id', 'asc')->get();


$Articles = Article::where('subject', 'like', '%'.$request->input('search').'%')->orWhere('content', 'like', '%'.$request->input('search').'%')->orderBy('published', Helper::setting('sort_order'))->select('id')->get();


$Articles = DB::table('categories')->join('feeds', 'categories.id', '=', 'feeds.category_id')->join('articles', 'feeds.id', '=', 'articles.feed_id')->where('categories.id', $request->input('category_id'))->where('articles.status', $request->input('status'))->orderBy('published', Helper::setting('sort_order'))->select('articles.id')->get();

```

**新增数据，可以直接 create**

``` php
$article = Article::create($request->all());

```




**修改数据, 用 ORM 找到对象后，直接 save()，用于修改多个字段**

``` php
$article = Article::find($id);
.....
$article->save();
```

**还可以 where 查询后 update, 用户修改单个字段方便**

``` php

Article::where('status', 'unread')->update(['status' => 'read']);

```

**删除数据**

```
$article = Article::find($id);
$article->delete();
```
