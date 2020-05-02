<?php

use Illuminate\Routing\Router;
use \App\Admin\Controllers\PaperController;

Admin::routes();

// 公共
Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'Common\HomeController@index');
    $router->get('/test', 'Common\HomeController@test');

    // 套卷管理
    $router->any('paper', 'Teacher\PaperController@index');
    $router->any('paper/create', 'Teacher\PaperController@create');
    $router->any('paper/{id}', 'Teacher\PaperController@detail');
    $router->any('paper/{id}/edit', 'Teacher\PaperController@edit');
    $router->get('paper/analysis/{papaer_id}', 'Teacher\AnalysisController@show');  // 获取考卷分析api
    // 选择题管理
    $router->any('question/selection', 'Teacher\SelectionController@index');
    $router->any('question/selection/create', 'Teacher\SelectionController@create');
    $router->any('question/selection/{id}', 'Teacher\SelectionController@detail');
    $router->any('question/selection/{id}/edit', 'Teacher\SelectionController@edit');
    // 判断题管理
    $router->any('question/judgement', 'Teacher\JudgementController@index');
    $router->any('question/judgement/create', 'Teacher\JudgementController@create');
    $router->any('question/judgement/{id}', 'Teacher\JudgementController@detail');
    $router->any('question/judgement/{id}/edit', 'Teacher\JudgementController@edit');
    // 主观题管理
    $router->any('question/subjective', 'Teacher\SubjectiveController@index');
    $router->any('question/subjective/create', 'Teacher\SubjectiveController@create');
    $router->any('question/subjective/{id}', 'Teacher\SubjectiveController@detail');
    $router->any('question/subjective/{id}/edit', 'Teacher\SubjectiveController@edit');

    // 主观题答案管理
    $router->any('answer/subjective', 'Teacher\SubjectiveAnswerController@index');
    $router->any('answer/subjective/create', 'Teacher\SubjectiveAnswerController@create');
    $router->any('answer/subjective/{id}', 'Teacher\SubjectiveAnswerController@detail');
    $router->any('answer/subjective/{id}/edit', 'Teacher\SubjectiveAnswerController@edit');

    // 学生答案管理
    $router->any('answer', 'Teacher\AnswerController@index');
    $router->any('answer/create', 'Teacher\AnswerController@create');
    $router->any('answer/{id}', 'Teacher\AnswerController@detail');
    $router->any('answer/{id}/edit', 'Teacher\AnswerController@edit');

    // 反馈管理
    $router->any('auth/feedback', 'Admin\FeedbackController@index');
    $router->any('auth/feedback/{id}', 'Admin\FeedbackController@detail');

    // 我的反馈
    $router->any('feedback', 'Common\MyFeedbackController@index');
    $router->any('feedback/create', 'Common\MyFeedbackController@create');
    $router->any('feedback/{id}', 'Common\MyFeedbackController@detail');

    // 我的试卷
    $router->any('student/paper', 'Student\PaperController@index');
    $router->any('student/paper/{id}', 'Student\PaperController@analysis');
});

