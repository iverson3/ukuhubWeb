<?php

namespace App\Admin\Controllers;

use App\Models\Music;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class MusicController extends Controller
{
    use ModelForm;

    protected $header = "曲谱管理";
    protected $action = '';

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            // 获取当前用户信息
            // Admin::user()
            // dump(Admin::user());

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

            $this->action = 'edit';
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

            $this->action = 'create';
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
        return Admin::grid(Music::class, function (Grid $grid) {

            // 过滤字段设置
            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();

                $filter->equal('type', '类型')->select(config('ukuhub.music.typeList'));
                $filter->equal('tag', '难度')->select(config('ukuhub.music.tagList'));
                $filter->equal('status', '状态')->radio([
                    '' => 'All',
                    1  => '可用',
                    0  => '禁用'
                ]);
                $filter->like('name', '曲谱名');
                $filter->like('author', '制谱者');
                $filter->like('theme', '主题');
                $filter->gt('views', '該瀏覽量以上');
                $filter->gt('likes', '該點讚數以上');
                $filter->gt('forwards', '該轉發數以上');
                $filter->between('created_at', '创建时间')->datetime();
            });

            $grid->name('曲谱名')->editable();
            $grid->type('类型');
            $grid->tag('难度');
            $grid->author('制谱者');
            $grid->theme('主题');
            $grid->views('浏览量')->sortable();
            $grid->likes('点赞数')->sortable();
            $grid->forwards('转发数')->sortable();
            $grid->sort('排序')->sortable();

            // 添加数据表中不存在的字段
            $grid->column('linkUser', '修改者')->display(function () {
                return Admin::user()->name;
            })->color('red');

            $grid->status('状态')->switch(config('ukuhub.music.statusList'));

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
        return Admin::form(Music::class, function (Form $form) {

            // 保存前回调
            $form->saving(function ($form) {
                // 在保存数据之前根据需要对表单数据进行需要的修改调整或校验
                if ($form->theme == '' || $form->theme == null) {
                    $form->theme = '未知';
                }
            });

            $form->display('id', 'ID');

            $form->text('name', '曲谱名')->rules('required|max:100', [
                'required' => '字段不能为空',
                'max'      => '不能超过100个字符',
            ]);

            $form->select('type', '曲谱类型')->options(config('ukuhub.music.typeList'))->rules('required', [
                'required' => '字段不能为空'
            ]);
           
            $form->select('tag', '曲谱难度')->options(config('ukuhub.music.tagList'))->rules('required', [
                'required' => '字段不能为空'
            ]);

            $form->text('author', '制谱者')->rules('max:20', [
                'max' => '不能超过20个字符'
            ]);

            $form->text('theme', '主题')->rules('max:20', [
                'max' => '不能超过20个字符'
            ]);

            $form->image('url', '曲谱封面图')->removable();

            $form->wangeditor('content', '曲谱详情')->rules('required', [
                'required' => '字段不能为空'
            ]);

            $form->switch('status', '状态')->states(config('ukuhub.music.statusList'))->default(1);
            
            $form->number('sort', '排序')->min(1);

            $form->hidden('views')->default(100);
            $form->hidden('likes')->default(10);
            $form->hidden('forwards')->default(5);
            $form->hidden('uid')->default(Admin::user()->id); // 获取当前登录用户ID

             // 通过自定义的属性$action 来判断某些特殊字段的表单呈现方式
            if ($this->action == 'create') {
                
            } else {
                
            }

            $form->display('created_at', '創建時間');
            $form->display('updated_at', '更新時間');
        });
    }
}
