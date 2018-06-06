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


安装 dingo
```
composer require dingo/api:2.0.0-alpha2@dev
composer update
```

bootstrap/app.php 添加

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