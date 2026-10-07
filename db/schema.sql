-- ============================================================================
-- PropFirm Conclave Kochi 2026 — Admin Portal database
-- MySQL 5.7+ / MariaDB 10.3+ · utf8mb4 · all times stored in UTC
-- Import:  mysql -u <user> -p <db> < db/schema.sql
-- Default admin: admin@finfluenze.com / ChangeMe123!  (change on first login)
-- ============================================================================

CREATE DATABASE IF NOT EXISTS propfirm_conclave
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE propfirm_conclave;

-- ---------------------------------------------------------------- admin users
CREATE TABLE IF NOT EXISTS admin_users (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name        VARCHAR(120) NOT NULL,
  email       VARCHAR(190) NOT NULL,
  pass_hash   VARCHAR(255) NOT NULL,
  role        ENUM('super','editor') NOT NULL DEFAULT 'editor',
  last_login  DATETIME NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_email (email)
) ENGINE=InnoDB;

-- ------------------------------------------------- site settings (key/value)
CREATE TABLE IF NOT EXISTS settings (
  skey   VARCHAR(80) NOT NULL,
  svalue TEXT NULL,
  PRIMARY KEY (skey)
) ENGINE=InnoDB;

-- ------------------------------------------- page content blocks (page CMS)
-- Lets the admin change banners, headings, text and images per page/section.
CREATE TABLE IF NOT EXISTS content_blocks (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  page_slug  VARCHAR(60) NOT NULL,
  section    VARCHAR(80) NOT NULL,
  field      VARCHAR(80) NOT NULL,
  ftype      ENUM('text','textarea','html','image') NOT NULL DEFAULT 'text',
  fvalue     TEXT NULL,
  sort       INT NOT NULL DEFAULT 0,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_block (page_slug, section, field)
) ENGINE=InnoDB;

-- ------------------------------------------------------------------ speakers
CREATE TABLE IF NOT EXISTS speakers (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name       VARCHAR(120) NOT NULL,
  role       VARCHAR(120) NOT NULL DEFAULT '',
  company    VARCHAR(120) NOT NULL DEFAULT '',
  bio        TEXT NULL,
  photo      VARCHAR(255) NOT NULL DEFAULT '',
  sort       INT NOT NULL DEFAULT 0,
  status     ENUM('draft','published') NOT NULL DEFAULT 'published',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_sort (sort)
) ENGINE=InnoDB;

-- ------------------------------------------------------------ sponsor tiers
CREATE TABLE IF NOT EXISTS sponsor_tiers (
  id    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name  VARCHAR(80) NOT NULL,
  slug  VARCHAR(80) NOT NULL,
  color VARCHAR(16) NOT NULL DEFAULT '#c9a24b',
  sort  INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uq_slug (slug)
) ENGINE=InnoDB;

-- ------------------------------------------------------------------ sponsors
CREATE TABLE IF NOT EXISTS sponsors (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  tier_id    INT UNSIGNED NULL,
  name       VARCHAR(120) NOT NULL,
  logo       VARCHAR(255) NOT NULL DEFAULT '',
  website    VARCHAR(255) NOT NULL DEFAULT '',
  sort       INT NOT NULL DEFAULT 0,
  status     ENUM('draft','published') NOT NULL DEFAULT 'published',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_tier_sort (tier_id, sort),
  CONSTRAINT fk_sp_tier FOREIGN KEY (tier_id)
    REFERENCES sponsor_tiers (id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------------- gallery
CREATE TABLE IF NOT EXISTS gallery (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title      VARCHAR(160) NOT NULL DEFAULT '',
  image      VARCHAR(255) NOT NULL,
  sort       INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_sort (sort)
) ENGINE=InnoDB;

-- --------------------------------------------------------------- ticket types
CREATE TABLE IF NOT EXISTS ticket_types (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name        VARCHAR(80) NOT NULL,
  price       DECIMAL(10,2) NOT NULL DEFAULT 0,
  description VARCHAR(255) NOT NULL DEFAULT '',
  sort        INT NOT NULL DEFAULT 0,
  status      ENUM('active','hidden') NOT NULL DEFAULT 'active',
  PRIMARY KEY (id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------ ticket orders
CREATE TABLE IF NOT EXISTS ticket_orders (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  type_id      INT UNSIGNED NULL,
  buyer_name   VARCHAR(120) NOT NULL,
  buyer_email  VARCHAR(190) NOT NULL,
  buyer_phone  VARCHAR(40) NOT NULL DEFAULT '',
  qty          INT UNSIGNED NOT NULL DEFAULT 1,
  amount       DECIMAL(10,2) NOT NULL DEFAULT 0,
  status       ENUM('pending','paid','cancelled') NOT NULL DEFAULT 'pending',
  checked_in   TINYINT(1) NOT NULL DEFAULT 0,
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_status (status),
  CONSTRAINT fk_ord_type FOREIGN KEY (type_id)
    REFERENCES ticket_types (id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- -------------------------------------------------------------------- booths
CREATE TABLE IF NOT EXISTS booths (
  id     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  code   VARCHAR(20) NOT NULL,
  size   VARCHAR(40) NOT NULL DEFAULT '',
  price  DECIMAL(10,2) NOT NULL DEFAULT 0,
  status ENUM('available','reserved','sold') NOT NULL DEFAULT 'available',
  PRIMARY KEY (id),
  UNIQUE KEY uq_code (code)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------- exhibitors
CREATE TABLE IF NOT EXISTS exhibitors (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  company      VARCHAR(140) NOT NULL,
  contact_name VARCHAR(120) NOT NULL DEFAULT '',
  email        VARCHAR(190) NOT NULL DEFAULT '',
  phone        VARCHAR(40) NOT NULL DEFAULT '',
  booth_id     INT UNSIGNED NULL,
  package      VARCHAR(80) NOT NULL DEFAULT '',
  amount       DECIMAL(10,2) NOT NULL DEFAULT 0,
  status       ENUM('lead','confirmed','cancelled') NOT NULL DEFAULT 'lead',
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_exh_booth FOREIGN KEY (booth_id)
    REFERENCES booths (id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------ P&L ledger
CREATE TABLE IF NOT EXISTS transactions (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ttype       ENUM('income','expense') NOT NULL,
  category    VARCHAR(80) NOT NULL,
  description VARCHAR(255) NOT NULL DEFAULT '',
  amount      DECIMAL(12,2) NOT NULL DEFAULT 0,
  txn_date    DATE NOT NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_date (txn_date),
  KEY ix_type (ttype)
) ENGINE=InnoDB;

-- --------------------------------------------------------------- agenda days
CREATE TABLE IF NOT EXISTS agenda_days (
  id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  day_label VARCHAR(80) NOT NULL,
  day_date  VARCHAR(40) NOT NULL DEFAULT '',
  sort      INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

-- ----------------------------------------------------------- agenda sessions
CREATE TABLE IF NOT EXISTS agenda_sessions (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  day_id      INT UNSIGNED NOT NULL,
  start_time  VARCHAR(16) NOT NULL DEFAULT '',
  end_time    VARCHAR(16) NOT NULL DEFAULT '',
  title       VARCHAR(180) NOT NULL,
  speaker     VARCHAR(120) NOT NULL DEFAULT '',
  description TEXT NULL,
  tag         VARCHAR(40) NOT NULL DEFAULT '',
  sort        INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY ix_day_sort (day_id, sort),
  CONSTRAINT fk_ses_day FOREIGN KEY (day_id)
    REFERENCES agenda_days (id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------- blog posts
CREATE TABLE IF NOT EXISTS posts (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  slug         VARCHAR(160) NOT NULL,
  title        VARCHAR(200) NOT NULL,
  excerpt      VARCHAR(300) NOT NULL DEFAULT '',
  cover        VARCHAR(255) NOT NULL DEFAULT '',
  body         MEDIUMTEXT NULL,
  status       ENUM('draft','published') NOT NULL DEFAULT 'draft',
  published_at DATETIME NULL,
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_slug (slug)
) ENGINE=InnoDB;

-- ------------------------------------------------------------------------ faq
CREATE TABLE IF NOT EXISTS faqs (
  id       INT UNSIGNED NOT NULL AUTO_INCREMENT,
  question VARCHAR(255) NOT NULL,
  answer   TEXT NOT NULL,
  sort     INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

-- ---------------------------------------------------- newsletter subscribers
CREATE TABLE IF NOT EXISTS subscribers (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name       VARCHAR(120) NOT NULL DEFAULT '',
  email      VARCHAR(190) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_email (email)
) ENGINE=InnoDB;

-- ---------------------------------------------------------- contact enquiries
CREATE TABLE IF NOT EXISTS enquiries (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name       VARCHAR(120) NOT NULL,
  email      VARCHAR(190) NOT NULL,
  subject    VARCHAR(160) NOT NULL DEFAULT '',
  message    TEXT NOT NULL,
  status     ENUM('new','replied','closed') NOT NULL DEFAULT 'new',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_status (status)
) ENGINE=InnoDB;

-- =================================================================== SEED ==
-- Admin login: ceo@finfluenze.com  (set by owner, 2026-10-07)
INSERT IGNORE INTO admin_users (name, email, pass_hash, role) VALUES
  ('CEO', 'ceo@finfluenze.com',
   '$2y$10$iGw2VjHfVml9nIGj3lN6ueVr2096n3/fWkATjeZXmHH9O9zJ1I3r6',
   'super');

INSERT IGNORE INTO settings (skey, svalue) VALUES
  ('site_name', 'PropFirm Conclave Kochi'),
  ('tagline', 'Connect | Trade | Get Funded'),
  ('event_dates', '12–13 December 2026'),
  ('venue_name', 'Adlux International Convention Centre'),
  ('venue_city', 'Kochi, Kerala'),
  ('contact_email', 'events@finfluenze.com'),
  ('sponsor_email', 'sponsors@finfluenze.com'),
  ('instagram_url', 'https://www.instagram.com/finfluenzeofficial'),
  ('facebook_url', ''),
  ('x_url', ''),
  ('youtube_url', ''),
  ('linkedin_url', ''),
  ('currency', '₹');

INSERT IGNORE INTO sponsor_tiers (name, slug, color, sort) VALUES
  ('Title Sponsor',  'title',   '#e8c56a', 1),
  ('Platinum',       'platinum','#d7dbe0', 2),
  ('Gold',           'gold',    '#c9a24b', 3),
  ('Silver',         'silver',  '#9aa0a8', 4),
  ('Exhibitor',      'exhibitor','#7d8590',5);

INSERT IGNORE INTO ticket_types (name, price, description, sort, status) VALUES
  ('Student',    499.00,  'Valid student ID required at entry', 1, 'active'),
  ('Early Bird', 999.00,  'Limited passes at the launch price',  2, 'active'),
  ('Standard',   1999.00, 'Full 2-day access to all sessions',   3, 'active'),
  ('VIP',        4999.00, 'Front-row seating, lounge + gala dinner', 4, 'active');

INSERT IGNORE INTO agenda_days (day_label, day_date, sort) VALUES
  ('Day 1', 'Saturday, 12 December 2026', 1),
  ('Day 2', 'Sunday, 13 December 2026', 2);

INSERT IGNORE INTO booths (code, size, price, status) VALUES
  ('A1','3x3 m', 45000.00,'available'),('A2','3x3 m', 45000.00,'available'),
  ('A3','3x3 m', 45000.00,'available'),('A4','3x3 m', 45000.00,'available'),
  ('B1','6x3 m', 80000.00,'available'),('B2','6x3 m', 80000.00,'available'),
  ('B3','6x3 m', 80000.00,'available'),('B4','6x3 m', 80000.00,'available');

-- Editable homepage hero defaults (mirrors the current live copy)
INSERT IGNORE INTO content_blocks (page_slug, section, field, ftype, fvalue, sort) VALUES
  ('home','hero','kicker','text','Finfluenze presents',1),
  ('home','hero','title_line1','text','PROPFIRM',2),
  ('home','hero','title_line2','text','CONCLAVE',3),
  ('home','hero','subtitle','text','K O C H I  2 0 2 6',4),
  ('home','hero','tagline','text','Connect | Trade | Get Funded',5),
  ('home','hero','dates','text','12–13 December 2026 · Sat–Sun',6),
  ('home','hero','venue','text','Adlux Convention Centre, Kochi, Kerala',7),
  ('home','hero','cta_primary','text','Get Your Pass',8),
  ('home','hero','cta_secondary','text','Explore Agenda',9),
  ('home','hero','bg_image','image','assets/img/hero-arena.jpg',10);
