<?php

namespace App\Admin\Controllers;

use App\User;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Cache, Auth;

class UserController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('用户管理');
            $content->description('用户信息管理');

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

            $content->header('header');
            $content->description('description');

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

            $content->header('header');
            $content->description('description');

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
        return Admin::grid(User::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->name('Name')->sortable();
            $grid->email('E-mail');

            $grid->created_at();
            $grid->updated_at();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(User::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('name', 'Name')->rules('required|max:16', [
                'required' => '字段不能为空',
                'max'      => '不能超过16个字符',
            ]);
            $form->email('email', 'E-mail')->rules('required|email', [
                'required' => '字段不能为空',
                'email'    => '不符合邮件地址格式',
            ]);

            $form->password('password', '密码')->rules('required|confirmed', [
                'required'  => '字段不能为空',
                'confirmed' => '两次输入的密码不同',
            ]);
            $form->password('password_confirmation', '重复密码')->rules('required', [
                    'required' => '字段不能为空',
                ])->default(function ($form) {
                    // 默认返回表单中password字段的值 (貌似没什么用 验证规则中设置了不能为空)
                    return $form->model()->password;
                });
            $form->ignore(['password_confirmation']);  // 操作数据库时忽略该字段，因为数据库中不存在该字段


            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
