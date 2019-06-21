<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalmonResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fail_reasons', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->statInkKey('key');
        });

        $fail_reasons = [
            'annihilated',
            'time_up',
        ];

        foreach ($fail_reasons as $key) {
            DB::table('fail_reasons')->insert(['key' => $key]);
        }

        Schema::create('salmon_results', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('schedule_id');
            $table->dateTime('start_at');
            $table->json('members'); // array of pids
            $table->unsignedBigInteger('uploader_user_id');
            $table->unsignedTinyInteger('clear_waves');
            $table->unsignedTinyInteger('fail_reason_id')->nullable();
            $table->decimal('danger_rate', 4, 1);
            $table->timestamps();

            $table->foreign('schedule_id')->references('schedule_id')->on('salmon_schedules');
            $table->foreign('uploader_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fail_reasons');
        Schema::dropIfExists('salmon_results');
    }
}