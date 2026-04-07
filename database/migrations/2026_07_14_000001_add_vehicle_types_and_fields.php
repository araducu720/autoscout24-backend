<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $isMysql = DB::connection()->getDriverName() === 'mysql';

        if ($isMysql) {
            // Expand vehicle_makes type enum to include van, atv, trailer, construction, agricultural, forklift, bus
            DB::statement("ALTER TABLE vehicle_makes MODIFY COLUMN `type` ENUM('car','motorcycle','truck','caravan','van','atv','trailer','construction','agricultural','forklift','bus') NOT NULL DEFAULT 'car'");

            // Expand vehicles body_type enum with more types
            DB::statement("ALTER TABLE vehicles MODIFY COLUMN `body_type` ENUM('sedan','suv','coupe','hatchback','wagon','convertible','van','pickup','truck','motorcycle','cruiser','motorhome','caravan','minivan','compact','roadster','limousine','panel_van','box_van','chassis_cab','tipper','flatbed','tractor_unit','reefer','tanker','curtainsider','hook_loader','skip_loader','concrete_mixer','crane_truck','car_transporter','garbage_truck','fire_truck','ambulance','naked','enduro','chopper','scooter','sport','touring_bike','quad','buggy','snowmobile','other') NULL DEFAULT NULL");

            // Expand fuel_type enum
            DB::statement("ALTER TABLE vehicles MODIFY COLUMN `fuel_type` ENUM('petrol','diesel','electric','hybrid','lpg','cng','hydrogen','ethanol','other') NOT NULL DEFAULT 'petrol'");

            // Expand transmission enum
            DB::statement("ALTER TABLE vehicles MODIFY COLUMN `transmission` ENUM('manual','automatic','semi-automatic') NOT NULL DEFAULT 'manual'");

            // Expand condition enum
            DB::statement("ALTER TABLE vehicles MODIFY COLUMN `condition` ENUM('new','used','certified','damaged') NOT NULL DEFAULT 'used'");
        }

        // Add missing fields
        Schema::table('vehicles', function (Blueprint $table) {
            $table->enum('drive_type', ['fwd', 'rwd', 'awd', '4wd'])->nullable()->after('transmission');
            $table->enum('emission_class', ['euro1', 'euro2', 'euro3', 'euro4', 'euro5', 'euro6', 'euro6d'])->nullable()->after('drive_type');
            $table->integer('co2_emissions')->nullable()->after('emission_class')->comment('g/km');
            $table->decimal('fuel_consumption', 5, 1)->nullable()->after('co2_emissions')->comment('l/100km or kWh/100km');
            $table->integer('weight')->nullable()->after('fuel_consumption')->comment('kg - GVW for trucks');
            $table->integer('payload')->nullable()->after('weight')->comment('kg');
            $table->string('axle_configuration', 10)->nullable()->after('payload')->comment('e.g. 4x2, 6x4');
            $table->integer('previous_owners')->nullable()->after('axle_configuration');
            $table->boolean('accident_free')->nullable()->after('previous_owners');
            $table->date('inspection_valid_until')->nullable()->after('accident_free');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'drive_type', 'emission_class', 'co2_emissions', 'fuel_consumption',
                'weight', 'payload', 'axle_configuration', 'previous_owners',
                'accident_free', 'inspection_valid_until'
            ]);
        });

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE vehicle_makes MODIFY COLUMN `type` ENUM('car','motorcycle','truck','caravan') NOT NULL DEFAULT 'car'");
            DB::statement("ALTER TABLE vehicles MODIFY COLUMN `body_type` ENUM('sedan','suv','coupe','hatchback','wagon','convertible','van','pickup','truck','motorcycle','cruiser','motorhome','caravan') NULL DEFAULT NULL");
            DB::statement("ALTER TABLE vehicles MODIFY COLUMN `fuel_type` ENUM('petrol','diesel','electric','hybrid','lpg') NOT NULL DEFAULT 'petrol'");
            DB::statement("ALTER TABLE vehicles MODIFY COLUMN `transmission` ENUM('manual','automatic') NOT NULL DEFAULT 'manual'");
            DB::statement("ALTER TABLE vehicles MODIFY COLUMN `condition` ENUM('new','used') NOT NULL DEFAULT 'used'");
        }
    }
};
