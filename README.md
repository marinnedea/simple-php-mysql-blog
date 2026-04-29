# simple-php-mysql-blog

A lightweight blog built with PHP and MySQL. Clean admin panel, rich text editing via TinyMCE, featured images, category and user management.

## Features

- **Public frontend** — post listing with featured image thumbnails, category chips, and 200-char excerpts; sticky footer
- **Rich text editor** — TinyMCE 7 on all post forms (bold, italic, lists, links, tables, inline image upload, code blocks)
- **Featured images** — optional per post; thumbnail on listing pages, full-width hero on the post view
- **Category management** — add, rename, delete; post count shown per category
- **User management** — create users, change passwords, delete users; self-deletion blocked
- **Admin Area link** — shown in the public nav bar when an admin is already logged in
- **CSRF protection** — token validated on every admin POST
- **Prepared statements** — all queries use MySQLi prepared statements throughout
- **Password hashing** — `password_hash` / `password_verify` with `PASSWORD_DEFAULT`
- **Environment-based DB config** — credentials read from env vars, never hardcoded
- **Site settings** — title, subtitle, footer, logo, favicon as constants in `includes/config.php`

## Requirements

- PHP 8.0 or higher
- MySQL 5.7+ or MariaDB 10.3+
- A web server (Apache or Nginx with PHP-FPM)

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/marinnedea/simple-php-mysql-blog.git
cd simple-php-mysql-blog
```

### 2. Create the database

```bash
mysql -u your_username -p your_database < sql/setup.sql
```

### 3. Set environment variables

The app reads DB credentials from the environment — never from source code.

| Variable  | Description       |
|-----------|-------------------|
| `DB_HOST` | Database hostname |
| `DB_USER` | Database username |
| `DB_PASS` | Database password |
| `DB_NAME` | Database name     |

**Apache** (`VirtualHost` block):
```apache
SetEnv DB_HOST localhost
SetEnv DB_USER bloguser
SetEnv DB_PASS secret
SetEnv DB_NAME blogdb
```

**Nginx** — set in your PHP-FPM pool config (e.g. `/etc/php/8.x/fpm/pool.d/blog.conf`):
```ini
env[DB_HOST] = localhost
env[DB_USER] = bloguser
env[DB_PASS] = secret
env[DB_NAME] = blogdb
```

> Avoid special characters like `!` or `$` in the password when using Nginx — the FPM pool config parser does not support escaping them.

### 4. Configure site settings

Edit `includes/config.php` — no UI needed:

```php
define('SITE_TITLE',    'My Blog');
define('SITE_SUBTITLE', 'Thoughts, notes and ideas.');
define('SITE_FOOTER',   'My Blog © 2024');
define('SITE_LOGO',     '');        // e.g. 'uploads/logo.png'
define('SITE_FAVICON',  '');        // e.g. 'uploads/favicon.ico'
define('TINYMCE_API_KEY', 'no-api-key');
```

### 5. Set up TinyMCE (optional)

The editor works out of the box with `no-api-key` but shows a small notification. To remove it:

1. Register for a free API key at [tiny.cloud](https://www.tiny.cloud/)
2. Replace `'no-api-key'` with your key in `includes/config.php`

### 6. Create the first admin user

Open `includes/passgenerator.php` in your browser, enter a password, copy the hash, then insert the user:

```sql
INSERT INTO users (username, password) VALUES ('admin', 'paste-hash-here');
```

> Remove or restrict access to `passgenerator.php` once you're done — it has no authentication.

### 7. Add at least one category

You can do this via the admin panel after logging in, or directly:

```sql
INSERT INTO categories (name) VALUES ('General');
```

## Upgrading an existing install

If you already have the database set up without the `featured_image` column, run the migration:

```bash
mysql -u your_username -p your_database < sql/add_featured_image.sql
```

## File Structure

```
simple-php-mysql-blog/
│
├── index.php                    # Public homepage
├── category.php                 # Posts filtered by category
├── view_post.php                # Single post view
│
├── admin/
│   ├── login.php                # Login form and handler
│   ├── logout.php               # Destroys session, redirects to homepage
│   ├── admin.php                # Dashboard (auth required)
│   ├── add_post.php             # Create post with TinyMCE + featured image
│   ├── edit_post.php            # Edit post with TinyMCE + featured image
│   ├── posts.php                # List, edit, delete posts
│   ├── categories.php           # Add, rename, delete categories
│   ├── users.php                # Add users, change passwords, delete users
│   ├── upload_image.php         # TinyMCE inline image upload endpoint
│   └── index.php                # Redirects to dashboard or login
│
├── includes/
│   ├── config.php               # DB connection, site settings, TinyMCE key
│   ├── functions.php            # sanitize_html(), excerpt(), save_featured_image()
│   ├── header.php               # Shared public header (reads SITE_* constants)
│   ├── footer.php               # Shared public footer
│   ├── passgenerator.php        # Dev utility: generates bcrypt password hashes
│   └── index.php                # Returns 403 to prevent directory listing
│
├── sql/
│   ├── setup.sql                # Full schema: categories, posts, users
│   └── add_featured_image.sql   # Migration: adds featured_image column
│
├── uploads/                     # Featured images and TinyMCE inline images
│
└── css/
    └── style.css                # Stylesheet
```

## Usage

### Admin panel

Navigate to `admin/login.php`. After logging in:

- **Posts** → list, edit, delete all posts
- **Add Post** → rich text editor with optional featured image
- **Categories** → add, rename, delete
- **Users** → create users, change passwords, delete accounts
- **Admin Area** button appears in the public blog nav when you're logged in

### Public blog

- **Homepage** — `index.php`
- **Category** — `category.php?id=<id>`
- **Post** — `view_post.php?id=<id>`

## Security notes

- DB credentials in environment variables only — never in source code
- All queries use MySQLi prepared statements
- CSRF token on every admin POST
- Session ID regenerated on login (prevents session fixation)
- HTML content from TinyMCE is sanitised with a `strip_tags` allowlist before storing
- Admin pages redirect unauthenticated requests to `login.php`
- Deleting a post removes its image file from disk
- `passgenerator.php` is a dev-only utility — remove or restrict it in production

## License

MIT — see [LICENSE](LICENSE).

## Author

[Marin Nedea](https://github.com/marinnedea)
