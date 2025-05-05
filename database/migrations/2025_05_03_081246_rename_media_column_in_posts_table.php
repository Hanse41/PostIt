<?php
// filepath: c:\Users\Support\Desktop\laravelApps\PostIt\database\migrations\<timestamp>_rename_media_column_in_posts_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->renameColumn('media', 'media_data');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->renameColumn('media_data', 'media');
        });
    }
};
