<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');            
            $table->string('subject');
            $table->string('body');
            $table->timestamps();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
        });

        // 创建一个多对多的模型
        Schema::create('post_tag', function (Blueprint $table) {
            $table->unsignedInteger('tag_id');
            $table->unsignedInteger('post_id');
            $table->index('tag_id');
            $table->index('post_id');
            $table->foreign('tag_id', 'tag_post_foreign')
                ->references('id')
                ->on('tags')
                ->onDelete('cascade');
            $table->foreign('post_id', 'post_tag_foreign')
                ->references('id')
                ->on('posts')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('post_tag', function (Blueprint $table) {
            $table->dropForeign('tag_post_foreign');
            $table->dropForeign('post_tag_foreign');
        });

        Schema::dropIfExists('post_tag');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('posts');
    }
}
