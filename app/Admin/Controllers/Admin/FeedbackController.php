<?php

namespace App\Admin\Controllers\Admin;

use App\Admin\Actions\Feedback\FeedbackRemark;
use App\AdminUser;
use App\Feedback;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;

class FeedbackController extends AdminController
{
    // 反馈管理
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Feedback';

    /*
         *  列表
         *
         *  @return Content
         */
    public function index(Content $content)
    {
        $content->header('反馈管理');
        $content->description(date("Y年m月d日"));
        $content->breadcrumb(
            ['text' => '反馈管理']
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
        $grid = new Grid(new Feedback);

        $grid->column('id', __('ID'))->sortable();
        $grid->column('uid', __('反馈用户'))->display(function ($uid) {
            $user = AdminUser::find($uid);
            return $user->name;
        })->sortable();
        $grid->column('content', __('反馈内容'));
        $grid->column('status', __('状态'))->action(\App\Admin\Actions\Feedback\Feedback::class)->sortable();
        $grid->column('remark', __('处理备注'));
        $grid->column('created_at', __('创建时间'))->sortable();
        $grid->column('updated_at', __('更新时间'))->sortable();

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
            // 新增备注添加
            $actions->add(new FeedbackRemark());
        });

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
        $show = new Show(Feedback::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('uid', __('反馈用户'))->as(function ($uid) {
            $user = AdminUser::find($uid);
            return $user->name;
        });
        $show->field('content', __('反馈内容'));
        $show->field('status', __('状态'))->using([0 => '未处理', 1 => '已处理']);
        $show->field('remark', __('处理备注'));
        $show->field('created_at', __('创建时间'));
        $show->field('updated_at', __('更新时间'));

        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableDelete();
            });;

        return $show;
    }
}
