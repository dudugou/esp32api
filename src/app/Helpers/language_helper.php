<?php

if (!function_exists('get_locale')) {
    /**
     * 現在の言語を取得
     */
    function get_locale(): string
    {
        return session()->get('locale') ?? service('request')->getLocale();
    }
}

if (!function_exists('set_locale')) {
    /**
     * 言語を設定
     */
    function set_locale(string $locale): void
    {
        session()->set('locale', $locale);
        service('request')->setLocale($locale);
    }
}

if (!function_exists('get_language_name')) {
    /**
     * 言語コードから言語名を取得
     */
    function get_language_name(string $locale): string
    {
        $languages = [
            'ja' => '日本語',
            'en' => 'English',
            'zh-CN' => '中文',
            'ko' => '한국어',
        ];
        
        return $languages[$locale] ?? $locale;
    }
}

if (!function_exists('get_supported_locales')) {
    /**
     * サポートされている言語のリストを取得
     */
    function get_supported_locales(): array
    {
        return [
            'ja' => '日本語',
            'en' => 'English',
            'zh-CN' => '中文',
            'ko' => '한국어',
        ];
    }
}
