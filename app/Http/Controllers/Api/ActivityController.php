<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Activity;

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

        	// print_r($res);exit;
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
}
