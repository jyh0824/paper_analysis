<?php

namespace App\Admin\Controllers\Common;

use App\Feedback;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;
use Encore\Admin\Grid\Displayers\Actions;

class MyFeedbackController extends AdminController
{
    // 我的反馈(教师/学生)
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Feedback';

    public function index(Content $content)
    {
        $id = Admin::user()->id;
        $content->header('我的反馈');
        $content->description(date("Y年m月d日"));
        $content->breadcrumb(
            ['text' => '我的反馈']
        );
        $grid = new Grid(new Feedback);

        $grid->model()->where('uid', '=', $id);
        $grid->column('id', __('ID'));
        $grid->column('content', __('反馈内容'));
        $grid->column('status', __('状态'))->display(function ($status) {
            return $status ? "<i class=\"fa fa-check\" style='color: darkgreen;'></i>已处理" : "<i class=\"fa fa-times\" style='color: darkred;'></i>未处理";
        });
        $grid->column('remark', __('处理备注'));
        $grid->column('created_at', __('创建时间'))->hide();
        $grid->column('updated_at', __('更新时间'));

        // 禁用导出键
        $grid->disableExport();
        $grid->actions(function ($actions) {
            // 去掉删除
            $actions->disableDelete();
            // 去掉编辑
            $actions->disableEdit();
        });

        $content->body($grid);

        return $content;
    }

    // 新增
    public function create(Content $content)
    {
        $this->save();
        $content->header('我的反馈-新建');
        $content->description(date("Y年m月d日"));
        $content->breadcrumb(
            ['text' => '我的反馈', 'url' => '/feedback'],
            ['text' => '新建']
        );
        $content->body($this->form());
        return $content;
    }

    // 详情
    public function detail($id)
    {
        $show = new Show(Feedback::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('content', __('反馈内容'));
        $show->field('status', __('状态'))->using([0 => '未处理', 1 => '已处理']);
        $show->field('remark', __('处理备注'));
        $show->field('created_at', __('创建时间'));
        $show->field('updated_at', __('更新时间'));

        $show->panel()->tools(function ($tools) {
            $tools->disableDelete();
            $tools->disableEdit();
        });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Feedback);

        $form->setAction('create');
        $form->textarea('content', __('反馈内容'))->required();

        return $form;
    }

    // 数据处理
    public function save()
    {
        if (!empty($_POST)) {
            $input = $_POST;
            $res = Feedback::create([
                'uid' => Admin::user()->id,
                'content' => $input['content'],
                'status' => 0,
            ]);
            if ($res) {
                admin_toastr('反馈成功', 'success', ['timeOut' => 2000]);
            } else {
                admin_toastr('反馈失败', 'error', ['timeOut' => 2000]);
            }
        }
    }
}
