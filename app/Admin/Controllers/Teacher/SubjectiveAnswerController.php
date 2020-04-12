<?php

namespace App\Admin\Controllers\Teacher;

use App\Admin\Actions\Answer\DeleteSubjective;
use App\Paper;
use App\Question;
use App\StudentScore;
use App\StudentUser;
use App\Subjective;
use App\SubjectiveAnswer;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Encore\Admin\Facades\Admin;

class SubjectiveAnswerController extends AdminController
{
    // 主观题答案模型

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\SubjectiveAnswer';

    // 题目-Subjective类
    public $question = null;

    /*
     *  列表
     *
     *  @return Content
     */
    public function index(Content $content)
    {
        $content->header('主观题答案');
        $content->description(date("Y年m月d日"));
        $content->breadcrumb(
            ['text' => '主观题答案']
        );
        $content->body($this->grid());

        return $content;
    }

    /*
     *  新增
     *
     *  @return Content
     */
    public function create(Content $content)
    {
        $this->save(null);
        $content->header('主观题答案-新建');
        $content->description(date("Y年m月d日"));
        $content->breadcrumb(
            ['text' => '主观题答案', 'url' => '/answer/subjective'],
            ['text' => '新建']
        );
        $content->body($this->form(null));
        return $content;
    }

    /**
     * 编辑
     *
     * @return Content
     */
    public function edit($id, Content $content)
    {
        $this->save($id);
        $content->header('主观题答案-编辑');
        $content->description(date("Y年m月d日"));
        $content->breadcrumb(
            ['text' => '主观题答案', 'url' => '/answer/subjective'],
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
        $grid = new Grid(new SubjectiveAnswer);

        $grid->column('id', __('ID'))->sortable();
        $grid->column('username', __('学号'))->sortable();
        $grid->column('paper_id', __('套卷'))->display(function ($paper_id) {
            $paper = Paper::find($paper_id);
            return $paper_id . '-' . $paper->classname . '-' . $paper->year;
        })->width(400)->sortable();
        $grid->column('sort', __('题序'))->sortable();
        $grid->column('score', __('得分'));
        $grid->column('remark', __('备注'));

        // 筛选条件
        $grid->filter(function ($filter) {
            $filter->like('username', '学号');
            $filter->equal('paper_id', '套卷ID');
        });

        // 重写删除功能
        $grid->actions(function($actions) {
            $actions->disableDelete();
            $actions->add(new DeleteSubjective());
        });

        $grid->disableCreateButton();
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
        $subjectA = SubjectiveAnswer::findOrFail($id);
        $this->question = Subjective::where('paper_id', $subjectA->paper_id)->where('sort', $subjectA->sort)->first(['title', 'answer', 'score'])->toArray();
        $content->header('主观题答案-详情');
        $content->description(date("Y年m月d日"));
        $content->breadcrumb(
            ['text' => '主观题答案', 'url' => '/answer/subjective'],
            ['text' => $id],
            ['text' => '详情']
        );
        $show = Admin::show($subjectA, function (Show $show) {
            $show->panel()->title('详情');
            $show->field('id', __('ID'));
            $show->field('username', __('学号'));
            $show->field('paper_id', __('套卷ID'));
            $show->paper('套卷信息', function ($paper) {
                $paper->setResource('/admin/paper');
                $paper->id('套卷ID');
                $paper->classname('课程名称');
                $paper->year('年份');
                $paper->panel()->tools(function ($tools) {
                    $tools->disableEdit();
                    $tools->disableDelete();
                });
            });
            $show->field('sort', __('题序'));
            // 自定义扩展展示题目描述
            $show->field('title', __('题目'))->questioninfo($this->question['title']);
            $show->field('question_answer', __('参考答案'))->questioninfo($this->question['answer']);
            $show->field('answer', __('学生答案'));
            $show->field('full_score', __('满分'))->questioninfo($this->question['score']);
            $show->field('score', __('得分'));
            $show->field('auto_score', __('自动评分分数'))->as(function ($auto_score) {
                if ($auto_score == null) {
                    $res = '未开启自动评分';
                } else {
                    $res = $auto_score;
                }
                return $res;
            });
            $show->field('remark', __('备注'));
            $show->field('created_at', __('创建时间'));
            $show->field('updated_at', __('更新时间'));
            $show->panel()->tools(function ($tools) {
//            $tools->disableEdit();
//            $tools->disableList();
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
            $answer = SubjectiveAnswer::findOrFail($id);
            $form = new Form($answer);
            $form->setAction('edit');
            $form->setTitle('编辑');
            $form->tools(function (Form\Tools $tools) {
                $url = Input::url();
                $url = substr($url, 0, -5);
                $list = substr($url, 0, -2);
                $tools->add("<a href='{$url}' class='btn btn-sm btn-primary' style='float: right; margin-right: 5px;'><i class='fa fa-eye'></i>&nbsp;查看</a>");
                $tools->add("<a href='{$list}' class='btn btn-sm btn-default' style='float: right; margin-right: 5px;'><i class='fa fa-list'></i>&nbsp;列表</a>");
            });
            $form->select('username', __('学号'))->options([$answer->username => $stu_arr[$answer->username]])->value($answer->username)->disable();
            $form->select('paper_id', __('套卷'))->options([$answer->paper_id => $paper_arr[$answer->paper_id]])->value($answer->paper_id)->disable();
            $form->textarea('answer', __('答案'))->value($answer->answer)->required();
            $form->hidden('sa_id')->value($id);
            $criterion = $answer->criterion($answer->paper_id, $answer->sort);
            $form->number('score', __('得分'))->value($answer->score)->max($criterion->score)->help('是否开启自动评分不影响人工修改得分，最终以人工修改的得分为结果')->required();
            $form->text('auto_score', __('自动评分分数'))->value($answer->auto_score != null ? $answer->auto_score : '未开启自动评分')->disable();
            $form->text('remark', __('备注'))->value($answer->remark);
            $form->tools(function (Form\Tools $tools) use ($id) {
                $score_id = SubjectiveAnswer::findOrFail($id)->score_id;
                $back = "/admin/answer/".$score_id;
                $tools->add("<a href='{$back}' class='btn btn-sm btn-default' style='float: right; margin-right: 5px;'><i class='fa fa-list'></i>&nbsp;返回</a>");
            });
        } else {
            $form = new Form(new SubjectiveAnswer);
            $form->setAction('create');
            $form->setTitle('新建');
            $score_id = isset($_GET['score_id']) ? $_GET['score_id'] : null;
            $sort_arr = [];
            if (!empty($score_id)) {
                $score = StudentScore::find($score_id);
                $form->select('uname', __('学号'))->options([$score->username => $stu_arr[$score->username]])->value($score->username)->disable();
                $form->select('p_id', __('套卷'))->options([$score->paper_id => $paper_arr[$score->paper_id]])->value($score->paper_id)->disable();
                $form->hidden('username')->value($score->username);
                $form->hidden('paper_id')->value($score->paper_id);
                $form->hidden('score_id')->value($score_id);
                // 获取未录入答案的题序
                $questions = Question::where('paper_id', $score->paper_id)->where('type', 3)->orderBy('sort')->pluck('sort')->toArray();
                $answers = SubjectiveAnswer::where('username', $score->username)->where('paper_id', $score->paper_id)->orderBy('sort')->pluck('sort')->toArray();
                foreach (array_diff($questions, $answers) as $value) {
                    $sort_arr[$value] = $value;
                }
            }
            $form->select('sort', __('题序'))->options($sort_arr)->required();
            $form->textarea('answer', __('答案'))->required();
            $form->text('score', __('得分'))->help('是否开启自动评分不影响人工修改得分，最终以人工修改的得分为结果');
            $form->tools(function (Form\Tools $tools) use ($score_id) {
                $list = '/admin/answer/subjective';
                $back = "/admin/answer/" . $score_id;
                $tools->add("<a href='{$list}' class='btn btn-sm btn-default' style='float: right; margin-right: 5px;'><i class='fa fa-list'></i>&nbsp;列表</a>");
                $tools->add("<a href='{$back}' class='btn btn-sm btn-default' style='float: right; margin-right: 5px;'><i class='fa fa-list'></i>&nbsp;返回</a>");
            });
        }
        $form->tools(function (Form\Tools $tools) {
            $tools->disableList();
        });
        $form->footer(function ($footer) {
            $footer->disableCreatingCheck();
            $footer->disableViewCheck();
            $footer->disableEditingCheck();
        });
        return $form;
    }

    // 数据处理
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
                    $info = SubjectiveAnswer::find($id);
                    if (!($info instanceof SubjectiveAnswer)) {
                        $error_msg = "该答案不存在";
                    }
                    $old_score = $info->score;
                    $old_answer = $info->answer;
                    // 判断是否修改了答案,修改了需更新自动评分分数
                    if (strcmp($old_answer, $input['answer']) != 0) {
                        $info->answer = $input['answer'];
                        $auto_score = $this->getScore($input, $time);
                        $info->auto_score = $auto_score;
                    }
                    $info->score = $input['score'];
                    $info->remark = $input['remark'] ? $input['remark'] : null;
                    $info->save();
                    $stu = $info->studentScore;
                    $stu->subjective_score += ($input['score'] - $old_score);
                    $stu->score += ($input['score'] - $old_score);
                    $stu->save();
                    $success_msg = "编辑成功";
                } else {
                    // 新增（入口为学生答案的主观题列表）
                    $info_arr = $this->getScore($input, $time);
                    if (!empty($info_arr['info'])) {
                        DB::table('subjective_answer')->insert($info_arr['info']);
                        $stu = StudentScore::find($input['score_id']);
                        $stu->subjective_score += $info_arr['subjective_score'];
                        $stu->score += $info_arr['subjective_score'];
                        $stu->save();
                    }
                    $success_msg = "新增成功";
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

    // 计算主观题分数
    public function getScore($input, $time)
    {
        $subjective_score = 0;
        if (isset($input['sa_id'])) {
            // 编辑时修改答案
            $sub_answer = SubjectiveAnswer::find($input['sa_id']);
            $answer = Subjective::where('paper_id', $sub_answer->paper_id)->where('sort', $sub_answer->sort)->first(['answer', 'score', 'is_auto', 'model']);
            if ($answer->is_auto == 1) {
                // 自动评分
                if ($answer->model == 1) {
                    // 关键词评分模式
                    // 获取关键词评分结果
                    $auto_score = $this->getKeyScore($answer->answer, $input['answer']);
                } else {
                    // 相似度评分模式
                    // 获取相似度评分结果
                    $sim = $this->getSim($answer->answer, $input['answer'], $sub_answer->paper_id . '_' . $sub_answer->sort, substr($sub_answer->username,-4));
                    $auto_score = round(($answer->score + 0) * $sim, 2);
                }
            } else {
                // 未开启自动评分
                $auto_score = null;
            }
            return $auto_score;
        } else {
            // 新增学生主观题答案
            $info = [];
            // 获取参考答案、分值、是否自动评分、评分模式
            $answer = Subjective::where('paper_id', $input['paper_id'])->where('sort', $input['sort'])->first(['answer', 'score', 'is_auto', 'model']);
            if ($answer->is_auto == 1) {
                // 自动评分
                if ($answer->model == 1) {
                    // 关键词评分模式
                    // 获取关键词评分结果
                    $auto_score = $this->getKeyScore($answer->answer, $input['answer']);
                } else {
                    // 相似度评分模式
                    // 获取相似度评分结果
                    $sim = $this->getSim($answer->answer, $input['answer'], $input['paper_id'] . '_' . $input['sort'], substr($input['username'], -4));
                    $auto_score = round(($answer->score + 0) * $sim, 2);
                }
            } else {
                // 未开启自动评分
                $auto_score = null;
            }
            $data = [
                'username' => $input['username'],
                'paper_id' => $input['paper_id'],
                'score_id' => $input['score_id'],
                'answer' => $input['answer'],
                'sort' => $input['sort'],
                'score' => empty($input['score']) ? $auto_score : $input['score'],
                'auto_score' => $auto_score,
                'created_at' => $time,
                'updated_at' => $time,
            ];
            $subjective_score += empty($input['score']) ? $auto_score + 0 : $input['score'];
            $info[] = $data;

            return [
                'info' => $info,
                'subjective_score' => $subjective_score
            ];
        }
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
        $word_sim = system('python ./auto_score/word2vec/page_sim.py '.$file_a.' '.$file_b);
        $doc_sim = system('python ./auto_score/doc2vec/page_sim.py '.$file_a.' '.$file_b);

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

}
