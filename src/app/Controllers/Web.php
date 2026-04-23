<?php

namespace App\Controllers;

class Web extends BaseController
{
    /**
     * トップページ
     * GET /
     */
    public function index()
    {
        return redirect()->to('/login');
    }

    /**
     * ユーザー登録画面
     * GET /register
     */
    public function register()
    {
        return view('register');
    }

    /**
     * ログイン画面
     * GET /login
     */
    public function login()
    {
        log_message('info', '[WEB] ログイン画面アクセス IP=' . $this->request->getIPAddress() . ' UA=' . $this->request->getUserAgent()->getAgentString());
        return view('login');
    }

    /**
     * ダッシュボード画面
     * GET /dashboard
     */
    public function dashboard()
    {
        return view('dashboard');
    }

    /**
     * プロフィール編集画面
     * GET /profile
     */
    public function profile()
    {
        return view('profile');
    }
}
