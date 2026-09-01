<?php

namespace App\Http\Controllers\Admin\Mail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MailAddressController extends Controller
{
    private $addressServices;
    private $mailConfig;

    public function __construct()
    {
        $this->addressServices = (new \App\Services\Admin\Mail\MailAddressServices());
        $this->mailConfig = config('site.mail');

        view()->share([
            'main_key' => 'mail',
            'mailConfig' => $this->mailConfig,
        ]);
    }

    public function index(Request $request)
    {
        return view('admin.mail.address.index', $this->addressServices->indexService($request));
    }

    public function upsert(Request $request)
    {
        return view('admin.mail.address.upsert', $this->addressServices->upsertService($request));
    }

    public function data(Request $request)
    {
        return $this->addressServices->dataAction($request);
    }
}
