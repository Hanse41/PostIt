<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->json('tags')->nullable()->after('caption'); // Add tags column
            $table->dropColumn('location'); // Remove location column
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('location')->nullable();
            $table->dropColumn('tags');
        });
    }
};
