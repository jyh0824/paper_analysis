<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePapaerTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 试卷表
        Schema::create('paper', function (Blueprint $table) {
            $table->increments('id');
            $table->string('classname', 255)->comment('课程名称');
            $table->string('year', 11)->comment('年份');
            $table->unsignedInteger('uid')->comment('操作人id');
            $table->timestamps();
        });
        // 题目表
        Schema::create('question', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('paper_id')->comment('试卷id');
            $table->unsignedInteger('type')->comment('题型id');
            $table->text('title')->comment('题目');
            $table->string('option1')->comment('A');
            $table->string('option2')->comment('B');
            $table->string('option3')->comment('C');
            $table->string('option4')->comment('D');
            $table->text('answer')->comment('答案');
            $table->text('analysis')->nullable()->comment('解析');
            $table->string('score',11)->comment('分值');
            $table->string('sort')->comment('排序值');
            $table->integer('is_auto')->comment('是否开启自动评分 1-开启，2-关闭');
            $table->integer('model')->comment('1-关键词模式，2-相似度模式');
            $table->timestamps();
        });
        // 题型表
        Schema::create('question_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('名称');
            $table->timestamps();
        });
        // 主观题答案表
        Schema::create('subjective_answer', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->comment('学号');
            $table->unsignedInteger('paper_id')->comment('试卷id');
            $table->unsignedInteger('score_id')->comment('成绩id');
            $table->string('sort')->comment('题目序号');
            $table->text('answer')->comment('答案');
            $table->string('score')->comment('得分');
            $table->string('auto_score')->comment('自动评分得分');
            $table->string('remark', 255)->comment('备注');
            $table->timestamps();
        });
        // 学生成绩表
        Schema::create('student_score', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->comment('学号');
            $table->unsignedInteger('paper_id')->comment('试卷id');
            $table->string('score')->comment('成绩');
            $table->string('remark', 255)->comment('备注');
            $table->string('selection_answer', 255)->comment('选择题答案');
            $table->string('selection_score')->comment('选择题分数');
            $table->string('judgement_answer', 255)->comment('判断题答案');
            $table->string('judgement_score')->comment('判断题分数');
            $table->string('subjective_score')->comment('主观题分数');
            $table->timestamps();
        });
        // 用户反馈表
        Schema::create('feedback', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('uid')->comment('用户id');
            $table->string('content', 255)->comment('内容');
            $table->string('remark', 255)->comment('反馈回复');
            $table->integer('status')->comment('状态');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('papaer');
        Schema::dropIfExists('question');
        Schema::dropIfExists('question_type');
        Schema::dropIfExists('subjective_answer');
        Schema::dropIfExists('student_score');
        Schema::dropIfExists('feedback');
    }
}
