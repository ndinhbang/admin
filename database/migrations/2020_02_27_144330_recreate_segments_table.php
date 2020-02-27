<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecreateSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('segments');
        Schema::dropIfExists('segments_criteria');
        Schema::dropIfExists('segments_accounts');
        Schema::create('segments', function (Blueprint $table) {
            $table->increments('id');
            $table->char('uuid',21)->unique();
            $table->unsignedInteger('place_id')->nullable()->index();
            $table->string('name');
            $table->string('desc')->default('');
            $table->json('customers');
            $table->json('consitions');
            $table->timestamps();
        });

        Schema::create('account_segment', function (Blueprint $table) {
            $table->unsignedInteger('account_id');
            $table->unsignedInteger('segment_id');
            $table->primary(['account_id', 'segment_id']);
            $table->index(['segment_id', 'account_id']);
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
        Schema::dropIfExists('segments');
        Schema::dropIfExists('account_segment');
    }
}
