<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropPlanNameAndStopLimitAndRevenueSplitAndPlanIdFromUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['plan_name', 'stop_limit', 'revenue_split', 'plan_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('plan_name')->default("starter");
            $table->boolean('revenue_split')->default(1);
            $table->string('stop_limit')->default(30);
            $table->string('plan_id', 100)->nullable();
        });
    }
}
