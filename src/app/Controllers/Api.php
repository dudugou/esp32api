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

        return $this->respond([
            'status'  => 'success',
            'message' => 'デバイスデータを取得しました',
            'data'    => $sampleData,
            'user_id' => $user->id ?? null,
        ]);
    }
}
