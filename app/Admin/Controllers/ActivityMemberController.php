<?php

namespace App\Admin\Controllers;

use App\Models\ActivityMember;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\MessageBag;
use Illuminate\Http\Request;
use App\Models\Activity;
use Encore\Admin\Show;
use View;

class ActivityMemberController extends Controller
{
    use ModelForm;

    protected $header = "活動人員管理";
    protected $action = '';

    protected $member_id = 0;
    protected $picFieldType = '';

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header($this->header);
            $content->description('列表');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id, Request $request)
    {
        $this->picFieldType = $request->picFieldType;

        return Admin::content(function (Content $content) use ($id) {

            $content->header($this->header);
            $content->description('編輯');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header($this->header);
            $content->description('新建');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(ActivityMember::class, function (Grid $grid) {

            $grid->actions(function ($actions) {
                // 可取消掉默认的两个操作按钮
                $actions->disableDelete();   
                $actions->disableEdit();

                $id  = $actions->getKey();
                $row = $actions->row;
                if ($row->level === '萌新') {
                    $picFieldType = 'pic';
                } else {
                    $picFieldType = 'url';
                }
                $url = '/admin/activityMember/show?id=' . $id . '&picFieldType=' . $picFieldType;
                $actions->append('<a href="' . $url . '"><i class="fa fa-eye"></i></a>');

                $url2 = '/admin/activityMember/'.$id.'/edit?picFieldType=' . $picFieldType;
                $actions->append('<a href="' . $url2 . '"><i class="fa fa-edit"></i></a>');
            });
            // 禁用新建按钮
            $grid->disableCreateButton();

            // 过滤字段设置
            $grid->filter(function ($filter) {
                $filter->disableIdFilter();

                $res = Activity::where('status', 1)->select('id','name')->get();
                $activityList = array();
                foreach ($res->toArray() as $key => $value) {
                    $activityList[$value['id']] = $value['name'];
                }
                $filter->equal('activity_id', '活動')->select($activityList);

                $filter->like('name', '名字');
                $filter->like('wechat', '微信號');
                $filter->equal('music_type', '樂器類型')->select(config('ukuhub.music.music_type'));
                $filter->equal('level', '分組')->select(config('ukuhub.music.level'));
                $filter->equal('join_status', '报名状态')->select(config('ukuhub.music.joinStatus'));
                $filter->equal('status', '状态')->select(config('ukuhub.music.statusSelect'));
                $filter->between('created_at', '報名时间')->datetime();
            });

            $grid->id('ID')->sortable();
            $grid->name('名字');
            $grid->wechat('微信號');
            $grid->music_type('樂器類型');
            $grid->level('分組');
            $grid->remark('備註信息');
            $grid->join_status('报名状态')->switch(config('ukuhub.music.joinStatusList'));
            $grid->status('状态')->switch(config('ukuhub.music.statusList'));
            $grid->created_at('報名時間')->sortable();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(ActivityMember::class, function (Form $form) {

            // 保存前回调
            $form->saving(function ($form) {
                // 在保存数据之前根据需要对表单数据进行需要的修改调整或校验
                if ($form->remark == null) {
                    $form->remark = '';
                }
            });

            $form->display('id', 'ID');

            $res = Activity::where('status', 1)->select('id','name')->get();
            $activityList = array();
            foreach ($res->toArray() as $key => $value) {
                $activityList[$value['id']] = $value['name'];
            }
            $form->select('activity_id', '報名活動')->options($activityList)->rules('required', [
                'required' => '字段不能为空'
            ]);

            $form->text('wechat', '微信號')->rules('required|max:30', [
                'required' => '字段不能为空',
                'max'      => '不能超过30个字符',
            ]);

            $form->text('name', '網名/真名')->rules('required|max:30', [
                'required' => '字段不能为空',
                'max'      => '不能超过30个字符',
            ]);

            $form->select('music_type', '樂器類型')->options(config('ukuhub.music.music_type'))->rules('required', [
                'required' => '字段不能为空'
            ]);
            $form->select('level', '分組')->options(config('ukuhub.music.level'))->rules('required', [
                'required' => '字段不能为空'
            ]);

            if ($this->picFieldType === 'pic') {
                $form->image('pic', '琴照')->removable();
            } else {
                $form->text('pic', '视频地址')->rules('required|max:100', [
                    'required' => '字段不能为空',
                    'max' => '不能超过100个字符'
                ]);
            }

            $form->text('remark', '備註信息')->rules('max:100', [
                'max' => '不能超过100个字符',
            ]);
            $form->switch('join_status', '报名状态')->states(config('ukuhub.music.joinStatusList'))->default(1);
            $form->switch('status', '状态')->states(config('ukuhub.music.statusList'))->default(1);

            $form->display('created_at', '創建時間');
            $form->display('updated_at', '更新時間');
        });
    }

    public function show(Request $request)
    {
        $id           = intval($request->id);
        $picFieldType = $request->picFieldType;

        return Admin::content(function (Content $content) use ($id, $picFieldType) {

            $content->header('活动人员');
            $content->description('详情');

            $content->body(Admin::show(ActivityMember::findOrFail($id), function (Show $show) use ($picFieldType) {

                $show->panel()
                    ->tools(function ($tools) {
                        $tools->disableEdit();
                        $tools->disableDelete();
                    });

                $show->id('ID');
                $show->name('名字');
                $show->wechat('微信号');
                $show->music_type('乐器类型');
                $show->level('能力分级');

                if ($picFieldType === 'pic') {
                    $show->pic('琴图')->image('', 300);
                } else {
                    $show->pic('视频链接')->link();
                }

                $show->remark('备注信息');
                $show->join_status('报名状态')->as(function ($status) {
                    if (intval($status) === 1) {
                        return '已报名';
                    } else {
                        return '已取消';
                    }
                });
                $show->status('状态')->as(function ($status) {
                    if (intval($status) === 1) {
                        return '可用';
                    } else {
                        return '禁用';
                    }
                });
                $show->created_at('报名时间');

            }));
        });
    }



    // 自定义详情页 (弃用 可作参考)
    public function info(Request $request)
    {
        $this->member_id = $request->id;

        // 数据库查询操作 获取需要渲染到模板里面的数据
        $data = [];
        $list = array();

        return Admin::content(function (Content $content) use ($data, $list) {

            // 选填
            $content->header('活動人員詳情');

            // 选填
            $content->description('小标题');

            // 添加面包屑导航 since v1.5.7
            // $content->breadcrumb(
            //     ['text' => '首页', 'url' => '/admin'],
            //     ['text' => '用户管理', 'url' => '/admin/users'],
            //     ['text' => '编辑用户']
            // );

            // 填充页面body部分，这里可以填入任何可被渲染的对象
            $content->body($this->member_id);

            // body方法可接受 laravel的视图模板作为参数
            $content->body(view('admin.member.info', ['data' => $data, 'list' => $list]));

            // 在body中添加另一段内容
            $content->body('foo bar');

            // `row`是`body`方法的别名
            $content->row('hello world');
        });



        // $member = ActivityMember::where('id', $this->member_id)->first();
        //
        // // $member->pic = config('filesystems.disks.admin.url') . '/' . $member->pic;
        // // dump($member);
        //
        // View::share('member', $member);
        // return View::make('admin.member.info', []);
    }
}
