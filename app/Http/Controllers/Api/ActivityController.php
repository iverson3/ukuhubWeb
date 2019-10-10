<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityMember;
use App\Models\Group;
use Maatwebsite\Excel\Facades\Excel;
use Cache;

class ActivityController extends Controller
{
    public function list(Request $request)
    {
        // $res = $this->verifyToken($request);
        // if (is_array($res)) {
        //     return response()->json($res);
        // }
        
    	$orderby = $request->orderby;
    	$map = array();
    	$map['status'] = 1;
    	// $map[] = ['start_time', '>', time() + 60 * 60 * 12];  // 活动开始前12h不再允许报名
    	$res = Activity::where($map)->orderBy($orderby, 'desc')->get();
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

    public function incrementView(Request $request)
    {
        $res = Activity::where('id', $request->id)->increment('views');
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

    public function memberList(Request $request)
    {
    	$id = $request->id;
    	$map['activity_id'] = $id;
    	$map['status']      = 1;
    	$map['join_status'] = 1;
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

    private function verifyToken($request) {
        $result = array();

        $openid = $request->header('openId');
        if ($openid === null || $openid == '') {
            $result = [
                'success' => false,
                'code'    => 'noopenid',
                'data'    => null,
                'error'   => '沒有openId參數'
            ];
        }

        if (!Cache::has($openid)) {
            $result = [
                'success' => false,
                'code'    => 'expired',
                'data'    => null,
                'error'   => 'token不存在或已過期'
            ];
        }

        $token  = $request->header('token');
        if ($token === null || $token == '') {
            $result = [
                'success' => false,
                'code'    => 'notoken',
                'data'    => null,
                'error'   => '沒有token參數'
            ];
        }

        $_token = Cache::get($openid);
        if ($token != $_token) {
            $result = [
                'success' => false,
                'code'    => 'wrongtoken',
                'data'    => null,
                'error'   => 'token不對'
            ];
        }
        if (count($result) == 0) {
            return true;
        } else {
            return $result;
        }
    }

    public function joinActivity(Request $request)
    {
        $res = $this->verifyToken($request);
        if (is_array($res)) {
            return response()->json($res);
        }

    	$pic = $request->pic;
    	if ($request->remark == null) {
    		$remark = '';
    	} else {
    		$remark = $request->remark;
    	}
    	if ($request->isOldMember == 1) {
    		$member = ActivityMember::where('wechat', $request->wechat)->first();
    		if ($member) {
    			$pic = $member->pic;
    		}
    	}

    	// 黑名单用户不许报名
        $map1['wechat'] = $request->wechat;
    	$map1['status'] = 0;
        $res1 = ActivityMember::where($map1)->first();
        if ($res1) {
            $result = [
                'success' => false,
                'data'    => '',
                'error'   => 'black'
            ];
            return response()->json($result);
        }

    	// 不許重複報名
    	$map2['activity_id'] = $request->activity_id;
    	$map2['wechat']      = $request->wechat;
        $map2['join_status'] = 1;
    	$res2 = ActivityMember::where($map2)->first();
    	if ($res2) {
    		$result = [
                'success' => false,
                'data'    => '',
                'error'   => 'repeat'
            ];
            return response()->json($result);
    	}

    	$activityMember = new ActivityMember;
    	$activityMember->activity_id = $request->activity_id;
    	$activityMember->wechat      = $request->wechat;
    	$activityMember->name        = $request->name;
    	$activityMember->music_type  = $request->music_type;
    	$activityMember->level       = $request->level;
    	$activityMember->pic         = $pic;
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

    public function validateMemberByWechat(Request $request)
    {
    	$wechat = $request->wechat;
    	$res = ActivityMember::where('wechat', $wechat)->first();
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
                'error'   => '沒有該用戶'
            ];
    	}
    	return response()->json($result);
    }

    public function searchMember(Request $request)
    {
    	$map['activity_id'] = $request->activity_id;
    	$map['wechat']      = $request->wechat;
    	$map['join_status'] = 1;
    	$res = ActivityMember::where($map)->first();
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
                'error'   => '沒有該用戶'
            ];
    	}
    	return response()->json($result);
    }

    public function cancelActivity(Request $request)
    {
    	$map['activity_id'] = $request->activity_id;
    	$map['wechat']      = $request->wechat;
    	$data['join_status'] = 0;
    	$res = ActivityMember::where($map)->update($data);
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
                'error'   => '沒有刪除或刪除失敗'
            ];
    	}
    	return response()->json($result);
    }


    public function getGroupAndMember(Request $request)
    {
        $activity_id = $request->activity_id;
        $uid         = $request->uid;
        $groups = array();

        $groupList = Group::where('activity_id', $activity_id)->select('leader', 'members')->get();
        $groupList = $groupList->toArray();

        if ($groupList) {

            $idStr = '';
            foreach ($groupList as $key => $group) {
                $idStr = $idStr . ',' . $group['members'];
            }
            $idStr = substr($idStr, 1);
            $idArr = explode(',', $idStr);

            $map['activity_id'] = $activity_id;
            $map['join_status'] = 1;
            $map['status']      = 1;
            $members = ActivityMember::where($map)->get();
            $members = $members->toArray();

            $otherMember = array();
            foreach ($members as $key => $member) {
                if (!in_array($member['id'], $idArr)) {
                    $otherMember[] = $member;
                }
            }
            if (count($otherMember) > 0) {
                $groups['未分组'] = $otherMember;
            }


            foreach ($groupList as $key => $group) {
                $memberList = array();
                $ids = explode(',', $group['members']);
                foreach ($ids as $k => $v) {
                    $member = ActivityMember::where('id', $v)->select('id','wechat','name','music_type','level','remark','join_status','status')->first();
                    if ($member) {
                        $memberList[] = $member;
                    }
                }

                $groups[$group['leader']] = $memberList;
            }
        } else {
            $groupLeaderList = explode(',', config('activity.team.leader'));

            $map['activity_id'] = $activity_id;
            $map['status']      = 1;
            $map['join_status'] = 1;
            $memberList = ActivityMember::where($map)->orderBy('created_at')->get();

            $sum = floor(count($memberList->toArray()) / count($groupLeaderList));
            $n   = 0;

            foreach ($groupLeaderList as $k => $leader) {

                $mark = 0;
                $idArr = array();
                for ($i = $n; $i < count($memberList); $i++) {
                    $idArr[] = $memberList[$i]['id'];
                    $groups[$leader][] = $memberList[$i]->toArray();
                    $n++;
                    $mark++;
                    if ($mark == $sum && ($k < count($groupLeaderList) - 1)) {
                        break;
                    }
                }
                $ids = implode(',', $idArr);

                $group = new Group;
                $group->activity_id = $activity_id;
                $group->leader      = $leader;
                $group->music_type  = '未知';
                $group->level       = '未知';
                $group->members     = $ids;
                $group->remark      = '';
                $group->uid         = $uid;
                $res = $group->save();
            }
        }

        $data['groups'] = $groups;
        return response()->json($data);
    }

    // 保存分组设置
    public function saveGroupSetting(Request $request)
    {
        $activity_id = $request->activity_id;
        $groups      = $request->groups;
        foreach ($groups as $key => $group) {
            if ($key != '未分组') {
                $ids = implode(',', $group);
                $map['activity_id'] = $activity_id;
                $map['leader']      = $key;

                $data['members'] = $ids;
                Group::where($map)->update($data);
            }
        }
        $result = [
            'success' => true,
            'data'    => '',
            'error'   => null
        ];
        return response()->json($result);
    }

    // 导出分组信息
    public function exportGroup(Request $request)
    {
        // 导出的member字段
        $fields      = $request->fields;
        $activity_id = $request->activity_id;

        $fieldArr = explode(',', $fields);
        $data = array();

        $list = Group::where('activity_id', $activity_id)->select('leader', 'members')->get();
        foreach ($list as $group) {
            $leader = $group['leader'];
            $idArr  = explode(',', $group['members']);

            $members = array($leader);
            foreach ($idArr as $id) {
                $info = '';
                $member = ActivityMember::where('id', $id)->select('*')->first();
                $member = $member->toArray();
                foreach ($member as $key => $value) {
                    if (in_array($key, $fieldArr)) {
                        $info = $info . $value . '-';
                    }
                }
                $members[] = substr($info,0,strlen($info) - 1);
            }
            $data[] = $members;
        }
        $data = $this->RowColumnExchange($data);

        Excel::create('分组结果', function($excel) use ($data){
            $excel->sheet('score', function($sheet) use ($data){
                $sheet->rows($data);
            });
        })->export('xls');
    }

    // 二维数组的行列转换
    private function RowColumnExchange($arr1)
    {
        $rowCount = count($arr1);
        $columnCount = 0;
        foreach ($arr1 as $value) {
            if (count($value) > $columnCount) {
                $columnCount = count($value);
            }
        }
        for ($i = 0; $i < $columnCount; $i++) {
            for ($j = 0; $j < $rowCount; $j++) {
                $arr2[$i][$j] = '';
            }
        }

        for ($i = 0; $i < count($arr1); $i++) {
            for ($j = 0; $j < count($arr1[$i]); $j++) {
                $arr2[$j][$i] = $arr1[$i][$j];
            }
        }
        return $arr2;
    }
}
