<?php

namespace App\Http\Controllers\Admin\Mail;

use App\Http\Controllers\Controller;
use App\Services\Admin\Mail\MailServices;
use Illuminate\Http\Request;

class MailController extends Controller
{
    private $mailServices;
    private $mailConfig;

    public function __construct()
    {
        $this->mailServices = (new MailServices());
        $this->mailConfig = getConfig('mail');
        
        view()->share([
            'mailConfig' => $this->mailConfig,
            'main_key' => 'mail',
        ]);
    }

    public function index(Request $request)
    {
        return view('admin.mail.index', $this->mailServices->indexService($request));
    }

    public function upsert(Request $request)
    {
        return view('admin.mail.upsert', $this->mailServices->upsertService($request));
    }

    public function detail(Request $request)
    {
        return view('admin.mail.detail', $this->mailServices->detailService($request));
    }
    
    public function preview(Request $request)
    {
        $data = $this->mailServices->previewService($request);
        return view($this->mailConfig['admin_template'][$data['mail']->template]['path'], $data);
    }

    public function data(Request $request)
    {
        return $this->mailServices->dataAction($request);
    }
}
