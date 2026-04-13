-- Gamers Hub — database and users table
-- Run this in phpMyAdmin (SQL tab) or: mysql -u root -p < schema.sql

CREATE DATABASE IF NOT EXISTS gamers_hub
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE gamers_hub;

CREATE TABLE IF NOT EXISTS users (
  id INT NOT NULL AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL,
  email_or_phone VARCHAR(100) NOT NULL,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
