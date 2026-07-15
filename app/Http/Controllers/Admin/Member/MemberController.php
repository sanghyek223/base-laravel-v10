<?php

namespace App\Http\Controllers\Admin\Member;

use App\Http\Controllers\Controller;
use App\Services\Admin\Member\MemberServices;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    private $memberServices;

    public function __construct()
    {
        $this->memberServices = (new MemberServices());

        view()->share([
            'userConfig' => getConfig('user'),
            'main_key' => 'M1',
        ]);
    }

    public function index(Request $request)
    {
        return view('admin.member.index', $this->memberServices->indexService($request));
    }

    public function upsert(Request $request)
    {
        return view('admin.member.upsert', $this->memberServices->upsertService($request));
    }

    public function excel(Request $request)
    {
        $request->merge(['excel' => true]);
        return $this->memberServices->indexService($request);
    }

    public function data(Request $request)
    {
        return $this->memberServices->dataAction($request);
    }
}
