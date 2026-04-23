<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class Api extends ResourceController
{
    protected $format = 'json';

    /**
     * 公開エンドポイント
     * GET /api/public
     */
    public function public()
    {
        return $this->respond([
            'status'  => 'success',
            'message' => 'これは公開APIです',
            'data'    => [
                'timestamp' => date('Y-m-d H:i:s'),
                'server'    => 'ESP32 API Server',
            ]
        ]);
    }

    /**
     * 保護されたエンドポイント（JWT認証が必要）
     * GET /api/protected
     */
    public function protected()
    {
        // JwtAuthフィルターで認証済み
        $user = service('request')->user ?? null;

        return $this->respond([
            'status'  => 'success',
            'message' => 'これは保護されたAPIです',
            'data'    => [
                'timestamp' => date('Y-m-d H:i:s'),
                'user'      => $user,
                'message'   => '認証されたユーザーのみアクセス可能です'
            ]
        ]);
    }

    /**
     * ESP32デバイス用データ送信エンドポイント
     * POST /api/device/data
     */
    public function deviceData()
    {
        $user = service('request')->user ?? null;
        
        $data = [
            'temperature' => $this->request->getVar('temperature'),
            'humidity'    => $this->request->getVar('humidity'),
            'sensor_id'   => $this->request->getVar('sensor_id'),
        ];

        // ここでデータベースに保存する処理を追加できます
        // 例: $dataModel->save($data);
        log_message('info', '[DEVICE] データ受信 sensor_id=' . ($data['sensor_id'] ?? '-') . ' temp=' . ($data['temperature'] ?? '-') . ' hum=' . ($data['humidity'] ?? '-') . ' user_id=' . ($user->id ?? '-') . ' IP=' . $this->request->getIPAddress());

        return $this->respond([
            'status'  => 'success',
            'message' => 'データを受信しました',
            'data'    => [
                'received'  => $data,
                'user_id'   => $user->id ?? null,
                'timestamp' => date('Y-m-d H:i:s'),
            ]
        ]);
    }

    /**
     * ESP32デバイス用データ取得エンドポイント
     * GET /api/device/data
     */
    public function getDeviceData()
    {
        $user = service('request')->user ?? null;

        // ここでデータベースからデータを取得する処理を追加できます
        $sampleData = [
            [
                'id'          => 1,
                'sensor_id'   => 'ESP32-001',
                'temperature' => 25.5,
                'humidity'    => 60.2,
                'timestamp'   => date('Y-m-d H:i:s', strtotime('-5 minutes')),
            ],
            [
                'id'          => 2,
                'sensor_id'   => 'ESP32-001',
                'temperature' => 25.8,
                'humidity'    => 59.8,
                'timestamp'   => date('Y-m-d H:i:s'),
            ],
        ];

        log_message('info', '[DEVICE] データ取得 count=' . count($sampleData) . ' user_id=' . ($user->id ?? '-') . ' IP=' . $this->request->getIPAddress());

        return $this->respond([
            'status'  => 'success',
            'message' => 'デバイスデータを取得しました',
            'data'    => $sampleData,
            'user_id' => $user->id ?? null,
        ]);
    }

    /**
     * ESP32カメラ写真アップロードエンドポイント
     * POST /api/device/photo
     *
     * multipart/form-data: フィールド名 "photo"
     * application/octet-stream または image/jpeg: リクエストボディに生バイナリ
     */
    public function uploadPhoto()
    {
        $user      = service('request')->user ?? null;
        $uploadDir = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR;

        // アップロードディレクトリが存在しない場合は作成
        if (! is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $contentType = $this->request->getHeaderLine('Content-Type');

        // ---- multipart/form-data ----
        if (strpos($contentType, 'multipart/form-data') !== false) {
            $file = $this->request->getFile('photo');

            if ($file === null || ! $file->isValid()) {
                $error = $file ? $file->getErrorString() : 'ファイルが見つかりません';
                return $this->failValidationError('アップロードエラー: ' . $error);
            }

            // MIME タイプを画像に限定
            $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/webp'];
            if (! in_array($file->getMimeType(), $allowedMimes, true)) {
                return $this->failValidationError('許可されていないファイル形式です: ' . $file->getMimeType());
            }

            // ファイルサイズ制限 (10MB)
            if ($file->getSize() > 10 * 1024 * 1024) {
                return $this->failValidationError('ファイルサイズが上限(10MB)を超えています');
            }

            $ext      = $file->getClientExtension() ?: 'jpg';
            $filename = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

            if (! $file->move($uploadDir, $filename)) {
                return $this->failServerError('ファイルの保存に失敗しました');
            }

            return $this->respondCreated([
                'status'    => 'success',
                'message'   => '写真をアップロードしました',
                'data'      => [
                    'filename'  => $filename,
                    'size'      => $file->getSize(),
                    'mime'      => $file->getMimeType(),
                    'path'      => 'writable/uploads/' . $filename,
                    'user_id'   => $user->id ?? null,
                    'timestamp' => date('Y-m-d H:i:s'),
                ],
            ]);
        }

        // ---- application/octet-stream / image/* (生バイナリ) ----
        if (strpos($contentType, 'image/') !== false || strpos($contentType, 'application/octet-stream') !== false) {
            $rawData = $this->request->getBody();

            if (empty($rawData)) {
                return $this->failValidationError('リクエストボディが空です');
            }

            // ファイルサイズ制限 (10MB)
            if (strlen($rawData) > 10 * 1024 * 1024) {
                return $this->failValidationError('ファイルサイズが上限(10MB)を超えています');
            }

            // JPEG マジックバイト確認 (FF D8 FF)
            $ext = 'bin';
            if (substr($rawData, 0, 2) === "\xFF\xD8") {
                $ext = 'jpg';
            } elseif (substr($rawData, 0, 4) === "\x89PNG") {
                $ext = 'png';
            }

            $filename = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $filePath = $uploadDir . $filename;

            if (file_put_contents($filePath, $rawData) === false) {
                return $this->failServerError('ファイルの保存に失敗しました');
            }
            chmod($filePath, 0644);

            return $this->respondCreated([
                'status'    => 'success',
                'message'   => '写真をアップロードしました',
                'data'      => [
                    'filename'  => $filename,
                    'size'      => strlen($rawData),
                    'path'      => 'writable/uploads/' . $filename,
                    'user_id'   => $user->id ?? null,
                    'timestamp' => date('Y-m-d H:i:s'),
                ],
            ]);
        }

        return $this->failValidationError('サポートされていない Content-Type です: ' . $contentType);
    }
}
