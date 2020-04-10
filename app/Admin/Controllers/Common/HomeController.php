<?php

namespace App\Admin\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Paper;
use App\Question;
use App\Subjective;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use http\Env\Request;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->title('首页'.Admin::user()->id)
            ->description(date("Y年m月d日"))
            ->row(Dashboard::title())

            ->row(function (Row $row) {

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::environment());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::extensions());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::dependencies());
                });
            })
            ;
    }

    public function test()
    {
//        if (!file_exists('./auto_score/answer_log/test'.'.txt')) {
//            $studentf = fopen('./auto_score/answer_log/test' . '.txt', 'w');
//            fwrite($studentf, 'test test');
//            echo 'write end';
//            fclose($studentf);
//        } else {
//            echo 'exist';
//        }
//        $sim = system('python ./auto_score/word2vec/page_sim.py test1.txt test2.txt');
//        echo $sim;
//        $questions = Question::where('paper_id', 9)->where('type', 3)->orderBy('sort')->pluck('sort');
//        var_dump($questions[1]);
//        $test = Question::where('paper_id', 11)->whereIn('type', [1,2])->orderBy('type')->orderBy('sort')->get()->toArray();
//        var_dump(range(0,5));
        system("python ./auto_score/word2vec/page_sim.py 1_2.txt 1_2_4601.txt");
    }

}
