<?php

namespace App\Http\Controllers\Api;

use App\Models\Music;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class MusicController extends Controller
{
	// DB::connection()->enableQueryLog(); // 开启执行日志
    // $user = DB::table('users')->where($where)->select('*')->get();
    // print_r(DB::getQueryLog());

    public function list(Request $request)
    {
    	$page     = $request->page;
        $pagesize = $request->pagesize;
        $name     = $request->name;
        $type     = $request->type;
        $level    = $request->level;
        $orderby  = $request->order;

        $map = array();
        if ($name != '') {
        	$map[] = ['name', 'like' , "%{$name}%"];
        }
        if ($type != '') {
        	$map['type'] = $type;
        }
        if ($level != '') {
        	$map['tag'] = $level;
        }
        if ($orderby == '' || $orderby == 'mix') {
        	$orderby = 'views';
        }

        $res = DB::table('musics')
        		 ->where($map)
                 ->select('*')
                 ->orderBy($orderby, 'desc')
                 ->paginate($pagesize, ['*'], 'p', $page);
        if ($res) {
        	$res = $res->toArray();
        	foreach ($res['data'] as $key => &$value) {
        		$value->url = config('filesystems.disks.admin.url') . '/' . $value->url;
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
                'error'   => '没有曲譜'
            ];
        }
        // 最后一个参数 options = 1 则不会把返回的数据中int类型的数据转换成string类型
        return response()->json($result, 200, [], 1);
    }

    public function getMusicDetail(Request $request) 
    {
        $id = $request->id;
        $res = Music::where('id', $id)->first();
        if ($res) {
            $result = [
                'success' => true,
                'data'    => $res,
                'error'   => null
            ];
        } else {
            $result = [
                'success' => false,
                'data'    => null,
                'error'   => '没有数据'
            ];
        }
        return response()->json($result);
    }

    public function incrementView(Request $request)
    {
        $res = Music::where('id', $request->id)->increment('views');
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
                'error'   => '操作失败'
            ];
        }
        return response()->json($result);
    }
}
