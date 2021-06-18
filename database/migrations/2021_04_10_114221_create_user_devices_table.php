<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_devices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->foreignUuid('user_id')->constrained('users');
            $table->foreignUuid('device_id')->constrained('devices')->onDelete('cascade');
            $table->foreignUuid('place_id')->constrained('places')->onDelete('cascade');
            $table->macAddress('mac_address');
            $table->ipAddress('ip_address');
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
        Schema::dropIfExists('user_devices');
    }
}
