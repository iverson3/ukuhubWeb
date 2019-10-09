<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommonController extends Controller
{
	protected $success;
	protected $error;
	protected $message;
	protected $otherInfo = null;

	protected $apiResult = array();

	public function __construct() 
	{

	}

	// 校验用户是否处于登录状态
	protected function checkUserIsLogin() 
	{
		if (Auth::user()) {
			return true;
		}
		return false;
	}

	// 校验用户是否处于登录状态 
	// 不在登录状态则重定向到登录页面
	protected function checkUserAndRedirect() {
		if (!Auth::check()) {
			return redirect('/login');
		}
		return true;
	}

	// 检查用户是否处于登录状态，否则直接返回api响应
	protected function checkLoginAndapiResponse() {
		if (!Auth::check()) {
            $this->success = false;
            $this->message = "请先登录，再进行操作";
            return $this->apiResponse();
        }
        return false;
	}

	protected function getUserId() {
		$uid  = "-1";
        $user = Auth::user();
        if ($user) {
            $user = $user->toArray();
            $uid  = $user['id'];
        }
        return $uid;
	}

	protected function assignResponseParas($parasArr)
	{
		$this->success = $parasArr['success'];
		$this->error   = $parasArr['error'];
		$this->message = $parasArr['message'];
	}

	protected function apiResponse() 
	{
		$this->apiResult = array(
			'success' => $this->success,
			'error'   => $this->error,
			'message' => $this->message
		);
		if ($this->otherInfo != null) {
			$this->apiResult['otherinfo'] = $this->otherInfo;
		}
		return response()->json($this->apiResult);
	}
}