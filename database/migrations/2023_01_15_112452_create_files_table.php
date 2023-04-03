<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string              ('name', 100);
            $table->text                ('description')->nullable();
            $table->string                ('type')->default('file');
            $table->boolean             ('is_pinned')->default(false);
            $table->unsignedBigInteger  ('size');
            $table->string              ('path', 255)->nullable();
            $table->string              ('mime_type')->nullable();

            $table->foreignId('parent_folder_id')
                  ->nullable()
                  ->constrained('folders')
                  ->onDelete('cascade');

            $table->boolean             ('is_shortcut')->default(false);
            $table->foreignId('original_id')
                  ->nullable()
                  ->constrained('files')
                  ->onDelete('cascade');


            $table->foreignId('owner_id')
                    ->constrained('users')
                    ->onDelete('cascade');

            $table->foreignId('space_id')
                    ->constrained('spaces')
                    ->onDelete('cascade');

            $table->timestamps();
        });

        // create trigger to add the size of the file to the size of the parent folder
        DB::getPdo()->exec("
                CREATE TRIGGER files_size_update
                AFTER INSERT ON files
                FOR EACH ROW
                BEGIN
                    UPDATE folders
                    SET size = size + NEW.size
                    WHERE id = NEW.parent_folder_id;
                END
            ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
};
