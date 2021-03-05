<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropZipcodeAddLngAndLatToUsers extends Migration
{
    public function up()
      {
          Schema::table('users', function($table) {
             $table->dropColumn('zipcode');
          });
          Schema::table('users', function($table) {
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
         });
      }

      public function down()
      {
        Schema::table('users', function($table) {
            $table->string('zipcode');
         });
         Schema::table('users', function($table) {
           $table->dropColumn(['latitude', 'longitude']);
        });
      }
}
