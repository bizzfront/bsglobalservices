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
    .store-shell { background:#f6f2ec; }
    .store-hero-new { background: linear-gradient(120deg, #f0e6d9, #f9f5ef); padding: 48px 0; margin-bottom: 12px; }
    .store-hero-new h1 { color: var(--burgundy); margin-bottom: 8px; }
    .store-hero-grid { display:grid; grid-template-columns: 1.2fr 1fr; gap:24px; align-items:center; }
    .store-hero-card { background:#fff; padding:18px; border-radius:18px; box-shadow:0 14px 40px rgba(0,0,0,0.06); }
    .store-hero-pills { display:flex; flex-wrap:wrap; gap:8px; margin-top:12px; }
    .store-hero-pills .pill { background:#591320; color:#fff; }
    .store-type-switch { display:flex; gap:10px; flex-wrap:wrap; }
    .store-filter-bar { display:flex; gap:12px; flex-wrap:wrap; padding:12px 0 6px; border-bottom:1px solid #ded4cc; }
    .store-filter-bar select, .store-filter-bar input { padding:10px 12px; border-radius:10px; border:1px solid #cfc5bd; background:#fff; }
    .store-grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(260px,1fr)); gap:16px; margin-top:18px; }
    .store-card-new { background:#fff; border-radius:18px; overflow:hidden; box-shadow:0 10px 28px rgba(0,0,0,0.08); display:flex; flex-direction:column; }
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
      .store-hero-grid { grid-template-columns: 1fr; }
      .store-grid { grid-template-columns: repeat(auto-fill, minmax(210px,1fr)); }
    }
  </style>
</head>
<body class="store-shell">
<?php include $base.'includes/header.php'; ?>
<section class="store-hero-new">
  <div class="container store-hero-grid">
    <div>
      <h1><?= htmlspecialchars($heroTitle) ?></h1>
      <p><?= htmlspecialchars($heroSubtitle) ?></p>
      <div class="store-type-switch" style="margin-top:14px;">
        <a class="btn <?= $type === 'flooring' ? 'btn-primary' : 'btn-ghost' ?>" href="?type=flooring">Flooring</a>
        <a class="btn <?= $type === 'molding' ? 'btn-primary' : 'btn-ghost' ?>" href="?type=molding">Moldings</a>
      </div>
      <div class="store-hero-pills">
        <span class="pill">Waterproof core</span>
        <span class="pill">Attached pad</span>
        <span class="pill">Contractor pricing</span>
      </div>
    </div>
    <div class="store-hero-card">
      <h3 style="color:var(--burgundy); margin-bottom:6px;">How it works</h3>
      <ol style="padding-left:20px; color:#514744; line-height:1.6;">
        <li>Select floors or moldings and set the needed sqft/lf.</li>
        <li>Choose stock vs. order-in pricing and add install/delivery.</li>
        <li>Send your project; we confirm stock, lead times and scheduling.</li>
      </ol>
    </div>
  </div>
</section>
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
    <div style="margin-left:auto; display:flex; align-items:center; gap:6px;">
      <label for="sortSel" class="muted">Sort</label>
      <select id="sortSel">
        <option value="relevance" selected>Relevance</option>
        <option value="price-asc">Price (low → high)</option>
        <option value="price-desc">Price (high → low)</option>
      </select>
    </div>
  </div>
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
