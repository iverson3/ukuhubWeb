<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');     // 活動標題
            $table->string('author');   // 活動發起者 (人或組織)
            $table->string('pic');      // 封面图
            $table->integer('start_time'); // 開始時間
            $table->integer('end_time');   // 結束時間
            $table->text('content');    // 活動詳情
            $table->integer('views');   // 访问量
            $table->integer('forwards');// 转发数
            $table->tinyInteger('status')->default(1); // 活動狀態：1-開啟 2-關閉 0-結束
            $table->integer('sort');
            $table->integer('uid');     // 創建者ID
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
        Schema::dropIfExists('activities');
    }
}
