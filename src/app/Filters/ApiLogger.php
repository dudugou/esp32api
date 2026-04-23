<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * APIアクセスログフィルター
 * すべてのAPIリクエストの受信・レスポンスをログに記録する
 */
class ApiLogger implements FilterInterface
{
    /** @var float リクエスト開始時刻（マイクロ秒） */
    private static float $startTime;

    /**
     * リクエスト受信時にログを記録する
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        self::$startTime = microtime(true);

        $method  = $request->getMethod(true);
        $uri     = (string) $request->getUri();
        $ip      = $request->getIPAddress();
        $ua      = $request->getUserAgent()->getAgentString() ?: '-';

        // リクエストボディ（バイナリ・ファイルアップロードはスキップ、passwordフィールドはマスク）
        $contentType = $request->getHeaderLine('Content-Type');
        $isBinary = preg_match('#(image/|audio/|video/|application/octet-stream|multipart/form-data)#i', $contentType);

        $body = '';
        if (!$isBinary) {
            $body = $request->getBody() ?: '';
            if (!empty($body)) {
                $decoded = json_decode($body, true);
                if (is_array($decoded)) {
                    if (isset($decoded['password'])) {
                        $decoded['password'] = '***';
                    }
                    $body = json_encode($decoded, JSON_UNESCAPED_UNICODE);
                } else {
                    // フォームデータの場合
                    $post = $request->getPost();
                    if (!empty($post)) {
                        if (isset($post['password'])) {
                            $post['password'] = '***';
                        }
                        $body = http_build_query($post);
                    }
                }
            }
        }

        // クエリパラメータ
        $query = $request->getUri()->getQuery();

        $bodyLog = '';
        if ($isBinary) {
            $size = strlen($request->getBody() ?: '');
            $bodyLog = " BODY=[binary {$size} bytes skipped]";
        } elseif ($body !== '') {
            $bodyLog = " BODY={$body}";
        }
        $queryLog = $query !== '' ? " QUERY={$query}" : '';

        log_message('info', "[API REQUEST] {$method} {$uri} IP={$ip} UA={$ua}{$queryLog}{$bodyLog}");
    }

    /**
     * レスポンス送信時にステータスとレスポンス時間をログに記録する
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $elapsed = isset(self::$startTime)
            ? round((microtime(true) - self::$startTime) * 1000, 2)
            : 0;

        $method     = $request->getMethod(true);
        $uri        = (string) $request->getUri();
        $statusCode = $response->getStatusCode();
        $ip         = $request->getIPAddress();

        // ステータスコードに応じてログレベルを変更
        if ($statusCode >= 500) {
            $level = 'error';
        } elseif ($statusCode >= 400) {
            $level = 'warning';
        } else {
            $level = 'info';
        }

        log_message($level, "[API RESPONSE] {$method} {$uri} STATUS={$statusCode} IP={$ip} TIME={$elapsed}ms");
    }
}
