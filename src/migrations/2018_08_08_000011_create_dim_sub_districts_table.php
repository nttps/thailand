<?php

use Nttps\Countries\Models\Country;
use Nttps\Thailand\Models\District;
// use Nttps\Thailand\Models\District;
use Nttps\Thailand\Models\Geography;
use Nttps\Thailand\Models\Province;
// use Nttps\Thailand\Models\Province;
use Nttps\Thailand\Models\SubDistrict;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\File;

class CreateDimsubDistrictsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create((new SubDistrict())->getTable(), function (Blueprint $table) {
            $table->unsignedSmallInteger('id')->primary();
            $table->unsignedInteger('code');
            $table->unsignedSmallInteger('district_id');
            $table->unsignedTinyInteger('geography_id');
            $table->string('name_english')->nullable();
            $table->string('name_thai');
            $table->unsignedTinyInteger('province_id');
            $table->audits();
            // $table->moderations();
            // $table->owner();
            $table->softDeletes();

            // Foreign Key Constraints

            $table->foreign('geography_id')
                ->references('id')
                ->on((new Geography())->getTable())
                ->onUpdate('cascade');

            // ไม่มี 97
            // $table->foreign('province_id')
            //     ->references('id')
            //     ->on((new Province())->getTable())
            //     ->onUpdate('cascade');

            // ไม่มี 468
            // $table->foreign('district_id')
            //     ->references('id')
            //     ->on((new District())->getTable())
            //     ->onUpdate('cascade');
        });

        // Data
        collect(json_decode(File::get(__DIR__ . '/../jsons/subDistricts.json')))
            ->map(function ($obj) {
                return [
                    'id'            => (int) $obj->SUB_DISTRICT_ID,
                    'code'          => $obj->SUB_DISTRICT_CODE,
                    'district_id'   => $obj->DISTRICT_ID,
                    'geography_id'  => $obj->GEO_ID,
                    'name_thai'     => $obj->SUB_DISTRICT_NAME,
                    'province_id'   => $obj->PROVINCE_ID,
                ];
            })->chunk(290)->each(function ($chunk) {
                // SQL Server supports a maximum of 2100 parameters
                SubDistrict::insert($chunk->toArray());
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists((new SubDistrict())->getTable());
    }
}
