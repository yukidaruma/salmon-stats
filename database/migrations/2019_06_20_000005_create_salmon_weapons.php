<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use GuzzleHttp\Client;
use function GuzzleHttp\json_decode;
use Mockery\Exception\RuntimeException;

class CreateSalmonWeapons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            Schema::create('salmon_weapons', function (Blueprint $table) {
                // Use negative integer for Grizzco weapons and random weapons
                $table->unsignedSmallInteger('id');
                $table->statInkKey('key', 32);

                $table->primary('id');
            });

            $client = new Client();
            $result = $client->get('https://stat.ink/api/v2/weapon');
            if ($result->getStatusCode() == 200) {
                $weapons = json_decode($result->getBody());
            } else {
                throw new RuntimeException("Stat.ink API is unavailable.");
            }

            // Filter weapon variants (e.g. Tentatek Splattershot to Splattershot)
            $weapons = array_filter($weapons, function ($weapon) {
                return $weapon->{'main_ref'} === $weapon->{'key'};
            });

            array_push(
                $weapons,
                (object)['key' => 'kuma_blaster', 'splatnet' => 20000],
                (object)['key' => 'kuma_brella',  'splatnet' => 20010],
                (object)['key' => 'kuma_charger', 'splatnet' => 20020],
                (object)['key' => 'kuma_slosher', 'splatnet' => 20030],
            );

            foreach ($weapons as $weapon) {
                DB::table('salmon_weapons')->insert([
                    'id' => $weapon->{'splatnet'},
                    'key' => $weapon->{'key'},
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salmon_weapons');
    }
}
