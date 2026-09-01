<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private $authServices;

    public function __construct()
    {
        $this->authServices = (new \App\Services\Auth\AuthServices());

        view()->share([
            'main_key' => 'GUEST',
            'userConfig' => (new \App\Models\User())->getUserConfig(),
        ]);
    }

    public function signup(Request $request)
    {
        view()->share(['sub_key' => 'S2']);

        return response()
            ->view('auth.signup.index', $this->authServices->signupAction($request))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public function find(Request $request)
    {
        view()->share(['sub_key' => 'S3']);
        return view('auth.find.index');
    }

    public function data(Request $request)
    {
        return $this->authServices->dataAction($request);
    }
}
