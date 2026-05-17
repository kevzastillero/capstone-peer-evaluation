<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('evaluation_questions', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('evaluation_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('evaluation_question_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('score');
            $table->timestamps();

            $table->unique(['evaluation_id', 'evaluation_question_id'], 'evaluation_answer_unique');
        });

        $this->setLegacyScoreColumnsNullable();

        $now = now();
        $defaults = [
            ['question' => 'Participation', 'description' => 'Shows consistent involvement in group tasks and meetings.', 'sort_order' => 1],
            ['question' => 'Quality of contribution', 'description' => 'Contributes useful work that supports the capstone project goals.', 'sort_order' => 2],
            ['question' => 'Collaboration', 'description' => 'Works well with team members and supports shared responsibilities.', 'sort_order' => 3],
            ['question' => 'Communication', 'description' => 'Communicates updates, concerns, and progress clearly.', 'sort_order' => 4],
            ['question' => 'Reliability', 'description' => 'Completes assigned work on time and follows through on commitments.', 'sort_order' => 5],
        ];

        foreach ($defaults as $default) {
            DB::table('evaluation_questions')->insert($default + [
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $questionIds = DB::table('evaluation_questions')->orderBy('sort_order')->pluck('id')->values();
        $columns = [
            'participation_score',
            'quality_score',
            'collaboration_score',
            'communication_score',
            'reliability_score',
        ];

        DB::table('evaluations')->orderBy('id')->chunk(100, function ($evaluations) use ($questionIds, $columns, $now) {
            foreach ($evaluations as $evaluation) {
                foreach ($columns as $index => $column) {
                    if (!isset($questionIds[$index]) || $evaluation->{$column} === null) {
                        continue;
                    }

                    DB::table('evaluation_answers')->insertOrIgnore([
                        'evaluation_id' => $evaluation->id,
                        'evaluation_question_id' => $questionIds[$index],
                        'score' => $evaluation->{$column},
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluation_answers');
        Schema::dropIfExists('evaluation_questions');

        $this->setLegacyScoreColumnsRequired();
    }

    private function setLegacyScoreColumnsNullable()
    {
        foreach ($this->legacyScoreColumns() as $column) {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement("ALTER TABLE evaluations ALTER COLUMN {$column} DROP NOT NULL");
                continue;
            }

            DB::statement("ALTER TABLE evaluations MODIFY {$column} TINYINT UNSIGNED NULL");
        }
    }

    private function setLegacyScoreColumnsRequired()
    {
        foreach ($this->legacyScoreColumns() as $column) {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement("ALTER TABLE evaluations ALTER COLUMN {$column} SET NOT NULL");
                continue;
            }

            DB::statement("ALTER TABLE evaluations MODIFY {$column} TINYINT UNSIGNED NOT NULL");
        }
    }

    private function legacyScoreColumns()
    {
        return [
            'participation_score',
            'quality_score',
            'collaboration_score',
            'communication_score',
            'reliability_score',
        ];
    }
};
