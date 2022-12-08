<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;

use Encore\Admin\Admin;
use Encore\Admin\Controllers\AdminController;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;

use App\SomsClient;
use App\SomsUniversity;
use App\SomsCity;
use App\SomsState;

use Illuminate\Support\Facades\Hash;
use Log;

class SomsClientController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title;

    protected $script;

    public function __construct()
    {
        $this->script = "
       
        var isReset = false;
        
        $( document ).ready(function() {
            $('input').attr('autocomplete','off');
        });
        $('.password-reset').click(function() {
            if ($('#contact').val() === '') {
                alert('Input contact to reset password.');
            } else {
                alert('Password has been reset. You can change it in the password field.');
                isReset = true;
                $('.is_reset').val($('#contact').val());
                $('#password').val($('#contact').val());
                $('#password').removeAttr('disabled');
                $('#password').css({'border' : '1px solid red'});
                //$('#password_confirmation').val($('#contact').val());
            }
          });
        $('#password').change(function() {
            if (isReset) {
                $('.is_reset').val($('#password').val());
                $('#password').css({'border' : '1px solid #d2d6de'});
            }
        });
        ";
    }

    public function index(Content $content)
    {
        $content->title(__('somsclient.title'));
        $content->description(__('somsclient.title'));

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
        $grid = new Grid(new SomsClient);

        $grid->filter(function($filter){
            // Remove the default id filter
            $filter->disableIdFilter();
            // Add a column filter
            $filter->column(1/2, function ($filter) {
                $filter->like('name', __('somsclient.name'));
                $filter->like('email', __('somsclient.email'));
                $filter->like('student_id', __('somsclient.student_id'));
            });
            $filter->column(1/2, function ($filter) {
                $filter->like('id_number', __('somsclient.id_number'));
                $filter->like('contact', __('somsclient.contact'));
                // $filter->like('mobile_phone_hk', __('somsclient.mobile_phone_hk'));
                $filter->like('wechat', __('somsclient.wechat'));
            });
        });

        //$grid->column('id')->sortable();

        $grid->column('name', __('somsorder.client_id'))->sortable()->display(function ($name) {
            return "<a href='".url('/admin/soms/clients/'.$this->id.'/edit')."'>$name</a>";
        });
        $grid->column('id_number', __('somsclient.id_number'))->sortable();
        $grid->column('email', __('somsclient.email'))->sortable();

        $grid->column('contact', __('somsclient.contact'))->sortable();
        // $grid->column('mobile_phone_hk', __('somsclient.mobile_phone_hk'))->sortable();
        $grid->column('wechat', __('somsclient.wechat'))->sortable();
        $grid->column('student_id', __('somsclient.student_id'))->sortable();

        $grid->column('orderCount', __('somsclient.orders'))->sortable()->display(function ($value) {
            return "<a href='".url('/admin/soms/orders?&').urlencode('client[name]').'='.urlencode($this->name)."'>$value</a>";
        });

        $grid->updated_at()->sortable();

        $grid->disableExport();
        $grid->disableActions();

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
        $show = new Show(SomsClient::findOrFail($id));

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

        Admin::script($this->script);
        $form = new Form(new SomsClient);

        // $form->disableEditingCheck();
        //
        // $form->disableCreatingCheck();
        //
        // $form->disableViewCheck();
        //
//        $form->tools(function (Form\Tools $tools) {
////             $tools->disableDelete();
////             $tools->disableView();
////             $tools->disableList();
//        });

        /* Show the reset-password button in edit mode */
        if ($form->isEditing()) {
            $form->tools(function (Form\Tools $tools) {

                $tools->add('<a class="btn btn-sm btn-danger password-reset"><i class="fa fa-user"></i>&nbsp;&nbsp;' . __('somsclient.password_reset') . '</a>');
            });
        }

//        $form->display('id', __('ID'));
////                $form->display('university.display_name', __('somsclient.university_id'));
//        $form->select('university_id', __('somsclient.university_id'))->options(function () {
//            return SomsUniversity::get()->pluck('display_name', 'id');
//        });
//        $form->text('name', __('somsclient.name'))->rules('required');
//        $form->text('id_number', __('somsclient.id_number'));
//        // $form->mobile('mobile_phone_hk', __('somsclient.mobile_phone_hk'))->rules('required')->options(['mask' => '']);
//        // $form->mobile('mobile_phone_cn', __('somsclient.mobile_phone_cn'))->options(['mask' => '']);
//        $form->mobile('contact', __('somsclient.contact'))->rules('required')->options(['mask' => '']);
//
//        $form->text('address1', __('somsclient.address1'))->rules('required');
//        // $form->text('address2', __('somsclient.address2'));
//        $form->select('city_id', __('somsclient.city_id'))->options(function () {
//            return SomsCity::get()->pluck('display_name', 'id');
//        });
//        $form->select('state_id', __('somsclient.state_id'))->options(function () {
//            return SomsState::get()->pluck('display_name', 'id');
//        });
//
//        $form->text('student_id', __('somsclient.student_id'));
//        $form->text('wechat', __('somsclient.wechat'));
//
//        $form->display('created_at', __('somsclient.created_at'));
//        $form->display('updated_at', __('somsclient.updated_at'));

//        $form->tab('Basic', function (Form $form) {

//            $form->row(function ($row) use ($form)
//            {
//                $row->width(12)->select('university_id', __('somsclient.university_id'))->options(function () {
//                    return SomsUniversity::get()->pluck('display_name', 'id');
//                });
//
//            },  $form);
//
//            $form->row(function ($row) use ($form)
//            {
//                $row->width(6)->text('name', __('somsclient.name'))->rules('required');
//                $row->width(6)->text('id_number', __('somsclient.id_number'));
//            },  $form);
//
//            $form->row(function ($row) use ($form)
//            {
//                $row->width(6)->email('email', __('somsclient.email'))->rules('required|email|unique:soms_clients,email,{{id}}');
//                $row->width(6)->mobile('contact', __('somsclient.contact'))->rules('required')->options(['mask' => '']);
//            },  $form);
//
//            $form->row(function ($row) use ($form)
//            {
//                $row->width(6)->text('student_id', __('somsclient.student_id'));
//                $row->width(6)->text('wechat', __('somsclient.wechat'));
//            },  $form);
//
//            $form->row(function ($row) use ($form)
//            {
//                $row->width(6)->password('password')->rules('confirmed');
//                $row->width(6)->password('password_confirmation');
//            },  $form);


            $form->column(1 / 2, function ($form) {

//                $form->display('university.display_name', __('somsclient.university_id'));
                $form->select('university_id', __('somsclient.university_id'))->options(function () {
                    return SomsUniversity::get()->pluck('display_name', 'id');
                });

                $form->text('name', __('somsclient.name'))->rules('required');

                $form->email('email', __('somsclient.email'))->rules('required|email|unique:soms_clients,email,{{id}}');

                $form->text('student_id', __('somsclient.student_id'));

                if ($form->isCreating()) {
                     $form->password('password', __('somsclient.password'))->rules('confirmed');
                } else if ($form->isEditing()) {

                    $form->password('password', __('somsclient.password'))->disable();
                    $form->input('is_reset');
                    $form->hidden('is_reset');
                }

                $form->text('address1', __('somsclient.address1'))->rules('required');
                // $form->text('address2', __('somsclient.address2'));

                $form->select('state_id', __('somsclient.state_id'))->options(function () {
                    return SomsState::get()->pluck('display_name', 'id');
                });

//                $form->input('password');
//                $form->hidden('password');
            });

            $form->column(1 / 2, function ($form) {

                $form->display('id', __('ID'));
                $form->text('id_number', __('somsclient.id_number'));
                // $form->mobile('mobile_phone_hk', __('somsclient.mobile_phone_hk'))->rules('required')->options(['mask' => '']);
                // $form->mobile('mobile_phone_cn', __('somsclient.mobile_phone_cn'))->options(['mask' => '']);
                $form->mobile('contact', __('somsclient.contact'))->rules('required')->options(['mask' => '']);

                $form->text('wechat', __('somsclient.wechat'));

                if ($form->isCreating()) {
                    $form->text('private_notes', __('somsclient.private_notes'));
                    $form->password('password_confirmation', __('somsclient.password_confirmation'));
                }

                $form->text('address2', __('somsclient.address2'));
                $form->select('city_id', __('somsclient.city_id'))->options(function () {
                    return SomsCity::get()->pluck('display_name', 'id');
                });

                if ($form->isEditing()) {
                    $form->text('private_notes', __('somsclient.private_notes'));
                }

//                $form->display('created_at', __('somsclient.created_at'));
//                $form->display('updated_at', __('somsclient.updated_at'));
            });
//        });

        // ->tab('Password', function (Form $form) {
        //
        //     $form->password('password')->rules('confirmed');
        //     $form->password('password_confirmation');
        //
        // });

        // ->tab('Order', function (Form $form) {
        //
        //   $form->hasMany('orders', function (Form\NestedForm $form) {
        //     $form->display('code');
        //
        //   });
        //
        // });

        if ($form->isCreating()) {
            $form->ignore(['password_confirmation']);
        } else if ($form->isEditing()) {

            $form->ignore(['password']);
        }

//        $form->ignore(['is_reset']);

        $form->saving(function (Form $form) {
            if ($form->isCreating()) {
                $form->password = Hash::make($form->password);
            } else if ($form->isEditing()) {

                if ($form->is_reset != '') {
                    $form->password = Hash::make($form->is_reset);
                    $form->is_reset = null;
                }
            }
        });

        $form->saved(function(Form $form){

          $uid = $form->model()->university_id;
//           Log::debug('uid: '.$uid);
          // return '404';
          admin_toastr(__('更新成功'));
            return redirect('/admin/soms/clients');
//          return redirect('/admin/soms/orders'.(($uid != null)?'?uid='.$uid:''));
        });

        return $form;
    }
}
