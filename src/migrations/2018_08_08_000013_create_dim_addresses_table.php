<?php

use Nttps\Countries\Models\Country;
use Nttps\Laravel\Models\TelephoneNumber;
use Nttps\Thailand\Models\Address;
use Nttps\Thailand\Models\District;
use Nttps\Thailand\Models\Geography;
use Nttps\Thailand\Models\Province;
use Nttps\Thailand\Models\SubDistrict;
use Nttps\Thailand\Models\ZipCode;
use App\Models\Company;
use App\Models\User;
use Hootlex\Moderation\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDimAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create((new Address())->getTable(), function (Blueprint $table) {
            $table->uuid('id')->primary();
            // uuidMorphs(): 1071 Specified key was too long; max key length is 1000 bytes
            // $table->nullableUuidMorphs('addressable');
            $table->uuid('addressable_id')->nullable();
            $table->string('addressable_type')->nullable();
            $table->string('building')->nullable();
            $table->unsignedTinyInteger('country_id')->comment('FK @ dim_countries.id');
            $table->unsignedSmallInteger('district_id')->nullable()->comment('FK @ dim_districts.id');
            $table->string('floor')->nullable();
            $table->unsignedTinyInteger('geography_id')->nullable()->comment('FK @ dim_geographies.id');
            $table->decimal('latitude', 8, 6)->nullable();
            $table->text('line_1')->nullable()->comment('คำนวนจาก building, floor, ... เองที่ application หรือ view, ไม่เก็บ duplicate data');
            $table->string('line_2')->nullable();
            $table->decimal('longitude', 9, 6)->nullable();
            $table->string('moo')->nullable();
            $table->string('name')->nullable();
            $table->string('number')->nullable();
            $table->unsignedTinyInteger('province_id')->nullable()->comment('FK @ dim_provinces.id');
            $table->string('room')->nullable();
            $table->string('soi')->nullable();
            $table->string('street')->nullable();
            $table->unsignedSmallInteger('sub_district_id')->nullable()->comment('FK @ dim_sub_districts.id');
            $table->uuid('telephone_number_id')->nullable()->comment('FK @ dim_telephone_numbers.id');
            $table->unsignedSmallInteger('zip_code_id')->comment('FK @ dim_zip_codes.id');
            $table->audits();
            $table->moderations();
            $table->timestamps();
            $table->softDeletes();

            // Foreign Key Constraints
            $table->foreign('country_id')
                ->references('id')
                ->on((new Country())->getTable())
                ->onUpdate('cascade');

            $table->foreign('district_id')
                ->references('id')
                ->on((new District())->getTable())
                ->onUpdate('cascade');

            $table->foreign('geography_id')
                ->references('id')
                ->on((new Geography())->getTable())
                ->onUpdate('cascade');

            $table->foreign('province_id')
                ->references('id')
                ->on((new Province())->getTable())
                ->onUpdate('cascade');

            $table->foreign('sub_district_id')
                ->references('id')
                ->on((new SubDistrict())->getTable())
                ->onUpdate('cascade');

            $table->foreign('telephone_number_id')
                ->references('id')
                ->on((new TelephoneNumber())->getTable())
                ->onUpdate('cascade');

            $table->foreign('zip_code_id')
                ->references('id')
                ->on((new ZipCode())->getTable())
                ->onUpdate('cascade');
        });

        // Demo Account
        $sreephuvanart = Address::create([
            'id'                    => '34343434-3434-3434-3434-343434343434',
            'addressable_id'        => '99999999-9999-9999-9999-999999999999',
            'addressable_type'      => Company::class,
            'building'              => null,
            'country_id'            => 220,
            'district_id'           => 928,
            'floor'                 => null,
            'geography_id'          => 6,
            'latitude'              => 6.998158944698838,
            'longitude'             => 100.47428873391088,
            'moderated_at'          => (string) now(),
            'moderator_id'          => '22222222-2222-2222-2222-222222222222',
            'moderation_status'     => Status::APPROVED,
            'moo'                   => null,
            'number'                => '39/1',
            'province_id'           => 70,
            'room'                  => null,
            'soi'                   => null,
            'street'                => 'ศรีภูวนารถ',
            'sub_district_id'       => 8289,
            'zip_code_id'           => 6978
        ]);
        $chamchuriSquare = Address::create([
            'id'                    => '45454545-4545-4545-4545-454545454545',
            'addressable_id'        => '11111111-1111-1111-1111-111111111111',
            'addressable_type'      => User::class,
            'building'              => 'จตุรัสจามจุรี',
            'country_id'            => 220,
            'deleted_at'            => (string) now(),
            'district_id'           => 7,
            'floor'                 => '24',
            'geography_id'          => 2,
            'latitude'              => 13.733038711623514,
            'longitude'             => 100.53058022599716,
            'moderated_at'          => (string) now(),
            'moderator_id'          => '22222222-2222-2222-2222-222222222222',
            'moderation_status'     => Status::REJECTED,
            'moo'                   => null,
            'number'                => '319',
            'province_id'           => 1,
            'room'                  => '111',
            'soi'                   => null,
            'street'                => 'พญาไท',
            'sub_district_id'       => 53,
            'zip_code_id'           => 37
        ]);
        $chamchuriSquare->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists((new Address())->getTable());
    }
}
