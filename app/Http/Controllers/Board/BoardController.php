<?php

namespace App\Http\Controllers\Board;

use App\Http\Controllers\Controller;
use App\Services\Board\BoardServices;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    private $boardServices;
    private $boardConfig;

    public function __construct()
    {
        $code = request()->code ?? '';
        $this->boardConfig = getConfig("board")[$code] ?? [];
        $this->boardServices = new BoardServices();

        view()->share([
            'boardConfig' => $this->boardConfig,
            'main_key' => $this->boardConfig['key']['main'] ?? '',
            'sub_key' => $this->boardConfig['key']['sub'] ?? '',
            'code' => $code,
        ]);
    }

    public function index(Request $request)
    {
        return view("{$this->boardConfig['viewPath']}.{$this->boardConfig['skin']}.index", $this->boardServices->listService($request));
    }

    public function view(Request $request)
    {
        return view("{$this->boardConfig['viewPath']}.{$this->boardConfig['skin']}.view", $this->boardServices->viewService($request));
    }

    public function upsert(Request $request)
    {
        return view("{$this->boardConfig['viewPath']}.{$this->boardConfig['skin']}.upsert", $this->boardServices->upsertService($request));
    }

    public function data(Request $request)
    {
        return $this->boardServices->dataAction($request);
    }
}
