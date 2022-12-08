<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentController;

use App\Mail\ExtendedPaymentInvoice;
use App\Mail\PaymentInvoice;
use App\SomsOrderPayment;
use App\SomsUniversity;
use Encore\Admin\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\AdminController;

use App\SomsItem;
use App\SomsPaymentStatus;
use App\SomsPaymentType;

use App\SomsClient;

use App\SomsOrder;
use App\SomsOrderItem;
use App\SomsOrderStatus;

use App\SomsCity;
use App\SomsState;

use Log;
use Auth;
use Mail;
use Hash;
use Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\MessageBag;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class SomsOrderController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title;

    protected $script;

    protected $request;

    public function __construct()
    {
        $this->script = "
        function refreshStorageMonth(){
          if($('#checkin_date_other').parent().data('DateTimePicker').date() == null
            || $('#checkin_date_other').parent().data('DateTimePicker').date() == null)
            return 0;

          var checkin_date = $('#checkin_date_other').parent().data('DateTimePicker').date()._d;
          var checkout_date = $('#checkout_date_other').parent().data('DateTimePicker').date()._d;
          var month = calcDateDiffByMonth(checkin_date, checkout_date);
          // $('#storage_month_preview').html(month);
          $('#storage_month_preview').val(month);

          return month;
        }

        function multiply(arg1,arg2)
  			{
  				var m=0,s1=arg1.toString(),s2=arg2.toString();
  				try{m =s1.split('.')[1].length}catch(e){}
  				try{m =s2.split('.')[1].length}catch(e){}
  				return Number(s1.replace('.',''))*Number(s2.replace('.',''))/Math.pow(10,m);
  			}

        function add(arg1,arg2){
  				var r1,r2,m;
  				try{r1=arg1.toString().split('.')[1].length}catch(e){r1=0}
  				try{r2=arg2.toString().split('.')[1].length}catch(e){r2=0}
  				m=Math.pow(10,Math.max(r1,r2))
  				return (arg1*m+arg2*m)/m;
  			}

        function refreshTotalFee(){
          var new_storage_month = refreshStorageMonth();
          console.log('new_storage_month : '+new_storage_month);
          //
          var monthly_fee = 0;
          var other_fee = 0;
          $('.items.item_qty').each(function( index ) {
            var item_qty = $(this);
            var item_price = $(this).closest('.has-many-items-form').find('.item_price');
            var item_category = $(this).closest('.has-many-items-form').find('.item_category');
            // console.log(item_qty.attr('name')+':'+item_qty.val());
            // console.log(item_price.attr('name')+':'+item_price.val());
            // console.log(item_category.attr('name')+':'+item_category.val());
            if('box' == item_category.val()){
              monthly_fee = add(monthly_fee, multiply(item_qty.val(),item_price.val()));
            }else{
              other_fee = add(other_fee, multiply(item_qty.val(),item_price.val()));
            }
          });
          var delivery_service_fee = parseFloat($('#delivery_service_fee').val());
          // console.log(new_storage_month);
          // console.log(monthly_fee);
          // console.log(other_fee);
          // console.log(delivery_service_fee);
          var total_fee = add(multiply(new_storage_month,monthly_fee), add(other_fee,delivery_service_fee));
          // $('#total_fee_preview').html(total_fee);
          $('#total_fee_preview').val(total_fee);
        }

        $(function(){

        refreshTotalFee();

          $('#checkin_date_other').parent().on('dp.change', function(e){
            refreshTotalFee();
          });

          $('#checkout_date_other').parent().on('dp.change', function(e){
            refreshTotalFee();
          });

          $('#delivery_service_fee').change(function() {
            if($(this).attr('prev') != $(this).val())
            {
              refreshTotalFee();
              $(this).attr('prev', $(this).val());
            }
          });

          //          $('.items.item_qty').change(function() {
          //            if($(this).attr('prev') != $(this).val())
          //            {
          //              refreshTotalFee();
          //              $(this).attr('prev', $(this).val());
          //            }
          //          });
          //
          //          $('.items.item_price').blur(function() {
          //            if($(this).attr('prev') != $(this).val())
          //            {
          //              refreshTotalFee();
          //              $(this).attr('prev', $(this).val());
          //            }
          //          });

          $('.items.item_qty').change(function() {
            refreshTotalFee();
          });

          $('.items.item_price').blur(function() {
            refreshTotalFee();
          });

        });
      ";
    }

    public function index(Content $content)
    {
        $content->title(__('somsorder.title'));
        $content->description(__('somsorder.title'));

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
        $grid = new Grid(new SomsOrder);
        $grid->model()->orderBy('code', 'desc');

        $grid->filter(function ($filter) {
            // Remove the default id filter
            $filter->disableIdFilter();
            // Add a column filter
            $filter->column(1 / 2, function ($filter) {
                $filter->like('client.name', __('somsclient.name'));
                $filter->like('client.email', __('somsclient.email'));
                $filter->between('client.emptyout_date_other', __('somsorder.emptyout_date_other'))->date();
                $filter->between('client.checkin_date_other', __('somsorder.checkin_date_other'))->date();
                $filter->between('client.checkout_date_other', __('somsorder.checkout_date_other'))->date();
                $filter->in('order_status_id', __('somsorder.order_status_id'))->checkbox(SomsOrderStatus::get()->pluck('description', 'id'));
            });
            $filter->column(1 / 2, function ($filter) {
                $filter->like('client.student_id', __('somsclient.student_id'));
                $filter->like('client.contact', __('somsclient.contact'));
                $filter->like('client.wechat', __('somsclient.wechat'));
                $filter->like('remark_location', __('somsorder.remark_location'));
                $filter->like('remark_qrcode', __('somsorder.remark_qrcode'));
                $filter->like('code', __('somsorder.code'));
            });
        });

        $uid = request()->get('uid');

        $grid->model()->where('current_version', 1)->whereHas('client', function ($q) use ($uid) {
            if($uid != null)
              $q->where('university_id', $uid);
        });

        if(!request()->filled('order_status_id')){
          $grid->model()->where('order_status_id', '!=', 28);
        }
        //$grid->column('id', __('somsorder.id'))->sortable();
        $grid->column('client.name', __('somsorder.client_id'))->sortable()->display(function ($name) {
            return "<a href='" . url('/admin/soms/clients/' . $this->client->id . '/edit') . "'>$name</a>";
        });
        $grid->column('client.student_id', __('somsclient.student_id'))->sortable();
        $grid->column('client.email', __('somsclient.email'))->sortable();
        $grid->column('client.contact', __('somsclient.contact'))->sortable();
        $grid->column('client.wechat', __('somsclient.wechat'))->sortable();
        $grid->column('code', __('somsorder.code'))->sortable()->display(function ($name) {
            return "<a href='" . url('/admin/soms/orders/' . $this->id . '/edit') . "'>$name</a>";
        });
        $grid->column('paperBoxes', __('somsorder.paperBoxes')); //->sortable();
        $grid->column('standardBoxes', __('somsorder.standardBoxes')); //->sortable();
        $grid->column('oversizeItems', __('somsorder.oversizeItems')); //->sortable();
        $grid->column('wardrobe', __('somsorder.wardrobe')); //->sortable();
        $grid->column('vacuumBags', __('somsorder.vacuumBags')); //->sortable();
        $grid->column('storage_month', __('somsorder.storage_month'))->sortable();
        $grid->column('emptyout_date_other', __('somsorder.emptyout_date_other'))->sortable();
        $grid->column('checkin_date_other', __('somsorder.checkin_date_other'))->sortable();
        $grid->column('checkout_date_other', __('somsorder.checkout_date_other'))->sortable();
        $grid->column('checkout_location_other', __('somsorder.checkout_location_other'))->sortable();
        $grid->column('remark_location', __('somsorder.remark_location'))->sortable();
	$grid->column('remark_qrcode', __('somsorder.remark_qrcode'));
	$grid->column('special_instruction', __('somsorder.special_instruction'));
        $grid->column('total_fee', __('somsorder.total_fee'))->sortable();
        $grid->column('paid_fee', __('somsorder.paid_fee'))->sortable();
        $grid->column('balance', __('somsorder.balance'))->display(function ($balance) {
            return "<a href='" . url('/admin/soms/payments/?order[code]='.$this->code) . "'>$balance</a>";
        });

        $grid->column('status.description', __('somsorder.order_status_id'))->sortable();

        $grid->column('')->display(function () {
            if ($this->paid_fee < $this->total_fee)
                return "<a href = '" . url('/admin/soms/orders/payment-invoice/send/' . $this->id) . "' class=\"btn btn-warning\"> Send invoice </a>";
        });

        //$grid->updated_at(__('somsorder.updated_at'))->sortable();

        $grid->disableExport();
        $grid->disableActions();
        // $grid->disableCreateButton();

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
        $show = new Show(SomsOrder::findOrFail($id));

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

        $form = new Form(new SomsOrder);
        //        $form->tab('Basic', function (Form $form) {
        $form->column(1 / 2, function ($form) {

            $form->display('id', __('somsorder.id'));
            $form->display('code', __('somsorder.code'));
            //            $form->display('client.name', __('somsorder.client_id'));
            $form->select('client_id', __('somsorder.client_id'))->options(function () {
                return SomsClient::get()->pluck('name', 'id');
            });

            $form->text('emptyout_location_other', __('somsorder.emptyout_location_other'))->required();
            $form->select('emptyout_city_id', __('somsorder.emptyout_city_id'))->options(function () {
                return SomsCity::get()->pluck('name_cn', 'id');
            });
            $form->select('emptyout_state_id', __('somsorder.emptyout_state_id'))->options(function () {
                return SomsState::get()->pluck('name_cn', 'id');
            });

            $form->date('emptyout_date_other', __('somsorder.emptyout_date_other'))->required()->default(date('Y-m-d'));
            $form->text('emptyout_time_other', __('somsorder.emptyout_time_other'))->required()->default("9:00-11:00");

            $form->text('checkin_location_other', __('somsorder.checkin_location_other'))->required();
            $form->select('checkin_city_id', __('somsorder.checkin_city_id'))->options(function () {
                return SomsCity::get()->pluck('name_cn', 'id');
            });
            $form->select('checkin_state_id', __('somsorder.checkin_state_id'))->options(function () {
                return SomsState::get()->pluck('name_cn', 'id');
            });

            $form->date('checkin_date_other', __('somsorder.checkin_date_other'))->required()->default(date('Y-m-d'));
            $form->text('checkin_time_other', __('somsorder.checkin_time_other'))->required()->default("9:00-11:00");

            $form->text('checkout_location_other', __('somsorder.checkout_location_other'))->required();
            $form->select('checkout_city_id', __('somsorder.checkout_city_id'))->options(function () {
                return SomsCity::get()->pluck('name_cn', 'id');
            });
            $form->select('checkout_state_id', __('somsorder.checkout_state_id'))->options(function () {
                return SomsState::get()->pluck('name_cn', 'id');
            });

            $form->date('checkout_date_other', __('somsorder.checkout_date_other'))->required()->default(date('Y-m-d'));
            $form->text('checkout_time_other', __('somsorder.checkout_time_other'))->required()->default("9:00-11:00");

            $form->select('order_status_id', __('somsorder.order_status_id'))->options(function () {
                return SomsOrderStatus::get()->pluck('description', 'id');
            })->required()->default(1);

            $form->display('created_at', __('somsorder.created_at'));
            $form->display('updated_at', __('somsorder.updated_at'));

        });

        $form->column(1 / 2, function ($form) {

            if ($form->isEditing()) {
                // edit mode
                $form->hasMany('items', function (Form\NestedForm $nestedForm) {

                    $nestedForm->select('item_id', __('somsorder.item_id'))->options(function () {
                        return SomsItem::withTrashed()->get()->pluck('display_name', 'id');
                    })->required();

                    $nestedForm->number('item_qty', __('somsorder.item_qty'))->min(0)->default(0); //->attribute(['data-id' => 'item_display_price','readonly' => true]);
                    $nestedForm->text('item_price', __('somsorder.item_price'))->required()->default(0);
                    $nestedForm->hidden('item_category');

                })->disableDelete();
            } else {
                // create mode
                $form->hasMany('items', function (Form\NestedForm $nestedForm) {

                    $nestedForm->select('item_id', __('somsorder.item_id'))->options(function () {
                        return SomsItem::withTrashed()->get()->pluck('display_name', 'id');
                    })->required();

                    $nestedForm->number('item_qty', __('somsorder.item_qty'))->min(0)->default(0); //->attribute(['data-id' => 'item_display_price','readonly' => true]);
                    $nestedForm->text('item_price', __('somsorder.item_price'))->required()->default(0);
                    $nestedForm->hidden('item_category');

                });
            }

            $form->text('delivery_service_fee', __('somsorder.delivery_service_fee'))->required()->default(0);

            $form->display('storage_month', __('somsorder.storage_month'));
            $form->display('total_fee', __('somsorder.total_fee'));
            $form->text('storage_month', __('somsorder.storage_month'))->attribute(['readonly' => true, 'id' => 'storage_month_preview']);
            $form->text('total_fee', __('somsorder.total_fee'))->attribute(['readonly' => true, 'id' => 'total_fee_preview']);

            $form->text('special_instruction', __('somsorder.special_instruction'));
            $form->text('remark_location', __('somsorder.remark_location'));
            $form->textarea('remark_qrcode', __('somsorder.remark_qrcode'))->rows(10);

              $form->select('payment_type_id', __('somsorder.payment_type_id'))->options(function () {
                  return SomsPaymentType::get()->pluck('description', 'id');
              })->required()->default(6);

              $form->select('payment_status_id', __('somsorder.payment_status_id'))->options(function () {
                  return SomsPaymentStatus::get()->pluck('description', 'id');
              })->required()->default(1);

              $form->display('payments', __('somsorder.payments'))->with(function ($value) {
                  return "<a href='" . url('/admin/soms/payments/?order[code]='.$this->code) . "'>View Payments</a>";
              });


            // if ($form->isEditing()) {
            //     $form->hasMany('payments', function (Form\NestedForm $nestedForm) {
            //
            //         $nestedForm->select('payment_type_id', __('somsorder.payment_type_id'))->options(function () {
            //             return SomsPaymentType::get()->pluck('description', 'id');
            //         })->required();
            //
            //         $nestedForm->select('payment_status_id', __('somsorder.payment_status_id'))->options(function () {
            //             return SomsPaymentStatus::get()->pluck('description', 'id');
            //         })->required();
            //
            //         $nestedForm->display('trans_id', __('somsorder.trans_id'));
            //
            //         $nestedForm->display('completed_at', __('somsorder.completed_at'));
            //
            //     })->disableDelete();
            // } else {
            //   $form->select('payment_type_id', __('somsorder.payment_type_id'))->options(function () {
            //       return SomsPaymentType::get()->pluck('description', 'id');
            //   })->required()->default(6);
            //
            //   $form->select('payment_status_id', __('somsorder.payment_status_id'))->options(function () {
            //       return SomsPaymentStatus::get()->pluck('description', 'id');
            //   })->required()->default(1);
            // }

            // $form->display('trans_id', __('somsorder.trans_id'));
            $form->text('paid_fee', __('somsorder.paid_fee'))->required()->default(0);
            $form->textarea('remark', __('somsorder.remark'))->rows(10);

        });

        $form->saving(function (Form $form) {

            // Get Exist Request Data
            $items = \request()->input('items');
            $temp = array();

            foreach ($items as $item) {

                if ($item['_remove_'])
                    continue;

                if (in_array($item['item_id'], $temp, true)) {

                    $error = new MessageBag([
                        'title' => '設定錯誤',
                        'message' => '不能為同一產品設定不同價錢',
                    ]);

                    return back()->withInput(\request()->input())->with(compact('error'));
                } else {
                    $temp[] = $item['item_id'];
                }
            }
        });

        $form->saved(function (Form $form) {
            $uid = $form->model()->client->university_id;

            $order = $form->model();
            $unpaid_amount = $order->total_fee - $order->paid_fee;
            if($unpaid_amount > 0 && $order->isNeedCreateNewPayment())
            {
              $order->payment_status_id = SomsPaymentStatus::UNPAID;
              $order->save();
              // Create new Payment
              $newPayment = new SomsOrderPayment;
              $newPayment->order_id = $order->id;
              $newPayment->amount = $unpaid_amount;
              $newPayment->payment_type_id = $order->payment_type_id;
              $newPayment->save();

              PaymentController::processPayment($newPayment, $order->client, null, route('wechatpay-return'), route('alipay-return'));
            }

            admin_toastr(__('更新成功'));
            return redirect('/admin/soms/orders');

            //            Log::debug("TEST: ".session('r_save'));

            //            if (empty(session('r_save')) || session('r_save') == '1') {
            //
            //                session()->put('r_save', '2');
            //
            //                Log::debug("TEST: " . auth()->user()->r_save);
            //                admin_toastr("New items have been added. Please save the order again.");
            //                return redirect('/admin/soms/orders/' . $form->model()->id . '/edit');
            //            } else {
            //
            //                session()->put('r_save', '1');
            //                admin_toastr(__('更新成功'));
            //                return redirect('/admin/soms/orders');
            //            }

            // return redirect('/admin/soms/orders/'.$form->model()->id.'/edit');
            //return redirect('/admin/soms/orders' . (($uid != null) ? '?uid=' . $uid : ''));
        });

        return $form;
    }

    protected function createByClient(Content $content, $id)
    {
        $somsClient = SomsClient::findOrFail($id);

        Admin::script($this->script);
        Admin::script($this->title);

        $form = new Form(new SomsOrder);

        $form->tab('Basic', function (Form $form) use ($somsClient) {

            $form->select('client.name', __('somsorder.client_id'))->options(function () {
                return SomsClient::get()->pluck('name', 'id');
            })->required()->default($somsClient->id);

            $form->hidden('client.university_id')->default($somsClient->university_id)->attribute(['id' => 'client_university_id']);

            $form->text('emptyout_location_other', __('somsorder.emptyout_location_other'))->required();
            $form->date('emptyout_date_other', __('somsorder.emptyout_date_other'))->required();
            $form->select('emptyout_time_other', __('somsorder.emptyout_time_other'))->options(['AM' => 'AM', 'PM' => 'PM'])->required();

            $form->text('checkin_location_other', __('somsorder.checkin_location_other'))->required();
            $form->date('checkin_date_other', __('somsorder.checkin_date_other'))->required();
            $form->select('checkin_time_other', __('somsorder.checkin_time_other'))->options(['AM' => 'AM', 'PM' => 'PM'])->required();

            $form->text('checkout_location_other', __('somsorder.checkout_location_other'))->required();
            $form->date('checkout_date_other', __('somsorder.checkout_date_other'))->required();
            $form->select('checkout_time_other', __('somsorder.checkout_time_other'))->options(['AM' => 'AM', 'PM' => 'PM'])->required();

            $form->select('order_status_id', __('somsorder.order_status_id'))->options(function () {
                return SomsOrderStatus::get()->pluck('description', 'id');
            })->required()->default(1);

        });

        $form->tab('Order Items', function (Form $form) {

            $form->hasMany('items', function (Form\NestedForm $nestedForm) {

                $nestedForm->select('item_id', __('somsorder.item_id'))->options(function () {
                    return SomsItem::withTrashed()->get()->pluck('display_name', 'id');
                });
                // $form->display('item.display_name', __('somsorder.item_id'));
                $nestedForm->hidden('item_category');
                $nestedForm->number('item_qty', __('somsorder.item_qty')); //->attribute(['data-id' => 'item_display_price','readonly' => true]);
                // $form->display('item_qty', __('somsorder.item_qty'));
                // $nestedForm->number('item.item_price', __('somsorder.item_price'))->attribute(['readonly' => true]);
                $nestedForm->text('item_price', __('somsorder.item_price'));
                // $form->display('display_price', __('somsorder.display_price'))->with(function ($value) {
                //     return '<span class="order_item_display_price">'.$value.'</span>';
                // });

            });

            $form->text('delivery_service_fee', __('somsorder.delivery_service_fee'))->required();

            $form->display('storage_month', __('somsorder.storage_month'));
            $form->display('total_fee', __('somsorder.total_fee'));
            $form->text('storage_month', __('somsorder.storage_month'))->attribute(['readonly' => true, 'id' => 'storage_month_preview']);
            $form->text('total_fee', __('somsorder.total_fee'))->attribute(['readonly' => true, 'id' => 'total_fee_preview']);

            $form->text('remark_location', __('somsorder.remark_location'));
            $form->textarea('remark_qrcode', __('somsorder.remark_qrcode'))->rows(10);

            // $form->display('storage_month', __('somsorder.storage_month_preview'))->with(function ($value) {
            //     return '<span id="storage_month_preview">'.$value.'</span>';
            // });
            // $form->display('total_fee', __('somsorder.total_fee_preview'))->with(function ($value) {
            //     return '<span id="total_fee_preview">'.$value.'</span>';
            // });

        });

        // $form->tab('Payment', function (Form $form) {
        //
        //     $form->select('payment_type_id', __('somsorder.payment_type_id'))->options(function () {
        //         return SomsPaymentType::get()->pluck('description', 'id');
        //     })->required();
        //
        //     $form->select('payment_status_id', __('somsorder.payment_status_id'))->options(function () {
        //         return SomsPaymentStatus::get()->pluck('description', 'id');
        //     })->required();
        //
        //     $form->display('trans_id', __('somsorder.trans_id'));
        //     $form->number('paid_fee', __('somsorder.paid_fee'))->required();
        //     $form->textarea('remark', __('somsorder.remark'))->rows(10);
        // });

        $form->saved(function (Form $form) {
            $uid = $form->model()->client->university_id;
            admin_toastr(__('更新成功'));
            return redirect('/admin/soms/orders' . (($uid != null) ? '?uid=' . $uid : ''));
        });

        $content->title('所有訂單');
        $content->description('所有訂單');

        $content->body($form);

        return $content;
    }

    public function processSendExtendedInvoice(Request $request, $id)
    {
        $order = SomsOrder::find($id);
        $payment = $order->incompletePayment();

        return $this->sendExtendedPaymentInvoice($request, $order, $payment);
    }

    public function sendExtendedPaymentInvoice($request, $order, $payment)
    {
        try{

            Mail::to($order->client->email)->cc( env('MAIL_TO_ADDRESS') )->send(new ExtendedPaymentInvoice($order));

            Log::debug('Payment Invoice successfully send with email : '.$order->client->email.' payment id: '.$payment->id);
            //
            //            $request->session()->flash('alert-class', 'alert-success');
            //            $request->session()->flash('message', '成功發送訂單! 請到閣下郵箱查看!');
            admin_toastr(__('成功'));
            return redirect('/admin/soms/orders');
        }
        catch(\Exception $e){
            Log::error('Payment Invoice cannot send with email : '.$order->client->email.' order code : '.$order->code);
        }
        //        admin_toastr(__('發送訂單失敗! 請確認閣下郵箱可正常收發電郵! 若情況持續, 請直接從網站查閱訂單, 謝謝!'));
        //        $request->session()->flash('alert-class', 'alert-danger');
        //        $request->session()->flash('message', '發送訂單失敗! 請確認閣下郵箱可正常收發電郵! 若情況持續, 請直接從網站查閱訂單, 謝謝!');
        admin_toastr(__('失败'));
        return redirect('/admin/soms/orders');
    }

    public function generateWeChatPayQrCode($request, $order, $payment)
    {
        try {

            $requestBody = ['appid' => '1021563', 'payment' => 'wechat.qrcode', 'total_fee' => $payment->amount * 100,
                'notify_url' => route('admin.soms.orders.wechatpay-return'), 'order_trade_no' => $order->code, 'payment_id' => $payment->id];
            Log::debug("Request: ".json_encode($requestBody));

            $strSign = $this->signData($requestBody);
            Log::debug("Request: ".$strSign);

            $requestBody['sign'] = $strSign;

            $httpClient = new Client();

            $response = $httpClient->post('https://api.hk.blueoceanpay.com/payment/pay', [
                RequestOptions::JSON => $requestBody // or 'json' => [...]
            ]);

            $content = json_decode($response->getBody()->getContents());

            Log::debug("JSON: ".json_encode($content));

            // Save qr code to DB
            $order->pay_qr_code = $content->data->qrcode;
            $order->save();

            Log::debug("QRCODE: " . $content->data->qrcode);

            $this->sendExtendedPaymentInvoice($request, $order, $payment);
        } catch (Exception $e) {
            Log::error('Wechat Payment Error : ' . $e->getMessage());
            return redirect()->back()->with('error', 'Payment is failed. Please contact customer service for further payment process. Order No. :' . $order->code);
        }
    }
    /**
     * @param array $data
     * @param string $key
     * @return string
     */
    public static function signData($data){

        $key = "Wvo01OpUZbJyJX2HhWk5sCnE6Mk5TT18";

        $ignoreKeys = ['sign', 'key'];
        ksort($data);
        $signString = '';
        foreach ($data as $k => $v) {
            if (in_array($k, $ignoreKeys)) {
                unset($data[$k]);
                continue;
            }
            $signString .= "{$k}={$v}&";
        }
        $signString .= "key={$key}";
        return strtoupper(md5($signString));
    }

    // public function createNewPaymentRecord($order)
    // {
    //
    //     $pending_payments = SomsOrderPayment::where([
    //         'order_id' => $order->id,
    //         'status' => 'PENDING'])->delete();
    //
    //     $payment = new SomsOrderPayment();
    //     $payment->client_id = $order->client_id;
    //     $payment->order_id = $order->id;
    //     $payment->amount = $order->total_fee - $order->paid_fee;
    //     $payment->status = "PENDING";
    //     $payment->save();
    //
    //     return $payment;
    // }

    // public function weChatPayReturn(Request $request)
    // {
    //     Log::info('start admin weChatPayReturn - ' . $request->get('payment_id'));
    //
    //     $payment_id = $request->get('payment_id');
    //
    //     $payment = SomsOrderPayment::find($payment_id);
    //     $payment->status = "PAID";
    //     $payment->trans_id = $request->get('transaction_id');
    //     $payment->save();
    //
    //     $currOrder = SomsOrder::where('code', $request->get('order_trade_no'))->first();
    //     $currOrder->payment_status_id = 2;
    //     $currOrder->paid_fee = $currOrder->total_fee;
    //     $currOrder->pay_qr_code = "";
    //     $currOrder->save();
    // }
}
