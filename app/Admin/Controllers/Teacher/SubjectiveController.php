<?php

namespace App\Admin\Controllers\Teacher;

use App\Admin\Actions\Paper\DeleteSubjective;
use App\Paper;
use App\StudentScore;
use App\Subjective;
use App\SubjectiveAnswer;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Encore\Admin\Facades\Admin;

class SubjectiveController extends AdminController
{
    // 主观题
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Subjective';

    /*
     *  列表
     *
     *  @return Content
     */
    public function index(Content $content)
    {
        $content->header('主观题管理');
        $content->description('列表');
        $content->breadcrumb(
            ['text' => '主观题管理']
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
        $content->header('主观题管理');
        $content->description('新建');
        $content->breadcrumb(
            ['text' => '主观题管理', 'url' => '/question/subjective'],
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
        $content->header('主观题管理');
        $content->description('编辑');
        $content->breadcrumb(
            ['text' => '主观题管理', 'url' => '/question/subjective'],
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
        $grid = new Grid(new Subjective());

        $grid->column('id', __('ID'))->sortable();
        $grid->column('paper_id', __('套卷'))->display(function ($paper_id) {
            $paper = Paper::find($paper_id);
            return $paper_id . '-' . $paper->classname . '-' . $paper->year;
        })->width(400)->sortable();
        $grid->column('sort', __('题序'))->sortable();
        $grid->column('title', __('题目描述'));
        $grid->column('score', __('分值'))->width(100);
        $grid->is_auto('是否自动评分')->using([1 => '是', 2 => '否'])->sortable();

        $grid->actions(function ($actions) {
            // 重写删除功能
            $actions->disableDelete();
            $actions->add(new DeleteSubjective());
        });

        // 筛选条件
        $grid->filter(function ($filter) {
            $filter->equal('paper_id', '套卷ID');
            $filter->like('title', '题目关键字');
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
        $content->header('主观题管理');
        $content->description('详情');
        $content->breadcrumb(
            ['text' => '主观题管理', 'url' => '/question/subjective'],
            ['text' => $id],
            ['text' => '详情']
        );
        $show = Admin::show(Subjective::findOrFail($id), function (Show $show) {
            $show->panel()->title('详情');
            $show->field('id', __('ID'));
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
            $show->field('title', __('题目描述'));
            $show->field('answer', __('答案'));
            $show->field('score', __('分值'));
            $show->field('is_auto',__('是否自动评分'))->using([1 => '是', 2 => '否']);
            $show->field('created_at', __('创建时间'));
            $show->field('updated_at', __('更新时间'));
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
            $paper_arr[$paper->id] = $paper->id . "-" . $paper->classname . "-" . $paper->year;
        }
        if (null != $id) {
            $question = Subjective::findOrFail($id);
            $form = new Form($question);
            $form->setAction('edit');
            $form->setTitle('编辑');
            $form->tools(function (Form\Tools $tools) {
                $url = Input::url();
                $url = substr($url, 0, -5);
                $tools->disableList();
                $tools->add("<a href='{$url}' class='btn btn-sm btn-primary' style='float: right'><i class='fa fa-eye'></i>&nbsp;查看</a>");
                $tools->add("<a href='/admin/question/subjective' class='btn btn-sm btn-default' style='float: right; margin-right: 5px'><i class='fa fa-list'></i>&nbsp;列表</a>");
            });
            $form->select('paper_id', __('套卷'))->options($paper_arr)->value($question->paper_id)->disable();
            $form->text('sort', __('题序'))->value($question->sort)->required();
            $form->text('title', __('题目描述'))->value($question->title)->required();
            $form->select('is_auto', __('是否开启主观题自动评分'))->options([1 => '是', 2 => '否'])->hideField('model')->value($question->is_auto)->required();
            $form->select('model', __('评分模式'))->options([1 => '关键词评分', 2 => '相似度评分'])->value($question->model == null ? 1 : $question->model)->help('关键词评分: 需给定关键词及分值<br>相似度评分: 需给定完整参考答案')->attribute('name', 'model')->required();
            $form->textarea('answer', __('答案'))->value($question->answer)->help('开启自动评分的<b style="color: darkred;">关键词评分</b>模式时，需使用<b style="color: darkred;">英文括号</b>将关键词分数标出，<b style="color: darkred;">顿号</b>分隔，答案格式如：<b style="color: darkred;">关键词1(5)、关键词2(3)</b><br>其余情况请填写完整参考答案')->required();
            $form->text('score', __('分值'))->value($question->score)->required();
        } else {
            $form = new Form(new Subjective);
            $form->setAction('create');
            $form->setTitle('新建');
            $question = null;
            $input = $_GET;
            if (isset($input['paper_id'])) {
                $form->select('paper_id', __('套卷'))->options([$input['paper_id'] => $paper_arr[$input['paper_id']]])->default($input['paper_id'])->required();
            } else {
                $form->select('paper_id', __('套卷'))->options($paper_arr)->required();
            }
            $form->hasMany('self', __('主观题'), function (Form\NestedForm $form) {
                $form->text('sort', __('题序'))->required();
                $form->text('title', __('题目描述'))->required();
                $form->select('is_auto', __('是否开启主观题自动评分'))->options([1 => '是', 2 => '否'])->default(1)->hideField1('model')->required();
                $form->select('model', __('评分模式'))->options([1 => '关键词评分', 2 => '相似度评分'])->default(1)->help('关键词评分: 需给定关键词及分值<br>相似度评分: 需给定完整参考答案')->attribute('name', 'model')->required();
                $form->textarea('answer', __('答案'))->help('开启自动评分的<b style="color: darkred;">关键词评分</b>模式时，需使用<b style="color: darkred;">英文括号</b>将关键词分数标出，<b style="color: darkred;">顿号</b>分隔，答案格式如：<b style="color: darkred;">关键词1(5)、关键词2(3)</b><br>其余情况请填写完整参考答案');
                $form->text('score', __('分值'));
            });
        }
        $form->footer(function ($footer) {
            $footer->disableCreatingCheck();
            $footer->disableViewCheck();
            $footer->disableEditingCheck();
        });
        return $form;
    }

    public function save($id)
    {
        if (!empty($_POST)) {
            $input = $_POST;
            if (null != $id) {
                if (empty($input['sort']) || empty($input['title']) || empty($input['answer'])) {
                    return admin_toastr('题序、题目描述、答案不能为空，请检查！', 'error', ['timeOut' => 2000]);
                }
                // 更新
                $info = Subjective::find($id);
                if (!($info instanceof Subjective)) {
                    return admin_toastr('该主观题不存在', 'error', ['timeOut' => 2000]);
                }
                $info->type = 3;
                $info->sort = $input['sort'];
                $info->title = $input['title'];
                $info->is_auto = $input['is_auto'] + 0;
                $info->model = $info->is_auto == 1 ? $input['model'] + 0 : null;
                if ($info->answer != $input['answer']) {
                    // 修改答案，更新原有学生答案的自动评分分数
                    $info->answer = $input['answer'];
                    $score = $info->score != ($input['score']+0) ? $input['score']+0 : $info->score;
                    if ($info->model != null) {
                        $this->updateAutoScore($id, $input['answer'], $score, $info->model);
                    }
                }
                $info->score = $input['score']+0;
                $res = $info->save();
                if ($res) {
                    return admin_toastr('修改成功', 'success', ['timeOut' => 2000]);
                } else {
                    return admin_toastr('修改失败', 'error', ['timeOut' => 2000]);
                }
            } else {
                // 新增
                DB::beginTransaction();
                try {
                    $time = date("Y-m-d H:i:s", time() + 3600 * 8);
                    $info_arr = [];
                    if (empty($input['self'])) {
                        return admin_toastr("请填写主观题信息！", 'error');
                    } else {
                        foreach ($input['self'] as $info) {
                            $data = [
                                'paper_id' => $input['paper_id'],
                                'type' => 3,
                                'sort' => $info['sort'],
                                'title' => $info['title'],
                                'answer' => $info['answer'],
                                'score' => $info['score'],
                                'is_auto' => $info['is_auto'] + 0,
                                'model' => $info['is_auto'] == 1 ? $info['model'] + 0 : null,
                                'created_at' => $time,
                                'updated_at' => $time,
                            ];
                            $info_arr[] = $data;
                        }
                        if (!empty($info_arr)) {
                            DB::table('question')->insert($info_arr);
                        }
                    }
                    DB::commit();
                } catch (\Illuminate\Database\QueryException $ex) {
                    DB::rollBack();
                    return admin_toastr("添加失败：" . $ex, 'error');
                }
                return admin_toastr('添加成功', 'success', ['timeOut' => 2000]);
            }
        }
    }

    // 更新自动评分分数
    public function updateAutoScore($id, $answer, $score, $model)
    {
        $stu_answers = SubjectiveAnswer::where('question_id', $id)->get();
        DB::beginTransaction();
        try {
            foreach ($stu_answers as $stu) {
                $auto_score = 0;
                if ($model == 1) {
                    // 关键词评分模式
                    $auto_score = $this->getKeyScore($answer, $stu->answer);
                } else {
                    // 相似度评分模式
                    $sim = $this->getSim($answer, $stu->answer, $stu->paper_id.'_'.$stu->sort, substr($stu->username, -4));
                    $auto_score = round(($score + 0) * $sim, 2);
                }
                $old_score = $stu->score;
                $stu->score = ($stu->auto_score == $stu->score ? $auto_score : $stu->score);
                $new_score = $stu->score;
                $stu->auto_score = $auto_score;
                $stu->save();
                $stu_score = StudentScore::find($stu->score_id);
                $stu_score->subjective_score += ($new_score-$old_score);
                $stu_score->score += ($new_score-$old_score);
                $stu_score->save();
            }
            DB::commit();
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollBack();
            admin_toastr("操作失败：" . $ex, 'error');
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
}
