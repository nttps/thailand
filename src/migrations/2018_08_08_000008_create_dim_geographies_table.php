<?php

use Nttps\Countries\Models\Country;
use Nttps\Thailand\Models\Geography;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDimGeographiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create((new Geography())->getTable(), function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
            $table->string('name_english')->nullable();
            $table->string('name_thai');
            $table->unsignedTinyInteger('country_id');
            $table->audits();
            // $table->moderations();
            // $table->owner();
            $table->softDeletes();

            // Foreign Key Constraints
            $table
                ->foreign('country_id')
                ->references('id')
                ->on((new Country())->getTable())
                ->onUpdate('cascade');
        });

        // Data
        $countryId = Country::where('cca3', 'LIKE', 'THA')->select('id')->first()->id ?? 220;
        collect(json_decode(File::get(__DIR__ . '/../jsons/geography.json')))
            ->map(function ($obj) use ($countryId) {
                return [
                    'id'            => (int) $obj->GEO_ID,
                    'country_id'    => $countryId,
                    'name_thai'     => $obj->GEO_NAME,
                ];
            })->chunk(600)->each(function ($chunk) {
                // SQL Server supports a maximum of 2100 parameters
                Geography::insert($chunk->toArray());
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists((new Geography())->getTable());
    }
}
