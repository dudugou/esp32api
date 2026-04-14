<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class Language extends BaseController
{
    /**
     * 言語切り替え
     * GET /language/switch/{locale}
     */
    public function switch($locale = null)
    {
        $supportedLocales = ['ja', 'en', 'zh-CN', 'ko'];
        
        if (!$locale || !in_array($locale, $supportedLocales)) {
            $locale = 'ja'; // デフォルトは日本語
        }
        
        // セッションに言語を保存
        session()->set('locale', $locale);
        
        // リファラーに戻る（またはダッシュボードへ）
        $redirect = $this->request->getServer('HTTP_REFERER') ?? '/dashboard';
        
        return redirect()->to($redirect);
    }
    
    /**
     * API用言語切り替え
     * POST /api/language/switch
     */
    public function apiSwitch()
    {
        $locale = $this->request->getJSON(true)['locale'] ?? $this->request->getPost('locale');
        $supportedLocales = ['ja', 'en', 'zh-CN', 'ko'];
        
        if (!$locale || !in_array($locale, $supportedLocales)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid locale'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }
        
        // セッションに言語を保存
        session()->set('locale', $locale);
        
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Language switched',
            'locale' => $locale
        ]);
    }
    
    /**
     * 現在の言語を取得
     * GET /api/language/current
     */
    public function current()
    {
        $locale = session()->get('locale') ?? 'ja';
        
        return $this->response->setJSON([
            'status' => 'success',
            'locale' => $locale
        ]);
    }
}
