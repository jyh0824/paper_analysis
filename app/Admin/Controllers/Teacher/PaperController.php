<?php

namespace App\Admin\Controllers\Teacher;

use App\Admin\Actions\Paper\DeletePaper;
use App\Admin\Actions\Paper\Analysis;
use App\Admin\Actions\Paper\Delete;
use App\Admin\Actions\Paper\DeleteJudgement;
use App\Admin\Actions\Paper\DeleteSubjective;
use App\AdminUser;
use App\Paper;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class PaperController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Paper';

    /*
     *  列表
     *
     *  @return Content
     */
    public function index(Content $content)
    {
        $content->header('套卷管理');
        $content->description('列表');
        $content->breadcrumb(
            ['text' => '套卷管理']
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
        $this->checkData(null);
        $content->header('套卷管理');
        $content->description('新建');
        $content->breadcrumb(
            ['text' => '套卷管理', 'url' => '/paper'],
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
        $this->checkData($id);
        $content->header('套卷管理');
        $content->description('编辑');
        $content->breadcrumb(
            ['text' => '套卷管理', 'url' => '/paper'],
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
        $grid = new Grid(new Paper);

        $grid->column('id', __('套卷ID'))->sortable();
        $grid->column('classname', __('课程名称'));
        $grid->column('year', __('年份'))->sortable();
        $grid->column('uid', __('操作人'))->display(function ($uid) {
            $name = AdminUser::where('id', '=', $uid)->first(['name']);
            return $name->name;
        });
        $grid->column('created_at', __('创建时间'))->sortable();
        $grid->column('updated_at', __('更新时间'))->sortable();

        // 禁用导出键
        $grid->disableExport();

        // 筛选条件
        $grid->filter(function ($filter) {
            // 年份
            $filter->equal('year');
        });

        // 添加 成绩分析 行操作
        $grid->actions(function ($actions) {
            $actions->add(new Analysis());
            $actions->disableDelete();
            $actions->add(new DeletePaper());
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
        $body = Admin::show(Paper::findOrFail($id), function (Show $show) {
            $show->id('套卷ID');
            $show->classname('课程名称');
            $show->year('年份');
            $show->uid('操作人id');
            $show->created_at('创建时间');
            $show->updated_at('更新时间');
            $show->selection('选择题', function ($selection) {
                $selection->resource('/admin/question/selection');
                $selection->id('ID');
                $selection->sort('序号');
                $selection->title('题目描述');
                $selection->answer('答案');
                $selection->score('分值');
                $selection->disableExport();
                $selection->disableFilter();
                $selection->actions(function ($actions) {
                    // 重写删除功能
                    $actions->disableDelete();
                    $actions->add(new Delete);
                });
            });
            $show->judgement('判断题', function ($judge) {
                $judge->resource('/admin/question/judgement');
                $judge->id('ID');
                $judge->sort('序号');
                $judge->title('题目描述');
                $judge->answer('答案')->display(function ($answer) {
                    $res = '';
                    if ($answer == 1) {
                        $res = '√';
                    } else if ($answer == 2) {
                        $res = '×';
                    }
                    return $res;
                });
                $judge->score('分值');
                $judge->disableExport();
                $judge->disableFilter();
                $judge->actions(function ($actions) {
                    // 重写删除功能
                    $actions->disableDelete();
                    $actions->add(new DeleteJudgement());
                });
            });
            $show->subjective('主观题', function ($subjective) {
                $subjective->resource('/admin/question/subjective');
                $subjective->id('ID');
                $subjective->sort('序号');
                $subjective->title('题目描述');
                $subjective->score('分值');
                $subjective->is_auto('是否自动评分')->using([1 => '是', 2 => '否']);
                $subjective->disableExport();
                $subjective->disableFilter();
                $subjective->actions(function ($actions) {
                    // 重写删除功能
                    $actions->disableDelete();
                    $actions->add(new DeleteSubjective());
                });
            });
            $show->panel()->tools(function ($tools) {
                $tools->disableDelete();
            });
        });

        $content->header('套卷管理');
        $content->description('详情');
        $content->breadcrumb(
            ['text' => '套卷管理', 'url' => '/paper'],
            ['text' => $id],
            ['text' => '详情']
        );
        $content->body($body);

        return $content;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form($id)
    {
        if ($id != null) {
            $paper = Paper::findOrFail($id);
            $value = [
                'classname' => $paper->classname,
                'year' => $paper->year,
            ];
            $form = new Form($paper);
            $form->setAction('edit');
            $form->setTitle('编辑');
            $form->tools(function (Form\Tools $tools) {
                $url = Input::url();
                $url = substr($url, 0, -5);
                $tools->disableList();
                $tools->add("<a href='{$url}' class='btn btn-sm btn-primary' style='float: right'><i class='fa fa-eye'></i>&nbsp;查看</a>");
                $tools->add("<a href='/admin/paper' class='btn btn-sm btn-default' style='float: right; margin-right: 5px'><i class='fa fa-list'></i>&nbsp;列表</a>");
            });
            $form->text('ID', __('套卷ID'))->disable()->value($id);
            $form->text('classname', __('课程名称'))->required()->value($value['classname']);
            $form->date('year', __('年份'))->width('')->format('YYYY')->required()->value($value['year']);
        } else {
            $form = new Form(new Paper);
            $form->setAction('create');
            $form->text('classname', __('课程名称'))->required();
            $form->date('year', __('年份'))->width('')->format('YYYY')->required();
            $form->divider();
        }
        $form->hasMany('selection', __('选择题'), function (Form\NestedForm $form) {
            $form->text('sort', __('序号'))->required();
            $form->text('title', __('题目描述'))->required();
            $form->text('option1', __('选项A'))->required();
            $form->text('option2', __('选项B'))->required();
            $form->text('option3', __('选项C'))->required();
            $form->text('option4', __('选项D'))->required();
            $form->select('answer', __('答案'))->options(['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D'])->required();
            $form->number('score', __('分值'))->required();
        });
        $form->hasMany('judgement', __('判断题'), function (Form\NestedForm $form) {
            $form->text('sort', __('序号'))->required();
            $form->text('title', __('题目描述'))->required();
            $form->select('answer', __('答案'))->options([1 => '√', 2 => '×'])->required();
            $form->number('score', __('分值'))->required();
        });
        $form->hasMany('subjective', __('主观题'), function (Form\NestedForm $form) {
            $form->text('sort', __('序号'))->required();
            $form->text('title', __('题目描述'))->required();
            $form->select('is_auto', __('是否开启主观题自动评分'))->options([1 => '是', 2 => '否'])->default(1)->hideField1('model')->required();
            $form->select('model', __('评分模式'))->options([1 => '关键词评分', 2 => '相似度评分'])->default(1)->help('关键词评分: 需给定关键词及分值<br>相似度评分: 需给定完整参考答案')->attribute('name', 'model')->required();
//            $form->text('point', __('得分点（得分点关键字后英文括号中写入关键点分值，例：关键字1(3)、关键字2(1)）'));
            $form->textarea('answer', __('答案'))->help('开启自动评分的<b style="color: darkred;">关键词评分</b>模式时，需使用<b style="color: darkred;">英文括号</b>将关键词分数标出，<b style="color: darkred;">顿号</b>分隔，答案格式如：<b style="color: darkred;">关键词1(5)、关键词2(3)</b><br>其余情况请填写完整参考答案')->required();
            $form->number('score', __('分值'))->required();
        });
        $form->footer(function ($footer) {
            $footer->disableCreatingCheck();
            $footer->disableViewCheck();
            $footer->disableEditingCheck();
        });

        return $form;
    }

    public function checkData($id)
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
                    $paper = Paper::find($id);
                    if (!($paper instanceof Paper)) {
                        $error_msg = "该试卷不存在";
                    }
                    $questions = $this->getQuestions($id, $input, $time);
                    $paper->year = $input['year'];
                    $paper->classname = $input['classname'];
                    $paper->uid = Admin::user()->id;
                    $paper->save();
                    if (!empty($questions)) {
                        DB::table('question')->insert($questions);
                    }
                    $success_msg = "编辑成功";
                } else {
                    // 新增
                    $check = Paper::where(['classname' => $input['classname'], 'year' => $input['year']])->first();
                    if ($check instanceof Paper) {
                        $error_msg = "试卷已存在";
                    } else {
                        $insert = [
                            'classname' => $input['classname'],
                            'year' => $input['year'],
                            'uid' => Admin::user()->id,
                            'created_at' => $time,
                            'updated_at' => $time,
                        ];
                        $paper_id = DB::table('paper')->insertGetId($insert);
                        if ($paper_id) {
                            $questions = $this->getQuestions($paper_id, $input, $time);
                            //批量插入question表
                            if (!empty($questions)) {
                                DB::table('question')->insert($questions);
                            }
                        }
                        $success_msg = "新增成功";
                    }
                }
                DB::commit();
            } catch (\Illuminate\Database\QueryException $ex) {
                DB::rollBack();
                admin_toastr('操作失败:' . $ex, 'error', ['timeOut' => 2000]);
            }
            if ($error_msg != null) {
                admin_toastr($error_msg, 'error', ['timeOut' => 2000]);
            } else {
                admin_toastr($success_msg, 'success', ['timeOut' => 2000]);
            }
        }
    }

    public function getQuestions($paper_id, $input, $time)
    {
        $info = [];
        //选择题
        $selection = isset($input['selection']) ? $input['selection'] : null;
        if (!empty($selection)) {
            foreach ($selection as $select) {
//                if ($select['title'] != '' && $select['answer'] != '' && $select['score']) {
                    $data = [
                        'paper_id' => $paper_id,
                        'type' => 1,
                        'title' => $select['title'],
                        'option1' => $select['option1'],
                        'option2' => $select['option2'],
                        'option3' => $select['option3'],
                        'option4' => $select['option4'],
                        'answer' => $select['answer'],
                        'analysis' => null,
                        'score' => $select['score'],
                        'sort' => $select['sort'],
                        'is_auto' => null,
                        'model' => null,
                        'created_at' => $time,
                        'updated_at' => $time,
                    ];
                    $info[] = $data;
//                }
            }
        }
        //判断题
        $judgement = isset($input['judgement']) ? $input['judgement'] : null;
        if (!empty($judgement)) {
            foreach ($judgement as $item) {
//                if ($item['title'] != '' && $item['answer'] != '' && $item['score'] != '') {
                    $data = [
                        'paper_id' => $paper_id,
                        'type' => 2,
                        'title' => $item['title'],
                        'option1' => null,
                        'option2' => null,
                        'option3' => null,
                        'option4' => null,
                        'answer' => $item['answer'],
                        'analysis' => null,
                        'score' => $item['score'],
                        'sort' => $item['sort'],
                        'is_auto' => null,
                        'model' => null,
                        'created_at' => $time,
                        'updated_at' => $time,
                    ];
                    $info[] = $data;
//                }
            }
        }
        //主观题
        $subjective = isset($input['subjective']) ? $input['subjective'] : null;
        if (!empty($subjective)) {
            foreach ($subjective as $value) {
//                if ($value['title'] != '' && $value['answer'] != '' && $value['score'] != '') {
                    $data = [
                        'paper_id' => $paper_id,
                        'type' => 3,
                        'title' => $value['title'],
                        'option1' => null,
                        'option2' => null,
                        'option3' => null,
                        'option4' => null,
                        'analysis' => null,
                        'answer' => $value['answer'],
                        'score' => $value['score'],
                        'sort' => $value['sort'],
                        'is_auto' => $value['is_auto'] + 0,
                        'model' => $value['is_auto'] == 1 ? $value['model'] + 0 : null,
                        'created_at' => $time,
                        'updated_at' => $time,
                    ];
                    $info[] = $data;
//                }
            }
        }

        return $info;
    }
}
