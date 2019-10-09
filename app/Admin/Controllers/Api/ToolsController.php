<?php

namespace App\Admin\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ToolsController extends Controller
{
    public function editor_upload_pic(Request $request)
    {
        if ($request->hasFile('wang-editor-image-file') && $request->file('wang-editor-image-file')->isValid()) {
            $file = $request->file('wang-editor-image-file');
            
            $store_result = $file->store(config('editor-upload_root_path') . config('editor-upload_folder'));
            $uri = str_replace('public', 'storage', $store_result);
            $url = 'http://' . $_SERVER["HTTP_HOST"] . '/' . $uri;

            $result = [
                "errno" => 0,
                "data"  => [ $url ]
            ];
            return response()->json($result);
        } else {
            $result = [
                "errno" => 1,
                "data"  => []
            ];
            return response()->json($result);
        }
    }
}
