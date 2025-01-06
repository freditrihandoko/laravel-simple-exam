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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('exam_duration');
            $table->datetime('exam_start');
            $table->datetime('exam_end');
            $table->boolean('show_score')->default(false); //tambahan
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('shuffle_answers')->default(false);
            $table->timestamps();
        });

        Schema::create('exam_groups', function (Blueprint $table) {
            $table->foreignId('exam_id')->constrained('exams');
            $table->foreignId('group_id')->constrained('groups');
            $table->primary(['exam_id', 'group_id']);
        });

        Schema::create('exam_topic', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams');
            $table->foreignId('topic_id')->constrained('topics');
            $table->integer('num_questions');
            $table->timestamps();
        });

        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams');
            $table->foreignId('question_id')->constrained('questions');
            $table->foreignId('topic_id')->constrained('topics');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_questions');
        Schema::dropIfExists('exam_topic');
        Schema::dropIfExists('exam_groups');
        Schema::dropIfExists('exams');
    }
};
