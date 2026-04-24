<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToCreatorProfiles extends Migration
{
    public function up()
    {
        Schema::table('creator_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('creator_profiles', 'website_url')) {
                $table->string('website_url', 255)->nullable()->after('profile_photo');
            }
            if (!Schema::hasColumn('creator_profiles', 'contact_email')) {
                $table->string('contact_email', 150)->nullable()->after('website_url');
            }
            if (!Schema::hasColumn('creator_profiles', 'social_links')) {
                $table->json('social_links')->nullable()->after('contact_email');
            }
            if (!Schema::hasColumn('creator_profiles', 'profile_photo')) {
                $table->string('profile_photo', 255)->nullable()->after('bio');
            }
        });
    }

    public function down()
    {
        Schema::table('creator_profiles', function (Blueprint $table) {
            foreach (['website_url', 'contact_email', 'social_links', 'profile_photo'] as $col) {
                if (Schema::hasColumn('creator_profiles', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
}
