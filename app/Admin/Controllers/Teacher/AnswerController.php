<?php

namespace App\Admin\Controllers\Teacher;

use App\Admin\Actions\Answer\Delete;
use App\Admin\Actions\Answer\DeleteSubjective;
use App\Admin\Actions\Answer\Detail;
use App\Question;
use App\Subjective;
use App\SubjectiveAnswer;
use App\StudentScore;
use App\Paper;
use App\StudentUser;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;
use function foo\func;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class AnswerController extends AdminController
{
    // 学生答案
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\StudentScore';

    // 标准答案 Array
    public $info = [];

    /*
     *  列表
     *
     *  @return Content
     */
    public function index(Content $content)
    {
        $content->header('学生答案');
        $content->description('列表');
        $content->breadcrumb(
            ['text' => '学生答案']
        );
        $content->body($this->grid());

        return $content;
    }

    /*
     *  新增
     *
     *  @return string
     */
    public function create(Content $content)
    {
        $this->save(null);
        $content->header('学生答案');
        $content->description('新建');
        $content->breadcrumb(
            ['text' => '学生答案', 'url' => '/answer'],
            ['text' => '新建']
        );
        $content->body($this->form(null));
        return $content;
    }

    /**
     * 编辑
     *
     * @return Form
     */
    public function edit($id, Content $content)
    {
        $this->save($id);
        $content->header('学生答案');
        $content->description('编辑');
        $content->breadcrumb(
            ['text' => '学生答案', 'url' => '/answer'],
            ['text' => $id],
            ['text' => '编辑']
        );
        $content->body($this->form($id));
        return $content;
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new StudentScore);

        $grid->column('id', __('ID'))->sortable();
        $grid->column('username', __('学号'))->sortable();
        $grid->column('paper_id', __('套卷'))->display(function ($paper_id) {
            $paper = Paper::find($paper_id);
            return $paper_id . '-' . $paper->classname . '-' . $paper->year;
        })->width(400)->sortable();
        $grid->column('score', __('成绩'))->sortable();
        $grid->column('created_at', __('创建时间'))->sortable();
        $grid->column('updated_at', __('更新时间'))->sortable();

        // 重写 删除 逻辑
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->add(new Delete());
        });

        // 筛选条件
        $grid->filter(function ($filter) {
            $filter->like('username', __('学号'));
            $filter->equal('paper_id', __('套卷ID'));
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id, Content $content)
    {
        $content->header('学生答案');
        $content->description('详情');
        $content->breadcrumb(
            ['text' => '学生答案', 'url' => '/answer'],
            ['text' => $id],
            ['text' => '详情']
        );
        $stu_score = StudentScore::findOrFail($id);
        $answer_arr = Question::where('paper_id', $stu_score->paper_id)->whereIn('type', [1,2])->orderBy('type')->orderBy('sort')->get()->toArray();
        $info_arr = [];
        // 处理答案数组
        array_walk($answer_arr,
            function ($value, $Key) use (&$info_arr) {
                $info_arr[$value['type']][] = $value['answer'];
            });
        $this->info = $info_arr;
        $show = Admin::show($stu_score, function (Show $show) {
            $show->panel()->title('详情');
            $show->field('username', __('学号'));
            $show->field('paper_id', __('套卷ID'));
            $show->paper('套卷信息', function ($paper) {
                $paper->setResource('/admin/paper');
                $paper->id('套卷ID');
                $paper->classname('课程名称');
                $paper->year('年份');
                $paper->panel()->tools(function ($tools) {
//                    $tools->disableEdit();
                    $tools->disableDelete();
                });
            });
            $show->field('score', __('成绩'));
            $show->field('selection_answer', __('选择题答案'));
            $show->field('sele_answer', __('选择题标准答案'))->questioninfo(implode('', $this->info[1]));
            $show->field('selection_score', __('选择题得分'));
            $show->field('judgement_answer', __('判断题答案'))->as(function ($answer) {
                if ($answer != null) {
                    $res = str_replace('1', '√', $answer);
                    $res = str_replace('2', '×', $res);
                    return $res;
                }
            });
            $show->field('judge_answer', __('判断题标准答案'))->questioninfo(implode('', $this->info[2]), true);
            $show->field('judgement_score', __('判断题得分'));
            $show->field('subjective_score', __('主观题得分'));
            $show->field('created_at', __('创建时间'));
            $show->field('updated_at', __('更新时间'));
            $show->subjective('主观题答案', function ($subjective) {
                $subjective->resource('/admin/answer/subjective');
                $subjective->id('ID');
                $subjective->sort('题序')->width(50);
                $subjective->answer('答案');
                $subjective->score('得分')->width(50);
                $subjective->disableExport();
                $subjective->actions(function ($actions) {
                    $actions->disableDelete();
                    $actions->add(new DeleteSubjective());
                });
                $subjective->filter(function ($filter) {
                    $filter->equal('paper_id', __('套卷ID'));
                    $filter->like('answer', __('答案'));
                });
            });
            $show->panel()->tools(function ($tools) {
//            $tools->disableEdit();
                $tools->disableDelete();
            });
        });

        $content->body($show);

        return $content;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form($id)
    {
        $papers = Paper::all(['id', 'classname', 'year']);
        $paper_arr = [];
        foreach ($papers as $paper) {
            $paper_arr[$paper->id] =  $paper->id . "-" . $paper->classname . "-" . $paper->year;
        }
        $students = StudentUser::all(['name', 'username']);
        $stu_arr = [];
        foreach ($students as $stu) {
            $stu_arr[$stu->username] = $stu->username.'-'.$stu->name;
        }
        if (null != $id) {
            $answer = StudentScore::findOrFail($id);
            $form = new Form($answer);
            $form->setAction('edit');
            $form->setTitle('编辑');
            $form->tools(function (Form\Tools $tools) {
                $url = Input::url();
                $url = substr($url, 0, -5);
                $tools->disableList();
                $tools->add("<a href='{$url}' class='btn btn-sm btn-primary' style='float: right'><i class='fa fa-eye'></i>&nbsp;查看</a>");
                $tools->add("<a href='/admin/answer' class='btn btn-sm btn-default' style='float: right; margin-right: 5px;'><i class='fa fa-list'></i>&nbsp;列表</a>");
            });
            $form->select('u', __('学号'))->options([$answer->username => $stu_arr[$answer->username]])->value($answer->username)->disable();
            $form->select('p', __('套卷'))->options([$answer->paper_id => $paper_arr[$answer->paper_id]])->value($answer->paper_id)->disable();
            $form->hidden('username', __('学号'))->value($answer->username);
            $form->hidden('paper_id', __('套卷'))->value($answer->paper_id);
            $form->text('selection_answer', __('选择题答案'))->value($answer->selection_answer)->placeholder('输入 选择题答案 (按顺序输入所有答案)');
            $form->text('judgement_answer', __('判断题答案'))->value($answer->judgement_answer)->placeholder('输入 判断题答案 (正确为1，错误为2，例：若正确答案为√√××√，则输入11221)')->help('没有判断题可不填');
        } else {
            $form = new Form(new SubjectiveAnswer);
            $form->setAction('create');
            $form->setTitle('新建');
            $form->select('username', __('学号'))->options($stu_arr)->required();
            $form->select('paper_id', __('套卷'))->options($paper_arr)->required();
            $form->text('selection_answer', __('选择题答案'))->placeholder('输入 选择题答案 (按顺序输入所有答案)');
            $form->text('judgement_answer', __('判断题答案'))->placeholder('输入 判断题答案 (正确为1，错误为2，例：若正确答案为√√××√，则输入11221)')->help('没有判断题可不填');
            $form->hasMany('subjective', __('主观题'), function (Form\NestedForm $form) {
                $form->text('sort', __('题序'))->required();
                $form->textarea('answer', __('答案'))->required();
                $form->text('score', __('得分'))->help('开启自动评分后可不填，后续可修改自动评分结果；若开启自动评分后填入得分，则最后得分以人工评分为准，系统仍会展示自动评分结果。')->required();
            });
        }
        $form->footer(function ($footer) {
//            $footer->disableCreatingCheck();
            $footer->disableViewCheck();
            $footer->disableEditingCheck();
        });
        return $form;

    }

    public function save($id)
    {
        $error_msg = null;
        $success_msg = null;
        if (!empty($_POST)) {
            $input = $_POST;
            $time = date("Y-m-d H:i:s", time() + 3600 * 8);
            DB::beginTransaction();
            try {
                if (null != $id) {
                    // 更新
                    $info = StudentScore::find($id);
                    if (!($info instanceof StudentScore)) {
                        $error_msg = "该学生成绩不存在";
                    }
                    $info->selection_answer = $input['selection_answer'];
                    $info->judgement_answer = $input['judgement_answer'];
                    $score = $this->getScore($input, $time);
                    $info->selection_score = $score['selection_score'];
                    $info->judgement_score = $score['judgement_score'];
                    $info->score = $score['total_score'] + $info->subjective_score;
                    $info->save();
                    $success_msg = "编辑成功";
                } else {
                    // 新增
                    $check = StudentScore::where('username', $input['username'])->where('paper_id', $input['paper_id'])->first(['id']);
                    if ($check instanceof StudentScore) {
                        $error_msg = "该学生答案已存在";
                    } else {
                        $info_arr = $this->getScore($input, $time);
                        if (!empty($info_arr['info'])) {
                            $score_id = DB::table('student_score')->insertGetId([
                                'username' => $input['username'],
                                'paper_id' => $input['paper_id'],
                                'score' => $info_arr['total_score'],
                                'selection_answer' => isset($input['selection_answer']) ? $input['selection_answer'] : null,
                                'selection_score' => $info_arr['selection_score'],
                                'judgement_answer' => isset($input['judgement_answer']) ? $input['judgement_answer'] : null,
                                'judgement_score' => $info_arr['judgement_score'],
                                'subjective_score' => $info_arr['subjective_score'] == 0 ? null : $info_arr['subjective_score'],
                                'created_at' => $time,
                                'updated_at' => $time,
                            ]);
                            foreach ($info_arr['info'] as &$sub) {
                                $sub['score_id'] = $score_id;
                            }
                            DB::table('subjective_answer')->insert($info_arr['info']);
                        }
                        $success_msg = "新增成功";
                    }
                }
                DB::commit();
            } catch (\Illuminate\Database\QueryException $ex) {
                DB::rollBack();
                admin_toastr("操作失败：" . $ex, 'error');
            }
            if ($error_msg != null) {
                admin_toastr($error_msg, 'error', ['timeOut' => 2000]);
            } else {
                admin_toastr($success_msg, 'success', ['timeOut' => 2000]);
            }
        }
    }

    public function getScore($input, $time)
    {
        $total_score = 0;
        //选择题
        $selection = strtoupper($input['selection_answer']);
        $selection_score = 0;
        if (!empty($selection)) {
            $arr = str_split($selection);
            $answer_arr = $this->getAnswer($input['paper_id'], 1);
            foreach ($arr as $key => $value) {
                $score = $answer_arr[$key+1]['answer'] == $value ? $answer_arr[$key+1]['score'] : 0;
                $selection_score += $score;
                $total_score += $score;
            }
        } else {
            $selection_score = null;
        }
        //判断题
        $judgement = $input['judgement_answer'];
        $judgement_score = 0;
        if (!empty($judgement)) {
            $arr = str_split($judgement);
            $answer_arr = $this->getAnswer($input['paper_id'], 2);
            foreach ($arr as $k => $v) {
                $score = $answer_arr[$k+1]['answer'] == $v ? $answer_arr[$k+1]['score'] : 0;
                $judgement_score += $score;
                $total_score += $score;
            }
        } else {
            $judgement_score = null;
        }
        //主观题
        $info = [];
        $subjective = isset($input['subjective']) ? $input['subjective'] : null;
        $subjective_score = 0;
        if (!empty($subjective)) {
            foreach ($subjective as $value) {
                // 获取参考答案、分值、是否自动评分、评分模式
                $answer = Subjective::where('paper_id', $input['paper_id'])->where('sort', $value['sort'])->first(['answer', 'score', 'is_auto', 'model']);
                if ($answer->is_auto == 1) {
                    // 自动评分
                    if ($answer->model == 1) {
                        // 关键词评分模式
                        // 获取关键词评分结果
                        $auto_score = $this->getKeyScore($answer->answer, $value['answer']);
                    } else {
                        // 相似度评分模式
                        // 获取相似度评分结果
                        $sim = $this->getSim($answer->answer, $value['answer'], $input['paper_id'].'_'.$value['sort'], substr($input['username'], -4));
                        $auto_score = round(($answer->score+0)*$sim, 2);
                    }
                } else {
                    // 未开启自动评分
                    $auto_score = null;
                }
                $question_id = Subjective::where('paper_id', $input['paper_id'])->where('sort', $value['sort'])->value('id');
                $data = [
                    'username' => $input['username'],
                    'question_id' => $question_id,
                    'answer' => $value['answer'],
                    'score' => empty($value['score']) ? $auto_score : $value['score']+0,
                    'auto_score' => $auto_score,
                    'created_at' => $time,
                    'updated_at' => $time,
                ];
                $subjective_score += empty($value['score']) ? $auto_score+0 : $value['score']+0;
                $total_score += empty($value['score']) ? $auto_score+0 : $value['score']+0;
                $info[] = $data;
            }
        } else {
            $subjective_score = null;
        }

        return [
            'info' => $info,
            'total_score' => $total_score,
            'selection_score' => $selection_score,
            'judgement_score' => $judgement_score,
            'subjective_score' => $subjective_score
        ];
    }

    // 获取相似度比较结果
    public function getSim($answer, $stu_answer, $prefix, $username)
    {
        $file_a = $prefix.'.txt';
        $file_b = $prefix.'_'.$username.'.txt';
        // 保存答案及分词结果（用于查看测试情况）
        if (!file_exists('./auto_score/answer_log/'.$file_a)) {
            $criterion = fopen('./auto_score/answer_log/'.$file_a, 'w');
            fwrite($criterion, $answer);
            fclose($criterion);
        }
        $studentf = fopen('./auto_score/answer_log/'.$file_b, 'w');
        fwrite($studentf, $stu_answer);
        fclose($studentf);

        // 注意修改文件读、写、执行权限
        ob_start();
        $word_sim = system('python ./auto_score/word2vec/page_sim.py '.$file_a.' '.$file_b);
        $doc_sim = system('python ./auto_score/doc2vec/page_sim.py '.$file_a.' '.$file_b);
        ob_clean();

        // doc2vec 占更高权重 (最佳值待确定)
        $res = round($doc_sim * 0.6 + $word_sim * 0.4, 2);

        return $res;
    }

    // 获取关键词评分结果
    public function getKeyScore($answer, $stu_answer)
    {
        // 处理关键词及对应分值
        $keyPoint = explode('、', $answer);
        $totalScore = 0;
        foreach ($keyPoint as $key) {
            $pos = strpos($key, '(');
            $word = substr($key, 0, $pos);
            $score = substr($key, $pos+1, -1);
            if (strpos($stu_answer, $word) !== false) {
                $totalScore += $score+0;
            }
        }
        return $totalScore;
    }

    public function getAnswer($paper_id, $type)
    {
        $answer = Question::where('paper_id', $paper_id)->where('type', $type)->orderBy('sort', 'asc')->get(['sort', 'answer', 'score']);
        $answer_arr = [];
        foreach ($answer as $a) {
            $answer_arr[$a->sort] = [
                'answer' => $a->answer,
                'score'  => $a->score
            ];
        }

        return $answer_arr;
    }
}
