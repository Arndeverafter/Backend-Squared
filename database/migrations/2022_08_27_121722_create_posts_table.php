<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() :void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string("title")->fulltext()->unique(); // We are making the title column unique : I believe it's a nice conversion for any blog reader with a sprinkle of fulltext for future improvements : Discard title that are not unique
            $table->foreignId("author");
            $table->text("description");
            $table->timestamp("publication_date");
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() :void
    {
        Schema::dropIfExists('posts');
    }
}
