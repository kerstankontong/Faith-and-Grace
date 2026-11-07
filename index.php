<?php
declare(strict_types=1);
require_once __DIR__ . '/config/db.php';

ensure_schema();
try {
    $products = db()->query(
        'SELECT id, name, category, description, price, icon, image, stock FROM products WHERE is_active = 1 ORDER BY category, name'
    )->fetchAll();
} catch (PDOException $e) { // image column missing and could not be added
    $products = db()->query(
        'SELECT id, name, category, description, price, icon, NULL AS image, stock FROM products WHERE is_active = 1 ORDER BY category, name'
    )->fetchAll();
}

$categories = [];
foreach ($products as $p) {
    $categories[$p['category']] = ($categories[$p['category']] ?? 0) + 1;
}

$categoryBlurbs = [
    'Bibles & Books' => 'Grow in the Word',
    'Clothing'       => 'Wear your faith',
    'Accessories'    => 'Carry your message',
    'Gifts'          => 'Give with love',
];
$categoryIcons = [
    'Bibles & Books' => '📖',
    'Clothing'       => '👕',
    'Accessories'    => '✝',
    'Gifts'          => '🎁',
];

$storeJs = [
    'currency' => CURRENCY,
    'freeOver' => FREE_DELIVERY_OVER,
    'fee'      => DELIVERY_FEE,
    'products' => array_map(static fn(array $p): array => [
        'id'    => (int) $p['id'],
        'name'  => $p['name'],
        'price' => (float) $p['price'],
        'icon'  => $p['icon'],
        'image' => image_path($p['image'] ?? null),
        'stock' => (int) $p['stock'],
    ], $products),
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e(STORE_NAME) ?> | Christian Bibles, Books, Clothing &amp; Gifts</title>
<meta name="description" content="Faith-filled Bibles, Christian books, clothing, accessories and gifts. Shop with faith and live with purpose.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="topbar">
    <span>✦ Free delivery on orders over <?= e(money(FREE_DELIVERY_OVER)) ?></span>
    <span class="topbar-right">Faith • Hope • Love</span>
</div>

<header class="nav">
    <div class="nav-inner">
        <a class="logo" href="index.php"><span class="logo-mark">✝</span> <?= e(STORE_NAME) ?></a>
        <nav class="nav-links" id="navLinks" aria-label="Main">
            <a href="#home">Home</a>
            <a href="#categories">Categories</a>
            <a href="#shop">Shop</a>
            <a href="#about">About</a>
            <a href="#contact">Contact</a>
        </nav>
        <div class="nav-actions">
            <button class="icon-btn" id="searchBtn" aria-label="Search products">⌕</button>
            <button class="cart-btn" id="cartBtn" aria-label="Open cart">
                <span aria-hidden="true">🛒</span> Cart <span class="badge" id="cartCount">0</span>
            </button>
            <button class="icon-btn menu-btn" id="menuBtn" aria-label="Menu" aria-expanded="false">☰</button>
        </div>
    </div>
</header>

<main>
<section class="hero" id="home">
    <div class="hero-copy">
        <span class="eyebrow">Welcome to <?= e(STORE_NAME) ?></span>
        <h1>Shop with <em>faith.</em><br>Live with purpose.</h1>
        <p>Discover inspiring Bibles, Christian books, clothing, accessories and thoughtful gifts for every faith-filled moment.</p>
        <div class="hero-cta">
            <a class="btn btn-gold" href="#shop">Shop the collection →</a>
            <a class="btn btn-ghost" href="#about">Our story</a>
        </div>
    </div>
    <div class="hero-visual">
        <div class="hero-card">
            <img class="hero-img" src="assets/img/hero.svg" alt="" width="600" height="700">
            <blockquote>“Walk by faith,<br>not by sight.”<cite>2 Corinthians 5:7</cite></blockquote>
        </div>
    </div>
</section>

<section class="trust" aria-label="Why shop with us">
    <div><span>✦</span><strong>Quality products</strong><small>Carefully selected</small></div>
    <div><span>◇</span><strong>Simple ordering</strong><small>Quick, easy checkout</small></div>
    <div><span>♡</span><strong>Made to inspire</strong><small>Faith-filled gifts</small></div>
    <div><span>✓</span><strong>Friendly service</strong><small>Here to help</small></div>
</section>

<section class="section" id="categories">
    <div class="section-head center">
        <span class="eyebrow">Shop by category</span>
        <h2>Find something meaningful</h2>
    </div>
    <div class="categories">
        <?php foreach ($categories as $name => $count): ?>
        <a class="cat-card" href="#shop" data-filter="<?= e($name) ?>">
            <span class="cat-icon"><?= e($categoryIcons[$name] ?? '✝') ?></span>
            <h3><?= e($name) ?></h3>
            <p><?= e($categoryBlurbs[$name] ?? 'Faith-filled finds') ?></p>
            <small><?= (int) $count ?> product<?= $count === 1 ? '' : 's' ?></small>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="section shop" id="shop">
    <div class="section-head split">
        <div>
            <span class="eyebrow">Our collection</span>
            <h2>Featured products</h2>
        </div>
        <div class="shop-tools">
            <input type="search" id="search" placeholder="Search products…" aria-label="Search products">
            <select id="sort" aria-label="Sort products">
                <option value="default">Sort: Featured</option>
                <option value="low">Price: Low to high</option>
                <option value="high">Price: High to low</option>
                <option value="name">Name: A–Z</option>
            </select>
        </div>
    </div>

    <div class="pills" id="pills" role="tablist" aria-label="Filter by category">
        <button class="pill active" data-filter="All">All</button>
        <?php foreach ($categories as $name => $count): ?>
        <button class="pill" data-filter="<?= e($name) ?>"><?= e($name) ?></button>
        <?php endforeach; ?>
    </div>

    <div class="products" id="products">
        <?php foreach ($products as $i => $p): $out = (int) $p['stock'] <= 0; ?>
        <article class="product" data-id="<?= (int) $p['id'] ?>" data-name="<?= e(strtolower($p['name'])) ?>"
                 data-category="<?= e($p['category']) ?>" data-price="<?= e($p['price']) ?>" data-order="<?= $i ?>">
            <?php $img = image_path($p['image'] ?? null); ?>
            <div class="product-img<?= $img ? ' has-photo' : '' ?>">
                <?php if ($img): ?><img src="<?= e($img) ?>" alt="<?= e($p['name']) ?>" loading="lazy" width="400" height="300"><?php else: ?><span><?= e($p['icon']) ?></span><?php endif; ?>
                <?php if ($out): ?><em class="sold">Sold out</em><?php endif; ?>
            </div>
            <div class="product-body">
                <small class="product-cat"><?= e($p['category']) ?></small>
                <h3><?= e($p['name']) ?></h3>
                <?php if (!empty($p['description'])): ?><p><?= e($p['description']) ?></p><?php endif; ?>
                <div class="product-foot">
                    <strong><?= e(money($p['price'])) ?></strong>
                    <button class="btn btn-navy btn-sm" data-add="<?= (int) $p['id'] ?>" <?= $out ? 'disabled' : '' ?>><?= $out ? 'Sold out' : 'Add to cart' ?></button>
                </div>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
    <p class="empty" id="noResults" hidden>No products match your search. Try a different word or category.</p>
</section>

<section class="scripture">
    <div class="scripture-inner">
        <span class="eyebrow light">Daily encouragement</span>
        <h2>“Let all that you do be done in love.”</h2>
        <p>1 Corinthians 16:14</p>
    </div>
</section>

<section class="section about" id="about">
    <div class="about-grid">
        <div>
            <span class="eyebrow">Our mission</span>
            <h2>More than a store.<br>A place of encouragement.</h2>
            <img class="about-img" src="assets/img/about.svg" alt="An open Bible with light shining from its pages" loading="lazy" width="640" height="420">
        </div>
        <div>
            <p><?= e(STORE_NAME) ?> exists to help people celebrate, share and grow in faith. Every item in our collection is chosen with purpose, quality and love, so that what you buy, wear or give carries meaning long after the day it arrives.</p>
            <ul class="ticks">
                <li>Thoughtfully curated Bibles, books and gifts</li>
                <li>Everyday wear and accessories that quietly share your faith</li>
                <li>Friendly, personal service on every order</li>
            </ul>
        </div>
    </div>
</section>

<section class="newsletter" id="contact">
    <div class="newsletter-copy">
        <span class="eyebrow light">Stay connected</span>
        <h2>Let faith fill your inbox</h2>
        <p>Get collection updates, encouragement and special offers.</p>
        <?php if (STORE_EMAIL || STORE_PHONE || STORE_WHATSAPP || STORE_ADDRESS): ?>
        <ul class="contact-list">
            <?php if (STORE_EMAIL): ?><li>✉ <a href="mailto:<?= e(STORE_EMAIL) ?>"><?= e(STORE_EMAIL) ?></a></li><?php endif; ?>
            <?php if (STORE_PHONE): ?><li>☎ <a href="tel:<?= e(preg_replace('/\s+/', '', STORE_PHONE)) ?>"><?= e(STORE_PHONE) ?></a></li><?php endif; ?>
            <?php if (STORE_WHATSAPP): ?><li>💬 <a href="https://wa.me/<?= e(STORE_WHATSAPP) ?>" target="_blank" rel="noopener">Chat on WhatsApp</a></li><?php endif; ?>
            <?php if (STORE_ADDRESS): ?><li>⌖ <?= e(STORE_ADDRESS) ?></li><?php endif; ?>
        </ul>
        <?php endif; ?>
    </div>
    <form class="newsletter-form" id="subscribeForm" novalidate>
        <label for="subEmail" class="sr">Email address</label>
        <input type="email" id="subEmail" name="email" required placeholder="Your email address" autocomplete="email">
        <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">
        <button class="btn btn-gold" type="submit">Subscribe</button>
        <p class="form-msg" id="subMsg" role="status"></p>
    </form>
</section>
</main>

<footer class="footer">
    <div class="footer-grid">
        <div>
            <a class="logo light" href="index.php"><span class="logo-mark">✝</span> <?= e(STORE_NAME) ?></a>
            <p>Faith-filled products for everyday life.</p>
        </div>
        <div>
            <h4>Explore</h4>
            <a href="#shop">Shop</a>
            <a href="#categories">Categories</a>
            <a href="#about">Our mission</a>
        </div>
        <div>
            <h4>Help</h4>
            <a href="#contact">Contact</a>
            <a href="#contact">Newsletter</a>
        </div>
        <?php if (SOCIAL_INSTAGRAM || SOCIAL_FACEBOOK || STORE_WHATSAPP): ?>
        <div>
            <h4>Connect</h4>
            <?php if (SOCIAL_INSTAGRAM): ?><a href="<?= e(SOCIAL_INSTAGRAM) ?>" target="_blank" rel="noopener">Instagram</a><?php endif; ?>
            <?php if (SOCIAL_FACEBOOK): ?><a href="<?= e(SOCIAL_FACEBOOK) ?>" target="_blank" rel="noopener">Facebook</a><?php endif; ?>
            <?php if (STORE_WHATSAPP): ?><a href="https://wa.me/<?= e(STORE_WHATSAPP) ?>" target="_blank" rel="noopener">WhatsApp</a><?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    <div class="footer-copy">© <?= date('Y') ?> <?= e(STORE_NAME) ?>. All rights reserved.</div>
</footer>

<!-- Cart drawer -->
<div class="overlay" id="overlay"></div>
<aside class="cart" id="cart" aria-label="Shopping cart" aria-hidden="true">
    <header>
        <h2>Your cart</h2>
        <button class="icon-btn" id="cartClose" aria-label="Close cart">×</button>
    </header>
    <div class="cart-body" id="cartItems"></div>
    <div class="cart-foot">
        <p class="ship-note" id="shipNote"></p>
        <div class="row"><span>Subtotal</span><span id="cartSubtotal">—</span></div>
        <div class="row"><span>Delivery</span><span id="cartDelivery">—</span></div>
        <div class="row total"><span>Total</span><strong id="cartTotal">—</strong></div>
        <button class="btn btn-gold btn-block" id="checkoutBtn">Checkout →</button>
    </div>
</aside>

<!-- Checkout dialog -->
<dialog class="checkout" id="checkout">
    <form id="checkoutForm" novalidate>
        <header>
            <h2>Complete your order</h2>
            <button type="button" class="icon-btn" id="checkoutClose" aria-label="Close">×</button>
        </header>
        <div id="checkoutFields">
            <label>Full name
                <input type="text" name="name" required maxlength="150" autocomplete="name">
            </label>
            <label>Phone number
                <input type="tel" name="phone" required maxlength="40" autocomplete="tel" placeholder="e.g. 024 123 4567">
            </label>
            <label>Email <small>(optional)</small>
                <input type="email" name="email" maxlength="150" autocomplete="email">
            </label>
            <label>Delivery address
                <input type="text" name="address" required maxlength="255" autocomplete="street-address" placeholder="Area, street, landmark">
            </label>
            <label>Order notes <small>(optional)</small>
                <textarea name="notes" rows="2" maxlength="500"></textarea>
            </label>
            <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">
            <div class="checkout-summary" id="checkoutSummary"></div>
            <p class="form-msg error" id="checkoutMsg" role="alert"></p>
            <button class="btn btn-gold btn-block" type="submit" id="placeOrder">Place order</button>
            <p class="fine">We'll contact you on the number above to confirm payment and delivery.</p>
        </div>
        <div class="success" id="checkoutDone" hidden>
            <div class="success-mark">✓</div>
            <h3>Thank you, your order is in!</h3>
            <p id="doneText"></p>
            <button type="button" class="btn btn-navy" id="doneClose">Continue shopping</button>
        </div>
    </form>
</dialog>

<div class="toast" id="toast" role="status" aria-live="polite"></div>

<script>window.STORE = <?= json_encode($storeJs, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>;</script>
<script src="assets/app.js"></script>
</body>
</html>
