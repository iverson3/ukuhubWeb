<?php

namespace App\Admin\Controllers;

use App\Models\ActivityMember;
use App\Models\Group;
use App\Models\Activity;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use DB;

class GroupController extends Controller
{
    use ModelForm;

    private $header = '人员分组';

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
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header($this->header);
            $content->description('编辑');

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
        // 取消新增功能
        return Admin::content(function (Content $content) {

            $content->header($this->header);
            $content->description('新增');

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
        return Admin::grid(Group::class, function (Grid $grid) {

            $grid->actions(function ($actions) {
                // 也可添加自定义的操作按钮及功能 (看文档)
                $id  = $actions->getKey();

                $url = '/admin/group/show?id=' . $id;
                $actions->append('<a href="' . $url . '"><i class="fa fa-eye"></i></a>');
            });

            $grid->disableCreateButton();

            $grid->filter(function ($filter) {
                $filter->disableIdFilter();

                $res = Activity::where('status', 1)->select('id','name')->get();
                $activityList = array();
                foreach ($res->toArray() as $key => $value) {
                    $activityList[$value['id']] = $value['name'];
                }
                $filter->equal('activity_id', '活动')->select($activityList);

                // $filter->between('created_at', '创建时间')->datetime();
            });

            $grid->id('ID')->sortable();

            $grid->activity_id('活动ID')->sortable();
            $grid->leader('组长');
            $grid->music_type('乐器类型');
            $grid->level('能力级别');
            $grid->members('组员');
            $grid->remark('备注');
            $grid->uid('分组者ID');

            $grid->created_at('分组时间');
            $grid->updated_at('更新时间');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Group::class, function (Form $form) {

            $form->saving(function ($form) {
                // 在保存数据之前根据需要对表单数据进行需要的修改调整或校验
                if ($form->remark == null) {
                    $form->remark = '';
                }
                if ($form->music_type == null) {
                    $form->music_type = '未知';
                }
                if ($form->level == null) {
                    $form->level = '未知';
                }
            });

            $form->display('id', 'ID');

            $form->display('activity_id', '活动ID');
            $form->display('leader', '组长');

            $form->select('music_type', '乐器类型')->options(config('ukuhub.music.music_type'));
            $form->select('level', '能力级别')->options(config('ukuhub.music.level'));

            $form->display('members', '组员');

            $form->text('remark', '备注信息')->rules('max:100', [
                'max' => '不能超过100个字符',
            ]);
            $form->display('uid', '分组者ID');

            $form->display('created_at', '创建时间');
            $form->display('updated_at', '更新时间');
        });
    }

    public function show(Request $request) {
        $id = intval($request->id);
        return Admin::content(function (Content $content) use ($id) {

            $content->header('人员分组');
            $content->description('详情');

            $data = Group::where('id', $id)->first();

            $activity = Activity::where('id', $data['activity_id'])->first();
            $data['activity'] = $activity['name'];

            $members = ActivityMember::whereIn('id', explode(',', $data['members']))->select('name', 'wechat')->get();
            $data['members'] = $members->toArray();

            $user = DB::table('admin_users')->where('id', '=', $data['uid'])->select('username', 'name')->first();
            $data['user'] = $user;

            $content->body(view('admin.group.show', ['data' => $data]));
        });
    }
}
