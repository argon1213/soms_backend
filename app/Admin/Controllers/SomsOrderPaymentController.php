<?php

namespace App\Admin\Controllers;

use App\SomsOrder;
use Illuminate\Support\MessageBag;


use Encore\Admin\Controllers\AdminController;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;

use Illuminate\Http\Request;

use App\SomsClient;
use App\SomsOrderPayment;
use App\SomsPaymentStatus;

use Log;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SomsOrderPaymentController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title;

    public function index(Content $content)
    {
        $content->title(__('somspayment.title'));

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
        $grid = new Grid(new SomsOrderPayment);

        $grid->filter(function($filter){
            // Remove the default id filter
            $filter->disableIdFilter();
            // Add a column filter
            $filter->column(1/2, function ($filter) {
                $filter->like('order.code', __('somspayment.order_code'));
            });
            $filter->column(1/2, function ($filter) {
                $filter->like('amount', __('somspayment.amount'));
            });
        });

        $grid->model()->whereHas('order', function ($q) {
            $q->where('current_version', 1);
        });

        //$grid->column('id')->sortable();
        $grid->column('code', __('somspayment.code'))->sortable()->display(function ($code) {
            return "<a href='" . url('/admin/soms/payments/' . $this->id . '/edit') . "'>$code</a>";
        });
        // $grid->column('code', __('somspayment.code'))->display(function ($code) {
        //     return "<a href='".url('/admin/soms/orders/'.$this->order_id.'/edit')."'>$code</a>";
        // })->sortable();
        $grid->column('order.client_id', __('somspayment.client'))->display(function ($id) {
            return "<a href='".url('/admin/soms/clients/'.$this->order->client_id.'/edit')."'>"
            .SomsClient::find($id)->name."</a>";
        })->sortable();
        $grid->column('order.code', __('somspayment.order_code'))->display(function ($code) {
            return "<a href='".url('/admin/soms/orders/'.$this->order_id.'/edit')."'>$code</a>";
        })->sortable();
        $grid->column('amount', __('somspayment.amount'))->sortable();
        $grid->column('payment_status_id', __('somsorder.order_status_id'))->display(function () {
            return SomsPaymentStatus::find($this->payment_status_id)->description;
        })->sortable();

        $grid->column('completed_at', __('somspayment.completed_at'))->sortable();

        $grid->column('Action')->display(function () {
            if ($this->status->id == SomsPaymentStatus::UNPAID)
                return "<a href = '" . url('/admin/soms/payments/mark-paid/' . $this->id) . "' class=\"btn btn-success\"> Mark as Paid</a>
                <a href = '" . url('/admin/soms/payments/mark-cancelled/' . $this->id) . "' class=\"btn btn-danger\"> Mark as Cancelled</a>";
        });

        $grid->disableExport();
        $grid->disableActions();

        $grid->actions(function ($actions) {

            //$actions->disableCreate();
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableView();
        });

         $grid->disableCreateButton();

        $grid->model()->orderBy('id','desc');

        return $grid;
    }

    protected function form()
    {
      $form = new Form(new SomsOrderPayment);

      $form->column(1 / 2, function ($form) {
        $form->display('code', __('somspayment.code'));
        $form->display('order.code', __('somspayment.order_code'));
        $form->display('order.client_id', __('somspayment.client'))->with(function ($id) {
            return SomsClient::find($id)->name;
        });
        $form->display('amount', __('somspayment.amount'));
        $form->display('status.description', __('somspayment.payment_status_id'));
        $form->display('type.description', __('somspayment.payment_type_id'));

        $form->display('trans_id', __('somspayment.trans_id'));
        $form->display('paid_fee', __('somspayment.paid_fee'));
        $form->display('pay_qr_code', __('somspayment.pay_qr_code'))->with(function ($qr_code_url) {
            return QrCode::size(200)->generate($qr_code_url);
        });
      });

      $form->column(1 / 2, function ($form) {
        $form->textarea('remark', __('somspayment.remark'))->rows(10);
        $form->display('completed_at', __('somspayment.completed_at'));
        $form->display('created_at', __('somsorder.created_at'));
        $form->display('updated_at', __('somsorder.updated_at'));

      });

      return $form;
    }

    public function markAsPaid($id) {

        $payment = SomsOrderPayment::find($id);
        $payment->payment_status_id = SomsPaymentStatus::PAID;
        $payment->paid_fee = $payment->amount;
        $payment->save();

        $currOrder = $payment->order;
        $currOrder->paid_fee = $currOrder->paid_fee + $payment->amount;
        $currOrder->payment_status_id = SomsPaymentStatus::PAID;
        $currOrder->save();

        // return redirect('/admin/soms/payments');
        return redirect()->back();
    }

    public function markAsCancelled($id) {

        $payment = SomsOrderPayment::find($id);
        $payment->payment_status_id = SomsPaymentStatus::CANCELLED;
        $payment->paid_fee = $payment->amount;
        $payment->save();

        $currOrder = $payment->order;
        $currOrder->total_fee = $currOrder->total_fee - $payment->amount;
        $currOrder->payment_status_id = SomsPaymentStatus::PAID;
        $currOrder->save();

        // return redirect('/admin/soms/payments');
        return redirect()->back();
    }
}
