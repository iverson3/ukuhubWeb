<?php

namespace App\Http\Controllers\Activity;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use View, Auth;

class ActivityController extends Controller
{
    // 活动分组
    public function selectGroup(Request $request)
    {
        $activity_id = $request->activity_id;
        $uid         = $request->uid;
        // $uid = Auth::user()->id;

        View::share('isEdit', 1);
        View::share('uid', $uid);
        View::share('activity_id', $activity_id);
        return View::make('activity.selectGroup', []);
    }

}
