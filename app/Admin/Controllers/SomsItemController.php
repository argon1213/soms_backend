<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;

use Encore\Admin\Controllers\AdminController;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;

use App\SomsItem;

use Log;

class SomsItemController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '所有產品';

    public function index(Content $content)
    {
        $content->title($this->title);
        $content->description($this->title);

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
        $grid = new Grid(new SomsItem);

        $grid->filter(function($filter){
            // Remove the default id filter
            $filter->disableIdFilter();
            // Add a column filter
            $filter->column(1/2, function ($filter) {
                $filter->like('name', __('somsitem.name'));
                $filter->like('name_cn', __('somsitem.name_cn'));
            });
        });

        //$grid->column('id')->sortable();
        $grid->column('name', __('somsitem.name'))->sortable()->display(function ($code) {
            return "<a href='".url('/admin/soms/items/'.$this->id.'/edit')."'>$code</a>";
        });
        $grid->column('name_cn', __('somsitem.name_cn'))->sortable();

        $grid->updated_at()->sortable();

        // $grid->column('Action')-

        $grid->disableExport();
        $grid->disableActions();
        $grid->disableCreateButton();

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
        $show = new Show(SomsPromotion::findOrFail($id));

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
        $form = new Form(new SomsItem);

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

        $form->tab('Basic', function (Form $form) {

            $form->display('id', __('ID'));

            $form->text('name', __('somsitem.name'))->rules('required');
            $form->text('name_cn', __('somsitem.name_cn'))->rules('required');

            $form->multipleImage('images', 'Item Image')->pathColumn('test')->removable();

            $form->display('created_at', __('somspromotion.created_at'));
            $form->display('updated_at', __('somspromotion.updated_at'));

        });

        // $form->ignore(['password_confirmation']);

        // $form->saved(function(Form $form){
        //   $uid = $form->model()->university_id;
        //   // Log::debug('uid: '.$uid);
        //   // return '404';
        //   admin_toastr(__('更新成功'));
        //   return redirect('/admin/soms/orders'.(($uid != null)?'?uid='.$uid:''));
        // });

        return $form;
    }
}
