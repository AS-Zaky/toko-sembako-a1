# Product Requirements Document: Inventory Management System (Toko Sembako A1)

**Team:** Raja Moreno (Project Manager), Ahmad Zaky Sultan (Back-End), Tegar Putra Pratama (Front-End), Satria Rizky Ramadhan (UI/UX) **Course:** PBL, Politeknik Negeri Malang, Class TI-2E, 2026 **Partner:** Toko Sembako A1 **Status:** Draft v1

## 1. Overview

Toko Sembako A1 records its stock by hand in a paper book. The system replaces that book with a web application focused only on stock recording: what comes in, what goes out, what is left, and what is the selling price. Finance (cashier, payments, invoices, income and expenses) is out of scope.

## 2. Problem Statement

The shop opens in two sessions (07:00-12:00 and 19:00-21:00 WIB). A shop attendant currently writes every incoming and outgoing item in a paper book while serving customers and tries to remember each selling price. This causes:

- Stock counts in the book that do not match the goods on the shelf
- Lost or damaged pages, so stock history cannot be traced
- Attendants forgetting selling prices and quoting wrong ones
- The owner not knowing when, or how much, to restock
- Customers having to visit the shop just to ask if an item is available

## 3. Goals and Non-Goals

### Goals

1. Record incoming and outgoing goods digitally so stock updates automatically.
2. Store product data with purchase and selling prices as the attendant's reference.
3. Warn the owner when an item reaches its minimum stock level.
4. Keep a safe, traceable history of every stock movement.
5. Let customers check item availability online.

### Non-Goals

- Sales transactions (cashier), payment processing, invoice or receipt printing
- Income, expense, and financial reports
- Online ordering or purchasing by customers
- Operating-hours settings
- Multi-store support (single store only)

## 4. Target Users

| User | Description | Main needs |
| --- | --- | --- |
| Admin / Owner | Owns the shop, manages data and monitors stock | See all stock at a glance, know what to restock, manage products, prices, suppliers, and accounts |
| Shop Attendant | Serves customers and handles daily goods | Record goods in and out quickly, check price and stock on the spot |
| Customer | Buyer, no account | Check whether an item is available before visiting |

### Primary persona

A shop attendant at Toko Sembako A1 who currently notes every item in a paper book and memorizes prices. Their pain: wrong counts, forgotten prices, lost pages. Success for them is recording an item in a few taps and trusting the number on screen.

## 5. Product Principles

- **Trustworthy stock count first.** Every feature serves the accuracy of the number on screen.
- **Fast to record.** Attendants are serving customers, so entry must take seconds.
- **Simple language and layout** for users without technical backgrounds.

**Top three priorities:** automatic stock count, goods in/out recording, low-stock warning.

**Core advantage over paper:** no more wrong counts. Every entry updates stock automatically.

**Daily return reason:** a trustworthy stock count, daily stock records, price checks on the spot, and low-stock warnings.

## 6. User Stories

| ID | As a... | I want to... | So that... |
| --- | --- | --- | --- |
| US-01 | Admin, Attendant | log in with my account | I only see menus for my role |
| US-02 | Admin | add, edit, delete, and search products and categories | all goods are registered with price and minimum stock |
| US-03 | Admin | record the supplier name on incoming goods | incoming goods are linked to their source |
| US-04 | Admin, Attendant | record incoming goods | stock increases automatically |
| US-05 | Admin, Attendant | record outgoing goods (sold, damaged, expired) | stock decreases automatically |
| US-06 | Admin | adjust stock after a physical count (stock opname) | system stock matches the shelf |
| US-07 | Admin | see a dashboard with stock summary and low-stock items | I know what to restock |
| US-08 | Admin, Attendant | search an item to see selling price and remaining stock | I never quote a wrong price |
| US-09 | Admin | view stock movement history and period reports | I can trace any change |
| US-10 | Customer | open a public catalog | I know if an item is available before coming |
| US-11 | Admin | manage attendant accounts | only authorized people use the system |

## 7. Functional Requirements

| ID | Feature | Requirement | Role | Priority |
| --- | --- | --- | --- | --- |
| FR-01 | Login and access control | Username and password login. Menus and pages restricted by role. Unauthenticated users cannot reach management pages. | Admin, Attendant | Must |
| FR-02 | Product management | CRUD for products with code, name, category, unit, purchase price, selling price, current stock, and minimum stock. Search and pagination. | Admin | Must |
| FR-03 | Category management | CRUD for categories used by the product dropdown. Unit (satuan) is a text field on the product. | Admin | Must |
| FR-04 | Supplier recording | The supplier name is entered as text on each incoming-goods transaction (the supplier column of TRANSAKSI). | Admin | Should |
| FR-05 | Goods in | Select a supplier and one or more products with quantities. Stock increases on save. | Admin, Attendant | Must |
| FR-06 | Goods out | Select products, quantities, and a reason (sold, damaged, expired). Stock decreases on save. Entry is rejected if it would make stock negative. | Admin, Attendant | Must |
| FR-07 | Stock adjustment | Enter the physical count. The system records the difference and corrects stock, with a mandatory note. | Admin | Should |
| FR-08 | Dashboard | Counts of product types, low or empty stock items, and recent stock activity. | Admin | Must |
| FR-09 | Low-stock warning | Items with stock at or below the minimum are flagged on the dashboard and in the price/stock lookup. | Admin, Attendant | Must |
| FR-10 | Price and stock lookup | Fast search by name or code showing selling price and remaining stock. | Admin, Attendant | Must |
| FR-11 | History and reports | Stock movement history (date, product, quantity, movement type, user) and inventory report filtered by period. | Admin | Must |
| FR-12 | Public catalog | Public page (no login) listing items and availability status. No cart or purchase. Purchase prices are never shown. | Customer | Should |
| FR-13 | User management | Create, edit, and deactivate attendant accounts. | Admin | Must |

## 8. Key User Flows

**First-use flow (the one thing a new user should finish):** Login, then Record goods in, choose product and quantity, save, and see the stock count rise immediately.

**Daily attendant flow:** Login, search item to check price, serve customer, record goods out, check stock.

**Owner flow:** Open dashboard, review low-stock list, restock from supplier, record goods in, review history or monthly report.

## 9. Data Model (PostgreSQL)

The data model follows the team's final five-entity ERD exactly: USER, KATEGORI, PRODUK, TRANSAKSI, and DETAIL\_TRANSAKSI. Supplier is a text column of TRANSAKSI and unit is the satuan column of PRODUK. The price columns (total\_harga, harga\_satuan, subtotal) record the value of each stock movement for reference only; the system produces no financial reports.

| Entity | Key attributes |
| --- | --- |
| USER | id\_user (PK), nama, username, password, role |
| KATEGORI | id\_kategori (PK), nama\_kategori, deskripsi |
| PRODUK | id\_produk (PK), id\_kategori (FK), kode\_produk, nama\_produk, satuan, harga\_beli, harga\_jual, stok |
| TRANSAKSI | id\_transaksi (PK), id\_user (FK), jenis\_transaksi, tanggal\_transaksi, total\_harga, supplier, keterangan |
| DETAIL\_TRANSAKSI | id\_detail (PK), id\_transaksi (FK), id\_produk (FK), jumlah, harga\_satuan, subtotal |

**Relationships:** one USER performs many TRANSAKSI (melakukan); one KATEGORI groups many PRODUK (mengelompokkan); one TRANSAKSI contains many DETAIL\_TRANSAKSI rows (berisi); one PRODUK appears in many DETAIL\_TRANSAKSI rows (termasuk).

**PostgreSQL specifics**

- Primary keys use `GENERATED ALWAYS AS IDENTITY`. The diagram labels id\_transaksi and id\_produk as FK; they are the primary keys of their own tables
- Prices use `NUMERIC(12,2)`, dates use `TIMESTAMP`
- `role` (admin, penjaga) and `jenis_transaksi` (masuk, keluar\_terjual, keluar\_rusak, keluar\_kedaluwarsa, penyesuaian) use `CHECK` constraints (or `ENUM`)
- `CHECK (stok >= 0)` on PRODUK
- `jumlah` is positive for goods in and out, and signed for stock adjustments (the difference found in the physical count)
- A trigger or function on DETAIL\_TRANSAKSI updates `produk.stok` inside the same database transaction, so stock and history never disagree
- Passwords stored with `password_hash()` (bcrypt)
- Open point: the ERD has no minimum-stock column, yet the low-stock warning needs a threshold. Either add `stok_minimum` to PRODUK or agree on one fixed threshold with the partner

## 10. Non-Functional Requirements

| Area | Requirement |
| --- | --- |
| Platform | Web app on PHP Native with PostgreSQL via PDO (pdo\_pgsql) |
| Compatibility | Latest browsers on computer, laptop, and phone; responsive layout |
| Performance | Pages load and search results return in under 3 seconds on normal connections |
| Security | Hashed passwords, prepared statements (against SQL injection), session-based role checks, output escaping, CSRF protection on forms |
| Data integrity | Stock changes happen only through recorded transactions; history is never edited or deleted |
| Reliability | Regular database backups so records cannot be lost as in the paper book |
| Usability | Simple labels in Indonesian, large buttons for quick entry, consistent sidebar and header navigation |

## 11. UI Overview

Existing wireframes cover Login, Admin Dashboard, Add Product form, and Product Data table. Still to design:

- Goods in and goods out forms
- Stock adjustment form
- Price and stock lookup page
- Stock history and report pages
- Public catalog page
- Low-stock list on the dashboard

Branding should be consistent as "Toko Sembako A1" (the dashboard wireframe currently says "Toko Sembako Online").

## 12. Success Metrics and Acceptance Criteria

1. All products sold by the shop are registered with category, unit, price, and stock.
2. Every goods in or out entry updates stock immediately with no manual calculation.
3. System stock matches physical counts throughout the trial period.
4. Items below minimum stock are flagged.
5. An attendant can find the price and stock of an item in under 10 seconds.
6. Stock history can be traced at any time.
7. All features pass functional testing and are validated by the partner.

## 13. Assumptions and Constraints

**Assumptions**

- Owner and attendants are willing to switch from the book and record every movement.
- The shop has a device with a browser and internet connection.
- Initial product and stock data can be taken from the book or a physical count.

**Constraints**

- 16 weeks, four students
- PHP Native and PostgreSQL
- One store (Toko Sembako A1)
- No financial features

## 14. Timeline and Milestones

| Weeks | Activities | Milestone |
| --- | --- | --- |
| 1-4 | Partner interview, proposal writing and finalization | CP-1 Proposal |
| 5-8 | UI/UX and database design, front-end and back-end integration | CP-2 Milestone 1 |
| 9-12 | Website demo and initial test with partner | CP-3 Milestone 2 |
| 13-16 | Fixes, polish, systematic functional testing | CP-4 Final/Expo |

## 15. Responsibilities

| Area | Owner |
| --- | --- |
| Project management (WBS, proposal, Notion) | Raja Moreno |
| Database (PostgreSQL schema, migrations) | Tegar Putra Pratama |
| Interface (wireframes, HTML/CSS) | Satria Rizky Ramadhan |
| System logic (CRUD, goods in/out, reports) | Tegar Putra Pratama, Ahmad Zaky Sultan |

## 16. Risks and Open Questions

| Risk or question | Mitigation |
| --- | --- |
| Attendants forget to record entries, so stock drifts | Make entry very fast; run periodic stock opname |
| Initial stock data is inaccurate | Do a full physical count before go-live |
| Interview results (Table 3.1) are still empty in the proposal | Fill in before final submission |
| Does goods out for "sold" need quantity only, or also customer info? | Quantity only, per scope |
| Should the public catalog show exact quantity or only status (available, low, empty)? | Recommend status only |

**Partner contact:** Ilham, +62 822-3223-5499

## 17. Folder Structure (PHP Native)

Only `public/` is exposed as the web root; config, models, and SQL files stay out of the browser's reach. Pages are grouped by module, and each page starts with a role check.

```text
toko-sembako-a1/
├── public/                          # web root, the only folder the browser can reach
│   ├── index.php                    # redirects to login or dashboard
│   ├── login.php                    # login form, checks username and password
│   ├── logout.php                   # ends the session
│   ├── katalog.php                  # public catalog, no login (customers)
│   ├── dashboard.php                # admin summary cards and low-stock list
│   ├── cek-harga.php                # price and stock lookup (admin, penjaga)
│   ├── produk/                      # product management (admin)
│   │   ├── index.php                # product table with search and pagination
│   │   ├── tambah.php               # add product form
│   │   ├── edit.php                 # edit product form
│   │   └── hapus.php                # delete product
│   ├── kategori/                    # category management (admin)
│   │   ├── index.php                # category list
│   │   ├── tambah.php               # add category
│   │   ├── edit.php                 # edit category
│   │   └── hapus.php                # delete category
│   ├── transaksi/                   # stock movements
│   │   ├── masuk.php                # record goods in, stock increases (admin, penjaga)
│   │   ├── keluar.php               # record goods out: sold, damaged, expired (admin, penjaga)
│   │   └── penyesuaian.php          # stock adjustment after physical count (admin)
│   ├── riwayat/
│   │   └── index.php                # stock movement history (admin)
│   ├── laporan/
│   │   └── index.php                # inventory report filtered by period (admin)
│   ├── user/                        # account management (admin)
│   │   ├── index.php                # user list
│   │   ├── tambah.php               # add attendant account
│   │   ├── edit.php                 # edit account or reset password
│   │   └── hapus.php                # delete account
│   └── assets/
│       ├── css/                     # stylesheets
│       ├── js/                      # lookup search, confirm dialogs, form checks
│       └── img/                     # Toko Sembako A1 logo and icons
├── app/                             # application code, not reachable from the browser
│   ├── config/
│   │   ├── app.php                  # app name, timezone, base URL
│   │   └── database.php             # PostgreSQL settings, read from .env
│   ├── core/
│   │   ├── Database.php             # PDO (pdo_pgsql) connection, one shared instance
│   │   ├── Auth.php                 # login session, require_login(), require_role()
│   │   └── Csrf.php                 # create and verify CSRF tokens for forms
│   ├── models/                      # one class per ERD entity, SQL with prepared statements
│   │   ├── User.php                 # USER: login lookup, account CRUD
│   │   ├── Kategori.php             # KATEGORI: category CRUD
│   │   ├── Produk.php               # PRODUK: product CRUD, search, low-stock query
│   │   ├── Transaksi.php            # TRANSAKSI: create movement, history, reports
│   │   └── DetailTransaksi.php      # DETAIL_TRANSAKSI: items of each movement
│   ├── helpers/
│   │   └── functions.php            # escape output, flash messages, format date and number
│   └── views/
│       ├── layouts/
│       │   ├── header.php           # top bar with profile menu and menu toggle
│       │   ├── sidebar.php          # navigation menu, items shown by role
│       │   └── footer.php           # closing tags and script includes
│       └── partials/
│           ├── alert.php            # success and error message box
│           └── pagination.php      # page links for long tables
├── database/
│   ├── schema.sql                   # CREATE TABLE for the five ERD entities
│   ├── triggers.sql                 # trigger that updates produk.stok on each detail row
│   └── seed.sql                     # first admin account, categories, sample products
├── storage/
│   └── logs/                        # error logs, outside the web root
├── .env                             # database user, password, host (never committed)
├── .gitignore                       # excludes .env and logs
└── README.md                        # setup steps: install pdo_pgsql, import SQL, run
```

| Folder or file | Purpose |
| --- | --- |
| `public/` | Web root: the pages the browser opens, plus CSS, JS, and images in `assets/` |
| `public/katalog.php` | Public availability catalog; the only data page without login |
| `public/produk`, `kategori`, `user` | CRUD pages, admin only |
| `public/transaksi` | Goods in and out (admin and penjaga); stock adjustment (admin only) |
| `public/cek-harga.php` | Price and stock lookup for admin and penjaga |
| `app/config` | App settings and database settings; credentials are read from `.env` |
| `app/core` | PDO connection (`pdo_pgsql`), login session and role check, CSRF token |
| `app/models` | One class per ERD entity; all SQL uses prepared statements |
| `app/views` | Shared header, sidebar, and footer so every page has the same navigation |
| `database/` | Table definitions, the stock-update trigger, and seed data for initial products |
| `storage/logs` | Error logs, kept out of the web root |
