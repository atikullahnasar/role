<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('beft_permission_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permission_id')->constrained('beft_permissions')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('beft_roles')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beft_permission_role');
    }
};
