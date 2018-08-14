<?php

namespace App\Admin\Controllers;

use App\Models\Activity;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ActivityController extends Controller
{
    use ModelForm;

    protected $header = "活動管理";
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
        return Admin::grid(Activity::class, function (Grid $grid) {

            $grid->actions(function ($actions) {
                // 也可添加自定义的操作按钮及功能 (看文档)
                $id  = $actions->getKey();
                $url = '/admin/activityMember?&activity_id=' . $id;
                $actions->append('<a href="' . $url . '"><i class="fa fa-eye"></i></a>');
            });

            // 过滤字段设置
            $grid->filter(function ($filter) {
                $filter->disableIdFilter();

                $filter->equal('status', '状态')->select(config('ukuhub.music.activityStatusList'));
                $filter->like('name', '活動標題');
                $filter->like('author', '發起者');
                $filter->gt('views', '該瀏覽量以上');
                $filter->gt('forwards', '該轉發數以上');
                $filter->between('created_at', '创建时间')->datetime();
            });

            $grid->id('ID')->sortable();

            $grid->name('活動標題');
            $grid->author('發起者');
            $grid->views('訪問量')->sortable();
            $grid->forwards('轉發量')->sortable();

            $grid->column('start_time', '開始時間')->display(function () {
                return date("Y.m.d", $this->start_time); 
            })->sortable();
            $grid->column('end_time', '結束時間')->display(function () {
                return date("Y.m.d", $this->end_time); 
            })->sortable();

            $grid->column('statusInfo', '状态')->display(function () {
                $list = config('ukuhub.music.activityStatusList');
                return $list[$this->status];
            })->color('red');

            $grid->sort('排序')->sortable();

            $grid->created_at('創建時間')->sortable();
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
                $form->start_time = strtotime($form->start_time);
                $form->end_time   = strtotime($form->end_time);
            });

            $form->display('id', 'ID');

            $form->text('name', '活動標題')->rules('required|max:180', [
                'required' => '字段不能为空',
                'max'      => '不能超过180个字符',
            ]);
            $form->text('author', '活動發起人')->rules('required|max:50', [
                'required' => '字段不能为空',
                'max'      => '不能超过50个字符',
            ]);

            $form->datetime('start_time', '開始時間')->rules('required', [
                'required' => '字段不能为空'
            ]);
            $form->datetime('end_time', '結束時間')->rules('required', [
                'required' => '字段不能为空'
            ]);
            $form->text('address', '活動地點')->rules('required|max:50', [
                'required' => '字段不能为空',
                'max'      => '不能超过50个字符',
            ]);

            $form->switch('status', '状态')->states(config('ukuhub.music.activityStatusList'))->default(1);

            $form->image('pic', '封面图')->removable()->rules('required', [
                'required' => '字段不能为空'
            ]);

            $form->wangeditor('content', '活動详情')->rules('required', [
                'required' => '字段不能为空'
            ]);
            
            $form->number('sort', '排序')->min(1);

            $form->hidden('views')->default(100);
            $form->hidden('forwards')->default(5);
            $form->hidden('uid')->default(Admin::user()->id); // 获取当前登录用户ID

            $form->display('created_at', '創建時間');
            $form->display('updated_at', '更新時間');
        });
    }
}
