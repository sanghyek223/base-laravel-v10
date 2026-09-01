<?php

namespace App\Http\Controllers\Admin\Stat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StatController extends Controller
{
    private $statServices;

    public function __construct()
    {
        $this->statServices = (new \App\Services\Admin\Stat\StatServices());

        view()->share([
            'main_key' => 'stat',
            'statConfig' => config('site.stat'),
        ]);
    }

    public function index(Request $request)
    {
        return view('admin.stat.index', $this->statServices->indexService($request));
    }

    public function referer(Request $request)
    {
        return view('admin.stat.referer', $this->statServices->refererService($request));
    }

    public function data(Request $request)
    {
        return $this->statServices->dataAction($request);
    }
}
