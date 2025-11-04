<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaxpagesizeToSapCdcRegistryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sap_cdc_registry', function (Blueprint $table) {
            // Add maxpagesize column
            $table->integer('maxpagesize')->default(100000)->after('id'); // Adjust 'after' position as needed
            
            // Add entity_name column
        });

        // Add column comments (PostgreSQL)
        DB::statement("COMMENT ON COLUMN sap_cdc_registry.maxpagesize IS 'Max page size for OData requests (odata.maxpagesize header)'");
        DB::statement("COMMENT ON COLUMN sap_cdc_registry.entity_name IS 'Entity name for constructing DeltaLinksOf endpoint (e.g., ZCDC_AFIH_1)'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sap_cdc_registry', function (Blueprint $table) {
            $table->dropColumn(['maxpagesize']);
        });
    }
}
