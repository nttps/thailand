<?php

use Nttps\Countries\Models\Country;
use Nttps\Thailand\Models\Geography;
use Nttps\Thailand\Models\Province;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\File;

class CreateDimProvincesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create((new Province())->getTable(), function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
            $table->unsignedTinyInteger('code');
            $table->unsignedTinyInteger('country_id');
            $table->unsignedTinyInteger('geography_id');
            $table->string('name_english')->nullable();
            $table->string('name_thai');
            $table->audits();
            // $table->moderations();
            // $table->owner();
            $table->softDeletes();

            // Foreign Key Constraints
            $table->foreign('country_id')
                ->references('id')
                ->on((new Country())->getTable())
                ->onUpdate('cascade');

            $table->foreign('geography_id')
                ->references('id')
                ->on((new Geography())->getTable())
                ->onUpdate('cascade');
        });

        // Data
        $countryId = Country::where('cca3', 'LIKE', 'THA')->select('id')->first()->id ?? 220;
        collect(json_decode(File::get(__DIR__ . '/../jsons/provinces.json')))
            ->map(function ($obj) use ($countryId) {
                return [
                    'id'            => (int) $obj->PROVINCE_ID,
                    'code'          => $obj->PROVINCE_CODE,
                    'name_thai'     => $obj->PROVINCE_NAME,
                    'country_id'    => $countryId,
                    'geography_id'  => $obj->GEO_ID,
                ];
            })->chunk(400)->each(function ($chunk) {
                // SQL Server supports a maximum of 2100 parameters
                Province::insert($chunk->toArray());
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists((new Province())->getTable());
    }
}
