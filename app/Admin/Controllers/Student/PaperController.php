<?php

namespace App\Admin\Controllers\Student;

use App\Admin\Actions\Student\Analysis;
use App\Question;
use App\StudentPaper;
use App\StudentScore;
use App\SubjectiveAnswer;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Row;
use Encore\Admin\Layout\Column;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Tab;
use Illuminate\Support\Facades\DB;

class PaperController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\StudentPaper';

    // 题目集
    public $questions = null;

    // 试卷ID
    public $paper_id = null;

    // 得分率
    public $rate = null;

    // 题型得分
    public $content = null;

    // 学生成绩
    public $student_score = null;

    /*
     *  列表
     *
     *  @return Content
     */
    public function index(Content $content)
    {
        $content->header('我的试卷');
        $content->description(date("Y年m月d日"));
        $content->breadcrumb(
            ['text' => '我的试卷']
        );
        $content->body($this->grid());

        return $content;
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new StudentPaper);

        $grid->column('id', __('ID'))->sortable();
        $grid->column('classname', __('课程名称'))->sortable();
        $grid->column('year', __('年份'))->sortable();

        // 筛选条件
        $grid->filter(function ($filter) {
            // 年份
            $filter->equal('year');
        });

        // 禁用导出键
        $grid->disableExport();
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            // 去掉删除
            $actions->disableDelete();
            // 去掉编辑
            $actions->disableEdit();
            // 去掉显示
            $actions->disableView();
            // 成绩分析
            $actions->add(new Analysis());
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function analysis($id, Content $content)
    {
        $this->paper_id = $id;
        $questions = Question::where('paper_id', $id)->orderBy('type', 'asc')->orderByRaw("LPAD(sort,'0',10) asc")->get()->toArray();  // orderby时>10的排在2前面，因为sort是字符型，'10'<'2'，字符左填充0可解决
        $this->questions = $questions;

        $content->header('成绩分析');
        $content->description(date("Y年m月d日"));
        $content->breadcrumb(
            ['text' => '我的试卷', 'url' => '/student/paper'],
            ['text' => $id.'_成绩分析']
        );
        $content->row(function (Row $row) {
            // 成绩分析
            $row->column(6, function (Column $column) {
                $sql = "type, sum(score) as score";
                $full_score = DB::table('question')->select(DB::raw($sql))->where('paper_id', $this->paper_id)->orderBy('type', 'asc')->groupBy('type')->pluck('score', 'type');
                $this->student_score = $student_score = StudentScore::where('username', Admin::user()->username)->where('paper_id', $this->paper_id)->first(['score','selection_score','judgement_score','subjective_score'])->toArray();
                // 没有判断题
                $judge = Question::where('paper_id', $this->paper_id)->where('type', 2)->value('id');
                if ($judge) {
                    $content = "<lable style='font-size: 18px; font-weight: bold'>选择题得分：".$student_score['selection_score']."</lable>
                                <lable style='font-size: 18px; font-weight: bold; margin-left: 30px;'>判断题得分：".($student_score['judgement_score']+0)."</lable>
                                <lable style='font-size: 18px; font-weight: bold; margin-left: 30px;'>主观题得分：".($student_score['subjective_score']+0)."</lable>";
                    $rate = [
                        'selection_score' => $student_score['selection_score']/$full_score[1]*100,
                        'judgement_score' => $student_score['judgement_score']/$full_score[2]*100,
                        'subjective_score' => $student_score['subjective_score']/$full_score[3]*100,
                    ];
                } else {
                    $content = "<lable style='font-size: 18px; font-weight: bold'>选择题得分：" . $student_score['selection_score'] . "</lable>
                                <lable style='font-size: 18px; font-weight: bold; margin-left: 30px;'>主观题得分：" . ($student_score['subjective_score']+0) . "</lable>";
                    $rate = [
                        'selection_score' => $student_score['selection_score']/$full_score[1]*100,
                        'subjective_score' => $student_score['subjective_score']/$full_score[3]*100,
                    ];
                }
                $this->rate = $rate;
                $this->content = $content;

                $column->row(function (Row $row) {
                    $box = new Box('各题型得分率', view('admin.student.rate')->with('rate', $this->rate));
                    $box->footer("<div style='font-size: 18px; font-weight: bold;'>{$this->content}<label style='float: right;'>总分：{$this->student_score['score']}</label></div>");
                    $row->column(6, $box);

                    $score = StudentScore::select('id','score','selection_score','judgement_score','subjective_score')->where('paper_id', $this->paper_id)->get();
                    $count = $score->count();
                    $a = $b = $c = $d = $e = 0;
                    $your = '';
                    foreach ($score as $score) {
                        $s = $score->score;
                        switch ($s) {
                            case $s<60:
                                $e++;
                                if ($score->username = Admin::user()->username) {
                                    $your = 4;
                                }
                                break;
                            case $s>=60 && $s<70:
                                $d++;
                                if ($score->username = Admin::user()->username) {
                                    $your = 3;
                                }
                                break;
                            case $s>=70 && $s<80:
                                $c++;
                                if ($score->username = Admin::user()->username) {
                                    $your = 2;
                                }
                                break;
                            case $s>=80 && $s<90:
                                $b++;
                                if ($score->username = Admin::user()->username) {
                                    $your = 1;
                                }
                                break;
                            case $s>=90:
                                $a++;
                                if ($score->username = Admin::user()->username) {
                                    $your = 0;
                                }
                                break;
                        }
                    }
                    $segment = ['a' => $a, 'b' => $b, 'c' => $c, 'd' => $d, 'e' => $e, 'your' => $your];
                    $level = new Box('成绩段', view('admin.student.segment')->with('segment', $segment));
                    // 计算排名
                    $sql1 = StudentScore::where('paper_id', $this->paper_id)->select(DB::raw('username,score,@rank := 0,@last_score := NULL'))->orderBy('score', 'desc');
                    $rank = DB::table(DB::raw("({$sql1->toSql()}) as a"))->mergeBindings($sql1->getQuery())->select(DB::raw("
                                            a.username as username,
                                            a.score as score,
                                            case when @last_score = score then @rank
                                                when @last_score := score then @rank := @rank +1
                                                when @last_score = 0 OR @last_score IS NULL then @rank := @rank +1
                                                else NULL
                                            end as rank"))->first();
                    $level->footer("<div style='font-size: 18px; font-weight: bold;'><label>你的排名：{$rank->rank}</label><label style='float: right;'>总人数：{$count}</label></div>");
                    $row->column(6, $level);
                });
            });

            // 试卷题目展示
            $row->column(6, function (Column $column) {
                $answers = StudentScore::where('username', Admin::user()->username)->where('paper_id', $this->paper_id)->first(['selection_answer', 'judgement_answer', 'id'])->toArray();
                $subjective_answer = SubjectiveAnswer::where('score_id', $answers['id'])->orderBy('sort', 'asc')->get(['sort', 'answer', 'score'])->toArray();
                $questions = $this->questions;
                $selection = "";
                $judgement = "";
                $subjective = "";
                foreach ($questions as $question) {
                    switch ($question['type']) {
                        // 选择题
                        case 1:
                            if (empty($answers['selection_answer'])) {
                                $your_answer = '暂无';
                                $color = 'black';
                            } else {
                                $your_answer = isset($answers['selection_answer'][$question['sort'] - 1]) ? $answers['selection_answer'][$question['sort'] - 1] : '无';
                                $color = isset($answers['selection_answer'][$question['sort'] - 1]) ? ($answers['selection_answer'][$question['sort'] - 1] == $question['answer'] ? 'darkgreen' : 'darkred') : 'black';
                            }
                            $selection .= "<div style='font-size: 20px; margin-left: 20px;'>
                                                <label>".$question['sort'].".&nbsp&nbsp".$question['title']."（".$question['score']."分）</label><br>
                                                A.&nbsp&nbsp".$question['option1']."<br>
                                                B.&nbsp&nbsp".$question['option2']."<br>
                                                C.&nbsp&nbsp".$question['option3']."<br>
                                                D.&nbsp&nbsp".$question['option4']."<br><br>
                                                正确答案：&nbsp&nbsp<label style='color: darkgreen; font-weight: bold;'>".$question['answer']."</label><br>
                                                你的答案：&nbsp&nbsp<label style='color: ".$color."; font-weight: bold;'>".$your_answer."</label><br>
                                           </div><br>";
                            break;
                        // 判断题
                        case 2:
                            if (empty($answers['judgement_answer'])) {
                                $your_answer = '暂无';
                                $color = 'black';
                            } else {
                                $your_answer = isset($answers['judgement_answer'][$question['sort'] - 1]) ? ($answers['judgement_answer'][$question['sort'] - 1] == 1 ? '√' : '×') : '无';
                                $color = isset($answers['judgement_answer'][$question['sort'] - 1]) ? ($answers['judgement_answer'][$question['sort'] - 1] == $question['answer'] ? 'darkgreen' : 'darkred') : 'black';
                            }
                            $right = $question['answer'] == 1 ? '√' : '×';
                            $judgement .= "<div style='font-size: 20px; margin-left: 20px;'>
                                                <label>".$question['sort'].". ".$question['title']."（".$question['score']."分）</label><br><br>
                                                正确答案：&nbsp&nbsp<label style='color: darkgreen; font-weight: bold;'>".$right."</label><br>
                                                你的答案：&nbsp&nbsp<label style='color: ".$color."; font-weight: bold;'>".$your_answer."</label><br>
                                           </div><br><br>";
                            break;
                        // 主观题
                        case 3:
                            if (empty($subjective_answer)) {
                                $your_answer = '暂无';
                                $score = 0;
                            } else {
                                $your_answer = isset($subjective_answer[$question['sort'] - 1]['answer']) ? $subjective_answer[$question['sort'] - 1]['answer'] : '无';
                                $score = isset($subjective_answer[$question['sort'] - 1]['score']) ? $subjective_answer[$question['sort'] - 1]['score'] : 0;
                            }
                            $subjective .= "<div style='font-size: 20px; margin-left: 20px;'>
                                                <label>".$question['sort'].". ".$question['title']."（".$question['score']."分）</label><br><br>
                                                参考答案：&nbsp&nbsp<label font-weight: bold;'>".$question['answer']."</label><br>
                                                你的答案：&nbsp&nbsp<label>".$your_answer."</label><br>
                                                得分：&nbsp&nbsp".$score."
                                           </div><br><br>";
                            break;
                    }
                }
                $selection_score = $this->student_score['selection_score'] == null ? 0 : $this->student_score['selection_score'];
                $judgement_score = $this->student_score['judgement_score'] == null ? 0 : $this->student_score['judgement_score'];
                $subjective_score = $this->student_score['subjective_score'] == null ? 0 : $this->student_score['subjective_score'];
                $selection .= !empty($selection) ? "<label style='font-size: 21px; margin-left: 20px;'>选择题成绩：{$selection_score}</label><br>" : "";
                $judgement .= !empty($judgement) ? "<label style='font-size: 21px; margin-left: 20px;'>判断题成绩：{$judgement_score}</label><br>" : "";
                $subjective .= !empty($subjective) ? "<label style='font-size: 21px; margin-left: 20px;'>主观题成绩：{$subjective_score}</label><br>" : "";
                $tab = new Tab();
                $tab->add('选择题', $selection);
                $tab->add('判断题', $judgement);
                $tab->add('主观题', $subjective);
                $column->row($tab);
            });
        });

        return $content;
    }
}
