<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommunityCommentsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('community_comments')) {
            return;
        }

        Schema::create('community_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('post_id');
            $table->unsignedInteger('user_id');
            $table->text('body');
            $table->timestamp('created_at')->useCurrent();

            $table->index('post_id');
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('community_comments');
    }
}
