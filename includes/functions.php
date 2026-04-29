<?php

/**
 * Sanitise HTML from TinyMCE before storing.
 * Strips tags not in the allowlist. Since only admins post content,
 * this is a safety net rather than a hard security boundary.
 */
function sanitize_html(string $html): string {
    $allowed = implode('', [
        '<p><br><strong><b><em><i><u><s>',
        '<h2><h3><h4><h5><h6>',
        '<ul><ol><li><blockquote><pre><code><hr>',
        '<a><img><figure><figcaption>',
        '<table><thead><tbody><tfoot><tr><th><td>',
        '<div><span>',
    ]);
    return strip_tags($html, $allowed);
}

/**
 * Return a plain-text excerpt from HTML content.
 */
function excerpt(string $html, int $length = 200): string {
    $text = strip_tags($html);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return mb_strimwidth(trim($text), 0, $length, '…');
}

/**
 * Handle a featured image upload. Returns the filename on success or null.
 */
function save_featured_image(array $file, string $upload_dir): ?string {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024;

    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    if (!in_array(mime_content_type($file['tmp_name']), $allowed_types)) return null;
    if ($file['size'] > $max_size) return null;

    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('img_', true) . '.' . strtolower($ext);
    if (!move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) return null;

    return $filename;
}
