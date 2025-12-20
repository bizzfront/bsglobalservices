<?php
require_once __DIR__.'/utils.php';

$products = array_map('normalize_store_product', load_store_products());
$maxMoldingLength = 0;
foreach ($products as $product) {
  if (($product['productType'] ?? '') !== 'molding') {
    continue;
  }
  $lengthFt = $product['lengthFt'] ?? null;
  if (is_numeric($lengthFt) && $lengthFt > $maxMoldingLength) {
    $maxMoldingLength = (float) $lengthFt;
  }
}
$maxMoldingLength = $maxMoldingLength > 0 ? $maxMoldingLength : null;
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
      --store-radius-sm: 8px;
      --store-shadow: 0 10px 24px rgba(0,0,0,0.08);
      --store-border: #ded4cc;
      --store-muted: #7a7270;
      --store-bg: #f6f2ec;
      --store-ink: #1f1f1f;
    }

    .store-shell { background:var(--store-bg); color:var(--store-ink); }

    .store-hero-new {
      background: linear-gradient(120deg, #f5ebdd, #fffdfa);
      border-bottom: 1px solid rgba(0,0,0,0.04);
      padding: 28px 0 18px;
      margin-bottom: 0;
    }
    .store-hero-inner { display:flex; flex-direction:column; gap:12px; }
    .store-hero-top { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; flex-wrap:wrap; }
    .store-hero-title { color: var(--burgundy); font-size:1.7rem; margin:0; }
    .store-hero-sub { color:#7a7270; max-width:680px; }
    .store-hero-pills { display:flex; flex-wrap:wrap; gap:8px; }
    .store-hero-pills .pill { background:#fff; color:#4a3c39; border:1px solid #ded4cc; padding:6px 12px; border-radius:999px; font-size:0.9rem; }
    .store-type-switch { display:flex; gap:10px; flex-wrap:wrap; }
    .store-promo { display:flex; gap:10px; flex-wrap:wrap; }
    .store-promo .promo-card { background:#fff; padding:10px 14px; border-radius:var(--store-radius-md); border:1px solid rgba(0,0,0,0.04); box-shadow:var(--store-shadow); font-size:0.9rem; display:flex; align-items:center; gap:8px; }

    .store-toolbar { background:#fbf8f4; border-bottom:1px solid rgba(0,0,0,0.04); }
    .store-toolbar-inner { display:flex; align-items:center; gap:12px; flex-wrap:wrap; padding:12px 0; font-size:0.92rem; }
    .breadcrumb { color:var(--store-muted); }
    .breadcrumb span { color:var(--burgundy); font-weight:600; }
    .store-toolbar-actions { display:flex; gap:10px; align-items:center; margin-left:auto; flex-wrap:wrap; }
    .store-toolbar-actions label { color:var(--store-muted); font-size:0.88rem; }
    .store-toolbar .select { padding:9px 12px; border-radius:999px; border:1px solid var(--store-border); background:#fff; min-width:150px; }
    .store-toolbar .pill { background:#fff; border:1px solid var(--store-border); border-radius:999px; padding:8px 12px; color:#4a3c39; }

    .store-main { display:grid; grid-template-columns:280px minmax(0, 1fr); gap:18px; margin:18px 0 32px; align-items:start; }
    .filters-panel { position:sticky; top:90px; }
    .filter-card { background:#fff; border:1px solid rgba(0,0,0,0.06); box-shadow:var(--store-shadow); border-radius:var(--store-radius-lg); padding:16px; display:flex; flex-direction:column; gap:12px; }
    .filter-card header { display:flex; justify-content:space-between; align-items:center; font-weight:600; color:#3a2e2b; }
    .filter-title { font-weight:600; color:#3a2e2b; margin-bottom:6px; }
    .filter-group { border-top:1px solid rgba(0,0,0,0.05); padding-top:10px; }
    .filter-group:first-of-type { border-top:none; padding-top:0; }
    .filter-options { display:flex; flex-direction:column; gap:6px; color:#4a3c39; font-size:0.95rem; }
    .filter-options label { display:flex; align-items:center; gap:8px; cursor:pointer; }
    .filter-options input { accent-color: var(--burgundy); width:16px; height:16px; }
    .filter-links { display:flex; gap:8px; flex-wrap:wrap; }
    .filter-pill { padding:8px 12px; border-radius:999px; border:1px solid var(--store-border); background:#fff; color:#4a3c39; font-weight:600; }
    .filter-pill.active { background:var(--burgundy); border-color:var(--burgundy); color:#fff; }
    .clear-btn { background:none; border:none; color:var(--burgundy); font-weight:600; cursor:pointer; }

    .store-grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(260px,1fr)); gap:16px; margin-top:10px; }
    .store-card-new { background:#fff; border-radius:var(--store-radius-lg); overflow:hidden; box-shadow:var(--store-shadow); display:flex; flex-direction:column; border:1px solid rgba(0,0,0,0.02); transition:transform 0.15s ease, box-shadow 0.15s ease; }
    .store-card-new:hover { transform:translateY(-3px); box-shadow:0 14px 30px rgba(0,0,0,0.10); }
    .store-card-img { padding-top:62%; background-size:cover; background-position:center; position:relative; display:block; }
    .store-tag { position:absolute; top:12px; left:12px; background:#591320; color:#fff; padding:6px 10px; border-radius:10px; font-size:0.8rem; }
    .store-tag.backorder { background:#cda349; color:#1f1f1f; }
    .store-card-body { padding:16px; display:flex; flex-direction:column; gap:10px; }
    .store-card-body h3 { margin:0; font-size:1.05rem; color:#1f1f1f; }
    .store-card-color { display:inline-flex; align-items:center; gap:7px; padding:7px 11px; border-radius:999px; background:#f4ede7; border:1px solid #e5dad2; color:#4a3c39; font-weight:600; font-size:0.9rem; width:fit-content; }
    .store-card-color .dot { width:10px; height:10px; border-radius:50%; background:radial-gradient(circle at 30% 30%, #fff, #c7b6a8); box-shadow:inset 0 0 0 1px rgba(0,0,0,0.05); }
    .store-meta { color:#6a605e; font-size:0.9rem; }
    .store-meta strong { color:#3a2e2b; }
    .store-prices { display:flex; flex-direction:column; gap:4px; }
    .store-price-line { display:flex; align-items:center; gap:8px; font-size:0.95rem; }
    .store-price-line b { color:#591320; }
    .store-cta-row { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
    .store-cta-row input { width:96px; }
    .store-badges { display:flex; flex-wrap:wrap; gap:6px; }
    .store-badge-new { background:#f6f2ec; padding:6px 10px; border-radius:10px; font-size:0.85rem; color:#5a504e; border:1px solid rgba(0,0,0,0.04); }

    @media (max-width: 1050px){
      .store-main { grid-template-columns:1fr; }
      .filters-panel { position:static; }
    }
    @media (max-width: 880px){
      .store-grid { grid-template-columns: repeat(auto-fill, minmax(210px,1fr)); }
      .store-toolbar .select { min-width:130px; }
    }
  </style>
</head>
<body class="store-shell">
<?php include $base.'includes/header.php'; ?>
<section class="store-hero-new">
  <div class="container store-hero-inner">
    <div class="store-hero-top">
      <div>
        <h1 class="store-hero-title" data-i18n="<?= $type === 'molding' ? 'store_hero_title_molding' : 'store_hero_title_flooring' ?>">
          B&S Store — <?= $type === 'molding' ? 'Moldings & Trim' : 'Waterproof LVP Floors' ?>
        </h1>
        <p class="store-hero-sub" data-i18n="<?= $type === 'molding' ? 'store_hero_subtitle_molding' : 'store_hero_subtitle_flooring' ?>">
          Explore in-stock and order-in options for Orlando & Meadow Woods. Prices by sqft and linear foot with matching trims, delivery and install add-ons.
        </p>
      </div>
      <div class="store-type-switch">
        <a class="btn <?= $type === 'flooring' ? 'btn-primary' : 'btn-ghost' ?>" href="?type=flooring" data-i18n="store_type_flooring">Flooring</a>
        <a class="btn <?= $type === 'molding' ? 'btn-primary' : 'btn-ghost' ?>" href="?type=molding" data-i18n="store_type_molding">Moldings</a>
      </div>
    </div>
    <div class="store-hero-pills">
      <span class="pill" data-i18n="store_pill_waterproof_core">Waterproof core</span>
      <span class="pill" data-i18n="store_pill_attached_pad">Attached pad</span>
      <span class="pill" data-i18n="store_pill_contractor_pricing">Contractor pricing</span>
      <span class="pill" data-i18n="store_pill_spc_wpc">SPC/WPC</span>
    </div>
    <div class="store-promo">
      <div class="promo-card" data-i18n="store_promo_delivery_install">🚚 Delivery + install quotes inside your cart</div>
      <div class="promo-card" data-i18n="store_promo_stock_order">🏷️ Stock vs. order-in pricing clearly labeled</div>
      <div class="promo-card" data-i18n="store_promo_trims">📦 Matching trims & reducers for every floor</div>
    </div>
  </div>
</section>
<div class="store-toolbar">
  <div class="container">
    <div class="store-toolbar-inner">
      <div class="breadcrumb"><span data-i18n="store_breadcrumb_home">Home</span> &gt; <span data-i18n="store_breadcrumb_store">Store</span></div>
      <span class="pill" id="resultSummary" data-i18n="store_result_summary_initial">Showing 0 products</span>
      <div class="store-toolbar-actions">
        <div>
          <label for="typeSwitch" data-i18n="store_label_category">Category</label><br />
          <select id="typeSwitch" class="select">
            <option value="flooring" <?= $type === 'flooring' ? 'selected' : '' ?> data-i18n="store_option_floors">Floors</option>
            <option value="molding" <?= $type === 'molding' ? 'selected' : '' ?> data-i18n="store_option_moldings_trim">Moldings &amp; Trim</option>
          </select>
        </div>
        <div>
          <label for="sortSel" data-i18n="store_label_sort_by">Sort by</label><br />
          <select id="sortSel" class="select">
            <option value="relevance" selected data-i18n="store_sort_recommended">Recommended</option>
            <option value="price-asc" data-i18n="store_sort_price_low_high">Price (low → high)</option>
            <option value="price-desc" data-i18n="store_sort_price_high_low">Price (high → low)</option>
          </select>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="container">
  <div class="store-main">
    <aside class="filters-panel">
      <div class="filter-card">
        <header>
          <span data-i18n="store_filters_title">Filters</span>
          <button id="clearFilters" class="clear-btn" type="button" data-i18n="store_filters_clear">Clear</button>
        </header>
        <div class="filter-group">
          <div class="filter-title" data-i18n="store_filter_category">Category</div>
          <div class="filter-links">
            <a class="filter-pill <?= $type === 'flooring' ? 'active' : '' ?>" href="?type=flooring" data-i18n="store_option_floors">Floors</a>
            <a class="filter-pill <?= $type === 'molding' ? 'active' : '' ?>" href="?type=molding" data-i18n="store_option_moldings_trim">Moldings &amp; Trim</a>
          </div>
        </div>
        <div class="filter-group">
          <div class="filter-title" data-i18n="store_filter_availability">Availability</div>
          <div class="filter-options">
            <label><input type="checkbox" class="filter-availability" value="stock" /> <span data-i18n="store_filter_in_stock">In stock</span></label>
            <label><input type="checkbox" class="filter-availability" value="backorder" /> <span data-i18n="store_filter_order_in">Order-in (7–30 days)</span></label>
            <label><input type="checkbox" class="filter-availability" value="nextday" /> <span data-i18n="store_filter_next_day">Next day (&lt; 7 days)</span></label>
          </div>
        </div>
        <div class="filter-group">
          <div class="filter-title" data-i18n="store_filter_color_tone">Color &amp; Tone</div>
          <div class="filter-options">
            <label><input type="checkbox" class="filter-color" value="Light" /> <span data-i18n="store_color_light">Light</span></label>
            <label><input type="checkbox" class="filter-color" value="Medium" /> <span data-i18n="store_color_medium">Medium</span></label>
            <label><input type="checkbox" class="filter-color" value="Dark" /> <span data-i18n="store_color_dark">Dark</span></label>
            <label><input type="checkbox" class="filter-color" value="Brown" /> <span data-i18n="store_color_brown">Brown</span></label>
            <label><input type="checkbox" class="filter-color" value="Gray" /> <span data-i18n="store_color_gray">Gray</span></label>
            <label><input type="checkbox" class="filter-color" value="Neutral" /> <span data-i18n="store_color_neutral">Neutral</span></label>
            <label><input type="checkbox" class="filter-color" value="White" /> <span data-i18n="store_color_white">White</span></label>
          </div>
        </div>
        <?php if($type === 'flooring'): ?>
        <div class="filter-group">
          <div class="filter-title" data-i18n="store_filter_specs">Specs</div>
          <div class="filter-options">
            <label><input id="fThkMin" type="number" step="0.1" placeholder="Thickness ≥ mm" data-i18n-placeholder="store_filter_thickness_placeholder" style="width:100%; padding:9px 10px; border-radius:8px; border:1px solid var(--store-border);" /></label>
            <label><input id="fWearMin" type="number" step="1" placeholder="Wear layer ≥ mil" data-i18n-placeholder="store_filter_wear_placeholder" style="width:100%; padding:9px 10px; border-radius:8px; border:1px solid var(--store-border);" /></label>
          </div>
        </div>
        <?php endif; ?>
        <?php if($type === 'molding'): ?>
        <div class="filter-group">
          <div class="filter-title">Length (ft)</div>
          <div class="filter-options">
            <label>
              <input id="fLengthMax" type="number" step="0.1" min="0" <?= $maxMoldingLength !== null ? 'max="'.$maxMoldingLength.'"' : '' ?> placeholder="Length ≤ ft" style="width:100%; padding:9px 10px; border-radius:8px; border:1px solid var(--store-border);" />
            </label>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </aside>
    <div>
      <section id="store-grid" class="store-grid" aria-live="polite"></section>
    </div>
  </div>
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
