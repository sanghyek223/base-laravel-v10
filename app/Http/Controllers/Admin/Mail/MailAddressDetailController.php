<?php

namespace App\Http\Controllers\Admin\Mail;

use App\Http\Controllers\Controller;
use App\Services\Admin\Mail\MailAddressDetailServices;
use Illuminate\Http\Request;

class MailAddressDetailController extends Controller
{
    private $mailAddressDetailServices;

    public function __construct()
    {
        $this->mailAddressDetailServices = (new MailAddressDetailServices());

        view()->share([
            'mailConfig' => getConfig('mail'),
            'main_key' => 'mail',
        ]);
    }

    public function index(Request $request)
    {
        return view('admin.mail.address.detail.index', $this->mailAddressDetailServices->indexService($request));
    }

    public function upsert(Request $request)
    {
        return view("admin.mail.address.detail.upsert-{$request->type}", $this->mailAddressDetailServices->upsertService($request));
    }

    public function data(Request $request)
    {
        return $this->mailAddressDetailServices->dataAction($request);
    }
}
