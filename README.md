# simple-php-mysql-blog

A lightweight blog built with PHP and MySQL. Supports categories, post management, and a password-protected admin panel.

## Features

- **Public frontend** — lists all posts and categories; click through to read individual posts
- **Category filtering** — browse posts by category
- **Admin panel** — protected by session-based login; create and edit posts
- **CSRF protection** — all admin form submissions are validated with a token
- **Prepared statements** — all queries use MySQLi prepared statements
- **Password hashing** — admin passwords stored with `password_hash` / `password_verify`
- **Environment-based config** — DB credentials read from environment variables, not hardcoded

## Requirements

- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.3+
- A web server (Apache or Nginx)

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

The app reads DB credentials from the environment. Add these to your web server config, `.env` file, or shell:

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

**Nginx** (via PHP-FPM pool config, `www.conf`):
```ini
env[DB_HOST] = localhost
env[DB_USER] = bloguser
env[DB_PASS] = secret
env[DB_NAME] = blogdb
```

### 4. Create the first admin user

Open `includes/passgenerator.php` in your browser, enter a password, and copy the resulting hash. Then insert the user directly into the database:

```sql
INSERT INTO users (username, password) VALUES ('admin', 'paste-hash-here');
```

> Remove or block access to `passgenerator.php` once you're done — it has no authentication.

### 5. Add at least one category

```sql
INSERT INTO categories (name) VALUES ('General');
```

## File Structure

```
simple-php-mysql-blog/
│
├── index.php                  # Public homepage — lists all posts and categories
├── category.php               # Posts filtered by category
├── view_post.php              # Single post view
│
├── admin/
│   ├── login.php              # Admin login form and handler
│   ├── logout.php             # Destroys session and redirects
│   ├── admin.php              # Admin dashboard (auth required)
│   ├── add_post.php           # Create a new post (auth required)
│   ├── edit_post.php          # Edit an existing post (auth required)
│   └── index.php              # Redirects to admin.php or login.php
│
├── includes/
│   ├── config.php             # MySQLi connection using environment variables
│   ├── passgenerator.php      # Dev utility: generates bcrypt hashes for passwords
│   └── index.php              # Prevents directory listing
│
├── sql/
│   └── setup.sql              # Creates categories, posts, and users tables
│
├── css/
│   └── style.css              # Basic stylesheet
│
└── structure.md               # Directory structure notes
```

## Usage

### Admin login

Navigate to `admin/login.php` and log in with the credentials you inserted during setup.

### Managing posts

- **Add post** — `admin/add_post.php`
- **Edit post** — `admin/edit_post.php?id=<post_id>` (link this from your admin dashboard as needed)
- **Logout** — `admin/logout.php`

### Public blog

- **Homepage** — `index.php`
- **Category view** — `category.php?id=<category_id>`
- **Single post** — `view_post.php?id=<post_id>`

## Security notes

- DB credentials are never in source code — environment variables only
- All form inputs go through prepared statements
- Admin pages redirect unauthenticated requests to `login.php`
- CSRF tokens are validated on every POST in the admin area
- Session ID is regenerated on login to prevent session fixation
- `passgenerator.php` is a development utility — restrict or remove it in production

## License

MIT — see [LICENSE](LICENSE).

## Author

[Marin Nedea](https://github.com/marinnedea)
