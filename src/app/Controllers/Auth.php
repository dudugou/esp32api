<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;

class Auth extends ResourceController
{
    protected $modelName = 'App\Models\UserModel';
    protected $format    = 'json';

    public function __construct()
    {
        helper('jwt');
    }

    /**
     * ユーザー登録
     * POST /api/auth/register
     */
    public function register()
    {
        $rules = [
            'userid'   => 'required|min_length[3]|max_length[100]|is_unique[users.userid]',
            'password' => 'required|min_length[6]',
            'email'    => 'permit_empty|valid_email',
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors(), 400);
        }

        $data = [
            'userid'   => $this->request->getVar('userid'),
            'password' => $this->request->getVar('password'),
            'email'    => $this->request->getVar('email'),
        ];

        $model = new UserModel();
        
        if ($model->save($data)) {
            $user = $model->where('userid', $data['userid'])->first();
            unset($user['password']);
            log_message('info', '[AUTH] ユーザー登録 userid=' . $data['userid'] . ' IP=' . $this->request->getIPAddress());
            return $this->respondCreated([
                'status'  => 'success',
                'message' => 'ユーザー登録が完了しました',
                'data'    => $user
            ]);
        } else {
            log_message('error', '[AUTH] ユーザー登録失敗 userid=' . $data['userid'] . ' IP=' . $this->request->getIPAddress());
            return $this->fail('ユーザー登録に失敗しました', 500);
        }
    }

    /**
     * ログイン（JWT発行）
     * POST /api/auth/login
     */
    public function login()
    {
        $rules = [
            'userid'   => 'required',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors(), 400);
        }

        $userid   = $this->request->getVar('userid');
        $password = $this->request->getVar('password');

        $model = new UserModel();
        $user = $model->authenticate($userid, $password);

        if (!$user) {
            log_message('warning', '[AUTH] ログイン失敗 userid=' . $userid . ' IP=' . $this->request->getIPAddress());
            return $this->fail('ユーザーIDまたはパスワードが正しくありません', 401);
        }

        // JWTトークンを生成
        $token = generateJWT($user);

        unset($user['password']);
        log_message('info', '[AUTH] ログイン成功 userid=' . $userid . ' user_id=' . ($user['id'] ?? '-') . ' IP=' . $this->request->getIPAddress());

        return $this->respond([
            'status'  => 'success',
            'message' => 'ログインしました',
            'data'    => [
                'user'  => $user,
                'token' => $token
            ]
        ]);
    }

    /**
     * 認証確認（保護されたエンドポイント）
     * GET /api/auth/me
     */
    public function me()
    {
        $decoded = verifyJWT(getJWTFromRequest());
        
        if (!$decoded) {
            return $this->fail('トークンが無効です', 401);
        }

        return $this->respond([
            'status'  => 'success',
            'message' => '認証済みユーザー情報',
            'data'    => $decoded->data
        ]);
    }

    /**
     * ユーザー情報更新（保護されたエンドポイント）
     * PUT /api/auth/profile
     */
    public function updateProfile()
    {
        $decoded = verifyJWT(getJWTFromRequest());
        
        if (!$decoded) {
            return $this->fail('トークンが無効です', 401);
        }

        $userId = $decoded->data->id;
        $model = new UserModel();
        $user = $model->find($userId);

        if (!$user) {
            return $this->fail('ユーザーが見つかりません', 404);
        }

        // 更新データを準備
        $updateData = [];

        // メールアドレスの更新
        $email = $this->request->getVar('email');
        if ($email !== null && $email !== '') {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->fail(['email' => '有効なメールアドレスを入力してください'], 400);
            }
            $updateData['email'] = $email;
        }

        // パスワードの更新
        $password = $this->request->getVar('password');
        $currentPassword = $this->request->getVar('current_password');
        
        if ($password !== null && $password !== '') {
            // 現在のパスワードを確認
            if (!$currentPassword || !password_verify($currentPassword, $user['password'])) {
                return $this->fail(['current_password' => '現在のパスワードが正しくありません'], 400);
            }

            if (strlen($password) < 6) {
                return $this->fail(['password' => 'パスワードは6文字以上で入力してください'], 400);
            }

            $updateData['password'] = $password;
        }

        // 更新するデータがない場合
        if (empty($updateData)) {
            return $this->fail('更新するデータがありません', 400);
        }

        // データ更新
        if ($model->update($userId, $updateData)) {
            $updatedUser = $model->find($userId);
            unset($updatedUser['password']);
            log_message('info', '[AUTH] プロフィール更新成功 user_id=' . $userId . ' IP=' . $this->request->getIPAddress());
            return $this->respond([
                'status'  => 'success',
                'message' => 'ユーザー情報を更新しました',
                'data'    => $updatedUser
            ]);
        } else {
            log_message('error', '[AUTH] プロフィール更新失敗 user_id=' . $userId . ' IP=' . $this->request->getIPAddress());
            return $this->fail('ユーザー情報の更新に失敗しました', 500);
        }
    }
}
