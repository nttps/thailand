<?php

use Nttps\Countries\Models\Country;
use Nttps\Thailand\Models\District;
use Nttps\Thailand\Models\Geography;
use Nttps\Thailand\Models\Province;
// use Nttps\Thailand\Models\SubDistrict;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\File;

class CreateDimDistrictsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create((new District())->getTable(), function (Blueprint $table) {
            $table->unsignedSmallInteger('id')->primary();
            $table->unsignedSmallInteger('code');
            $table->unsignedTinyInteger('country_id');
            $table->unsignedTinyInteger('geography_id');
            $table->string('name_english')->nullable();
            $table->string('name_thai');
            $table->unsignedTinyInteger('province_id');
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

            $table->foreign('province_id')
                ->references('id')
                ->on((new Province())->getTable())
                ->onUpdate('cascade');
        });

        // Data
        $countryId = Country::where('cca3', 'LIKE', 'THA')->select('id')->first()->id ?? 220;
        collect(json_decode(File::get(__DIR__ . '/../jsons/districts.json')))
            ->map(function ($obj) use ($countryId) {
                return [
                    'id'            => (int) $obj->DISTRICT_ID,
                    'code'          => $obj->DISTRICT_CODE,
                    'country_id'    => $countryId,
                    'geography_id'  => $obj->GEO_ID,
                    'name_thai'     => $obj->DISTRICT_NAME,
                    'province_id'   => $obj->PROVINCE_ID,
                ];
            })->chunk(340)->each(function ($chunk) {
                // SQL Server supports a maximum of 2100 parameters
                District::insert($chunk->toArray());
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists((new District())->getTable());
    }
}
