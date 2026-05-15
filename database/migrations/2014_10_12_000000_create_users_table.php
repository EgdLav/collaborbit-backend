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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('role')->default('user');
            $table->string('avatar')->default('avatars/default.svg');
            $table->enum('department', ['Backend Development', 'Frontend Development', 'Engineering', 'Mobile Development', 'DevOps', 'Quality Assurance', 'Data Engineering', 'Data Science', 'Product Management', 'UI/UX Design', 'Graphic Design', 'Research & Analytics', 'Marketing', 'Sales', 'Business Development', 'Human Resources', 'Finance', 'Legal', 'Operations', 'Public Relations', 'Copywriting' ]);
            $table->text('bio')->nullable();
            $table->string('email')->unique();
            $table->string('email_token')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
