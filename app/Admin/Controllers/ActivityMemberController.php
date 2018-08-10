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

class ActivityMemberController extends Controller
{
    use ModelForm;

    protected $header = "活動人員管理";
    protected $action = '';

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
                $url = '/admin/activityMember/info?id=' . $id;
                $actions->append('<a href="' . $url . '"><i class="fa fa-eye"></i></a>');
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
                $filter->equal('status', '状态')->select(config('ukuhub.music.statusSelect'));
                $filter->between('created_at', '報名时间')->datetime();
            });

            $grid->id('ID')->sortable();
            $grid->name('名字');
            $grid->wechat('微信號');
            $grid->music_type('樂器類型');
            $grid->level('分組');
            $grid->remark('備註信息');
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

                // dump($form->pic1);
                // dump($form->activity_id);

                // foreach ($form as $key => $value) {
                //     dump($key);
                //     dump($value);
                // }
                // exit;

                // $form->pic = "";
                // if ($form->level == '萌新') {
                //     $form->pic = $form->pic1;
                // } else {
                //     $form->pic = $form->pic2;
                // }

                // if ($form->pic == '') {
                //     $error = new MessageBag([
                //         'pic1' => '必二選其一',
                //     ]);
                //     return back()->with(compact('error'));
                //     return response('error');
                // }
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

            $form->image('pic', '琴照片')->removable();
            // $form->text('pic2', '視頻地址url')->rules('max:100', [
            //     'max' => '不能超过100个字符',
            // ]);
            // $form->ignore(['pic1', 'pic2']);

            $form->text('remark', '備註信息')->rules('max:100', [
                'max' => '不能超过100个字符',
            ]);
            $form->switch('status', '状态')->states(config('ukuhub.music.statusList'))->default(1);

            $form->display('created_at', '創建時間');
            $form->display('updated_at', '更新時間');
        });
    }

    public function info(Request $request)
    {
        dump($request->id);
    }
}
