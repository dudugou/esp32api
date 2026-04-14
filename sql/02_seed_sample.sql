-- ====================================
-- ESP32 API Sample Data
-- ====================================
-- Created: 2026-04-15
-- Description: Sample data for testing and development
-- ====================================

-- ====================================
-- Users Sample Data
-- ====================================
-- パスワードはすべて "password123" をハッシュ化したもの
-- 実際の使用時は適切なハッシュ値に変更してください

INSERT INTO `users` (`userid`, `password`, `email`, `is_active`, `created_at`, `updated_at`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@example.com', 1, NOW(), NOW()),
('testuser', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'test@example.com', 1, NOW(), NOW()),
('demo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'demo@example.com', 1, NOW(), NOW());

-- ====================================
-- 注意事項
-- ====================================
-- 1. パスワードハッシュは password_hash('password123', PASSWORD_DEFAULT) で生成した例です
-- 2. 本番環境では必ず強力なパスワードを使用してください
-- 3. is_active が 0 のユーザーはログインできません
-- 4. email は NULL 可能ですが、パスワードリセット機能には必要です
