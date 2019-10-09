<?php

use App\Admin\Extensions\WangEditor;
use Encore\Admin\Form;
use Encore\Admin\Grid\Column;

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */


// 注册自定义的表单组件
Form::extend('wangeditor', WangEditor::class);

// 注销已经存在的表单组件
Form::forget(['map', 'editor']);



// 扩展列功能 (自定义字段显示格式)
Column::extend('color', function ($value, $color) {
	// 其他逻辑处理
    return "<span style='color: $color'>$value</span>";
});

Column::extend('imageOrUrl', function ($value) {
	// 默认显示为文本 (即参数不是图片链接)
	$content = "<span style='max-width:120px;display:inline-block;word-break:break-all;'>" . $value . "</span>";
	$arr = explode(".", $value);
	$imageExt = array('png', 'jpg', 'jpeg', 'gif', 'bmp');
	if (count($arr) > 0) {
		$extension = end($arr);
		if (in_array($extension, $imageExt)) {
			// 如果参数是图片链接，则显示为图片
			$content = "<img src='" . $value . "' title='乐器照片' alt='乐器图片' style='max-width:120px;max-height:100px;' />";
		}
	}
    return $content;
});