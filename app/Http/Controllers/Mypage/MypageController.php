<?php

namespace App\Http\Controllers\Mypage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MypageController extends Controller
{
    private $mypageServices;

    public function __construct()
    {
        $this->mypageServices = (new \App\Services\Mypage\MypageServices());

        view()->share([
            'main_key' => 'MYPAGE',
            'userConfig' => (new \App\Models\User())->getUserConfig(),
        ]);
    }

    public function index(Request $request)
    {
        return view('mypage.index', $this->mypageServices->indexService($request));
    }

    public function data(Request $request)
    {
        return $this->mypageServices->dataAction($request);
    }
}
