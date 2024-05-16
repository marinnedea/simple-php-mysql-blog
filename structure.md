/blog
│
├── /admin
│   ├── add_post.php
│   ├── edit_post.php
│   ├── login.php
│   ├── admin.php
│
├── /includes
│   ├── config.php
│
├── /sql
│   ├── posts_table.sql
│
├── index.php
├── category.php
├── view_post.php
└── .htaccess


Description of the Directory Structure:

    /admin: Contains files for managing the blog, such as adding, editing posts, and admin login.
    /includes: Stores the configuration file.
    /sql: Contains SQL files for database structure.
    index.php: The main page displaying blog posts.
    view_post.php: Page for viewing individual posts.
    .htaccess: Optional for URL rewriting or security enhancements.

The .htaccess file handles clean URL routing for categories and articles.
The index.php and category.php files include links to view posts by category and individual articles.

This structure keeps the project organized and maintains separation between public and admin functionalities.