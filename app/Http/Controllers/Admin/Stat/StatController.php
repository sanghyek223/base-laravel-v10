<?php

namespace App\Http\Controllers\Admin\Stat;

use App\Http\Controllers\Controller;
use App\Services\Admin\Stat\StatServices;
use Illuminate\Http\Request;

class StatController extends Controller
{
    private $statServices;

    public function __construct()
    {
        $this->statServices = (new StatServices());
        view()->share([
            'statConfig' => getConfig('stat'),
            'main_key' => 'stat',
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
