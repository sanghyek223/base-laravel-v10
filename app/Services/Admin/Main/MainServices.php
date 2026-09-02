<?php

namespace App\Services\Admin\Main;

use App\Services\AppServices;
use Illuminate\Http\Request;

/**
 * Class MainServices
 * @package App\Services
 */
class MainServices extends AppServices
{
    public function indexService(Request $request)
    {
        return $this->data;
    }

    public function dataAction(Request $request)
    {
        return match ($request->case) {
            default => notFoundRedirect(),
        };
    }
}
