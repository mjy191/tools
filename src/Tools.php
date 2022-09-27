<?php

namespace Mjy191\Tools;

use App\Exceptions\ApiException;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Mjy191\Enum\Enum;
use Mjy191\MyLogs\MyLogs;

class Tools
{

    /**
     * 常用签名
     * @param $appKey
     * @param null $content
     * @return string
     */
    public static function sign($appKey, $content = null)
    {
        if ($content) {
            $str = $content;
        } else {
            $str = file_get_contents('php://input');
        }
        $str = $appKey . $str . $appKey;
        MyLogs::write('sign pre', $str);
        $sign = sha1($str);
        MyLogs::write('sign', $sign);
        return $sign;
    }

    /**
     * 接口统一返回数据格式
     * @param null $data
     * @param int $code
     * @param string $msg
     * @return array
     */
    public static function returnData($data = null, $code = Enum::codeSuccess, $msg = Enum::msg[Enum::codeSuccess])
    {
        if ($data == null) {
            $data = (object)[];
        }
        return [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
            'timestamp' => time()
        ];
    }

    /**
     * 记录异常日志
     * @param $e
     */
    public static function logException($logName, $e)
    {
        MyLogs::write($logName, ['code' => $e->getCode(), 'msg' => $e->getMessage(), 'errorFile' => $e->getFile() . ":" . $e->getLine()]);
    }

    /**
     * @param $param
     * @return false|string
     */
    public static function toString($param)
    {
        if (is_string($param)) {
            return $param;
        }
        return json_encode($param, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 随机生成字符串
     * @param integer $length
     */
    public static function randStr($length = 16)
    {
        $char = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $char[mt_rand(0, strlen($char) - 1)];
        }
        return $str;
    }


    /**
     * 生成加盐密码
     * @param $password
     */
    public static function getPassword($password)
    {
        $preSalt = config("tools.preSalt");
        $postSalt = config("tools.postSalt");
        return sha1("{$preSalt}{$password}{$postSalt}");
    }

    /**
     *静态方法校验
     * @param $data
     * @param $rules
     * @param null $msg
     * @throws ApiException
     */
    public static function validate($data, array $rules, array $messages = [], $customAttributes = [])
    {
        $validator = Validator::make($data, $rules, $messages, $customAttributes);
        if ($validator->fails()) {
            throw new ApiException($validator->errors()->first(), Enum::erCodeParam);
        }
    }

    /**
     * @param $request
     * @return bool
     */
    public static function checkCaptcha($request)
    {
        if (!captcha_api_check($request->input('captcha'), $request->input('key'))) {
            throw new ApiException('code is error!', Enum::erCodeParam);
        }
        return true;
    }

    /**
     * 批量创建新数据
     * @param array $key
     * @param array $data
     * @return array|mixed
     */
    public static function issetNewData(array $key, array $data)
    {
        $newData = [];
        foreach ($key as $val) {
            if (isset($data[$val])) {
                $newData[$val] = $data[$val];
            }
        }
        return $newData;
    }

    /**
     * 批量创建不为空新数据
     * @param array $key
     * @param array $data
     * @return array|mixed
     */
    public static function notEmptyNewData(array $key, array $data)
    {
        $newData = [];
        foreach ($key as $val) {
            if (isset($data[$val]) && !empty($data[$val])) {
                $newData[$val] = $data[$val];
            }
        }
        return $newData;
    }

    /**
     * 空值转换null插入数据库
     * @param array $keys
     * @param array $data
     */
    public static function emptyToNull(array $keys, array &$data)
    {
        foreach ($keys as $key) {
            if (isset($data[$key]) && empty($data[$key])) {
                $data[$key] = null;
            }
        }
    }


    /**
     * 获取ip地址
     * @return mixed|string
     */
    public static function getRealIp()
    {
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $res = preg_match('/[\d\.]{7,15}/', $ip, $matches) ? $matches [0] : '';
        return $res;
    }

    /*
     * 获取浏览器信息
     */
    public static function getBrowser()
    {
        $sys = $_SERVER['HTTP_USER_AGENT'];  //获取用户代理字符串
        if (stripos($sys, "Firefox/") > 0) {
            return "Firefox";
        } elseif (stripos($sys, "Maxthon") > 0) {
            return "傲游";
        } elseif (stripos($sys, "MSIE") > 0) {
            return "IE";
        } elseif (stripos($sys, "OPR") > 0) {
            return "Opera";
        } elseif (stripos($sys, "Edge") > 0) {
            return "Edge";
        } elseif (stripos($sys, "Chrome") > 0) {
            return "Chrome";
        } elseif (stripos($sys, 'rv:') > 0 && stripos($sys, 'Gecko') > 0) {
            return "IE";
        } else {
            return $sys;
        }
    }

    /**
     * 获取协议https
     */
    public static function getHttps()
    {
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        return $http_type;
    }


    /**
     * 获取域名
     */
    public static function getHost(){
        return self::getHttps().$_SERVER['HTTP_HOST'];
    }

    /**
     * 获取appId
     */
    public static function getAppId()
    {
        return config('tools.ak.' . Route::current()->getPrefix() . '.appId');
    }

    /**
     * 获取ak
     */
    public static function getAk()
    {
        return config('tools.ak.' . Route::current()->getPrefix() . '.appKey');
    }
}
