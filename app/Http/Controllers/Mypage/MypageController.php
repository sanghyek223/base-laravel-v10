<?php

namespace App\Http\Controllers\Mypage;

use App\Http\Controllers\Controller;
use App\Services\Mypage\MypageServices;
use Illuminate\Http\Request;

class MypageController extends Controller
{
    private $mypageServices;

    public function __construct()
    {
        $this->mypageServices = (new MypageServices());

        view()->share([
            'userConfig' => getConfig('user'),
            'main_key' => 'MYPAGE',
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
