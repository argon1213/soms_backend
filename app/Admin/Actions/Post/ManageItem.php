<?php

namespace App\Admin\Actions\Post;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

use App\SomsStoragePeriodItem;
use App\SomsStoragePeriod;
use App\SomsItem;

use Log;

class ManageItem extends RowAction
{
    public $name;

    public function __construct()
    {
      parent::__construct();

      $this->name = '產品價錢管理';
    }

    public function handle(SomsStoragePeriod $period, Request $request)
    {
        // Log::debug(json_encode($period));
        // Log::debug(json_encode($request->all()));
        $storageperioditems = $request->get('storageperioditem');
        foreach ($storageperioditems as $key => $value) {
          // Log::debug($key.':'.$value);
          //
          $storagePeriodItem = SomsStoragePeriodItem::where('item_id', $key)->where('storage_period_id', $period->id)->first();
          if($storagePeriodItem != null){
            if($value != null){
              $storagePeriodItem->price = $value;
              $storagePeriodItem->save();
            }else{
              $storagePeriodItem->delete();
            }
          }else{
            if($value != null){
              $storagePeriodItem = new SomsStoragePeriodItem;
              $storagePeriodItem->item_id = $key;
              $storagePeriodItem->storage_period_id = $period->id;
              $storagePeriodItem->price = $value;
              $storagePeriodItem->save();
            }
          }
        }

        return $this->response()->success('產品價錢已更新')->refresh();
    }

    public function form(SomsStoragePeriod $period)
    {
        $items = SomsItem::whereIn('category', ['box'])->get();

        foreach ($items as $item) {
          $storagePeriodItem = SomsStoragePeriodItem::where('item_id', $item->id)->where('storage_period_id', $period->id)->first();
          if($storagePeriodItem != null){
            $this->text('storageperioditem.'.$item->id, $item->display_name . ' - 原價 HKD'.$item->price)->placeholder('輸入價錢')->default($storagePeriodItem->price)->rules('nullable|numeric|min:0');
          }else{
            $this->text('storageperioditem.'.$item->id, $item->display_name . ' - 原價 HKD'.$item->price)->placeholder('輸入價錢')->rules('nullable|numeric|min:0');
          }
        }
    }
}
