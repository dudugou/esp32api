<?php $currentLocale = service('request')->getLocale(); ?>
<!DOCTYPE html>
<html lang="<?= $currentLocale ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン - ESP32 API</title>
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
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen lang-<?= $currentLocale ?>">
    <!-- 言語切り替え（右上） -->
    <div class="absolute top-4 right-4">
        <?= view('components/language_switcher') ?>
    </div>
    
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto">
            <!-- ロゴ・ヘッダー -->
            <div class="text-center mb-8">
                <div class="inline-block bg-white rounded-full p-4 shadow-lg mb-4">
                    <svg class="w-16 h-16 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-800"><?= lang('App.login.subtitle') ?></h1>
                <p class="text-gray-600 mt-2"><?= lang('App.login.title') ?></p>
            </div>

            <!-- ログインフォーム -->
            <div class="bg-white rounded-lg shadow-xl p-8">
                <form id="loginForm" class="space-y-6">
                    <!-- ユーザーID -->
                    <div>
                        <label for="userid" class="block text-sm font-medium text-gray-700 mb-2">
                            <?= lang('App.login.userId') ?>
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                id="userid" 
                                name="userid" 
                                required
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                placeholder="<?= lang('App.login.userId') ?>"
                                autofocus
                            >
                        </div>
                    </div>

                    <!-- パスワード -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <?= lang('App.login.password') ?>
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                placeholder="<?= lang('App.login.password') ?>"
                            >
                            <button 
                                type="button"
                                onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center"
                            >
                                <svg id="password-eye" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- エラーメッセージ -->
                    <div id="errorMessage" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <span id="errorText"></span>
                        </div>
                    </div>

                    <!-- ログインボタン -->
                    <button 
                        type="submit" 
                        id="submitBtn"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        <?= lang('App.login.submit') ?>
                    </button>
                </form>

                <!-- 登録リンク -->
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">または</span>
                        </div>
                    </div>

                    <div class="mt-6 text-center">
                        <p class="text-gray-600">
                            <?= lang('App.login.noAccount') ?>
                        </p>
                        <a 
                            href="/register" 
                            class="mt-2 inline-block w-full bg-white hover:bg-gray-50 text-indigo-600 font-semibold py-3 px-4 border-2 border-indigo-600 rounded-lg transition duration-200"
                        >
                            <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            <?= lang('App.login.registerLink') ?>
                        </a>
                    </div>
                </div>
            </div>

            <!-- フッター -->
            <div class="mt-8 text-center text-gray-500 text-sm">
                <p>&copy; 2026 ESP32 API. <?= lang('App.common.copyright') ?></p>
            </div>
        </div>
    </div>

    <script>
        // 多言語対応メッセージ
        const messages = {
            loggingIn: '<?= lang('App.login.loggingIn') ?>',
            loginSuccess: '<?= lang('App.msg.loginSuccess') ?>',
            redirectToDashboard: '<?= lang('App.msg.redirectToDashboard') ?>',
            loginFailed: '<?= lang('App.msg.loginFailed') ?>',
            networkError: '<?= lang('App.msg.networkError') ?>',
            submit: '<?= lang('App.login.submit') ?>'
        };

        const API_BASE_URL = window.location.origin;

        // パスワード表示/非表示切り替え
        function togglePassword() {
            const field = document.getElementById('password');
            field.type = field.type === 'password' ? 'text' : 'password';
        }

        // エラーメッセージ表示
        function showError(message) {
            const errorDiv = document.getElementById('errorMessage');
            const errorText = document.getElementById('errorText');
            errorText.textContent = message;
            errorDiv.classList.remove('hidden');
            
            setTimeout(() => {
                errorDiv.classList.add('hidden');
            }, 5000);
        }

        // フォーム送信
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const userid = document.getElementById('userid').value;
            const password = document.getElementById('password').value;
            
            // 送信中の状態
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin inline-block w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>' + messages.loggingIn;
            
            try {
                const response = await fetch(`${API_BASE_URL}/api/auth/login`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        userid: userid,
                        password: password
                    })
                });
                
                const data = await response.json();
                
                if (response.ok && data.status === 'success') {
                    // トークンをlocalStorageに保存
                    localStorage.setItem('jwt_token', data.data.token);
                    localStorage.setItem('user', JSON.stringify(data.data.user));
                    
                    // 成功
                    await Swal.fire({
                        icon: 'success',
                        title: messages.loginSuccess,
                        text: messages.redirectToDashboard,
                        confirmButtonColor: '#4F46E5',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    window.location.href = '/dashboard';
                } else {
                    // エラー
                    let errorMsg = messages.loginFailed;
                    if (data.message) {
                        errorMsg = data.message;
                    }
                    showError(errorMsg);
                }
            } catch (error) {
                console.error('Error:', error);
                showError(messages.networkError);
            } finally {
                // ボタンを元に戻す
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>' + messages.submit;
            }
        });

        // ページ読み込み時：既にログイン済みの場合はダッシュボードへ
        window.addEventListener('DOMContentLoaded', () => {
            const token = localStorage.getItem('jwt_token');
            if (token) {
                window.location.href = '/dashboard';
            }
        });
    </script>
</body>
</html>
