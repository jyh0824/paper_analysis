<?php

namespace App\Admin\Controllers\Teacher;

use App\Judgement;
use App\Paper;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Encore\Admin\Facades\Admin;

class JudgementController extends AdminController
{
    // 判断题
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Judgement';

    /*
     *  列表
     *
     *  @return Content
     */
    public function index(Content $content)
    {
        $content->header('判断题管理');
        $content->description(date("Y年m月d日"));
        $content->breadcrumb(
            ['text' => '判断题管理']
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
        $content->header('判断题管理-新建');
        $content->description(date("Y年m月d日"));
        $content->breadcrumb(
            ['text' => '判断题管理', 'url' => '/question/judgement'],
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
        $content->header('判断题管理-编辑');
        $content->description(date("Y年m月d日"));
        $content->breadcrumb(
            ['text' => '判断题管理', 'url' => '/question/judgement'],
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
        $grid = new Grid(new Judgement);

        $grid->column('id', __('ID'))->sortable();
        $grid->column('paper_id', __('套卷'))->display(function ($paper_id) {
            $paper = Paper::find($paper_id);
            return $paper_id.'-'.$paper->classname.'-'.$paper->year;
        })->width(400)->sortable();
        $grid->column('sort', __('题序'))->sortable();
        $grid->column('title', __('题目描述'));
        $grid->column('answer', __('答案'))->display(function ($answer) {
            $res = '';
            if ($answer == 1) {
                $res = '√';
            } else if ($answer == 2) {
                $res = '×';
            }
            return $res;
        });
        $grid->column('score', __('分值'))->width(100);

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
        $content->header('主观题管理-详情');
        $content->description(date("Y年m月d日"));
        $content->breadcrumb(
            ['text' => '主观题管理', 'url' => '/question/judgement'],
            ['text' => $id],
            ['text' => '详情']
        );
        $show = Admin::show(Judgement::findOrFail($id), function (Show $show) {
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
            $show->field('answer', __('答案'))->as(function ($answer) {
                $res = '';
                if ($answer == 1) {
                    $res = '√';
                } else if ($answer == 2) {
                    $res = '×';
                }
                return $res;
            });
            $show->field('score', __('分值'));
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
            $paper_arr[$paper->id] =  $paper->id . "-" . $paper->classname . "-" . $paper->year;
        }
        if (null != $id) {
            $question = Judgement::findOrFail($id);
            $form = new Form($question);
            $form->setAction('edit');
            $form->setTitle('编辑');
            $form->tools(function (Form\Tools $tools) {
                $url = Input::url();
                $url = substr($url, 0, -5);
                $tools->disableList();
                $tools->add("<a href='{$url}' class='btn btn-sm btn-primary' style='float: right'><i class='fa fa-eye'></i>&nbsp;查看</a>");
                $tools->add("<a href='/admin/question/judgement' class='btn btn-sm btn-default' style='float: right; margin-right: 5px'><i class='fa fa-list'></i>&nbsp;列表</a>");
            });
            $form->select('paper_id', __('套卷'))->options($paper_arr)->value($question->paper_id)->disable();
            $form->text('sort', __('题序'))->value($question->sort);
            $form->text('title', __('题目描述'))->value($question->title);
            $form->select('answer', __('答案'))->options([1 => '√', 2 => '×'])->value($question->answer);
            $form->text('score', __('分值'))->value($question->score);
        } else {
            $form = new Form(new Judgement);
            $form->setAction('create');
            $form->setTitle('新建');
            $input = $_GET;
            if (isset($input['paper_id'])) {
                $form->select('paper_id', __('套卷'))->options([$input['paper_id'] => $paper_arr[$input['paper_id']]])->default($input['paper_id'])->required();
            } else {
                $form->select('paper_id', __('套卷'))->options($paper_arr)->required();
            }
            $form->hasMany('self', __('判断题'), function (Form\NestedForm $form) {
                $form->text('sort', __('题序'));
                $form->text('title', __('题目描述'));
                $form->select('answer', __('答案'))->options([1 => '√', 2 => '×']);
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
        $error_msg = null;
        $success_msg = null;
        if (!empty($_POST)) {
            $input = $_POST;
            if (null != $id) {
                // 更新
                $info = Judgement::find($id);
                if (!($info instanceof Judgement)) {
                    $error_msg = "该判断题不存在";
                }
                $info->type = 2;
                $info->sort = $input['sort'];
                $info->title = $input['title'];
                $info->answer = $input['answer'];
                $info->score = $input['score'];
                $res = $info->save();
                if ($res) {
                    $success_msg = "编辑成功";
                } else {
                    $error_msg = "编辑失败";
                }
            } else {
                // 新增
                DB::beginTransaction();
                try {
                    $time = date("Y-m-d H:i:s", time()+3600*8);
                    $info_arr = [];
                    if (empty($input['self'])) {
                        $error_msg = "请填写判断题信息";
                    } else {
                        foreach ($input['self'] as $info) {
                            $data = [
                                'paper_id' => $input['paper_id'],
                                'type' => 2,
                                'sort' => $info['sort'],
                                'title' => $info['title'],
                                'answer' => $info['answer'],
                                'score' => $info['score'],
                                'created_at' => $time,
                                'updated_at' => $time,
                            ];
                            $info_arr[] = $data;
                        }
                        if (!empty($info_arr)) {
                            DB::table('question')->insert($info_arr);
                        }
                        $success_msg = "新增成功";
                    }
                    DB::commit();
                } catch (\Illuminate\Database\QueryException $ex) {
                    DB::rollBack();
                    admin_toastr("操作失败：".$ex, 'error');
                }
                if ($error_msg != null) {
                    admin_toastr($error_msg, 'error', ['timeOut' => 2000]);
                } else {
                    admin_toastr($success_msg, 'success', ['timeOut' => 2000]);
                }
            }
        }
    }
}
