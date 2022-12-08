<?php

namespace App\Admin\Controllers;

use Illuminate\Support\MessageBag;

use App\Http\Controllers\Controller;

use Encore\Admin\Controllers\AdminController;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;

use App\SomsStoragePeriod;
use App\SomsItem;

use App\Admin\Actions\Post\ManageItem;

use Log;

class SomsStoragePeriodController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title;

    public function index(Content $content)
    {
        $content->title(__('somsstorageperiod.title'));
        $content->description(__('somsstorageperiod.title'));

        $content->body($this->grid());

        return $content;
    }

    // public function list(Content $content)
    // {
    //     $uid = request()->get('uid');
    //
    //     $content->title('所有學生');
    //     $content->description('現時大學: '.$uid);
    //
    //     $content->body($this->grid($uid));
    //
    //     return $content;
    // }
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SomsStoragePeriod);

        $grid->filter(function($filter){
            // Remove the default id filter
            $filter->disableIdFilter();
            // Add a column filter
            $filter->column(1/2, function ($filter) {
                $filter->like('code', __('somsstorageperiod.code'));
                $filter->like('name', __('somsstorageperiod.name'));
            });
            // $filter->column(1/2, function ($filter) {
            //     $filter->between('effective_from', __('somsclient.effective_from'))->date();
            //     $filter->between('effective_to', __('somsclient.effective_to'))->date();
            // });
        });

        //$grid->column('id')->sortable();
        $grid->column('code', __('somsstorageperiod.code'))->sortable()->display(function ($code) {
            return "<a href='".url('/admin/soms/storageperiods/'.$this->id.'/edit')."'>$code</a>";
        });
        $grid->column('name', __('somsstorageperiod.name'))->sortable();
        // $grid->column('period_type', __('somsstorageperiod.period_type'))->sortable();
        $grid->column('min', __('somsstorageperiod.min'))->sortable();
        $grid->column('max', __('somsstorageperiod.max'))->sortable();

        $grid->updated_at()->sortable();

        // $grid->column('Action')-

        $grid->disableExport();

        $grid->actions(function ($actions) {

            // $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableView();
            //
            $actions->add(new ManageItem);

        });

        // $grid->disableCreateButton();

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(SomsStoragePeriod::findOrFail($id));

        $show->field('id', __('ID'));
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
        $form = new Form(new SomsStoragePeriod);

        // $form->disableEditingCheck();
        //
        // $form->disableCreatingCheck();
        //
        // $form->disableViewCheck();
        //
        // $form->tools(function (Form\Tools $tools) {
        //     $tools->disableDelete();
        //     $tools->disableView();
        //     $tools->disableList();
        // });

        $form->display('id', __('ID'));

        $form->text('code', __('somsstorageperiod.code'))->rules('required');
        $form->text('name', __('somsstorageperiod.name'))->rules('required');

        $form->hidden('period_type')->default('month');

        $form->text('min', __('somsstorageperiod.min'))->rules('nullable|required_without:max|numeric|min:0');
        $form->text('max', __('somsstorageperiod.max'))->rules('nullable|required_without:min|numeric|min:0');

        $form->display('created_at', __('somsstorageperiod.created_at'));
        $form->display('updated_at', __('somsstorageperiod.updated_at'));

        return $form;
    }
}
