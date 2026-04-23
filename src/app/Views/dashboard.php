<?php $currentLocale = service('request')->getLocale(); ?>
<!DOCTYPE html>
<html lang="<?= $currentLocale ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= lang('App.dashboard.title') ?> - ESP32 API</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Sans+JP:wght@400;500;700&family=Noto+Sans+KR:wght@400;500;700&family=Noto+Sans+SC:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body.lang-ja { font-family: 'Noto Sans JP', sans-serif; }
        body.lang-en { font-family: 'Inter', sans-serif; }
        body.lang-ko { font-family: 'Noto Sans KR', sans-serif; }
        body.lang-zh-CN { font-family: 'Noto Sans SC', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 lang-<?= $currentLocale ?>">
    <!-- ナビゲーションバー -->
    <nav class="bg-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                    </svg>
                    <span class="text-2xl font-bold text-gray-800">ESP32 API</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600" id="userName"></span>
                    <?= view('components/language_switcher') ?>
                    <a 
                        href="/profile"
                        class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg transition"
                    >
                        <svg class="inline-block w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <?= lang('App.nav.profile') ?>
                    </a>
                    <button 
                        onclick="logout()"
                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition"
                    >
                        <svg class="inline-block w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <?= lang('App.nav.logout') ?>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- メインコンテンツ -->
    <div class="container mx-auto px-4 py-8">
        <!-- ウェルカムメッセージ -->
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-xl p-8 text-white mb-8">
            <h1 class="text-3xl font-bold mb-2" id="welcomeMessage"></h1>
            <p class="text-indigo-100"><?= lang('App.dashboard.subtitle') ?></p>
        </div>

        <!-- ユーザー情報カード -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- ユーザー情報 -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center mb-4">
                    <svg class="w-8 h-8 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <h2 class="text-xl font-bold text-gray-800"><?= lang('App.dashboard.userInfo') ?></h2>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600"><?= lang('App.common.userId') ?>:</span>
                        <span class="font-semibold" id="userIdDisplay"></span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600"><?= lang('App.common.email') ?>:</span>
                        <span class="font-semibold" id="userEmailDisplay"></span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600"><?= lang('App.dashboard.status') ?>:</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            <?= lang('App.dashboard.active') ?>
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600"><?= lang('App.dashboard.registeredDate') ?>:</span>
                        <span class="font-semibold" id="userCreatedDisplay"></span>
                    </div>
                </div>
            </div>

            <!-- JWTトークン情報 -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center mb-4">
                    <svg class="w-8 h-8 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                    <h2 class="text-xl font-bold text-gray-800"><?= lang('App.dashboard.authToken') ?></h2>
                </div>
                <div class="bg-gray-50 rounded p-4 mb-4">
                    <p class="text-xs text-gray-500 mb-2"><?= lang('App.dashboard.jwtToken') ?>:</p>
                    <p class="text-xs font-mono bg-white p-3 rounded border break-all" id="tokenDisplay"></p>
                </div>
                <button 
                    onclick="copyToken()"
                    class="w-full bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg transition"
                >
                    <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <?= lang('App.dashboard.copyToken') ?>
                </button>
            </div>
        </div>

        <!-- API テストセクション -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <div class="flex items-center mb-6">
                <svg class="w-8 h-8 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h2 class="text-xl font-bold text-gray-800"><?= lang('App.dashboard.apiTest') ?></h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- 公開API -->
                <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-indigo-300 transition">
                    <h3 class="font-semibold text-gray-800 mb-2"><?= lang('App.dashboard.publicApi') ?></h3>
                    <p class="text-sm text-gray-600 mb-4"><?= lang('App.dashboard.noAuth') ?></p>
                    <button 
                        onclick="testPublicAPI()"
                        class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition"
                    >
                        GET /api/public
                    </button>
                </div>

                <!-- 保護されたAPI -->
                <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-indigo-300 transition">
                    <h3 class="font-semibold text-gray-800 mb-2"><?= lang('App.dashboard.protectedApi') ?></h3>
                    <p class="text-sm text-gray-600 mb-4"><?= lang('App.dashboard.jwtRequired') ?></p>
                    <button 
                        onclick="testProtectedAPI()"
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition"
                    >
                        GET /api/protected
                    </button>
                </div>

                <!-- デバイスデータ送信 -->
                <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-indigo-300 transition">
                    <h3 class="font-semibold text-gray-800 mb-2"><?= lang('App.dashboard.sendData') ?></h3>
                    <p class="text-sm text-gray-600 mb-4"><?= lang('App.dashboard.sendDataDesc') ?></p>
                    <button 
                        onclick="testSendData()"
                        class="w-full bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg transition"
                    >
                        POST /api/device/data
                    </button>
                </div>

                <!-- デバイスデータ取得 -->
                <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-indigo-300 transition">
                    <h3 class="font-semibold text-gray-800 mb-2"><?= lang('App.dashboard.getData') ?></h3>
                    <p class="text-sm text-gray-600 mb-4"><?= lang('App.dashboard.getDataDesc') ?></p>
                    <button 
                        onclick="testGetData()"
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition"
                    >
                        GET /api/device/data
                    </button>
                </div>
            </div>

            <!-- レスポンス表示エリア -->
            <div id="responseArea" class="mt-6 hidden">
                <h3 class="font-semibold text-gray-800 mb-2"><?= lang('App.dashboard.apiResponse') ?>:</h3>
                <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto text-sm" id="responseContent"></pre>
            </div>
        </div>

        <!-- ドキュメントリンク -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center mb-4">
                <svg class="w-8 h-8 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <h2 class="text-xl font-bold text-gray-800"><?= lang('App.dashboard.documentation') ?></h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="/API_DOCUMENTATION.md" target="_blank" class="border-2 border-gray-200 rounded-lg p-4 hover:border-indigo-300 hover:bg-indigo-50 transition text-center">
                    <svg class="w-12 h-12 mx-auto text-indigo-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="font-semibold text-gray-800"><?= lang('App.dashboard.apiDocs') ?></p>
                </a>
                <a href="/README.md" target="_blank" class="border-2 border-gray-200 rounded-lg p-4 hover:border-indigo-300 hover:bg-indigo-50 transition text-center">
                    <svg class="w-12 h-12 mx-auto text-indigo-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="font-semibold text-gray-800">README</p>
                </a>
                <a href="http://localhost:8081" target="_blank" class="border-2 border-gray-200 rounded-lg p-4 hover:border-indigo-300 hover:bg-indigo-50 transition text-center">
                    <svg class="w-12 h-12 mx-auto text-indigo-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                    </svg>
                    <p class="font-semibold text-gray-800">phpMyAdmin</p>
                </a>
            </div>
        </div>
    </div>

    <script>
        // 多言語対応メッセージ
        const messages = {
            welcome: '<?= lang('App.dashboard.welcome') ?>',
            sessionExpired: '<?= lang('App.msg.sessionExpired') ?>',
            pleaseLoginAgain: '<?= lang('App.msg.pleaseLoginAgain') ?>',
            copied: '<?= lang('App.msg.copied') ?>',
            tokenCopied: '<?= lang('App.msg.tokenCopied') ?>',
            apiSuccess: '<?= lang('App.msg.apiSuccess') ?>',
            error: '<?= lang('App.msg.error') ?>',
            dataSent: '<?= lang('App.msg.dataSent') ?>',
            dataReceived: '<?= lang('App.msg.dataReceived') ?>'
        };

        const API_BASE_URL = window.location.origin;
        let token = null;
        let user = null;

        // 初期化
        window.addEventListener('DOMContentLoaded', async () => {
            token = localStorage.getItem('jwt_token');
            const userStr = localStorage.getItem('user');
            
            if (!token || !userStr) {
                window.location.href = '/login';
                return;
            }

            user = JSON.parse(userStr);
            
            // ユーザー情報を表示
            document.getElementById('userName').textContent = user.userid;
            document.getElementById('welcomeMessage').textContent = messages.welcome.replace('{0}', user.userid);
            document.getElementById('userIdDisplay').textContent = user.userid;
            document.getElementById('userEmailDisplay').textContent = user.email || '<?= lang('App.dashboard.notSet') ?>';
            document.getElementById('userCreatedDisplay').textContent = new Date(user.created_at).toLocaleDateString('ja-JP');
            document.getElementById('tokenDisplay').textContent = token;

            // トークンの有効性を確認
            await verifyToken();
        });

        // トークン検証
        async function verifyToken() {
            try {
                const response = await fetch(`${API_BASE_URL}/api/auth/me`, {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Token invalid');
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: messages.sessionExpired,
                    text: messages.pleaseLoginAgain,
                    confirmButtonColor: '#4F46E5',
                }).then(() => {
                    logout();
                });
            }
        }

        // ログアウト
        function logout() {
            localStorage.removeItem('jwt_token');
            localStorage.removeItem('user');
            window.location.href = '/login';
        }

        // トークンをコピー
        function copyToken() {
            navigator.clipboard.writeText(token);
            Swal.fire({
                icon: 'success',
                title: messages.copied,
                text: messages.tokenCopied,
                timer: 1500,
                showConfirmButton: false
            });
        }

        // レスポンス表示
        function showResponse(data) {
            const responseArea = document.getElementById('responseArea');
            const responseContent = document.getElementById('responseContent');
            responseContent.textContent = JSON.stringify(data, null, 2);
            responseArea.classList.remove('hidden');
        }

        // 公開APIテスト
        async function testPublicAPI() {
            try {
                const response = await fetch(`${API_BASE_URL}/api/public`);
                const data = await response.json();
                showResponse(data);
                
                Swal.fire({
                    icon: 'success',
                    title: messages.apiSuccess,
                    timer: 1500,
                    showConfirmButton: false
                });
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: messages.error,
                    text: error.message
                });
            }
        }

        // 保護されたAPIテスト
        async function testProtectedAPI() {
            try {
                const response = await fetch(`${API_BASE_URL}/api/protected`, {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });
                const data = await response.json();
                showResponse(data);
                
                Swal.fire({
                    icon: 'success',
                    title: messages.apiSuccess,
                    timer: 1500,
                    showConfirmButton: false
                });
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: messages.error,
                    text: error.message
                });
            }
        }

        // データ送信テスト
        async function testSendData() {
            try {
                const response = await fetch(`${API_BASE_URL}/api/device/data`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        sensor_id: 'ESP32-001',
                        temperature: Math.random() * 10 + 20,
                        humidity: Math.random() * 20 + 50
                    })
                });
                const data = await response.json();
                showResponse(data);
                
                Swal.fire({
                    icon: 'success',
                    title: messages.dataSent,
                    timer: 1500,
                    showConfirmButton: false
                });
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: messages.error,
                    text: error.message
                });
            }
        }

        // データ取得テスト
        async function testGetData() {
            try {
                const response = await fetch(`${API_BASE_URL}/api/device/data`, {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });
                const data = await response.json();
                showResponse(data);
                
                Swal.fire({
                    icon: 'success',
                    title: messages.dataReceived,
                    timer: 1500,
                    showConfirmButton: false
                });
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: messages.error,
                    text: error.message
                });
            }
        }
    </script>
</body>
</html>
