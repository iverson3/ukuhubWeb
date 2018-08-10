<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_members', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('activity_id'); // 活動ID 關聯到activity表
            $table->string('wechat');       // 微信號 / 手機號
            $table->string('name');         // 參與者名字(網名)
            $table->string('music_type');   // 樂器類型： 尤克里里 吉他 鼓
            $table->string('level');        // 級別： 萌新 入門 大佬
            $table->string('pic');          // 琴圖url / 視頻地址url
            $table->string('remark');       // 備註信息
            $table->tinyInteger('status')->default(1); // 報名狀態：1-可用 0-禁止
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
        Schema::dropIfExists('activity_members');
    }
}
