<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets;

use App\SomsUniversity;
use App\SomsOrder;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        $content
            ->title('Dashboard')
            ->description('Description...');

        $content->row(function ($row) {

            $universities = SomsUniversity::get();
            $icons = [
              0 => 'address-book',
              1 => 'bus',
              2 => 'id-card',
              3 => 'home',
              4 => 'life-ring',
              5 => 'industry',
              6 => 'leaf',
              7 => 'plane',
              8 => 'home'
            ];
            $colors = [
              0 => 'aqua',
              1 => 'green',
              2 => 'yellow',
              3 => 'red',
              4 => 'maroon',
              5 => 'light-blue',
              6 => 'teal',
              7 => 'blue',
              8 => 'orange'
            ];

            foreach ($universities as $university) {
              $row->column(3, new Widgets\InfoBox(
                $university->display_name,
                $icons[($university->id%9)],
                $colors[($university->id%9)],
                '/admin/soms/orders?uid='.$university->id,
                $university->ordersCount()
              ));
            }
            // Handle Orders which university_id is null
            //$row->column(3, new Widgets\InfoBox(
            //  'Other',
            //  $icons[8],
            //  $colors[8],
            //  '/admin/soms/orders',
            //  SomsOrder::where('current_version',1)->whereHas('client', function($q) {
            //      $q->whereNull('university_id');
            //  })->count()
            //));

            // .bg-red,.bg-yellow,.bg-aqua,.bg-blue,.bg-light-blue,.bg-green,.bg-navy,.bg-teal,.bg-olive,.bg-lime,.bg-orange,.bg-fuchsia,.bg-purple,.bg-maroon,.bg-black,
            //
            // .bg-red-active,.bg-yellow-active,.bg-aqua-active,.bg-blue-active,.bg-light-blue-active,.bg-green-active,.bg-navy-active,.bg-teal-active,.bg-olive-active,.bg-lime-active,.bg-orange-active,.bg-fuchsia-active,.bg-purple-active,.bg-maroon-active,.bg-black-active

            // $row->column(3, new Widgets\InfoBox('New Orders', 'shopping-cart', 'green', '/demo/orders', '150%'));
            // $row->column(3, new Widgets\InfoBox('Articles', 'book', 'yellow', '/demo/articles', '2786'));
            // $row->column(3, new Widgets\InfoBox('Documents', 'file', 'red', '/demo/files', '698726'));
        });

        return $content;
    }

    public function cards()
    {
        return view('admin.dashboard');
    }
}
