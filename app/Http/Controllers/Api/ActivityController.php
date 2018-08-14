<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityMember;

class ActivityController extends Controller
{
    public function list(Request $request)
    {
    	$orderby = $request->orderby;
    	$map = array();
    	$map['status'] = 1;
    	$res = Activity::where($map)->orderBy($orderby)->get();
    	if ($res) {
        	$res = $res->toArray();
        	foreach ($res as $key => &$value) {
        		$value['pic'] = config('filesystems.disks.admin.url') . '/' . $value['pic'];
        	}
            $result = [
                'success' => true,
                'data'    => $res,
                'error'   => null
            ];
        } else {
            $result = [
                'success' => false,
                'data'    => null,
                'error'   => '没有活動'
            ];
        }
        return response()->json($result);
    }

    public function memberList(Request $request)
    {
    	$id = $request->id;
    	$map['activity_id'] = $id;
    	$map['status'] = 1;
    	$res = ActivityMember::where($map)->select('id', 'name', 'music_type', 'level', 'pic', 'remark', 'created_at')->orderBy('created_at', 'desc')->get();
    	if ($res) {
        	$res = $res->toArray();
        	// foreach ($res as $key => &$value) {
        	// 	$value['pic'] = config('filesystems.disks.admin.url') . '/' . $value['pic'];
        	// }
            $result = [
                'success' => true,
                'data'    => $res,
                'error'   => null
            ];
        } else {
            $result = [
                'success' => false,
                'data'    => null,
                'error'   => '没有參與者'
            ];
        }
        return response()->json($result);
    }

    public function joinActivity(Request $request)
    {
    	if ($request->remark == null) {
    		$remark = '';
    	} else {
    		$remark = $request->remark;
    	}
    	$activityMember = new ActivityMember;
    	$activityMember->activity_id = $request->activity_id;
    	$activityMember->wechat      = $request->wechat;
    	$activityMember->name        = $request->name;
    	$activityMember->music_type  = $request->music_type;
    	$activityMember->level       = $request->level;
    	$activityMember->pic         = $request->pic;
    	$activityMember->remark      = $remark;
    	$res = $activityMember->save();
    	if ($res) {
    		$result = [
                'success' => true,
                'data'    => '',
                'error'   => null
            ];
    	} else {
    		$result = [
                'success' => false,
                'data'    => null,
                'error'   => '插入失敗'
            ];
    	}
    	return response()->json($result);
    }

    public function uploadPic(Request $request)
    {
        if ($request->hasFile('imgBlob') && $request->file('imgBlob')->isValid()) {
            $file = $request->file('imgBlob');
            
            $store_result = $file->store(config('joinActivity-uploadPic-savePath'));
            $uri = str_replace("public", 'storage', $store_result);
            $url = 'http://' . $_SERVER["HTTP_HOST"] . '/' . $uri;

            $result = [
                'success' => true,
                'error'   => null,
                'url'     => $url
            ];
            return response()->json($result);
        } else {
            $result = [
                'success' => false,
                'error'   => '文件上传失败！',
                'url'     => '',
            ];
            return response()->json($result);
        }
    }
}
