<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMusicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('musics', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');    // 曲谱名
            $table->string('type');    // 类型：1-指弹 2-弹唱 3-合奏 4-单音
            $table->string('tag');     // 难度：1-萌新 2-入门 3-进阶 4-高难度 5-大神
            $table->string('author');  // 作者
            $table->string('theme');   // 专题/主题
            $table->string('url');     // 封面图
            $table->text('content');   // 曲谱介绍
            $table->tinyInteger('status')->default(1);
            $table->integer('views');  // 访问量
            $table->integer('likes');  // 点赞数
            $table->integer('forwards');// 转发数
            $table->integer('sort');
            $table->integer('uid');    // 上传用户ID
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('musics');
    }
}
