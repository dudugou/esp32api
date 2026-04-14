<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= lang('App.profile.title') ?> - ESP32 API</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50">
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
                    <?= view('components/language_switcher') ?>
                    <a href="/dashboard" class="text-indigo-600 hover:text-indigo-800 font-medium">
                        <svg class="inline-block w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <?= lang('App.nav.dashboard') ?>
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

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <!-- ヘッダー -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2"><?= lang('App.profile.title') ?></h1>
                <p class="text-gray-600"><?= lang('App.profile.subtitle') ?></p>
            </div>

            <!-- ユーザーID表示（変更不可） -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center space-x-3 mb-4">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <h2 class="text-xl font-semibold text-gray-700"><?= lang('App.profile.currentInfo') ?></h2>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500 mb-1"><?= lang('App.common.userId') ?></p>
                            <p class="text-lg font-medium text-gray-800" id="currentUserId"></p>
                        </div>
                        <span class="bg-gray-200 text-gray-600 px-3 py-1 rounded-full text-sm"><?= lang('App.profile.notEditable') ?></span>
                    </div>
                </div>
            </div>

            <!-- メールアドレス更新フォーム -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center space-x-3 mb-4">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <h2 class="text-xl font-semibold text-gray-700"><?= lang('App.profile.emailSection') ?></h2>
                </div>
                <form id="emailForm" class="space-y-4">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2"><?= lang('App.profile.newEmail') ?></label>
                        <input 
                            type="email"
                            id="emailInput"
                            placeholder="<?= lang('App.profile.emailPlaceholder') ?>"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        >
                        <p class="mt-2 text-sm text-gray-500" id="currentEmail"><?= lang('App.profile.currentEmail') ?>: <?= lang('App.profile.none') ?></p>
                    </div>
                    <button
                        type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <?= lang('App.profile.updateEmail') ?>
                    </button>
                </form>
            </div>

            <!-- パスワード変更フォーム -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center space-x-3 mb-4">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <h2 class="text-xl font-semibold text-gray-700"><?= lang('App.profile.passwordSection') ?></h2>
                </div>
                <form id="passwordForm" class="space-y-4">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2"><?= lang('App.profile.currentPassword') ?></label>
                        <div class="relative">
                            <input 
                                type="password"
                                id="currentPasswordInput"
                                placeholder="<?= lang('App.profile.currentPasswordPlaceholder') ?>"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            >
                            <button
                                type="button"
                                onclick="togglePasswordVisibility('currentPasswordInput', 'currentEyeIcon')"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                            >
                                <svg id="currentEyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2"><?= lang('App.profile.newPassword') ?></label>
                        <div class="relative">
                            <input 
                                type="password"
                                id="newPasswordInput"
                                placeholder="<?= lang('App.profile.newPasswordPlaceholder') ?>"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            >
                            <button
                                type="button"
                                onclick="togglePasswordVisibility('newPasswordInput', 'newEyeIcon')"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                            >
                                <svg id="newEyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2"><?= lang('App.profile.newPasswordConfirm') ?></label>
                        <div class="relative">
                            <input 
                                type="password"
                                id="confirmPasswordInput"
                                placeholder="<?= lang('App.profile.newPasswordConfirmPlaceholder') ?>"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            >
                            <button
                                type="button"
                                onclick="togglePasswordVisibility('confirmPasswordInput', 'confirmEyeIcon')"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                            >
                                <svg id="confirmEyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <button
                        type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <?= lang('App.profile.updatePassword') ?>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // 多言語対応メッセージ
        const messages = {
            authError: '<?= lang('App.msg.authError') ?>',
            pleaseLogin: '<?= lang('App.msg.pleaseLogin') ?>',
            current: '<?= lang('App.profile.currentEmail') ?>',
            inputError: '<?= lang('App.msg.inputError') ?>',
            pleaseEnterEmail: '<?= lang('App.msg.pleaseEnterEmail') ?>',
            updateComplete: '<?= lang('App.msg.updateComplete') ?>',
            emailUpdated: '<?= lang('App.msg.emailUpdated') ?>',
            updateFailed: '<?= lang('App.msg.updateFailed') ?>',
            emailUpdateFailed: '<?= lang('App.msg.emailUpdateFailed') ?>',
            error: '<?= lang('App.msg.error') ?>',
            networkError: '<?= lang('App.msg.networkError') ?>',
            pleaseEnterAllFields: '<?= lang('App.msg.pleaseEnterAllFields') ?>',
            passwordMinLength: '<?= lang('App.msg.passwordMinLength') ?>',
            passwordMismatch: '<?= lang('App.msg.passwordMismatch') ?>',
            passwordUpdated: '<?= lang('App.msg.passwordUpdated') ?>',
            passwordUpdateFailed: '<?= lang('App.msg.passwordUpdateFailed') ?>',
            logoutConfirm: '<?= lang('App.msg.logoutConfirm') ?>',
            yes: '<?= lang('App.common.yes') ?>',
            cancel: '<?= lang('App.common.cancel') ?>'
        };

        // ページ読み込み時の初期化
        window.addEventListener('DOMContentLoaded', async () => {
            // ログイン確認
            const token = localStorage.getItem('jwt_token');
            const user = JSON.parse(localStorage.getItem('user') || '{}');

            if (!token || !user.userid) {
                Swal.fire({
                    icon: 'error',
                    title: messages.authError,
                    text: messages.pleaseLogin,
                }).then(() => {
                    window.location.href = '/login';
                });
                return;
            }

            // ユーザー情報を表示
            document.getElementById('currentUserId').textContent = user.userid;
            if (user.email) {
                document.getElementById('currentEmail').textContent = `${messages.current}: ${user.email}`;
                document.getElementById('emailInput').value = user.email;
            }
        });

        // パスワード表示/非表示の切り替え
        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                `;
            } else {
                input.type = 'password';
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                `;
            }
        }

        // メールアドレス更新
        document.getElementById('emailForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const email = document.getElementById('emailInput').value.trim();
            const token = localStorage.getItem('jwt_token');

            if (!email) {
                Swal.fire({
                    icon: 'warning',
                    title: messages.inputError,
                    text: messages.pleaseEnterEmail,
                });
                return;
            }

            try {
                const response = await fetch('/api/auth/profile', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({ email })
                });

                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    // ローカルストレージのユーザー情報を更新
                    localStorage.setItem('user', JSON.stringify(data.data));
                    
                    Swal.fire({
                        icon: 'success',
                        title: messages.updateComplete,
                        text: messages.emailUpdated,
                    }).then(() => {
                        document.getElementById('currentEmail').textContent = `${messages.current}: ${data.data.email}`;
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: messages.updateFailed,
                        text: data.message || messages.emailUpdateFailed,
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: messages.error,
                    text: messages.networkError,
                });
            }
        });

        // パスワード更新
        document.getElementById('passwordForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const currentPassword = document.getElementById('currentPasswordInput').value;
            const newPassword = document.getElementById('newPasswordInput').value;
            const confirmPassword = document.getElementById('confirmPasswordInput').value;
            const token = localStorage.getItem('jwt_token');

            // バリデーション
            if (!currentPassword || !newPassword || !confirmPassword) {
                Swal.fire({
                    icon: 'warning',
                    title: messages.inputError,
                    text: messages.pleaseEnterAllFields,
                });
                return;
            }

            if (newPassword.length < 6) {
                Swal.fire({
                    icon: 'warning',
                    title: messages.inputError,
                    text: messages.passwordMinLength,
                });
                return;
            }

            if (newPassword !== confirmPassword) {
                Swal.fire({
                    icon: 'warning',
                    title: messages.inputError,
                    text: messages.passwordMismatch,
                });
                return;
            }

            try {
                const response = await fetch('/api/auth/profile', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({ 
                        current_password: currentPassword,
                        password: newPassword 
                    })
                });

                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: messages.updateComplete,
                        text: messages.passwordUpdated,
                    }).then(() => {
                        // フォームをクリア
                        document.getElementById('passwordForm').reset();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: messages.updateFailed,
                        text: data.message || messages.passwordUpdateFailed,
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: messages.error,
                    text: messages.networkError,
                });
            }
        });

        // ログアウト
        function logout() {
            Swal.fire({
                title: messages.logoutConfirm,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: messages.yes,
                cancelButtonText: messages.cancel
            }).then((result) => {
                if (result.isConfirmed) {
                    localStorage.removeItem('jwt_token');
                    localStorage.removeItem('user');
                    window.location.href = '/login';
                }
            });
        }
    </script>
</body>
</html>
