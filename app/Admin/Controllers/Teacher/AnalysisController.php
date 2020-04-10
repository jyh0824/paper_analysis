<?php

namespace App\Admin\Controllers\Teacher;

use App\Analysis;
use App\Paper;
use App\StudentScore;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Layout\Row;
use Encore\Admin\Layout\Column;
use Illuminate\Support\Facades\DB;
use function Sodium\compare;

class AnalysisController extends AdminController
{
    // 成绩分析
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Analysis';

    // 试卷
    public $paper = null;

    // 成绩
    public $score = null;

    /*
     *  列表
     *
     *  @return Content
     */
    public function show($paper_id, Content $content)
    {
        $paper = Paper::find($paper_id);
        $this->paper = $paper;
        $this->score = StudentScore::select('id','score','selection_score','judgement_score','subjective_score')->where('paper_id', $paper_id)->get();
        $content->header($paper->classname.'-'.$paper->year.'-成绩分析');
        $content->description(date("Y年m月d日"));
        $content->breadcrumb(
            ['text' => '套卷管理', 'url' => '/paper'],
            ['text' => '成绩分析']
        );
        if($this->score->count() < 1) {
            $content->body('<h3 style="font-weight: bold; color: #ab242e;" align="center">该试卷暂无成绩，请录入后再查看</h3>');
        } else {
            $content->row(function (Row $row) {
                // 分数段统计
                $row->column(4, function (Column $column) {
                    $count = $this->score->count();
                    $avg = empty($this->score->avg('score')) ? 0 : $this->score->avg('score');
                    $a = $b = $c = $d = $e = 0;
                    foreach ($this->score as $score) {
                        $s = $score->score;
                        switch ($s) {
                            case $s < 60:
                                $e++;
                                break;
                            case $s >= 60 && $s < 70:
                                $d++;
                                break;
                            case $s >= 70 && $s < 80:
                                $c++;
                                break;
                            case $s >= 80 && $s < 90:
                                $b++;
                                break;
                            case $s >= 90:
                                $a++;
                                break;
                        }
                    }
                    $segment = [$a, $b, $c, $d, $e];
                    $box = new Box("分数段统计", view('admin.analysis.segment')->with('segment', $segment));
                    $box->footer("<div style='font-size: 18px; font-weight: bold;'><label>平均分：{$avg}</label><label style='float: right;'>总人数：{$count}</label></div>");
                    $column->append($box);
                });

                // 各题型得分率
                $row->column(3, function (Column $column) {
                    $sql = "type, sum(score) as score";
                    $full_score = DB::table('question')->select(DB::raw($sql))->where('paper_id', $this->paper->id)->groupBy('type')->get();
                    $selection_rate = $this->score->avg('selection_score') / $full_score[0]->score;
                    $judgement_rate = $this->score->avg('judgement_score') / $full_score[1]->score;
                    $subjective_rate = $this->score->avg('subjective_score') / $full_score[2]->score;
                    $rate = [
                        $selection_rate * 100,
                        $judgement_rate * 100,
                        $subjective_rate * 100,
                    ];
                    $hard = round(1 - ($selection_rate + $judgement_rate + $subjective_rate) / 3, 2);
                    $box = new Box("各题型得分率", view('admin.analysis.rate')->with('rate', $rate));
                    $box->footer("<div style='font-size: 18px; font-weight: bold;'><label>试卷难度系数：{$hard}</label></div>");
                    $column->append($box);
                });

                // 历年指标对比(课程名称相同，年份不同的试卷)
                $classname = $this->paper->classname;
                $past = Paper::where('classname', $classname)->count();
                // 至少存在1份不同年份的试卷才展示
                if ($past > 1) {
                    $row->column(5, function (Column $column) {
                        $past = Paper::select('id', 'year')->where('classname', $this->paper->classname)->whereBetween('year', [$this->paper->year - 2, $this->paper->year + 2])->orderBy('year', 'asc')->get();
                        $past_arr = [];
                        foreach ($past as $p) {
                            $avg = round(StudentScore::where('paper_id', $p->id)->avg('score'), 0);
                            $sql = "type, sum(score) as score";
                            $full_score = DB::table('question')->select(DB::raw($sql))->where('paper_id', $this->paper->id)->groupBy('type')->get();
                            $selection_rate = DB::table('student_score')->where('paper_id', $p->id)->avg('selection_score') / $full_score[0]->score * 100;
                            $judgement_rate = DB::table('student_score')->where('paper_id', $p->id)->avg('judgement_score') / $full_score[1]->score * 100;
                            $subjective_rate = DB::table('student_score')->where('paper_id', $p->id)->avg('subjective_score') / $full_score[2]->score * 100;
                            $past_arr[] = [
                                'id' => $p->id,
                                'y' => $p->year,
                                'avg' => $avg,
                                'selection_rate' => $selection_rate,
                                'judgement_rate' => $judgement_rate,
                                'subjective_rate' => $subjective_rate,
                            ];
                        }
                        $box = new Box("历年指标对比", view('admin.analysis.compare')->with('compare', $past_arr));
                        $box->footer("<div style='font-size: 18px; font-weight: bold;'><label></label></div>");
                        $column->append($box);
                    });
                } else {
                    $row->column(5, function (Column $column) {
                        $box = new Box("历年指标对比");
                        $box->content('<lable style="font-size: 18px; font-weight: bold; color: #ab242e">暂无</lable>');
                        $column->append($box);
                    });
                }
            });
        }
//        $box = new Box('成绩分析', view('admin.analysis'));
//        $box->collapsable();
//        $content->body($box);

        return $content;
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Analysis);

        $grid->column('id', __('Id'));
        $grid->column('classname', __('Classname'));
        $grid->column('year', __('Year'));
        $grid->column('uid', __('Uid'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Analysis::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('classname', __('Classname'));
        $show->field('year', __('Year'));
        $show->field('uid', __('Uid'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Analysis);

        $form->text('classname', __('Classname'));
        $form->text('year', __('Year'));
        $form->number('uid', __('Uid'));

        return $form;
    }
}
