<?php
return [
    // *** 配置接口秘钥 ***
    // 接口签名appid和appkey，不同路由可以有不同的签名
    'ak' => [
        'api'=>[
            'appId'=>'',
            'appKey'=>'',
        ],
        'admin'=>[
            'appId'=>'',
            'appKey'=>'',
        ],
    ],

    // *** 密码盐前缀和后缀
    'preSalt' => '',
    'postSalt' => '',
];
