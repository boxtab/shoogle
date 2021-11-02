<?php

use App\Helpers\HelperMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CompaniesdDropUnique extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {

            if ( HelperMigration::hasUniqueKeyInTable('companies', 'companies_name_unique') ) {
                $table->dropUnique(['name']);
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
        Schema::table('companies', function (Blueprint $table) {

            if ( ! HelperMigration::hasUniqueKeyInTable('companies', 'companies_name_unique') ) {
                $table->unique('name');
            }

        });
    }
}
