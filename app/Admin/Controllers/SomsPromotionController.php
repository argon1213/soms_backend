<?php

namespace App\Admin\Controllers;

use Illuminate\Support\MessageBag;

use App\Http\Controllers\Controller;

use Encore\Admin\Controllers\AdminController;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;

use App\SomsPromotion;
use App\SomsItem;

use Log;

class SomsPromotionController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title;

    public function index(Content $content)
    {
        $content->title(__('somspromotion.title'));
        $content->description(__('somspromotion.title'));

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
        $grid = new Grid(new SomsPromotion);

        $grid->filter(function($filter){
            // Remove the default id filter
            $filter->disableIdFilter();
            // Add a column filter
            $filter->column(1/2, function ($filter) {
                $filter->like('code', __('somspromotion.code'));
                $filter->like('name', __('somspromotion.name'));
            });
            $filter->column(1/2, function ($filter) {
                $filter->between('effective_from', __('somsclient.effective_from'))->date();
                $filter->between('effective_to', __('somsclient.effective_to'))->date();
            });
        });

        //$grid->column('id')->sortable();
        $grid->column('code', __('somspromotion.code'))->sortable()->display(function ($code) {
            return "<a href='".url('/admin/soms/promotions/'.$this->id.'/edit')."'>$code</a>";
        });
        $grid->column('name', __('somspromotion.name'))->sortable();
        $grid->column('effective_from', __('somspromotion.effective_from'))->sortable();
        $grid->column('effective_to', __('somspromotion.effective_to'))->sortable();

        $grid->updated_at()->sortable();

        $grid->disableExport();

        $grid->actions(function ($actions) {

            // $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableView();

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
        $form = new Form(new SomsPromotion);

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

            $form->text('code', __('somspromotion.code'))->rules('required');
            $form->text('name', __('somspromotion.name'))->rules('required');

            $form->date('effective_from', __('somspromotion.effective_from'))->required();
            $form->date('effective_to', __('somspromotion.effective_to'))->required();

            $form->display('created_at', __('somspromotion.created_at'));
            $form->display('updated_at', __('somspromotion.updated_at'));

        });

        $form->tab('Item', function (Form $form) {

          $form->hasMany('items', function (Form\NestedForm $nestedForm) {

            $nestedForm->select('item_id', __('somsorder.item_id'))->options(function () {
                return SomsItem::get()->pluck('display_name', 'id');
            })->required();

            $nestedForm->text('price', __('somsorder.item_price'))->required();

          });

        });

        $form->saving(function (Form $form) {
          // Get Exist Request Data
          $items = \request()->input('items');
          // Log::debug(json_encode($items));
          $temp = array();
          //
          foreach ($items as $item) {
            //
            if($item['_remove_'])
              continue;

            if (in_array($item['item_id'], $temp, true)) {
              $error = new MessageBag([
                      'title'   => '設定錯誤',
                      'message' => '不能為同一產品設定不同價錢',
                  ]);

              return back()->withInput(\request()->input())->with(compact('error'));
            }else{
              $temp[] = $item['item_id'];
            }
          }
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
