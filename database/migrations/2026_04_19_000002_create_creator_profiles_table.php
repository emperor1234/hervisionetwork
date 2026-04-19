<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreatorProfilesTable extends Migration
{
    public function up()
    {
        Schema::create('creator_profiles', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->unsignedBigInteger('user_id');
            $table->string('display_name', 150)->default('');
            $table->text('bio')->nullable();
            $table->string('profile_photo', 255)->nullable();
            $table->string('website_url', 255)->nullable();
            $table->string('contact_email', 150)->nullable();
            $table->json('social_links')->nullable();
            $table->timestamps();

            $table->unique('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('creator_profiles');
    }
}
