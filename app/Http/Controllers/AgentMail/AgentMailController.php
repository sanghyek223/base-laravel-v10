<?php

namespace App\Http\Controllers\AgentMail;

use App\Http\Controllers\Controller;
use App\Services\AgentMail\AgentMailServices;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;

class AgentMailController extends Controller
{
    private $agentMailServices;

    public function __construct()
    {
        $this->agentMailServices = (new AgentMailServices());
    }

    public function template(Request $request)
    {
        $viewFile = "template.agent-mail.{$request->file}";

        // 뷰 파일이 존재하는지 확인
        if (View::exists($viewFile)) {
            return view($viewFile);
        }

        // 뷰 파일이 존재하지 않으면, 404 페이지
        return view('template.agent-mail.404');
    }

    public function data(Request $request)
    {
        return $this->agentMailServices->dataAction($request);
    }
}
