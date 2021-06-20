<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->foreignUuid('user_id')->constrained('users');
            $table->foreignUuid('device_type_id')->constrained('device_types')->onDelete('cascade');
            $table->foreignUuid('place_id')->constrained('places')->onDelete('cascade');
            $table->foreignUuid('fog_id')->constrained('fogs')->onDelete('cascade');
            $table->macAddress('mac_address');
            $table->ipAddress('ip_address');
            $table->timestamps();

            $table->boolean('is_synchronized')->default(false);
            $table->timestamp('synchronization_time')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('devices');
    }
};
