<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('activity_id'); // 活動ID 關聯到activity表
            $table->string('leader')->comment('组长名字');       // 组长
            $table->string('music_type')->comment('乐器类型：尤克里里 吉他 鼓'); // 樂器類型： 尤克里里 吉他 鼓
            $table->string('level')->comment('能力级别：萌新 入门 大佬');        // 級別： 萌新 入門 大佬
            $table->string('members')->comment('组员ID集');
            $table->string('remark')->comment('备注信息');        // 備註信息
            $table->integer('uid')->comment('执行分组的管理员ID'); // 执行分组的管理员ID
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
        Schema::dropIfExists('groups');
    }
}
