<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('evaluation_rating_scales', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('value')->unique();
            $table->string('label');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();
        $scales = [
            ['value' => 1, 'label' => 'Poor', 'description' => 'Rarely meets expectations.', 'sort_order' => 1],
            ['value' => 2, 'label' => 'Fair', 'description' => 'Sometimes meets expectations but needs improvement.', 'sort_order' => 2],
            ['value' => 3, 'label' => 'Satisfactory', 'description' => 'Meets expectations at an acceptable level.', 'sort_order' => 3],
            ['value' => 4, 'label' => 'Very Good', 'description' => 'Often exceeds expectations.', 'sort_order' => 4],
            ['value' => 5, 'label' => 'Excellent', 'description' => 'Consistently exceeds expectations.', 'sort_order' => 5],
        ];

        foreach ($scales as $scale) {
            DB::table('evaluation_rating_scales')->insert($scale + [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down()
    {
        Schema::dropIfExists('evaluation_rating_scales');
    }
};
