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
            $table->index('post_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('community_likes');
    }
}
