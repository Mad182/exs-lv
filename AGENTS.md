# AGENTS.md

Welcome to the **EXS.LV** repository. This document provides technical context, architecture details, development patterns, and deployment instructions for AI agents and developers working on this project.

---

## 1. Project Overview

**EXS.LV** is a Latvian gaming and community web portal built with a lightweight custom PHP core, MySQL/MariaDB database, Memcached caching, and Vanilla HTML/CSS/JavaScript.

### Repository Structure
```
exs-lv/
├── exs.lv/                    # Main application directory
│   ├── configdb.php           # Database & server configuration
│   ├── index.php              # Main router & request handler
│   ├── css/                   # Global stylesheets (core.css, auto-dark.css, dark.css)
│   ├── includes/              # Core functions, auth, database, template engine
│   ├── modules/               # Feature and game modules
│   │   ├── speles/            # Games catalog page (/speles)
│      ├── invaders/          # Space Invaders game module
│      ├── flappy/            # Lidojošais Eksis (Flappy Bird) game module
│      ├── snake/             # Snake game module
│      ├── tetris/            # Tetris game module
│      ├── wordle/            # Wordle game module
│      ├── minu-mekletajs/    # Minesweeper game module
│      └── ...                # Other site modules
│   └── tmpl/                  # Main layout templates (main.tpl)
├── dev-draza/                 # SQL schemas (cat.sql, schema.sql)
├── api.exs.lv/                # API project directory
├── img.exs.lv/                # Image hosting server directory
└── AGENTS.md                  # Developer & AI Agent Guide (this file)
```

---

## 2. Deployment Instructions

### Production Environment
* **Server SSH Host:** `root@exs.lv`
* **Production Path:** `/home/www/exs.lv`
* **Git Remote:** `origin` (`git@github.com:Mad182/exs-lv.git`)

### Deployment Workflow
When delivering fixes, new features, or new games, execute the following steps:

1. **Verify Code & Minify Game JS Locally:**
   Run PHP syntax check on modified/new PHP files and minify game assets:
   ```bash
   php -l exs.lv/modules/<module>/<module>.php
   php exs.lv/scripts/minify_games.php
   ```
   *(Note: Local `pre-commit` git hook and production `post-merge` git hook also run `php exs.lv/scripts/minify_games.php` automatically on commit & deployment).*

2. **Commit & Push to Remote:**
   ```bash
   git add .
   git commit -m "Descriptive commit message"
   git push origin master
   ```

3. **Deploy to Production Server via SSH:**
   Pull the latest changes directly on the production server (triggers post-merge minification hook automatically):
   ```bash
   ssh root@exs.lv "cd /home/www/exs.lv && git pull"
   ```

4. **Synchronize Database Category Rows (If Adding New Modules):**
   New modules require a corresponding record in the database `cat` table so the router recognizes the URL slug.
   ```bash
   ssh root@exs.lv "cd /home/www/exs.lv && php -r \"require('configdb.php'); \$db = new mdb(\$username, \$password, \$database, \$hostname); \$exists = \$db->get_var(\\\"SELECT id FROM cat WHERE textid='<module-slug>'\\\"); if (!\$exists) { \$db->query(\\\"INSERT INTO cat (textid, lang, title, intro, module, parent, content, tmpl, status, sitemap) VALUES ('<module-slug>', '1', '<Title>', '1', '<module-slug>', '2516', '<Description>', 'main', 'active', '1')\\\"); }\""
   ```

5. **Flush Memcached Cache:**
   Clear template and category caches on production after deployment:
   ```bash
   ssh root@exs.lv "cd /home/www/exs.lv && php -r \"require('configdb.php'); \$m = new Memcached; \$m->addServer(\$mc_host, \$mc_port); \$m->flush();\""
   ```

---

## 3. Game Module Development Patterns

Games on EXS.lv follow a standardized module pattern:

### File Structure
Each game module under `exs.lv/modules/<game-name>/` typically consists of 5 files:
* `head.tpl`: Head includes (`<link rel="stylesheet" href="..." />`, `<script src="..."></script>`).
* `<game>.php`: Backend controller (AJAX endpoints, user high score queries, leaderboard data fetching).
* `<game>.tpl`: HTML structure (canvas, overlays, HUD, leaderboards).
* `<game>.css`: Module-specific styling (with responsive & dark mode rules).
* `<game>.js`: Frontend game engine (HTML5 Canvas, input handlers, Web Audio API sound synth).

### Highscore System (`gamescore` table)
Scores are submitted via AJAX `POST` to `/<game>?action=push`:
* **Database Table:** `gamescore` (`user_id`, `game`, `score`, `time`).
* **Session Token Verification:** Use `action=init_token` at game start to store `$_SESSION['<game>_token']`.
* **Activity Feed Notification:** On personal high score update, trigger:
  ```php
  push('Uzstādīja jaunu rekordu spēlē <a href="/<game>">Title</a> (' . number_format($highScore, 0, '', ' ') . ' punktu)', '/bildes/icons/award_star_gold_3.png', 'game-<game>-' . $auth->id);
  ```

### Design & Theme Consistency
* **Styling:** Use Vanilla CSS. Ensure full light and dark mode support (`@media (prefers-color-scheme: dark)` and overrides in `css/auto-dark.css`).
* **Touch Controls:** Always include touch/button controls for mobile device compatibility.
* **Audio:** Prefer Web Audio API synthesized sound effects so no external audio assets are required.

---

## 4. Key Core Classes & Functions

* **`TemplatePower`**: Template engine (`$tpl->assignInclude()`, `$tpl->prepare()`, `$tpl->newBlock()`, `$tpl->assign()`).
* **`mdb`**: MySQL database wrapper (`$db->get_row()`, `$db->get_results()`, `$db->get_var()`, `$db->query()`).
* **`Auth`**: Session and user authentication (`$auth->ok`, `$auth->id`, `$auth->nick`).
* **`usercolor(nick, level)`**: Returns colored user nickname HTML according to rank/level.
* **`get_avatar(auth_obj, size)`**: Returns user avatar image path.
* **`push(message, icon, group)`**: Sends entry to global activity stream.
