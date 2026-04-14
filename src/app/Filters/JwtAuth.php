<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;
use Config\Services;

class JwtAuth implements FilterInterface
{
    use ResponseTrait;

    /**
     * JWTトークンの検証を行う
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        helper('jwt');
        
        $token = getJWTFromRequest();
        
        if (!$token) {
            return Services::response()
                ->setJSON([
                    'status' => 'error',
                    'message' => 'トークンが提供されていません'
                ])
                ->setStatusCode(401);
        }

        $decoded = verifyJWT($token);
        
        if (!$decoded) {
            return Services::response()
                ->setJSON([
                    'status' => 'error',
                    'message' => 'トークンが無効または期限切れです'
                ])
                ->setStatusCode(401);
        }

        // リクエストにユーザー情報を追加
        $request->user = $decoded->data;
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
