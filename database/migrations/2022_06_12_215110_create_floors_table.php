<?php

use App\Models\User;
use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_floors', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'owner_user_id');
            $table->foreignIdFor(Post::class, 'post_id');
            $table->text('content');
            $table->integer('floorNum');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_floors');
    }
};
