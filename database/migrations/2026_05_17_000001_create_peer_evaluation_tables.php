<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('academic_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('project_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_block_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('number');
            $table->timestamps();

            $table->unique(['academic_block_id', 'number']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('student_id')->nullable()->unique()->after('id');
            $table->foreignId('academic_block_id')->nullable()->after('role')->constrained()->nullOnDelete();
            $table->foreignId('project_group_id')->nullable()->after('academic_block_id')->constrained()->nullOnDelete();
        });

        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluator_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('evaluatee_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('participation_score');
            $table->unsignedTinyInteger('quality_score');
            $table->unsignedTinyInteger('collaboration_score');
            $table->unsignedTinyInteger('communication_score');
            $table->unsignedTinyInteger('reliability_score');
            $table->text('comments')->nullable();
            $table->timestamps();

            $table->unique(['evaluator_id', 'evaluatee_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluations');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['project_group_id']);
            $table->dropForeign(['academic_block_id']);
            $table->dropUnique(['student_id']);
            $table->dropColumn(['student_id', 'academic_block_id', 'project_group_id']);
        });

        Schema::dropIfExists('project_groups');
        Schema::dropIfExists('academic_blocks');
    }
};
