## 1.基本介绍
### 1.1 项目介绍
> 基于laravel框架的工具，开发常用工具封装成静态方法
### 1.2 配置
在config/app.php的providers添加```Mjy191\Tools\ServiceProvider::class```

使用命令```php artisan vendor:publish --provider="Mjy191\Tools\ServiceProvider"```发布配置config/tools.php

```
<?php
return [
    // 接口签名appid和appkey，不同路由可以有不同的签名
    'ak' => [
        'api'=>[
            'appId'=>'xxxx',
            'appKey'=>'xxxx',
        ],
        'admin'=>[
            'appId'=>'xxxx',
            'appKey'=>'xxxx',
        ],
    ],
    //密码加盐前缀
    'preSalt' => 'xxxx',
     //密码加盐后缀
    'postSalt' => 'xxxx',
];
```
### 1.3 配置捕获异常
新建app/Exceptions/ApiException.php
捕获ApiException抛出的异常进行处理
```
namespace App\Exceptions;

use Mjy191\Tools\Tools;
use Exception;

class ApiException extends Exception
{
    /**
     * 转换异常为 HTTP 响应
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response()->json(Tools::returnData(null,$this->getCode(),$this->getMessage()))->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }
}
```
### 1.4 使用举例
api接口统一标准返回数据

```$xslt
use App\Models\Api\UserModel;
use Mjy191\Tools\Tools;
use App\Http\Controllers\Controller;

class TestController extends Controller
{

    /**
     * 用户详情
     */
    public function index(){
        $data = UserModel::where('id',1)->first();
        return Tools::returnData($data);
    }
}
```
### 1.5 安装
```
composer require mjy191/tools
```

