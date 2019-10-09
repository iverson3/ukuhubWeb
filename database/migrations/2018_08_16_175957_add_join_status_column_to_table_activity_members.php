<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJoinStatusColumnToTableActivityMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 新增字段
        Schema::table('activity_members', function(Blueprint $table) {
            $table->tinyInteger('join_status')->default(1)->after('remark')->comment('报名状态：1-已报名 0-已取消');
        });

        // ->after('column')
        // ->comment('my comment')
        // ->default($value)
        // ->nullable($value = true)
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activity_members', function(Blueprint $table) {
            $table->dropColumn('join_status');
        });
    }
}
