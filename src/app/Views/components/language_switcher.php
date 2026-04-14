<!-- 言語切り替えドロップダウン --><div class="relative inline-block text-left" id="languageSwitcher">
    <button 
        type="button" 
        onclick="toggleLanguageMenu()"
        class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
    >
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
        </svg>
        <span id="currentLangName">日本語</span>
        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div id="languageMenu" class="hidden absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5">
        <div class="py-1" role="menu">
            <a href="#" onclick="switchLanguage('ja'); return false;" class="lang-option block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" data-lang="ja">
                <span class="font-medium">日本語</span>
            </a>
            <a href="#" onclick="switchLanguage('en'); return false;" class="lang-option block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" data-lang="en">
                <span class="font-medium">English</span>
            </a>
            <a href="#" onclick="switchLanguage('zh-CN'); return false;" class="lang-option block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" data-lang="zh-CN">
                <span class="font-medium">中文</span>
            </a>
            <a href="#" onclick="switchLanguage('ko'); return false;" class="lang-option block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" data-lang="ko">
                <span class="font-medium">한국어</span>
            </a>
        </div>
    </div>
</div>

<script>
    const languageNames = {
        'ja': '日本語',
        'en': 'English',
        'zh-CN': '中文',
        'ko': '한국어'
    };

    // 言語メニューの開閉
    function toggleLanguageMenu() {
        const menu = document.getElementById('languageMenu');
        menu.classList.toggle('hidden');
    }

    // 言語切り替え
    function switchLanguage(locale) {
        fetch('/api/language/switch', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ locale: locale })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // 現在の言語名を更新
                document.getElementById('currentLangName').textContent = languageNames[locale];
                // メニューを閉じる
                document.getElementById('languageMenu').classList.add('hidden');
                // ページをリロード
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Language switch error:', error);
        });
    }

    // メニュー外クリックで閉じる
    document.addEventListener('click', function(event) {
        const switcher = document.getElementById('languageSwitcher');
        const menu = document.getElementById('languageMenu');
        if (switcher && !switcher.contains(event.target)) {
            menu.classList.add('hidden');
        }
    });

    // 現在の言語を取得して表示
    fetch('/api/language/current')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.locale) {
                document.getElementById('currentLangName').textContent = languageNames[data.locale] || languageNames['ja'];
            }
        })
        .catch(error => {
            console.error('Get current language error:', error);
        });
</script>
