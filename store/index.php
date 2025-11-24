<?php
require_once __DIR__.'/utils.php';

$products = array_map('normalize_store_product', load_store_products());
$type = $_GET['type'] ?? 'flooring';
if (!in_array($type, ['flooring', 'molding'], true)) {
  $type = 'flooring';
}
$storeConfig = load_store_config();
$base = '../';
$active = 'store';
$contact_source = 'website_store';
$heroTitle = $type === 'molding' ? 'Store — Moldings' : 'Store — Waterproof LVP';
$heroSubtitle = $type === 'molding'
  ? 'Baseboards, casings, trims & more'
  : 'SPC/WPC core · Attached pad · Easy click install';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>B&S Floor Supply — Store</title>
  <link rel="stylesheet" href="<?=$base?>style.css" />
  <style>
    :root {
      --store-radius-lg: 18px;
      --store-radius-md: 12px;
      --store-shadow: 0 10px 24px rgba(0,0,0,0.08);
    }

    .store-shell { background:#f6f2ec; }

    .store-hero-new {
      background: linear-gradient(120deg, #f5ebdd, #fffdfa);
      border-bottom: 1px solid rgba(0,0,0,0.04);
      padding: 32px 0 22px;
      margin-bottom: 10px;
    }
    .store-hero-inner { display:flex; flex-direction:column; gap:12px; }
    .store-hero-top { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; flex-wrap:wrap; }
    .store-hero-title { color: var(--burgundy); font-size:1.7rem; margin:0; }
    .store-hero-sub { color:#7a7270; max-width:620px; }
    .store-hero-pills { display:flex; flex-wrap:wrap; gap:8px; }
    .store-hero-pills .pill { background:#fff; color:#4a3c39; border:1px solid #ded4cc; padding:6px 12px; border-radius:999px; font-size:0.9rem; }
    .store-type-switch { display:flex; gap:10px; flex-wrap:wrap; }
    .store-promo { display:flex; gap:10px; flex-wrap:wrap; }
    .store-promo .promo-card { background:#fff; padding:10px 14px; border-radius:var(--store-radius-md); border:1px solid rgba(0,0,0,0.04); box-shadow:var(--store-shadow); font-size:0.9rem; display:flex; align-items:center; gap:8px; }

    .store-toolbar { background:#fbf8f4; border-bottom:1px solid rgba(0,0,0,0.04); }
    .store-filter-bar { display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end; padding:14px 0; }
    .store-filter-bar label { display:flex; flex-direction:column; gap:6px; font-size:0.9rem; color:#4a3c39; }
    .store-filter-bar select, .store-filter-bar input { padding:11px 12px; border-radius:10px; border:1px solid #cfc5bd; background:#fff; min-width:140px; }
    .store-toolbar-actions { display:flex; gap:10px; align-items:center; margin-left:auto; }
    .store-toolbar-actions .muted { color:#7a7270; font-size:0.9rem; }

    .store-grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(260px,1fr)); gap:16px; margin-top:18px; }
    .store-card-new { background:#fff; border-radius:var(--store-radius-lg); overflow:hidden; box-shadow:var(--store-shadow); display:flex; flex-direction:column; border:1px solid rgba(0,0,0,0.02); }
    .store-card-img { padding-top:62%; background-size:cover; background-position:center; position:relative; }
    .store-tag { position:absolute; top:12px; left:12px; background:#591320; color:#fff; padding:6px 10px; border-radius:10px; font-size:0.8rem; }
    .store-tag.backorder { background:#cda349; color:#1f1f1f; }
    .store-card-body { padding:16px; display:flex; flex-direction:column; gap:10px; }
    .store-card-body h3 { margin:0; font-size:1.05rem; color:#1f1f1f; }
    .store-meta { color:#6a605e; font-size:0.9rem; }
    .store-prices { display:flex; flex-direction:column; gap:4px; }
    .store-price-line { display:flex; align-items:center; gap:8px; font-size:0.95rem; }
    .store-price-line b { color:#591320; }
    .store-cta-row { display:flex; gap:8px; flex-wrap:wrap; }
    .store-cta-row input { width:96px; }
    .store-badges { display:flex; flex-wrap:wrap; gap:6px; }
    .store-badge-new { background:#f6f2ec; padding:6px 10px; border-radius:10px; font-size:0.85rem; color:#5a504e; }
    @media (max-width: 880px){
      .store-grid { grid-template-columns: repeat(auto-fill, minmax(210px,1fr)); }
    }
  </style>
</head>
<body class="store-shell">
<?php include $base.'includes/header.php'; ?>
<section class="store-hero-new">
  <div class="container store-hero-inner">
    <div class="store-hero-top">
      <div>
        <h1 class="store-hero-title">B&S Store — <?= $type === 'molding' ? 'Moldings & Trim' : 'Waterproof LVP Floors' ?></h1>
        <p class="store-hero-sub">Explore in-stock and order-in options for Orlando & Meadow Woods. Prices by sqft and linear foot with matching trims, delivery and install add-ons.</p>
      </div>
      <div class="store-type-switch">
        <a class="btn <?= $type === 'flooring' ? 'btn-primary' : 'btn-ghost' ?>" href="?type=flooring">Flooring</a>
        <a class="btn <?= $type === 'molding' ? 'btn-primary' : 'btn-ghost' ?>" href="?type=molding">Moldings</a>
      </div>
    </div>
    <div class="store-hero-pills">
      <span class="pill">Waterproof core</span>
      <span class="pill">Attached pad</span>
      <span class="pill">Contractor pricing</span>
      <span class="pill">SPC/WPC</span>
    </div>
    <div class="store-promo">
      <div class="promo-card">🚚 Delivery + install quotes inside your cart</div>
      <div class="promo-card">🏷️ Stock vs. order-in pricing clearly labeled</div>
      <div class="promo-card">📦 Matching trims & reducers for every floor</div>
    </div>
  </div>
</section>
<div class="store-toolbar">
  <div class="container">
    <div class="store-filter-bar">
      <?php if($type === 'flooring'): ?>
      <label>Color
        <select id="fColor">
          <option value="">All</option>
          <option>Gray</option>
          <option>Brown</option>
          <option>Beige</option>
        </select>
      </label>
      <label>Tone
        <select id="fTone">
          <option value="">All</option>
          <option>Light</option>
          <option>Medium</option>
          <option>Dark</option>
        </select>
      </label>
      <label>Thickness min (mm)
        <input id="fThkMin" type="number" step="0.1" placeholder="5.0">
      </label>
      <label>Wear layer min (mil)
        <input id="fWearMin" type="number" step="1" placeholder="12">
      </label>
      <?php endif; ?>
      <label>Availability
        <select id="fAvail">
          <option value="">Stock & order-in</option>
          <option value="stock">In stock</option>
          <option value="backorder">Order-in</option>
        </select>
      </label>
      <button id="clearFilters" class="btn btn-ghost" type="button">Reset filters</button>
      <div class="store-toolbar-actions">
        <label for="sortSel" class="muted">Sort</label>
        <select id="sortSel">
          <option value="relevance" selected>Relevance</option>
          <option value="price-asc">Price (low → high)</option>
          <option value="price-desc">Price (high → low)</option>
        </select>
      </div>
    </div>
  </div>
</div>
<div class="container">
  <section id="store-grid" class="store-grid" aria-live="polite"></section>
</div>
<?php include $base.'includes/footer.php'; ?>
<script src="cart.js"></script>
<script>
const BS_PRODUCTS = <?= json_encode(array_values(array_filter($products, fn($p)=>($p['productType'] ?? 'flooring') === $type))) ?>;
const STORE_CONFIG = <?= json_encode($storeConfig) ?>;
const CURRENT_TYPE = <?= json_encode($type) ?>;
</script>
<script src="new-store.js"></script>
</body>
</html>
