<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Create igniter_visitortracker_tracker table.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('extension_settings')
            ->where('item', 'igniter_onlinetracker_settings')
            ->update([
                'item' => 'igniterlabs_visitortracker_settings',
            ]);

        $this->createTrackerTable();

        $this->createGeoIpTable();
    }

    public function down(): void
    {
        Schema::dropIfExists('igniterlabs_visitortracker_tracker');
        Schema::dropIfExists('igniterlabs_visitortracker_geoip');
    }

    protected function createTrackerTable(): void
    {
        if (Schema::hasTable('igniter_onlinetracker_tracker')) {
            Schema::rename('igniter_onlinetracker_tracker', 'igniterlabs_visitortracker_tracker');

            return;
        }

        if (Schema::hasTable('customers_online')) {
            Schema::rename('customers_online', 'igniterlabs_visitortracker_tracker');

            Schema::table('igniterlabs_visitortracker_tracker', function(Blueprint $table): void {
                $table->bigIncrements('activity_id')->change();
                $table->string('session_id')->nullable();
                $table->integer('geoip_id')->nullable();
                $table->string('platform')->nullable();
                $table->text('headers')->nullable();
                $table->text('query')->nullable();
                $table->dropColumn('date_added');
                $table->timestamps();
            });

            return;
        }

        Schema::create('igniterlabs_visitortracker_tracker', function(Blueprint $table): void {
            $table->engine = 'InnoDB';
            $table->bigIncrements('activity_id');
            $table->integer('customer_id');
            $table->string('access_type')->nullable();
            $table->string('browser')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('country_code')->nullable();
            $table->text('request_uri')->nullable();
            $table->text('referrer_uri')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->text('user_agent')->nullable();
            $table->string('session_id')->nullable();
            $table->integer('geoip_id')->nullable();
            $table->string('platform')->nullable();
            $table->text('headers')->nullable();
            $table->text('query')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    protected function createGeoIpTable(): void
    {
        if (Schema::hasTable('igniter_onlinetracker_geoip')) {
            Schema::rename('igniter_onlinetracker_geoip', 'igniterlabs_visitortracker_geoip');

            return;
        }

        Schema::create('igniterlabs_visitortracker_geoip', function(Blueprint $table): void {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->double('latitude')->nullable()->index('visitortracker_geoip_latitude_index');
            $table->double('longitude')->nullable()->index('visitortracker_geoip_longitude_index');
            $table->string('region')->nullable();
            $table->string('city')->nullable()->index('visitortracker_geoip_city_index');
            $table->string('postal_code')->nullable();
            $table->string('country_iso_code_2')->nullable()->index('visitortracker_geoip_country_iso_code_2_index');
            $table->timestamps();
        });
    }
};
