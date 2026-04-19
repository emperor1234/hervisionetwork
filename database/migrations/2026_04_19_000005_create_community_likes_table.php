<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommunityLikesTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('community_likes')) {
            return;
        }

        Schema::create('community_likes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('post_id');
            $table->unsignedInteger('user_id');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['post_id', 'user_id']);
            $table->foreign('post_id')->references('id')->on('community_posts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('community_likes');
    }
}
