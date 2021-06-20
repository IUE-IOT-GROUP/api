<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('fogs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');
            $table->foreignUuid('user_id')->constrained('users');
            $table->foreignUuid('place_id')->constrained('places');
            $table->macAddress('mac_address');
            $table->ipAddress('ip_address');
            $table->integer('port')->nullable()->default(80);

            $table->timestamps();
            $table->softDeletes();

            $table->boolean('is_synchronized')->default(false);
            $table->timestamp('synchronization_time')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fogs');
    }
};
