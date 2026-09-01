<?php

namespace App\Http\Controllers\Admin\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MainController extends Controller
{
    private $mainServices;

    public function __construct()
    {
        $this->mainServices = (new \App\Services\Admin\Main\MainServices());

        view()->share([

        ]);
    }

    public function main(Request $request)
    {
        return view('admin.index', $this->mainServices->indexService($request));
    }

    public function data(Request $request)
    {
        return $this->mainServices->dataAction($request);
    }
}
