<?php

namespace App\Http\Controllers\Api;

// use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB, Session, Cache;

class UserController extends Controller
{
	// DB::connection()->enableQueryLog(); // 开启执行日志
    // $user = DB::table('users')->where($where)->select('*')->get();
    // print_r(DB::getQueryLog());

    // Cache::put('accessToken', $val, 60);
    // Cache::has('accessToken');
    // Cache::get('accessToken', null);

    public function fetchSessionKey(Request $request) 
    {
        // token有效期是7天
        // $minutes = 60 * 24 * 7;
        $minutes = 3;

        $code     = $request->code;
        $userinfo = $request->userinfo; // 將用戶數據存入數據庫中 to do

        if ($code == '') {
            $openid = $request->openid;
            // 生成token
            $token = md5($openid . time());
            Cache::put('token', $token, $minutes);

            $data = array(
                'token'    => $token, 
                'duration' => $minutes
            );
            $result = [
                'success' => true,
                'data'    => $data,
                'error'   => null
            ];

        } else {
            // 注意，這裡要使用小程序的appid和secret
            $appid  = config('wechat.mini_program.default.app_id');
            $secret = config('wechat.mini_program.default.secret');

            // 使用curl調用微信接口(GET) 獲取openid unionid(不一定有) session_key
            $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$secret.'&js_code='.$code.'&grant_type=authorization_code';
            $curl = curl_init(); 
            curl_setopt($curl, CURLOPT_URL, $url); 
            curl_setopt($curl, CURLOPT_AUTOREFERER, 1); 
            curl_setopt($curl, CURLOPT_TIMEOUT, 30); 
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); 
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); 
            $res = curl_exec($curl); 
            if (curl_errno($curl)) {
                // echo 'Errno'.curl_error($curl); // 捕抓异常
                $result = [
                    'success' => false,
                    'data'    => [],
                    'error'   => '訪問微信接口異常'
                ];
            } else {
                $data = json_decode($res, true);
                $openid = $data['openid'];

                // 生成token
                $token = md5($openid . time());
                Cache::put('token', $token, $minutes);

                $data['token']    = $token;
                $data['duration'] = $minutes;

                $result = [
                    'success' => true,
                    'data'    => $data,
                    'error'   => null
                ];
            }
            curl_close($curl);
        }
        
        return response()->json($result);
    }

   
}
