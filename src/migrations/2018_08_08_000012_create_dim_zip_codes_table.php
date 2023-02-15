<?php

use Nttps\Countries\Models\Country;
use Nttps\Thailand\Models\District;
use Nttps\Thailand\Models\Geography;
use Nttps\Thailand\Models\Province;
use Nttps\Thailand\Models\SubDistrict;
use Nttps\Thailand\Models\ZipCode;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\File;

class CreateDimZipcodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create((new ZipCode())->getTable(), function (Blueprint $table) {
            $table->unsignedSmallInteger('id')->primary();
            $table->unsignedSmallInteger('district_id');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->unsignedTinyInteger('geography_id');
            $table->unsignedTinyInteger('province_id');
            $table->unsignedSmallInteger('sub_district_id');
            $table->unsignedInteger('zip_code');
            $table->softDeletes();

            // Foreign Key Constraints
            $table->foreign('geography_id')
                ->references('id')
                ->on((new Geography())->getTable())
                ->onUpdate('cascade');

            $table->foreign('sub_district_id')
                ->references('id')
                ->on((new SubDistrict())->getTable())
                ->onUpdate('cascade');
        });

        // Data
        $coordinates        = collect(json_decode(File::get(__DIR__ . '/../jsons/coordinates.json')))
                                ->groupBy('zipcode');
        // $districts          = District::select(['id'])->get()->keyBy('id'); // ,'name_thai'
        $provincesById      = Province::select(['id', 'geography_id'])->get()->keyBy('id'); // ,'name_thai'
        $subDistricts       = SubDistrict::select(['id', 'name_thai'])->get()->keyBy('id'); 

        collect(json_decode(File::get(__DIR__ . '/../jsons/zipcodes.json')))
            ->map(function ($obj) use (&$coordinates, &$provincesById, &$subDistricts) {
                // , &$districts) {
                $zipCode          = $obj->ZIPCODE;
                $coordinate       = $coordinates->get($zipCode);
                // $district         = $districts->get($obj->DISTRICT_ID)->name_thai;
                // $province         = $provincesById->get($obj->PROVINCE_ID)->name_thai;
                $subDistrict      = $subDistricts->get($obj->SUB_DISTRICT_ID)->name_thai;

                if ($coordinate && $coordinate->count() > 1) {
                    $coordinate = $coordinate->where('subdistrict', $subDistrict);
                    // if ($coordinate->count() > 1) {
                    //     $coordinate = $coordinate->where('district', $district);
                    //     // if ($coordinate->count() > 1) {
                    //     //     $coordinate = $coordinate->where('province', $province);
                    //     // }
                    // }
                }

                if ($coordinate && $coordinate = $coordinate->first()) {
                    $latitude = $coordinate->latitude;
                    $longitude = $coordinate->longitude;
                }

                return [
                    'id'                => (int) $obj->ZIPCODE_ID,
                    'district_id'       => $obj->DISTRICT_ID,
                    'geography_id'      => $provincesById->get($obj->PROVINCE_ID)->geography_id,
                    "latitude"          => $latitude ?? null,
                    "longitude"         => $longitude ?? null,
                    'province_id'       => $obj->PROVINCE_ID,
                    'sub_district_id'   => $obj->SUB_DISTRICT_ID,
                    'zip_code'          => $obj->ZIPCODE,
                ];
            })->chunk(1000)->each(function ($chunk) {
                // SQL Server supports a maximum of 2100 parameters
                ZipCode::insert($chunk->toArray());
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists((new ZipCode())->getTable());
    }
}
