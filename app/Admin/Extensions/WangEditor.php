<?php

namespace App\Admin\Extensions;

use Encore\Admin\Form\Field;

class WangEditor extends Field
{
    protected $view = 'admin.vendor.wang-editor';

    protected static $css = [
        '/vendor/wangEditor-3.1.1/release/wangEditor.min.css',
    ];

    protected static $js = [
        '/vendor/wangEditor-3.1.1/release/wangEditor.min.js',
    ];

    public function render()
    {
        $fieldname    = $this->formatName($this->column);
        $editor_id    = $this->id;
        $z_index      = 999999;
        $uploadImgUrl = config('editor-uploadImgUrl');
        $mapAk        = config('editor-mapAk', 'TVhjYjq1ICT2qqL5LdS8mwas');
        $pasteFilter  = config('editor-pasteFilter', 'false');
        $pasteText    = 'true';
        if ($pasteFilter == 'true') {
            $pasteText = config('editor-pasteText', 'true');
        }
        $token = csrf_token();

        // 自定义菜单内容
        // editor.customConfig.menus = [
        //     'head',
        //     'bold',
        //     'italic',
        //     'underline'
        // ]

        // editor.customConfig.uploadImgUrl = "{$uploadImgUrl}";
        // editor.customConfig.uploadImgFileName = {$uploadfieldname};
        // editor.customConfig.mapAk = '{$mapAk}';

        // 上传表单的字段名写死
        $uploadfieldname = 'wang-editor-image-file';


        $this->script = <<<EOT

var E = window.wangEditor
var editor = new E('#{$editor_id}');

editor.customConfig.zIndex = {$z_index}
editor.customConfig.uploadImgServer = "{$uploadImgUrl}";
editor.customConfig.uploadImgMaxSize = 3 * 1024 * 1024;
editor.customConfig.uploadImgMaxLength = 20;
editor.customConfig.uploadFileName = '{$uploadfieldname}';
editor.customConfig.uploadImgTimeout = 8000;
editor.customConfig.debug = true
var _pasteFilter = {$pasteFilter};
editor.customConfig.pasteFilter = _pasteFilter;
if (_pasteFilter == true) {
    editor.customConfig.pasteText = {$pasteText};
}
editor.customConfig.uploadImgShowBase64 = true
editor.customConfig.onchange = function (html) {
    $('input[name=\'$fieldname\']').val(html);
}
editor.create()

EOT;
        return parent::render();
    }
}