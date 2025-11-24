<?php
require_once __DIR__.'/utils.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$products = load_store_products();
$sku = $_GET['sku'] ?? '';
$product = null;
foreach ($products as $p) {
  if (($p['sku'] ?? '') === $sku) {
    $product = $p;
    break;
  }
}
if (!$product) {
  http_response_code(404);
  echo 'Product not found';
  exit;
}
$imagePaths = array_filter([$product['image'] ?? '', $product['hoverImage'] ?? '']);
$baseDir = $imagePaths ? dirname(dirname($imagePaths[0])) : null;
$basePath = $baseDir ? __DIR__ . '/../' . $baseDir : null;
$images = [];
if ($basePath && is_dir($basePath)) {
  $images = array_merge(glob($basePath.'/*.png') ?: [], glob($basePath.'/*/*.png') ?: []);
  $images = array_map(function($p){return str_replace(__DIR__.'/../','',$p);}, $images);
}
$selected = array_unique(array_filter([$product['image'] ?? '', $product['hoverImage'] ?? '']));
$others = array_diff($images, $selected);
shuffle($others);
$selected = array_merge($selected, array_slice($others, 0, max(0, 4 - count($selected))));
$base = '../';
$active = 'store';
$contact_source = 'website_store';
$normalizedProducts = array_map('normalize_store_product', $products);
$flooringProducts = array_values(array_filter($normalizedProducts, fn($p)=>($p['productType'] ?? 'flooring') === 'flooring'));
$productType = $product['product_type'] ?? 'flooring';
$isFlooring = $productType === 'flooring';
$measurementUnit = strtolower($product['measurement_unit'] ?? ($isFlooring ? 'sqft' : 'lf'));
$unitLabels = [
  'sqft' => 'sqft',
  'lf' => 'linear feet',
  'piece' => 'pieces'
];
$packageLabelSingular = $product['package_label'] ?? ($isFlooring ? 'box' : 'piece');
$packageLabelPlural = $product['package_label_plural'] ?? ($isFlooring ? 'boxes' : 'pieces');
$unitLabel = $unitLabels[$measurementUnit] ?? $measurementUnit;
$unitSuffix = $measurementUnit === 'lf' ? '/lf' : ($measurementUnit === 'piece' ? '/piece' : '/sqft');
$lengthFtValue = isset($product['length_ft']) ? (float)$product['length_ft'] : null;
$piecesPerBoxValue = isset($product['pieces_per_box']) ? (float)$product['pieces_per_box'] : null;
$coveragePerBoxValue = $isFlooring
  ? ($product['computed_coverage_per_package'] ?? $product['coverage_per_box'] ?? $product['sqft_per_box'] ?? null)
  : ($lengthFtValue ?? ($product['computed_coverage_per_package'] ?? $product['coverage_per_box'] ?? $product['sqft_per_box'] ?? null));
$stockAvailableValue = isset($product['availability']['stockAvailable']) ? (float)$product['availability']['stockAvailable'] : null;
$hasInventoryAvailable = $stockAvailableValue !== null && $stockAvailableValue > 0;
$activePriceMode = $product['availability']['activePriceType'] ?? ($hasInventoryAvailable ? 'stock' : 'backorder');
$coveragePerBoxValue = $coveragePerBoxValue !== null ? (float)$coveragePerBoxValue : null;
if($coveragePerBoxValue !== null){
  $coveragePerBoxValue = (float)$coveragePerBoxValue;
}
$formatCurrency = static function($value) {
  if($value === null){
    return '';
  }
  return '$'.number_format((float)$value, 2);
};
$priceModes = [];
$stockUnitValue = isset($product['computed_price_per_unit_stock']) ? (float)$product['computed_price_per_unit_stock'] : ($product['product_type'] === 'flooring' ? null : ($product['computed_price_per_unit'] ?? $product['price_per_unit'] ?? $product['price_sqft'] ?? null));
if($stockUnitValue !== null){
  $stockPackageValue = isset($product['computed_price_per_package_stock']) ? (float)$product['computed_price_per_package_stock'] : (($coveragePerBoxValue && $stockUnitValue !== null) ? $stockUnitValue * $coveragePerBoxValue : null);
  $priceModes['stock'] = [
    'label' => 'In stock',
    'unit' => (float)$stockUnitValue,
    'package' => $stockPackageValue !== null ? (float)$stockPackageValue : null,
  ];
}
$backorderUnitValue = isset($product['computed_price_per_unit_backorder']) ? (float)$product['computed_price_per_unit_backorder'] : null;
if($backorderUnitValue !== null){
  $backorderPackageValue = isset($product['computed_price_per_package_backorder']) ? (float)$product['computed_price_per_package_backorder'] : (($coveragePerBoxValue && $backorderUnitValue !== null) ? $backorderUnitValue * $coveragePerBoxValue : null);
  $priceModes['backorder'] = [
    'label' => 'Backorder',
    'unit' => (float)$backorderUnitValue,
    'package' => $backorderPackageValue !== null ? (float)$backorderPackageValue : null,
  ];
}
if(!$priceModes){
  $fallbackUnit = $product['computed_price_per_unit'] ?? $product['price_per_unit'] ?? $product['price_sqft'] ?? null;
  if($fallbackUnit !== null){
    $fallbackPackage = $product['computed_price_per_package'] ?? (($coveragePerBoxValue && $fallbackUnit !== null) ? (float)$fallbackUnit * $coveragePerBoxValue : null);
    $priceModes['stock'] = [
      'label' => $isFlooring ? 'In stock' : 'Standard',
      'unit' => (float)$fallbackUnit,
      'package' => $fallbackPackage !== null ? (float)$fallbackPackage : null,
    ];
  }
}
$preferredMode = $activePriceMode ?? null;
if($preferredMode && isset($priceModes[$preferredMode])){
  $priceModes = [$preferredMode => $priceModes[$preferredMode]];
}
$defaultPriceMode = isset($priceModes[$preferredMode]) ? $preferredMode : (isset($priceModes['stock']) ? 'stock' : (isset($priceModes['backorder']) ? 'backorder' : (array_key_first($priceModes) ?: 'stock')));
$firstMode = $priceModes[$defaultPriceMode] ?? (array_values($priceModes)[0] ?? ['unit' => null, 'package' => null, 'label' => '']);
$priceModesData = [];
foreach($priceModes as $key => $data){
  $priceModesData[$key] = [
    'label' => $data['label'],
    'unitValue' => $data['unit'],
    'packageValue' => $data['package'],
  ];
}
$formatNumber = static function($value) {
  if($value === null) return '';
  $formatted = number_format((float)$value, 2, '.', '');
  return rtrim(rtrim($formatted, '0'), '.');
};
$coveragePerBoxLabel = $coveragePerBoxValue ? $formatNumber($coveragePerBoxValue) . ' ' . ($unitLabels[$measurementUnit] ?? $measurementUnit) . ' / ' . $packageLabelSingular : '';
$normalizedProduct = normalize_store_product($product);
$storeConfig = load_store_config();
$installRateValue = $normalizedProduct['services']['installRate'] ?? ($isFlooring
  ? ($storeConfig['install']['defaultFlooringRate'] ?? null)
  : ($storeConfig['install']['defaultMoldingRate'] ?? null));
$installRateLabel = $installRateValue !== null
  ? $formatCurrency($installRateValue) . ' / ' . ($isFlooring ? 'sq ft' : $unitLabel)
  : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($product['name']) ?> — B&S Floor Supply</title>
  <link rel="stylesheet" href="<?=$base?>style.css" />
  <style>
    .calc-flooring-helper { margin-top:12px; border:1px solid #e6dcd9; background:#fbf7f5; border-radius:12px; padding:12px; box-shadow:0 8px 20px rgba(0,0,0,0.06); position:relative; }
    .calc-helper-header { display:flex; flex-direction:column; gap:4px; margin-bottom:10px; }
    .calc-helper-title { font-weight:700; color:#5a1620; }
    .calc-helper-sub { color:#6a605e; font-size:0.92rem; }
    .calc-helper-list { display:flex; flex-direction:column; gap:8px; }
    .calc-helper-item { width:100%; text-align:left; border:1px solid #e6dcd9; background:#fff; border-radius:10px; padding:10px 12px; display:flex; justify-content:space-between; gap:10px; align-items:center; cursor:pointer; transition:border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease; }
    .calc-helper-item:hover { border-color:#c3b6b2; box-shadow:0 8px 16px rgba(89,19,32,0.10); }
    .calc-helper-item:disabled { opacity:0.6; cursor:not-allowed; box-shadow:none; }
    .calc-helper-name { font-weight:600; color:#2f2523; }
    .calc-helper-meta { color:#6a605e; font-size:0.9rem; }
    .calc-helper-action { color:#5a1620; font-weight:700; white-space:nowrap; }
    .calc-helper-empty { color:#6a605e; font-size:0.95rem; text-align:left; }
  </style>
</head>
<body class="store-product">
<?php include $base.'includes/header.php'; ?>
  <main class="container" style="padding-bottom:3vw;">
    <div class="sec-head">
        <div style="margin-top:1.6vw;" >
          <div class="eyebrow" data-i18n="ey_popular">Popular Vinyl Plank Options</div>
          <h1 data-i18n="h_popular"><?= htmlspecialchars($product['name']) ?></h2>
        </div>
        <span class="pill"><?= htmlspecialchars($product['name']) ?></span>
    </div>
    <h1></h1>
    <div class="product-top">
      <div class="slider">
        <div>
          <?php foreach($selected as $i => $img): ?>
            <img src="../<?= $img ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="<?= $i===0 ? 'active' : '' ?>">
          <?php endforeach; ?>
          <button class="prev" type="button">Prev</button>
          <button class="next" type="button">Next</button>
          <?php if(!empty($product['promo'])): ?>
            <div class="store-promo"><span><?= htmlspecialchars($product['promo']) ?></span></div>
          <?php endif; ?>
        </div>
      </div>
      <div class="product-cart">
        <?php
        ?>
        <?php if($isFlooring && count($priceModes) > 1): ?>
          <div class="store-price-options" id="priceOptions">
            <?php foreach($priceModes as $modeKey => $modeData): ?>
              <label class="store-price-option">
                <input type="radio" name="price_mode" value="<?= htmlspecialchars($modeKey) ?>" <?= $modeKey === $defaultPriceMode ? 'checked' : '' ?>>
                <div>
                  <span class="store-price-option-label"><?= htmlspecialchars($modeData['label']) ?></span>
                  <?php if($modeData['unit'] !== null): ?>
                    <span class="store-price-option-main"><b><?= $formatCurrency($modeData['unit']) ?></b><span class="store-per"><?= htmlspecialchars($unitSuffix) ?></span></span>
                  <?php else: ?>
                    <span class="store-price-option-main"><b>Call for price</b></span>
                  <?php endif; ?>
                  <?php if($modeData['package'] !== null): ?>
                    <span class="store-price-option-sub">≈ <?= $formatCurrency($modeData['package']) ?> / <?= htmlspecialchars($packageLabelSingular) ?></span>
                  <?php endif; ?>
                </div>
              </label>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="store-price">
            <?php if($firstMode['unit'] !== null): ?>
              <div><b><?= $formatCurrency($firstMode['unit']) ?></b><span class="store-per"><?= htmlspecialchars($unitSuffix) ?></span></div>
            <?php else: ?>
              <div><b>Call for price</b></div>
            <?php endif; ?>
            <?php if($firstMode['package'] !== null): ?>
              <div><span class="store-per">≈ <?= $formatCurrency($firstMode['package']) ?> / <?= htmlspecialchars($packageLabelSingular) ?></span></div>
            <?php endif; ?>
          </div>
        <?php endif; ?>
        <?php if($coveragePerBoxLabel): ?>
          <div class="store-price-note"><span class="store-per"><?= htmlspecialchars($coveragePerBoxLabel) ?></span></div>
        <?php endif; ?>
        <?php if($hasInventoryAvailable): ?>
          <div class="store-price-note"><span class="store-per">In stock: <?= number_format((float)$stockAvailableValue, 0) ?> <?= $stockAvailableValue == 1 ? htmlspecialchars($packageLabelSingular) : htmlspecialchars($packageLabelPlural) ?><?php if($coveragePerBoxValue): ?> (≈ <?= number_format((float)$stockAvailableValue * (float)$coveragePerBoxValue, 0) ?> <?= htmlspecialchars($unitLabel) ?>)<?php endif; ?></span></div>
        <?php endif; ?>

        <div id="calc" class="calc">
          <h3 class="calc-title" style="color: var(--burgundy);">Calculate your floor</h3>
          <p class="calc-subtitle">Estimate material and optional services for this product.</p>
          <?php if($isFlooring): ?>
            <div class="calc-mode-toggle">
              <button type="button" class="calc-mode-btn active" data-calc-mode="dims">I know my room dimensions</button>
              <button type="button" class="calc-mode-btn" data-calc-mode="sqft">I already know my total sq ft</button>
            </div>

            <div class="calc-mode calc-mode-dims" data-mode="dims">
              <div class="calc-field-row">
                <label>
                  Length
                  <div class="calc-input-wrap">
                    <input type="number" id="calcLen" step="0.1" min="0" placeholder="e.g. 20">
                    <span class="calc-unit">ft</span>
                  </div>
                </label>
                <label>
                  Width
                  <div class="calc-input-wrap">
                    <input type="number" id="calcWid" step="0.1" min="0" placeholder="e.g. 12">
                    <span class="calc-unit">ft</span>
                  </div>
                </label>
              </div>
              <button type="button" class="calc-action" id="calcFromDims">Calculate area</button>
            </div>

            <div class="calc-mode calc-mode-sqft" data-mode="sqft" style="display:none;">
              <label class="full">
                Square footage (sqft)
                <div class="calc-input-wrap">
                  <input type="number" id="calcSqft" step="0.1" min="0" placeholder="e.g. 240">
                  <span class="calc-unit">sq ft</span>
                </div>
              </label>
            </div>
          <?php else: ?>
            <label class="full">
              <?= htmlspecialchars(ucfirst($unitLabel)) ?> needed
              <input type="number" id="calcUnits" step="0.1" min="0">
            </label>
            <label class="full">
              <?= htmlspecialchars(ucfirst($packageLabelPlural)) ?>
              <input type="number" id="calcBoxes" min="1" value="1" <?= $hasInventoryAvailable ? 'max="'.(int)$stockAvailableValue.'"' : '' ?>>
            </label>
            <div id="flooringHelper" class="calc-flooring-helper" aria-live="polite"></div>
          <?php endif; ?>

          <div class="calc-options">
            <h4>Optional services</h4>
            <div class="calc-option-cards">
              <div class="calc-option-card" data-checkbox="calcInstall" role="button" tabindex="0" aria-pressed="false">
                <input type="checkbox" id="calcInstall" class="calc-option-checkbox" aria-hidden="true">
                <div class="calc-option-content">
                  <div class="calc-option-title">Include installation estimate<?= $installRateLabel ? ' ('.$installRateLabel.')' : '' ?></div>
                  <div class="calc-option-desc">Get a ballpark for installation based on your area.</div>
                </div>
              </div>
              <?php if($isFlooring): ?>
                <div class="calc-option-card" data-checkbox="calcIncludeDelivery" role="button" tabindex="0" aria-pressed="false">
                  <input type="checkbox" id="calcIncludeDelivery" class="calc-option-checkbox" aria-hidden="true">
                  <div class="calc-option-content">
                    <div class="calc-option-title">Include delivery</div>
                    <div class="calc-option-desc">Select delivery zone and see the estimated fee.</div>
                  </div>
                </div>
              <?php endif; ?>
            </div>
            <?php if($isFlooring): ?>
              <div id="calcDeliveryWrap" class="calc-delivery" style="display:none;">
                <label>
                  Delivery zone
                  <select id="calcDelivery">
                    <?php foreach(($normalizedProduct['delivery']['zones'] ?? ($storeConfig['delivery']['zones'] ?? [])) as $zone): ?>
                      <option value="<?= htmlspecialchars($zone['id'] ?? '') ?>"><?= htmlspecialchars(($zone['label'] ?? 'Zone').' '.(isset($zone['fee']) ? ' — $'.number_format((float)$zone['fee'], 2) : '')) ?></option>
                    <?php endforeach; ?>
                  </select>
                </label>
              </div>
            <?php else: ?>
              <label class="full">
                Delivery / pick-up
                <select id="calcDelivery">
                  <?php foreach(($normalizedProduct['delivery']['zones'] ?? ($storeConfig['delivery']['zones'] ?? [])) as $zone): ?>
                    <option value="<?= htmlspecialchars($zone['id'] ?? '') ?>"><?= htmlspecialchars(($zone['label'] ?? 'Zone').' '.(isset($zone['fee']) ? ' — $'.number_format((float)$zone['fee'], 2) : '')) ?></option>
                  <?php endforeach; ?>
                </select>
              </label>
            <?php endif; ?>
          </div>

          <div class="calc-summary">
            <h4>Estimate summary</h4>
            <div class="calc-summary-line"><span>Area:</span><span id="calcSummaryArea">—</span></div>
            <div class="calc-summary-line"><span><?= htmlspecialchars(ucfirst($packageLabelPlural)) ?> needed:</span><span id="calcSummaryBoxes">—</span></div>
            <div class="calc-summary-line"><span>Condition:</span><span id="calcSummaryCondition">—</span></div>
            <div class="calc-summary-line"><span>Material:</span><span id="calcSummaryMaterial">—</span></div>
            <div class="calc-summary-line"><span>Installation:</span><span id="calcSummaryInstall">—</span></div>
            <div class="calc-summary-line"><span>Delivery:</span><span id="calcSummaryDelivery">—</span></div>
            <div class="calc-summary-total"><span>Estimated total:</span><span id="calcSummaryTotal">—</span></div>
            <p class="calc-note" id="calcSummaryNote">This is an estimate. Final quote may adjust based on project details and promotions.</p>
            <p id="calcAlert" class="calc-note calc-alert"></p>
          </div>

          <button type="button" id="addToCart" class="btn btn-primary full" style="margin-top:10px;">Add to cart</button>
        </div>
      </div>
    </div>

    <div class="tabs">
      <button class="tab-btn active" data-target="overview">Overview</button>
      <button class="tab-btn" data-target="specs">Specifications</button>
    </div>
    <div id="overview" class="tab-content active">
      <?php if(!empty($product['short_desc'])): ?>
        <ul>
          <?php foreach (explode(';', $product['short_desc']) as $b): if(trim($b)): ?>
            <li><?= htmlspecialchars(trim($b)) ?></li>
          <?php endif; endforeach; ?>
        </ul>
      <?php endif; ?>
      <?php if(!empty($product['long_desc'])): ?>
        <p><?= htmlspecialchars($product['long_desc']) ?></p>
      <?php endif; ?>
    </div>
    <div id="specs" class="tab-content">
      <ul>
        <?php if($isFlooring): ?>
          <?php if($product['thickness_mm']): ?><li><strong>Thickness:</strong> <?= $product['thickness_mm'] ?> mm</li><?php endif; ?>
          <?php if($product['wear_layer_mil']): ?><li><strong>Wear layer:</strong> <?= $product['wear_layer_mil'] ?> mil</li><?php endif; ?>
          <?php if($product['width_in'] && $product['length_in']): ?><li><strong>Plank size:</strong> <?= $product['width_in'] ?>×<?= $product['length_in'] ?> in</li><?php endif; ?>
          <?php if($product['core']): ?><li><strong>Core:</strong> <?= htmlspecialchars($product['core']) ?></li><?php endif; ?>
          <?php if($product['pad']): ?><li><strong>Pad:</strong> <?= htmlspecialchars($product['pad']) ?> <?= htmlspecialchars($product['pad_material'] ?? '') ?></li><?php endif; ?>
          <?php if($product['installation']): ?><li><strong>Installation:</strong> <?= htmlspecialchars($product['installation']) ?></li><?php endif; ?>
          <?php if($product['waterproof']): ?><li><strong>Waterproof:</strong> <?= htmlspecialchars($product['waterproof']) ?></li><?php endif; ?>
          <?php if($product['scratch_resistant']): ?><li><strong>Scratch resistant:</strong> <?= htmlspecialchars($product['scratch_resistant']) ?></li><?php endif; ?>
        <?php else: ?>
          <?php if($product['category']): ?><li><strong>Category:</strong> <?= htmlspecialchars($product['category']) ?></li><?php endif; ?>
          <?php if($product['nominal_size']): ?><li><strong>Nominal size:</strong> <?= htmlspecialchars($product['nominal_size']) ?></li><?php endif; ?>
          <?php if($product['actual_size']): ?><li><strong>Actual size:</strong> <?= htmlspecialchars($product['actual_size']) ?></li><?php endif; ?>
          <?php if($product['length_ft']): ?><li><strong>Length per piece:</strong> <?= $product['length_ft'] ?> ft</li><?php endif; ?>
          <?php if($product['pieces_per_box']): ?><li><strong>Pieces per box:</strong> <?= $product['pieces_per_box'] ?></li><?php endif; ?>
          <?php if($coveragePerBoxValue): ?><li><strong>Coverage per <?= htmlspecialchars($packageLabelSingular) ?>:</strong> <?= $coveragePerBoxLabel ?></li><?php endif; ?>
          <?php if($product['packaging_notes']): ?><li><strong>Packaging:</strong> <?= htmlspecialchars($product['packaging_notes']) ?></li><?php endif; ?>
          <?php if($product['comments']): ?><li><strong>Comments:</strong> <?= htmlspecialchars($product['comments']) ?></li><?php endif; ?>
        <?php endif; ?>
      </ul>
    </div>
    <div class="hero-cta" style="margin-top:1rem;">
      <a href="#contact" class="btn btn-primary">Get install Quote</a>
    </div>
  </main>

<?php include $base.'includes/contact.php'; ?>
<?php include $base.'includes/footer.php'; ?>
  <script>
    const burger = document.getElementById('burger');
    const menu = document.getElementById('menu');
    burger?.addEventListener('click', () => {
      const open = menu.classList.toggle('show');
      burger.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    const imgs = document.querySelectorAll('.slider img');
    let idx = 0;
    function showSlide(n){
      if(!imgs.length) return;
      imgs[idx].classList.remove('active');
      idx = (n + imgs.length) % imgs.length;
      imgs[idx].classList.add('active');
    }
    document.querySelector('.next')?.addEventListener('click', ()=>showSlide(idx+1));
    document.querySelector('.prev')?.addEventListener('click', ()=>showSlide(idx-1));
    document.querySelectorAll('.tab-btn').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c=>c.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById(btn.dataset.target).classList.add('active');
      });
    });
    const SKU = <?= json_encode($product['sku']) ?>;
    const PRODUCT_TYPE = <?= json_encode($productType) ?>;
    const MEASUREMENT_UNIT = <?= json_encode($measurementUnit) ?>;
    const UNIT_LABEL = <?= json_encode($unitLabel) ?>;
    const PACKAGE_LABEL = <?= json_encode($packageLabelSingular) ?>;
    const PACKAGE_LABEL_PLURAL = <?= json_encode($packageLabelPlural) ?>;
    const COVERAGE_PER_PACKAGE = <?= json_encode($coveragePerBoxValue) ?>;
    const NORMALIZED_PRODUCT = <?= json_encode($normalizedProduct) ?>;
    const STORE_CONFIG = <?= json_encode($storeConfig) ?>;
    const FLOORING_PRODUCTS = PRODUCT_TYPE === 'molding' ? <?= json_encode($flooringProducts) ?> : [];
    const PRICE_MODES = <?= json_encode($priceModesData) ?>;
    const PRICE_MODE_KEYS = Object.keys(PRICE_MODES);
    const MAX_PURCHASE_QTY = Number(NORMALIZED_PRODUCT?.availability?.maxPurchaseQuantity ?? null);
    const STOCK_AVAILABLE = Number(NORMALIZED_PRODUCT?.availability?.stockAvailable ?? null);
    const DELIVERY_PREF_KEY = 'bs_delivery_pref';
    const IS_STOCK_MODE = (NORMALIZED_PRODUCT?.availability?.mode || '').toLowerCase() === 'stock' && Number.isFinite(STOCK_AVAILABLE) && STOCK_AVAILABLE > 0;
    let currentPriceMode = <?= json_encode($defaultPriceMode) ?>;
    if(!PRICE_MODES[currentPriceMode]){
      currentPriceMode = PRICE_MODE_KEYS.length ? PRICE_MODE_KEYS[0] : 'stock';
    }
    const LENGTH_FT = <?= json_encode($product['length_ft'] ?? null) ?>;
    const PIECES_PER_BOX = <?= json_encode($product['pieces_per_box'] ?? null) ?>;
    function getActivePriceMode(){
      return PRICE_MODES[currentPriceMode] || {};
    }
    function getDeliveryZones(){
      return NORMALIZED_PRODUCT?.delivery?.zones || STORE_CONFIG?.delivery?.zones || [];
    }
    function getDefaultDeliveryZone(){
      const zones = getDeliveryZones();
      return zones[0]?.id || null;
    }
    function loadDeliveryPreferences(){
      try{
        const saved = JSON.parse(localStorage.getItem(DELIVERY_PREF_KEY));
        const zones = getDeliveryZones();
        const zone = zones.some(z=>z.id === saved?.zone) ? saved.zone : getDefaultDeliveryZone();
        return {includeDelivery: saved?.includeDelivery !== false, zone};
      }catch(err){
        return {includeDelivery: true, zone: getDefaultDeliveryZone()};
      }
    }
    function saveDeliveryPreferences(prefs){
      localStorage.setItem(DELIVERY_PREF_KEY, JSON.stringify(prefs));
    }
    let deliveryPreferences = loadDeliveryPreferences();
    function formatUnits(value){
      const num = Number(value);
      if(!Number.isFinite(num)) return '';
      if(Math.abs(num) >= 1000 && Number.isInteger(num)){
        return num.toLocaleString();
      }
      return num.toLocaleString(undefined, {maximumFractionDigits: 2});
    }
    function formatInputValue(value){
      const num = Number(value);
      if(!Number.isFinite(num) || num <= 0) return '';
      const rounded = Math.round(num * 100) / 100;
      return Number.isInteger(rounded) ? String(rounded) : rounded.toString();
    }
    const FLOORING_MAP = new Map((FLOORING_PRODUCTS || []).map(p=>[p.sku, p]));
    function buildFlooringHelperData(){
      if(PRODUCT_TYPE !== 'molding') return [];
      return cart.getItems().map(item=>{
        const product = FLOORING_MAP.get(item.sku);
        if(!product) return null;
        const coverage = Number(product.packageCoverage);
        const qty = Number(item.quantity);
        if(!Number.isFinite(coverage) || coverage <= 0 || !Number.isFinite(qty) || qty <= 0) return null;
        const sqft = coverage * qty;
        const suggestedLf = Math.round(sqft * 0.3 * 100) / 100;
        return {product, sqft, suggestedLf, quantity: qty};
      }).filter(Boolean);
    }
    function renderFlooringHelper(){
      const helper = document.getElementById('flooringHelper');
      if(!helper || PRODUCT_TYPE !== 'molding') return;
      const data = buildFlooringHelperData();
      if(!data.length){
        helper.innerHTML = '<div class="calc-helper-empty">Agrega un piso al carrito para calcular moldings al 30%.</div>';
        return;
      }
      const itemsHtml = data.map(d=>{
        const lfText = Number.isFinite(d.suggestedLf) ? `${formatUnits(d.suggestedLf)} lf` : '—';
        const sqftText = `${formatUnits(d.sqft)} sqft`;
        const boxesText = `${formatUnits(d.quantity)} ${PACKAGE_LABEL_PLURAL || 'boxes'}`;
        const lfValue = Number.isFinite(d.suggestedLf) && d.suggestedLf > 0 ? d.suggestedLf : '';
        const disabled = lfValue === '' ? 'disabled' : '';
        return `<button type="button" class="calc-helper-item" data-lf="${lfValue}" ${disabled}><div><div class="calc-helper-name">${d.product.name}</div><div class="calc-helper-meta">${boxesText} · ${sqftText} · 30% = ${lfText}</div></div><span class="calc-helper-action">Usar</span></button>`;
      }).join('');
      helper.innerHTML = `<div class="calc-helper-header"><div class="calc-helper-title">Vincula tus pisos</div><div class="calc-helper-sub">Selecciona un piso y rellenamos automáticamente el 30% en lf.</div></div><div class="calc-helper-list">${itemsHtml}</div>`;
      helper.querySelectorAll('.calc-helper-item').forEach(btn=>{
        if(btn.disabled) return;
        btn.addEventListener('click', ()=>{
          const lf = Number(btn.dataset.lf);
          if(!Number.isFinite(lf) || lf <= 0) return;
          const unitsInput = document.getElementById('calcUnits');
          if(unitsInput){
            unitsInput.value = formatInputValue(lf) || '';
            unitsInput.dispatchEvent(new Event('input', {bubbles:true}));
            updateCalc('calcUnits');
          }
        });
      });
    }
      let calcMode = 'dims';
      let lastComputedBoxes = 0;
    function syncDeliveryControls(){
      const deliverySelect = document.getElementById('calcDelivery');
      const deliveryToggle = document.getElementById('calcIncludeDelivery');
      const zones = getDeliveryZones();
      const preferredZone = zones.some(z=>z.id === deliveryPreferences.zone) ? deliveryPreferences.zone : getDefaultDeliveryZone();
      deliveryPreferences = {...deliveryPreferences, zone: preferredZone};
      if(deliverySelect){
        deliverySelect.value = preferredZone || '';
      }
      if(deliveryToggle){
        deliveryToggle.checked = deliveryPreferences.includeDelivery !== false;
      }
      updateDeliveryVisibility();
    }
    function updateDeliveryVisibility(){
      const deliveryWrap = document.getElementById('calcDeliveryWrap');
      const deliveryToggle = document.getElementById('calcIncludeDelivery');
      if(deliveryWrap){
        deliveryWrap.style.display = deliveryToggle?.checked ? '' : 'none';
      }
    }
    function updateCalc(source){
      const boxesInput = document.getElementById('calcBoxes');
      const sqftInput = document.getElementById('calcSqft');
      const lenInput = document.getElementById('calcLen');
      const widInput = document.getElementById('calcWid');
      const unitsInputEl = document.getElementById('calcUnits');
      const deliveryToggle = document.getElementById('calcIncludeDelivery');
      const deliverySelect = document.getElementById('calcDelivery');
      const alertEl = document.getElementById('calcAlert');
      let boxes = boxesInput ? parseInt(boxesInput.value, 10) : 0;
      let requestedBoxes = boxes;
      if(!Number.isFinite(boxes) || boxes < 0){
        boxes = 0;
        requestedBoxes = 0;
      }
      if(boxesInput && source === 'calcBoxes'){
        boxes = Math.max(1, Math.round(boxes));
        boxesInput.value = boxes > 0 ? boxes : '';
        requestedBoxes = boxes;
      }
      const maxQty = Number.isFinite(MAX_PURCHASE_QTY) && MAX_PURCHASE_QTY > 0 ? Math.floor(MAX_PURCHASE_QTY) : null;
      const activePrice = getActivePriceMode();
      const pricePerUnitNum = Number(activePrice.unitValue);
      const lengthPerPiece = Number(LENGTH_FT) || 0;
      const piecesPerBox = Number(PIECES_PER_BOX) || 0;
      let coveragePerPackage = Number(COVERAGE_PER_PACKAGE);
      if(PRODUCT_TYPE === 'molding'){
        coveragePerPackage = lengthPerPiece > 0 ? lengthPerPiece : (Number.isFinite(coveragePerPackage) && coveragePerPackage > 0 ? coveragePerPackage : 0);
      }else if(!Number.isFinite(coveragePerPackage) || coveragePerPackage <= 0){
        if(lengthPerPiece > 0 && piecesPerBox > 0){
          coveragePerPackage = lengthPerPiece * piecesPerBox;
        }else{
          coveragePerPackage = 0;
        }
      }
      let unitsNeeded = null;
      if(PRODUCT_TYPE === 'flooring'){
        let sqft = 0;
        const manualSqft = parseFloat(sqftInput?.value);
        if(calcMode === 'sqft'){
          if(Number.isFinite(manualSqft) && manualSqft > 0){
            sqft = manualSqft;
          }
        }else{
          const len = parseFloat(lenInput?.value);
          const wid = parseFloat(widInput?.value);
          if(Number.isFinite(len) && len > 0 && Number.isFinite(wid) && wid > 0){
            sqft = len * wid;
            if(sqftInput){
              const formatted = formatInputValue(sqft);
              sqftInput.value = formatted !== '' ? formatted : '';
            }
          }else if(Number.isFinite(manualSqft) && manualSqft > 0){
            sqft = manualSqft;
          }
        }
        if(coveragePerPackage > 0 && sqft > 0){
          boxes = Math.max(1, Math.ceil(sqft / coveragePerPackage));
        }
        unitsNeeded = sqft > 0 ? sqft : null;
      }else{
        const unitsInput = parseFloat(unitsInputEl?.value);
        if(Number.isFinite(unitsInput) && unitsInput > 0){
          if(lengthPerPiece > 0){
            boxes = Math.max(1, Math.ceil(unitsInput / lengthPerPiece));
          }else if(coveragePerPackage > 0){
            boxes = Math.max(1, Math.ceil(unitsInput / coveragePerPackage));
          }
          requestedBoxes = boxes;
          if(boxesInput && boxes > 0) boxesInput.value = boxes;
          unitsNeeded = unitsInput;
        }else if(boxes > 0){
          if(lengthPerPiece > 0){
            unitsNeeded = boxes * lengthPerPiece;
          }else if(coveragePerPackage > 0){
            unitsNeeded = boxes * coveragePerPackage;
          }
        }
      }
      if(unitsNeeded === null && boxes > 0 && coveragePerPackage > 0){
        unitsNeeded = boxes * coveragePerPackage;
      }
      if(boxes > 0){
        requestedBoxes = boxes;
      }
      if(maxQty && boxes > maxQty){
        boxes = maxQty;
        if(PRODUCT_TYPE === 'flooring' && coveragePerPackage > 0){
          const cappedUnits = boxes * coveragePerPackage;
          if(sqftInput){
            const formattedSqft = formatInputValue(cappedUnits);
            sqftInput.value = formattedSqft || '';
          }
          unitsNeeded = cappedUnits;
        }
        if(boxesInput){
          boxesInput.value = boxes;
        }
      }
      let pricePerPackage = Number(activePrice.packageValue);
      if(!Number.isFinite(pricePerPackage) || pricePerPackage <= 0){
        const pricePerUnit = Number.isFinite(pricePerUnitNum) ? pricePerUnitNum : 0;
        pricePerPackage = coveragePerPackage > 0 && pricePerUnit > 0 ? coveragePerPackage * pricePerUnit : 0;
      }
      const totalPrice = boxes > 0 && pricePerPackage > 0 ? boxes * pricePerPackage : 0;
      const installSelected = document.getElementById('calcInstall')?.checked;
      const deliveryEnabled = PRODUCT_TYPE === 'flooring' ? (deliveryToggle?.checked ?? false) : true;
      const deliveryZone = deliveryEnabled ? document.getElementById('calcDelivery')?.value : null;
      let installTotal = 0;
      const installRate = PRODUCT_TYPE === 'molding'
        ? (NORMALIZED_PRODUCT?.services?.installRate ?? STORE_CONFIG?.install?.defaultMoldingRate)
        : (NORMALIZED_PRODUCT?.services?.installRate ?? STORE_CONFIG?.install?.defaultFlooringRate);
      if(installSelected){
        const unitsForInstall = Number(unitsNeeded || (boxes > 0 && coveragePerPackage > 0 ? boxes * coveragePerPackage : 0));
        if(Number.isFinite(installRate) && Number.isFinite(unitsForInstall)){
          installTotal = installRate * unitsForInstall;
        }
      }
      let deliveryTotal = 0;
      if(deliveryZone){
        const zones = NORMALIZED_PRODUCT?.delivery?.zones || STORE_CONFIG?.delivery?.zones || [];
        const zone = zones.find(z=>z.id === deliveryZone);
        if(zone && Number.isFinite(Number(zone.fee))){
          deliveryTotal = Number(zone.fee);
        }
      }
      const grandTotal = totalPrice + installTotal + deliveryTotal;
      const areaText = unitsNeeded ? `${formatUnits(unitsNeeded)} ${UNIT_LABEL}` : '—';
      document.getElementById('calcSummaryArea').textContent = areaText;
      document.getElementById('calcSummaryBoxes').textContent = boxes > 0 ? formatUnits(boxes) : '—';
      const priceModeLabel = activePrice.label || (currentPriceMode === 'backorder' ? 'Backorder' : 'In stock');
      document.getElementById('calcSummaryCondition').textContent = priceModeLabel || '—';
      document.getElementById('calcSummaryMaterial').textContent = totalPrice > 0 ? `$${totalPrice.toFixed(2)}` : '—';
      document.getElementById('calcSummaryInstall').textContent = installTotal > 0 ? `$${installTotal.toFixed(2)}` : '—';
      document.getElementById('calcSummaryDelivery').textContent = deliveryTotal > 0 ? `$${deliveryTotal.toFixed(2)}` : (deliveryEnabled ? '$0.00' : '—');
      document.getElementById('calcSummaryTotal').textContent = grandTotal > 0 ? `$${grandTotal.toFixed(2)}` : '—';
      const exceededStock = IS_STOCK_MODE && Number.isFinite(STOCK_AVAILABLE) && Number.isFinite(requestedBoxes) && requestedBoxes > STOCK_AVAILABLE;
      if(alertEl){
        if(exceededStock){
          const pkgLabel = STOCK_AVAILABLE === 1 ? (PACKAGE_LABEL || 'box') : (PACKAGE_LABEL_PLURAL || ((PACKAGE_LABEL || 'box') + 'es'));
          const coverageText = coveragePerPackage > 0 ? ` (≈ ${formatUnits(STOCK_AVAILABLE * coveragePerPackage)} ${UNIT_LABEL})` : '';
          alertEl.textContent = `Requested quantity exceeds available stock. ${formatUnits(STOCK_AVAILABLE)} ${pkgLabel} in stock${coverageText}.`;
        }else{
          alertEl.textContent = '';
        }
      }
      lastComputedBoxes = boxes;
      return boxes;
    }
    function bindOptionCards(){
      document.querySelectorAll('.calc-option-card').forEach(card=>{
        const checkboxId = card.dataset.checkbox;
        const checkbox = checkboxId ? document.getElementById(checkboxId) : null;
        if(!checkbox) return;
        const syncState = ()=>{
          const checked = checkbox.checked;
          card.classList.toggle('selected', checked);
          card.setAttribute('aria-pressed', checked ? 'true' : 'false');
        };
        card.addEventListener('click', (e)=>{
          if(e.target === checkbox) return;
          checkbox.checked = !checkbox.checked;
          checkbox.dispatchEvent(new Event('change', {bubbles:true}));
        });
        card.addEventListener('keydown', (e)=>{
          if(e.key === ' ' || e.key === 'Enter'){
            e.preventDefault();
            checkbox.checked = !checkbox.checked;
            checkbox.dispatchEvent(new Event('change', {bubbles:true}));
          }
        });
        checkbox.addEventListener('change', syncState);
        syncState();
      });
    }
    document.querySelectorAll('[data-calc-mode]').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        const mode = btn.dataset.calcMode;
        if(!mode) return;
        calcMode = mode;
        document.querySelectorAll('.calc-mode-btn').forEach(el=>el.classList.toggle('active', el === btn));
        document.querySelectorAll('.calc-mode').forEach(el=>{
          el.style.display = el.dataset.mode === mode ? '' : 'none';
        });
        updateCalc();
      });
    });
    document.getElementById('calcFromDims')?.addEventListener('click', ()=>updateCalc('calcFromDims'));
    ['calcLen','calcWid','calcSqft','calcUnits','calcBoxes'].forEach(id=>{
      const el = document.getElementById(id);
      if(el){
        el.addEventListener('input', ()=>updateCalc(id));
        if(id === 'calcBoxes' && Number.isFinite(MAX_PURCHASE_QTY) && MAX_PURCHASE_QTY > 0){
          el.max = Math.floor(MAX_PURCHASE_QTY);
        }
      }
    });
    document.getElementById('calcInstall')?.addEventListener('change', ()=>updateCalc());
    document.getElementById('calcIncludeDelivery')?.addEventListener('change', (evt)=>{
      deliveryPreferences = {...deliveryPreferences, includeDelivery: evt.target.checked};
      if(!evt.target.checked){
        const pickup = getDeliveryZones().find(z=>z.id === 'pick-up');
        if(pickup){
          deliveryPreferences = {...deliveryPreferences, zone: pickup.id};
        }
      }
      saveDeliveryPreferences(deliveryPreferences);
      updateDeliveryVisibility();
      updateCalc();
    });
    document.getElementById('calcDelivery')?.addEventListener('change', (evt)=>{
      deliveryPreferences = {...deliveryPreferences, zone: evt.target.value};
      saveDeliveryPreferences(deliveryPreferences);
      updateCalc();
    });
    bindOptionCards();
    document.querySelectorAll('input[name="price_mode"]').forEach(input=>{
      input.addEventListener('change', ()=>{
        if(input.checked){
          currentPriceMode = input.value;
          updateCalc();
        }
      });
    });
    document.getElementById('addToCart')?.addEventListener('click', ()=>{
      const boxes = updateCalc();
      if(boxes>0){
        const selectedMode = document.querySelector('input[name="price_mode"]:checked');
        const priceType = selectedMode ? selectedMode.value : currentPriceMode;
        const installSelected = document.getElementById('calcInstall')?.checked;
        const currentDeliveryZone = document.getElementById('calcDelivery')?.value || null;
        deliveryPreferences = {
          includeDelivery: PRODUCT_TYPE !== 'flooring' || (document.getElementById('calcIncludeDelivery')?.checked ?? true),
          zone: currentDeliveryZone || deliveryPreferences.zone || getDefaultDeliveryZone()
        };
        saveDeliveryPreferences(deliveryPreferences);
        cart.addItem(SKU, boxes, priceType || 'stock', {install: installSelected});
        if(PRODUCT_TYPE === 'flooring'){
          const goToMolding = window.confirm('¿Quieres añadir moldings para este piso? Te llevaremos a la sección de moldings.');
          if(goToMolding){
            window.location.href = 'index.php?type=molding';
          }
        }
      }
    });
    syncDeliveryControls();
    updateCalc();
    if(PRODUCT_TYPE === 'molding'){
      renderFlooringHelper();
      document.addEventListener('cartchange', renderFlooringHelper);
    }
    function trackEvent(name, params){
      if (window.gtag) gtag('event', name, params || {});
      window.dataLayer && window.dataLayer.push({event: name, ...params});
    }
    function bindForm(fid, sendBtnId, statusId, formName){
      const form = document.getElementById(fid);
      const status = document.getElementById(statusId);
      const sendBtn = document.getElementById(sendBtnId);
      form?.addEventListener('submit', async (e)=>{
        e.preventDefault();
        const originalBtnText = sendBtn ? sendBtn.textContent : '';
        trackEvent('lead_submit', {form_name: formName});
        status?.classList.remove('hide');
        if(status) status.textContent = 'Sending your request…';
        const formData = new FormData(form);
        Array.from(form.elements).forEach(el=>el.disabled=true);
        if(sendBtn) sendBtn.textContent = 'Sending…';
        try{
          const res = await fetch(form.action || '<?=$base?>lead.php', {method:'POST', body:formData});
          const data = await res.json();
          if(status) status.textContent = data.data || 'Request sent.';
          if(res.ok && data.code === '01') form.reset();
        }catch(err){
          if(status) status.textContent = 'An error occurred. Please try again later.';
        }finally{
          if(form) Array.from(form.elements).forEach(el=>el.disabled=false);
          if(sendBtn) sendBtn.textContent = originalBtnText;
        }
      });
    }
    bindForm('lead-form-bottom','send-btn-bottom','form-status-bottom','B&S – Web Lead (bottom)');
    document.getElementById('year').textContent = new Date().getFullYear();
  </script>
</body>
</html>
