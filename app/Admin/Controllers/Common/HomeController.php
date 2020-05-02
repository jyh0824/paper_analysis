<?php

namespace App\Admin\Controllers\Common;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use http\Env\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    // 角色id
    public $role = null;

    public function index(Content $content)
    {
        $content->title('首页');
        $content->description(date("Y年m月d日"));

        $id = Admin::user()->id;
        $role = $this->role($id);
        $this->role = $role;
        $content->row(Dashboard::title(Admin::user()->name, $role));
        $content->row(function (Row $row) {
            if ($this->role == 1) {
                // 管理员
                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::papers());
                });
                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::answers());
                });
                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::feedbacks());
                });
            } else if ($this->role == 3) {
                // 教师
                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::papers());
                });
                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::answers());
                });
                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::myfeedbacks());
                });
            } else if ($this->role == 4) {
                // 普通管理员
                $row->column(12, function (Column $column) {
                    $column->append(Dashboard::feedbacks());
                });
            } else {
                // 学生
                $row->column(6, function (Column $column) {
                    $column->append(Dashboard::mypapers());
                });
                $row->column(6, function (Column $column) {
                    $column->append(Dashboard::myfeedbacks());
                });
            }
        });
        return $content;
    }

    // 获取用户角色
    public function role($uid)
    {
        $role_id = DB::table('admin_role_users')->where('user_id', $uid)->value('role_id');
        return $role_id;
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
