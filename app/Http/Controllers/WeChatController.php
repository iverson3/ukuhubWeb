<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log, DB, View, Cache, Input;
use Illuminate\Support\Facades\Storage;
use App\Models\Music;

class WeChatController extends Controller
{
    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve()
    {
        // Log::info('request arrived.');

        $app = app('wechat.official_account');
        $app->server->push(function ($message) {
        	switch ($message['MsgType']) {
                case 'event': 
                	$user_openid = $message['FromUserName'];
		            if ($message['Event'] == 'subscribe') {
						// 下面是你点击关注时，进行的操作
						// $user_info['unionid'] = $message->ToUserName;
						// $user_info['openid'] = $user_openid;
						// $userService = $app->user;
						// $user = $userService->get($user_info['openid']);
						// $user_info['subscribe_time'] = $user['subscribe_time'];
						// $user_info['nickname'] = $user['nickname'];
						// $user_info['avatar'] = $user['headimgurl'];
						// $user_info['sex'] = $user['sex'];
						// $user_info['province'] = $user['province'];
						// $user_info['city'] = $user['city'];
						// $user_info['country'] = $user['country'];
						// $user_info['is_subscribe'] = 1;

                        $res = "欢迎关注\r\n";
                        $res .= "您可以通过回复[活动] [曲谱]等关键字来获取相关信息\r\n";
                        $res .= "或者通过回复具体的'曲谱名'获取想要的曲谱";
		                return $res;
		            } else if ($message['Event'] == 'unsubscribe') {
		            	// 用户取消关注时执行的业务逻辑
		            	return "已取消关注";
		            }
                    break;
                case 'text':
                	// ToUserName	开发者微信号
					// FromUserName	发送方帐号（一个OpenID）
					// CreateTime	消息创建时间 （整型）
					// Content	    文本消息内容
                    // $user_openid = $message['FromUserName'];
                    // $unionid     = $message['ToUserName'];
		        	$content     = $message['Content'];
		        	// $createTime  = $message['CreateTime'];

		        	// if (is_numeric($content)) {
		        	// 	$res = DB::table('pictures')->where('id', intval($content))->select('url')->first();
		        	// 	if ($res) {
		        	// 		$url = 'http://' . $_SERVER['HTTP_HOST'] . $res->url;
		        	// 		return '曲谱地址：' . $url;
		        	// 	} else {
		        	// 		return '找不到您要的曲谱';
		        	// 	}
		        	// } else {
		        	// 	return "无法识别您的消息";
		        	// }

		        	if (strpos($content, '报名') !== false || strpos($content, '活动') !== false) {
		        		$url = 'http://wap.ukuhub.com/activity/list';
		        		return '活动入口： ' . $url;
		        	}

                    if (strpos($content, '曲谱') !== false || strpos($content, '谱子') !== false) {
                        $url = 'http://wap.ukuhub.com/home/music';
                        return '曲谱中心： ' . $url;
                    }

                    $map[] = ['name', 'like', '%'.$content.'%'];
                    $list = Music::where($map)->select('id')->get();

                    if (!$list || count($list) == 0) {
                        return '无法识别您的消息或找不到您搜的曲谱，请回复相关关键字';
                    }
                    if (count($list) == 1) {
                        $url = "搜索结果：\r\n";
                        $url .= "http://wap.ukuhub.com/music/detail?id=" . $list[0]['id'];
                        return $url;
                    } else {
                        $res = "多个结果：\r\n";
                        foreach ($list as $music) {
                            $url = "http://wap.ukuhub.com/music/detail?id=" . $music['id'] . "\r\n";
                            $res .= $url;
                        }
                        return $res;
                    }

		        	// $url = 'http://' . $_SERVER["HTTP_HOST"] . '/wechat/music/list';
                    break;
                case 'image':
                    return '收到图片消息';
                    break;
                case 'voice':
                    return '收到语音消息';
                    break;
                case 'video':
                    return '收到视频消息';
                    break;
                case 'location':
                    return '收到坐标消息';
                    break;
                case 'link':
                    return '收到链接消息';
                    break;
                // ... 其它消息
                default:
                    return '收到其它消息';
                    break;
            }
        });

        return $app->server->serve();
    }



    // 沒有權限
    public function createMenu()
    {
    	// $list = $app->menu->list();

    	$app = app('wechat.official_account');
    	$menuList = [
		    [
		        "type" => "click",
		        "name" => "測試",
		        "key"  => "V1001_TODAY_MUSIC"
		    ],
		    [
		        "type" => "view",
		        "name" => "報名入口",
		        "url"  => "http://wap.ukuhub.com/"
		    ],
		    [
		        "name"       => "曲譜",
		        "sub_button" => [
		            [
		                "type" => "view",
		                "name" => "指彈",
		                "url"  => "http://wap.ukuhub.com/"
		            ],
		            [
		                "type" => "view",
		                "name" => "彈唱",
		                "url"  => "http://wap.ukuhub.com/"
		            ],
		            [
		                "type" => "click",
		                "name" => "測試click",
		                "key" => "V1001_GOOD"
		            ],
		        ],
		    ],
		];
		$res = $app->menu->create($menuList);
		dump($res);
    }











    // 批量导入曲谱图片
    public function multImportImgs()
    {
    	return View::make('wechat.multUploadMusic', []);
    }
    public function setOptions(Request $request)
    {
    	Cache::put('wechat_music_type', $request->wechat_music_type, 60);
    	Cache::put('wechat_music_tag',  $request->wechat_music_tag, 60);
    	// Cache::put('wechat_music_author',  $value, 60);
    	$result = [
            'success' => true,
            'error'   => null
        ];
        return response()->json($result); 
    }
    public function wechatupload(Request $request)
    {
    	if ($request->hasFile('wechatfile') && $request->file('wechatfile')->isValid()) {
            $file = $request->file('wechatfile');

            $truename = Input::file('wechatfile')->getClientOriginalName();
            $truename = str_replace(strrchr($truename, "."), "", $truename); 

            $store_result = $file->store(config('blog.upload_root_path') . config('blog.wechat_music_folder'));
            $url = str_replace("public", 'storage', $store_result);

            $music = new Music();
            $music->name = $truename;
            $music->type = Cache::get('wechat_music_type', '未知');
            $music->tag = Cache::get('wechat_music_tag', '未知');
            $music->author = '未知';
            $music->theme = '未知';
            $music->url = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $url;
            $music->content = '';
            $music->views = 100;
            $music->sort = 100;
            $res = $music->save();

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

    // 曲谱查询中心
    public function musicCenter(Request $request)
    {
    	$type = '全部';
    	$tag  = '全部';
    	$orderby = "id";
    	$where = array();
    	$order = 'id';
    	$namelike = '';
    	if ($request->has('type')) {
    		if ($request->input('type') != '全部') {
    			$where['type'] = $request->input('type');
    			$type = $request->input('type');
    		}
    		if ($request->input('tag') != '全部') {
    			$where['tag'] = $request->input('tag');
    			$tag = $request->input('tag');
    		}
    		$order = $request->input('orderby');
    		$orderby = $request->input('orderby');
    	}

        $typeList = [
        	['name' => '弹唱'],
        	['name' => '指弹'],
        	['name' => '合奏']
       	];
       	$tagList = [
       		['name' => '入门'],
       		['name' => '进阶'],
       		['name' => '高难度'],
       		['name' => '大神']
       	];
       	$orderList = [
       		['name' => '谱名', 'key' => 'name'],
       		['name' => '浏览量', 'key' => 'views'],
       		['name' => '时间', 'key' => 'created_at'],
       	];

       	if ($request->has('namelike') && ($request->input('namelike') != '')) {
			$namelike = $request->input('namelike');
			$list = Music::where($where)->where('name', 'like', '%' . $namelike . '%')->select('id','name','views','author')->orderBy($order, 'desc')->get();
       	} else {
       		$list = Music::where($where)->select('id','name','views','author')->orderBy($order, 'desc')->get();
       	}
    	View::share('curtype', $type);
    	View::share('curtag',  $tag);
    	View::share('curorderby',  $orderby);
    	View::share('namelike', $namelike);
    	View::share('typeList', $typeList);
    	View::share('tagList',  $tagList);
    	View::share('orderList',$orderList);
    	View::share('musicList', $list);
    	return View::make('wechat.MusicList', []);
    }

    // 曲谱详情页
    public function musicInfo(Request $request)
    {
    	Music::where('id', $request->id)->increment('views');
    	$music = Music::where('id', $request->id)->select('id','name','views','author', 'theme', 'content', 'url')->first();
    	View::share('music', $music);
    	return View::make('wechat.MusicInfo', []);
    }
}
