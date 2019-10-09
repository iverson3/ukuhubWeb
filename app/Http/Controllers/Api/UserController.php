<?php

namespace App\Http\Controllers\Api;

use App\Models\Music;
// use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class UserController extends Controller
{
	// DB::connection()->enableQueryLog(); // 开启执行日志
    // $user = DB::table('users')->where($where)->select('*')->get();
    // print_r(DB::getQueryLog());

    public function fetchSessionKey(Request $request) 
    {
        $code   = $request->code;
        // 注意，這裡要使用小程序的appid和secret
        $appid  = config('wechat.mini_program.default.app_id');
        $secret = config('wechat.mini_program.default.secret');

        // 使用curl調用微信接口 獲取openid unionid session_key
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$secret.'&js_code='.$code.'&grant_type=authorization_code';
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        // curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        // curl_setopt($curl, CURLOPT_POST, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        // curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 跳过证书检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // 不从证书中检查SSL加密算法是否存在

        $res = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            // echo 'Errno'.curl_error($curl);//捕抓异常
            $result = [
                'success' => false,
                'data'    => [],
                'error'   => '訪問微信接口異常'
            ];
        } else {
            $data = json_decode($res, true);
            $result = [
                'success' => true,
                'data'    => $data,
                'error'   => null
            ];
        }
        curl_close($curl); // 关闭CURL会话
        
        return response()->json($result);
    }

   
}
