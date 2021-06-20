<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('places', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->foreignUuid('user_id')->nullable()->constrained('users');
            $table->uuid('parent_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->boolean('is_synchronized')->default(false);
            $table->timestamp('synchronization_time')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('places');
    }
};
