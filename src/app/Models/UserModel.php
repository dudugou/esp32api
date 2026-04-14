<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['userid', 'password', 'email', 'is_active'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'userid'   => 'required|min_length[3]|max_length[100]|is_unique[users.userid]',
        'password' => 'required|min_length[6]',
        'email'    => 'permit_empty|valid_email',
    ];
    protected $validationMessages   = [
        'userid' => [
            'required'    => 'ユーザーIDは必須です',
            'min_length'  => 'ユーザーIDは3文字以上である必要があります',
            'is_unique'   => 'このユーザーIDは既に使用されています',
        ],
        'password' => [
            'required'    => 'パスワードは必須です',
            'min_length'  => 'パスワードは6文字以上である必要があります',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['hashPassword'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * パスワードをハッシュ化
     */
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_BCRYPT);
        }
        return $data;
    }

    /**
     * ユーザーIDとパスワードで認証
     */
    public function authenticate(string $userid, string $password)
    {
        $user = $this->where('userid', $userid)
                     ->where('is_active', 1)
                     ->first();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }
}
