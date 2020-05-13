<?php

namespace App\Admin\Controllers\Teacher;

use App\Admin\Actions\Paper\Delete;
use App\Paper;
use App\Selection;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class SelectionController extends AdminController
{
    // 选择题
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Selection';

    /*
     *  列表
     *
     *  @return Content
     */
    public function index(Content $content)
    {
        $content->header('选择题管理');
        $content->description('列表');
        $content->breadcrumb(
            ['text' => '选择题管理']
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
        $content->header('选择题管理');
        $content->description('新建');
        $content->breadcrumb(
            ['text' => '选择题管理', 'url' => '/question/selection'],
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
        $content->header('选择题管理');
        $content->description('编辑');
        $content->breadcrumb(
            ['text' => '选择题管理', 'url' => '/question/selection'],
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
        $grid = new Grid(new Selection);

        $grid->column('id', __('ID'))->sortable();
        $grid->column('paper_id', __('套卷'))->display(function ($paper_id) {
            $paper = Paper::find($paper_id);
            return $paper_id.'-'.$paper->classname . '-' . $paper->year;
        })->sortable();
        $grid->column('sort', __('题序'))->sortable();
        $grid->column('title', __('题目描述'));
        $grid->column('answer', __('答案'));
        $grid->column('score', __('分值'));

        // 禁用导出键
        $grid->disableExport();

        $grid->actions(function ($actions) {
            // 重写删除功能
            $actions->disableDelete();
            $actions->add(new Delete());
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
        $content->header('选择题管理');
        $content->description('详情');
        $content->breadcrumb(
            ['text' => '选择题管理', 'url' => '/question/selection'],
            ['text' => $id],
            ['text' => '详情']
        );
        $show = Admin::show(Selection::findOrFail($id), function (Show $show) {
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
            $show->field('option1', __('选项A'));
            $show->field('option2', __('选项B'));
            $show->field('option3', __('选项C'));
            $show->field('option4', __('选项D'));
            $show->field('answer', __('答案'));
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
            $question = Selection::findOrFail($id);
            $form = new Form($question);
            $form->setAction('edit');
            $form->setTitle('编辑');
            $form->tools(function (Form\Tools $tools) {
                $url = Input::url();
                $url = substr($url, 0, -5);
                $tools->disableList();
                $tools->add("<a href='{$url}' class='btn btn-sm btn-primary' style='float: right'><i class='fa fa-eye'></i>&nbsp;查看</a>");
                $tools->add("<a href='/admin/question/selection' class='btn btn-sm btn-default' style='float: right; margin-right: 5px'><i class='fa fa-list'></i>&nbsp;列表</a>");
            });
            $form->select('paper_id', __('套卷'))->options($paper_arr)->value($question->paper_id)->disable();
            $form->text('sort', __('题序'))->value($question->sort)->required();
            $form->text('title', __('题目描述'))->value($question->title)->required();
            $form->text('option1', __('选项A'))->value($question->option1)->required();
            $form->text('option2', __('选项B'))->value($question->option2)->required();
            $form->text('option3', __('选项C'))->value($question->option3)->required();
            $form->text('option4', __('选项D'))->value($question->option4)->required();
            $form->select('answer', __('答案'))->options(['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D'])->value($question->answer)->required();
            $form->text('score', __('分值'))->value($question->score)->required();
        } else {
            $form = new Form(new Selection);
            $form->setAction('create');
            $form->setTitle('新建');
            $question = null;
            $input = $_GET;
            if (isset($input['paper_id'])) {
                $form->select('paper_id', __('套卷'))->options([$input['paper_id'] => $paper_arr[$input['paper_id']]])->default($input['paper_id'])->required();
            } else {
                $form->select('paper_id', __('套卷'))->options($paper_arr)->required();
            }
            $form->hasMany('self', __('选择题'), function (Form\NestedForm $form) {
                $form->text('sort', __('题序'))->required();
                $form->text('title', __('题目描述'))->required();
                $form->text('option1', __('选项A'))->required();
                $form->text('option2', __('选项B'))->required();
                $form->text('option3', __('选项C'))->required();
                $form->text('option4', __('选项D'))->required();
                $form->select('answer', __('答案'))->options(['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D'])->required();
                $form->text('score', __('分值'))->required();
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
                // 更新
                $info = Selection::find($id);
                if (!($info instanceof Selection)) {
                    admin_toastr('该选择题不存在', 'error', ['timeOut' => 2000]);
                }
                $info->type = 1;
                $info->sort = $input['sort'];
                $info->title = $input['title'];
                $info->option1 = $input['option1'];
                $info->option2 = $input['option2'];
                $info->option3 = $input['option3'];
                $info->option4 = $input['option4'];
                $info->answer = $input['answer'];
                $info->score = $input['score'];
                $res = $info->save();
                if ($res) {
                    admin_toastr('修改成功', 'success', ['timeOut' => 2000]);
                } else {
                    admin_toastr('修改失败', 'error', ['timeOut' => 2000]);
                }
            } else {
                // 新增
                DB::beginTransaction();
                try {
                    $time = date("Y-m-d H:i:s", time()+3600*8);
                    $info_arr = [];
                    if (empty($input['self'])) {
                        admin_toastr("请填写选择题信息！", 'error');
                    } else {
                        foreach ($input['self'] as $info) {
                            $data = [
                                'paper_id' => $input['paper_id'],
                                'type' => 1,
                                'sort' => $info['sort'],
                                'title' => $info['title'],
                                'option1' => $info['option1'],
                                'option2' => $info['option2'],
                                'option3' => $info['option3'],
                                'option4' => $info['option4'],
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
                    }
                    DB::commit();
                } catch (\Illuminate\Database\QueryException $ex) {
                    DB::rollBack();
                    admin_toastr("添加失败：".$ex, 'error');
                }
                admin_toastr('添加成功', 'success', ['timeOut' => 2000]);
            }
        }
    }

}
