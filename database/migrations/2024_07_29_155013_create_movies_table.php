<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name', 255)->comment("Movie's title");
            $table->year('release_year')->comment("Release Year of the movie");
            $table->text('cover')->default(config('app.url') . "/assets/images/no_image_available.png")->comment("File that represents the movie's cover");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::dropIfExists('movies');
    }
};
