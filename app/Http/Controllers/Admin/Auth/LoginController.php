<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    private $loginServices;

    public function __construct()
    {
        $this->loginServices = (new \App\Services\Admin\Auth\LoginServices());
    }

    public function logout(Request $request)
    {
        return $this->loginServices->logoutAction($request);
    }
}
