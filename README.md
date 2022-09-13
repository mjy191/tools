## 1.基本介绍
### 1.1 项目介绍
> 基于laravel框架的工具，开发常用工具封装成静态方法
### 1.2 配置
在laravel的 config\tools.php添加如下配置
记录mysql操作日志
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

