<?php

namespace App\Http\Controllers\Admin\Mail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MailAddressDetailController extends Controller
{
    private $addressDetailServices;
    private $mailConfig;

    public function __construct()
    {
        $this->addressDetailServices = (new \App\Services\Admin\Mail\MailAddressDetailServices());
        $this->mailConfig = config('site.mail');

        view()->share([
            'main_key' => 'mail',
            'mailConfig' => $this->mailConfig,
        ]);
    }

    public function index(Request $request)
    {
        return view('admin.mail.address.detail.index', $this->addressDetailServices->indexService($request));
    }

    public function upsert(Request $request)
    {
        return view("admin.mail.address.detail.upsert-{$request->type}", $this->addressDetailServices->upsertService($request));
    }

    public function data(Request $request)
    {
        return $this->addressDetailServices->dataAction($request);
    }
}
