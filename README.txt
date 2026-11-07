FAITH & GRACE — CHRISTIAN STORE (PHP + MySQL)
=============================================

A complete online store with a customer storefront, shopping cart, checkout,
newsletter sign-up and a secure admin dashboard.

REQUIREMENTS
- XAMPP (or any PHP 8.0+ and MySQL/MariaDB setup)

SETUP (XAMPP on Windows)
1. Install XAMPP from https://www.apachefriends.org
2. Unzip this folder and copy "Faith_Grace_PHP_Store" into C:\xampp\htdocs\
3. Open the XAMPP Control Panel and start Apache and MySQL.
4. Open http://localhost/phpmyadmin, click the "Import" tab,
   choose database.sql from this folder and click "Import".
   (This creates the database, tables, sample products and the admin account.)
5. Visit the store:   http://localhost/Faith_Grace_PHP_Store/
6. Visit the admin:   http://localhost/Faith_Grace_PHP_Store/admin/

ADMIN LOGIN
   Username: admin
   Password: Grace@2026
CHANGE THIS PASSWORD IMMEDIATELY: Admin -> Account -> Change password.

WHAT THE ADMIN CAN DO
- Dashboard: revenue, orders, pending orders, low-stock alerts
- Products: add, edit, hide/show, delete, manage stock
- Orders: view details, update status (Pending, Confirmed, Shipped,
  Delivered, Cancelled). Cancelling an order returns items to stock.
- Subscribers: view, remove, download as CSV
- Account: change your password

CUSTOMISING THE STORE
Open config/db.php to change:
- Database login (if your MySQL has a password)
- Currency, free-delivery threshold and delivery fee
- Contact email, phone, WhatsApp number, address and social links
  (leave a value blank to hide it on the website)

PICTURES
- The store comes with original illustrations for the sample products, plus a
  hero image and an About image (all in assets/img/).
- To use your own product photos: Admin -> Products -> Edit -> "Product
  picture" -> choose a JPG, PNG, WebP or GIF (up to 3 MB) -> Save.
  A landscape 4:3 photo with the product in the middle looks best.
- Uploaded photos are saved in the uploads/products/ folder. On online
  hosting, make sure that folder is writable (permission 755 or 775).
- Products without a picture show their emoji icon instead.
- To change the hero or About image, replace assets/img/hero.svg or
  assets/img/about.svg with your own file of the same name (or edit the
  image path in index.php).
- Already imported the earlier database.sql? You don't need to re-import:
  the store adds the new picture column by itself the first time the website
  or admin is opened.

GOING LIVE (ONLINE HOSTING)
- Upload all files to your host (public_html).
- Create a MySQL database in your hosting panel, import database.sql, then
  update the DB_* values in config/db.php.
- If you create your own database name, remove the first two lines of
  database.sql (CREATE DATABASE / USE) before importing.
- Change the default admin password straight away and use HTTPS.

SECURITY NOTES
- All database queries use prepared statements.
- Prices and delivery fees are always calculated on the server.
- Admin pages require login and use CSRF protection; passwords are hashed.
- config/ is blocked from direct web access on Apache.

FILE STRUCTURE
index.php            Storefront
assets/              style.css, app.js, img/ (illustrations)
uploads/products/    Product photos uploaded from the admin
api/                 checkout.php, subscribe.php
admin/               Dashboard (login, orders, products, subscribers, account)
config/db.php        Settings and database connection
database.sql         Database structure and sample data
