-- ====================================
-- ESP32 API Database Schema
-- ====================================
-- Created: 2026-04-15
-- Description: Database structure for ESP32 API application
-- Database Name: esp32api_db
-- ====================================

-- データベース作成（既存の場合は削除）
-- DROP DATABASE IF EXISTS esp32api_db;
-- CREATE DATABASE esp32api_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE esp32api_db;

-- ====================================
-- Users Table
-- ====================================
-- ユーザー認証と管理のためのテーブル
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ユーザーID（主キー）',
    `userid` VARCHAR(100) NOT NULL COMMENT 'ユーザー名（ログインID）',
    `password` VARCHAR(255) NOT NULL COMMENT 'ハッシュ化されたパスワード',
    `email` VARCHAR(255) DEFAULT NULL COMMENT 'メールアドレス',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'アクティブ状態（0:無効, 1:有効）',
    `created_at` DATETIME DEFAULT NULL COMMENT '作成日時',
    `updated_at` DATETIME DEFAULT NULL COMMENT '更新日時',
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_userid` (`userid`),
    KEY `idx_userid` (`userid`),
    KEY `idx_email` (`email`),
    KEY `idx_is_active` (`is_active`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ユーザー情報テーブル';

-- ====================================
-- Index説明
-- ====================================
-- unique_userid: ユーザーIDの一意性を保証
-- idx_userid: ログイン時の検索高速化
-- idx_email: メールアドレスでの検索高速化
-- idx_is_active: アクティブユーザーのフィルタリング高速化
-- idx_created_at: 登録日時でのソート・フィルタリング高速化
