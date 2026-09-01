<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $data = [];

    protected $commonServices;

    public function __construct()
    {
        $this->commonServices = (new \App\Services\CommonServices());
    }

    public function captchaMake(Request $request)
    {
        return $this->commonServices->captchaMakeService();
    }

    public function tinyUpload(Request $request)
    {
        return [
            'location' => $this->commonServices->fileUploadService($request->file('file'), '/tinymce')['realfile'],
        ];
    }

    public function plUpload(Request $request)
    {
        return $this->commonServices->fileUploadService($request->file('file'), $request->directory);
    }

    public function download(Request $request)
    {
        return ($request->type === 'only')
            ? $this->commonServices->fileDownloadService($request)
            : $this->commonServices->zipDownloadService($request);
    }
}
