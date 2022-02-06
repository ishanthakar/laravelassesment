<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->id();
            $table->string('username')->nullable();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('email')->nullable();
            $table->string('otp')->nullable();
            $table->string('password',255)->nullable();
            $table->tinyInteger('user_role')->nullable()->default(2)->comment('(1=>Admin, 2 =>User) default 2');
            $table->tinyInteger('status')->default(0)->comment('(0=>Unverified, 1 =>Verified) default 0');
            $table->text('profile_image')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamp('registered_at')->nullable();
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
        Schema::dropIfExists('user');
    }
}
