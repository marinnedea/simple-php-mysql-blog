-- Migration: add featured_image to posts table
ALTER TABLE posts ADD COLUMN featured_image VARCHAR(255) NULL AFTER category_id;
