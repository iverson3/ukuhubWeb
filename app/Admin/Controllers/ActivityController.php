<?php

namespace App\Admin\Controllers;

use App\Models\Activity;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Encore\Admin\Show;

class ActivityController extends Controller
{
    use ModelForm;

    protected $header = "活动管理";
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
        return Admin::grid(Activity::class, function (Grid $grid) {

            $grid->actions(function ($actions) {
                // 也可添加自定义的操作按钮及功能 (看文档)
                $id  = $actions->getKey();
                $url = '/admin/activityMember?&activity_id=' . $id;
                $actions->append('<a href="' . $url . '"><i class="fa fa-paper-plane"></i></a>');

                $url2 = '/admin/activity/show?id=' . $id;
                $actions->append('<a href="' . $url2 . '"><i class="fa fa-eye"></i></a>');

                // 通过判断时间来决定是否显示这个操作按钮
                $url3 = '/activity/selectgroup?activity_id=' . $id . '&uid=' . Admin::user()->id;
                $actions->append('<a href="' . $url3 . '"><i class="fa fa-list"></i></a>');
            });

            // 过滤字段设置
            $grid->filter(function ($filter) {
                $filter->disableIdFilter();

                $filter->equal('status', '状态')->select(config('ukuhub.music.activityStatusList'));
                $filter->like('name', '活动标题');
                $filter->like('author', '发起者');
                $filter->gt('views', '该浏览量以上');
                $filter->gt('forwards', '该转发数以上');
                $filter->between('created_at', '创建时间')->datetime();
            });

            $grid->id('ID')->sortable();

            $grid->name('活动标题');
            $grid->author('发起者');
            $grid->views('访问量')->sortable();
            $grid->forwards('转发量')->sortable();

            $grid->column('start_time', '开始时间')->display(function () {
                return substr($this->start_time, 5, 11); 
            })->sortable();
            $grid->column('end_time', '结束时间')->display(function () {
                return substr($this->end_time, 5, 11); 
            })->sortable();

            // $grid->column('statusInfo', '状态')->display(function () {
            //     $list = config('ukuhub.music.activityStatusList');
            //     return $list[$this->status];
            // })->color('red');
            $grid->status('状态')->switch(config('ukuhub.music.statusList'));

            $grid->sort('排序')->sortable();

            $grid->created_at('创建时间')->sortable();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Activity::class, function (Form $form) {

            // 保存前回调
            $form->saving(function ($form) {
                // 在保存数据之前根据需要对表单数据进行需要的修改调整或校验
                // 已在Activity模型中進行時間格式轉換，此處不再需要
                // $form->start_time = strtotime($form->start_time);
                // $form->end_time   = strtotime($form->end_time);

                // 验证值是够有重复
                // if ($from->nick_name !== $form->model()->email && User::where('email',$form->email)->value('id')) {
                //     // 错误信息提示
                //     $error = new MessageBag(['title' => '提示', 'message' => '邮箱已存在!']);
                //     return back()->withInput()->with(compact('error'));
                // }
            });

            $form->display('id', 'ID');

            $form->text('name', '活动标题')->rules('required|max:180', [
                'required' => '字段不能为空',
                'max'      => '不能超过180个字符',
            ]);
            $form->text('author', '活动发起人')->rules('required|max:50', [
                'required' => '字段不能为空',
                'max'      => '不能超过50个字符',
            ]);

            $form->datetime('start_time', '开始时间')->rules('required', [
                'required' => '字段不能为空'
            ]);
            $form->datetime('end_time', '结束时间')->rules('required', [
                'required' => '字段不能为空'
            ]);
            $form->text('address', '活动地点')->rules('required|max:50', [
                'required' => '字段不能为空',
                'max'      => '不能超过50个字符',
            ]);

            $form->switch('status', '状态')->states(config('ukuhub.music.activityStatusList'))->default(1);

            $form->image('pic', '封面图')->removable()->rules('required', [
                'required' => '字段不能为空'
            ]);

            $form->wangeditor('content', '活动详情')->rules('required', [
                'required' => '字段不能为空'
            ]);
            
            $form->number('sort', '排序')->min(1);

            $form->hidden('views')->default(100);
            $form->hidden('forwards')->default(5);
            $form->hidden('uid')->default(Admin::user()->id); // 获取当前登录用户ID

            $form->display('created_at', '创建时间');
            $form->display('updated_at', '更新时间');
        });
    }

    public function show(Request $request)
    {
        $id = intval($request->id);
        return Admin::content(function (Content $content) use ($id) {

            $content->header('活动详情');
            $content->description('详情');

            $content->body(Admin::show(Activity::findOrFail($id), function (Show $show) {

                // 修改面板的样式和标题
                // style的取值可以是primary、info、danger、warning、default
                $show->panel()
                    ->style('default')
                    ->title('活动详情面板');

                // 设置面板右上角默认的三个按钮编辑、删除、列表
                // $show->panel()
                //     ->tools(function ($tools) {
                //         $tools->disableEdit();
                //         $tools->disableList();
                //         $tools->disableDelete();
                //     });

                // 在字段之间添加一条分隔线
                // $show->divider();

                $show->id('ID');
                $show->name('标题');
                $show->author('发起人');
                $show->pic('封面图')->image('', 300);
                $show->content('内容');
                $show->created_at();
                $show->updated_at();

            }));
        });
    }
}
