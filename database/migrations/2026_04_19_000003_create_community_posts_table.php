<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommunityPostsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('community_posts')) {
            return;
        }

        Schema::create('community_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('title', 255);
            $table->text('body');
            $table->enum('status', ['published', 'draft', 'removed'])->default('published');
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('community_posts');
    }
}
