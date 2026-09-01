<?php

namespace App\Http\Controllers\Board;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
    private $replyServices;
    private $boardConfig;

    public function __construct()
    {
        $code = request()->code ?? '';
        $this->boardConfig = config("site.board.{$code}") ?? [];
        $this->replyServices = (new \App\Services\Board\ReplyServices());

        view()->share([
            'boardConfig' => $this->boardConfig,
            'main_key' => $this->boardConfig['key']['main'] ?? '',
            'sub_key' => $this->boardConfig['key']['sub'] ?? '',
            'b_sid' => request()->b_sid ?? '',
            'code' => $code,
        ]);
    }

    public function view(Request $request)
    {
        return view("{$this->boardConfig['viewPath']}.{$this->boardConfig['skin']}.reply.view", $this->replyServices->viewService($request));
    }

    public function upsert(Request $request)
    {
        return view("{$this->boardConfig['viewPath']}.{$this->boardConfig['skin']}.reply.upsert", $this->replyServices->upsertService($request));
    }

    public function data(Request $request)
    {
        return $this->replyServices->dataAction($request);
    }
}
