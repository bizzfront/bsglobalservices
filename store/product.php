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
$productType = $product['product_type'] ?? 'flooring';
$isFlooring = $productType === 'flooring';
$measurementUnit = strtolower($product['measurement_unit'] ?? ($isFlooring ? 'sqft' : 'lf'));
$unitLabels = [
  'sqft' => 'sqft',
  'lf' => 'linear feet',
  'piece' => 'pieces'
];
$packageLabelSingular = $product['package_label'] ?? ($isFlooring ? 'box' : 'package');
$packageLabelPlural = $product['package_label_plural'] ?? ($isFlooring ? 'boxes' : 'packages');
$unitLabel = $unitLabels[$measurementUnit] ?? $measurementUnit;
$unitSuffix = $measurementUnit === 'lf' ? '/lf' : ($measurementUnit === 'piece' ? '/piece' : '/sqft');
$coveragePerBoxValue = $product['computed_coverage_per_package'] ?? $product['coverage_per_box'] ?? $product['sqft_per_box'] ?? null;
$lengthFtValue = isset($product['length_ft']) ? (float)$product['length_ft'] : null;
$piecesPerBoxValue = isset($product['pieces_per_box']) ? (float)$product['pieces_per_box'] : null;
if($coveragePerBoxValue === null && $lengthFtValue && $piecesPerBoxValue){
  $coveragePerBoxValue = $lengthFtValue * $piecesPerBoxValue;
}
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
$defaultPriceMode = isset($priceModes['stock']) ? 'stock' : (isset($priceModes['backorder']) ? 'backorder' : (array_key_first($priceModes) ?: 'stock'));
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($product['name']) ?> — B&S Floor Supply</title>
  <link rel="stylesheet" href="<?=$base?>style.css" />
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

        <div id="calc" class="calc">
          <h3 style="color: var(--burgundy);">Material calculator</h3>
          <form id="calcForm" class="form">
            <?php if($isFlooring): ?>
              <label>
                Length (ft)
                <input type="number" id="calcLen" step="0.1" min="0">
              </label>
              <label>
                Width (ft)
                <input type="number" id="calcWid" step="0.1" min="0">
              </label>
              <label class="full">
                Square footage (sqft)
                <input type="number" id="calcSqft" step="0.1" min="0">
              </label>
              <div class="full or">or</div>
            <?php else: ?>
              <label class="full">
                <?= htmlspecialchars(ucfirst($unitLabel)) ?> needed
                <input type="number" id="calcUnits" step="0.1" min="0">
              </label>
              <div class="full or">or</div>
            <?php endif; ?>
            <label class="full">
              <?= htmlspecialchars(ucfirst($packageLabelPlural)) ?>
              <input type="number" id="calcBoxes" min="1" value="1">
            </label>
            <div class="full" style="display:flex; align-items:center; gap:10px; margin-top:6px;">
              <input type="checkbox" id="calcInstall"> <span>Add installation (uses project rates)</span>
            </div>
            <label class="full">
              Delivery / pick-up
              <select id="calcDelivery">
                <?php foreach(($normalizedProduct['delivery']['zones'] ?? ($storeConfig['delivery']['zones'] ?? [])) as $zone): ?>
                  <option value="<?= htmlspecialchars($zone['id'] ?? '') ?>"><?= htmlspecialchars(($zone['label'] ?? 'Zone').' '.(isset($zone['fee']) ? ' — $'.number_format((float)$zone['fee'], 2) : '')) ?></option>
                <?php endforeach; ?>
              </select>
            </label>
            <p id="calcSummary" class="note full"></p>
            <button type="button" id="addToCart" class="btn btn-primary full">Add to cart</button>
          </form>
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
          <?php if($coveragePerBoxValue): ?><li><strong>Coverage per box:</strong> <?= $coveragePerBoxLabel ?></li><?php endif; ?>
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
    const PRICE_MODES = <?= json_encode($priceModesData) ?>;
    const PRICE_MODE_KEYS = Object.keys(PRICE_MODES);
    let currentPriceMode = <?= json_encode($defaultPriceMode) ?>;
    if(!PRICE_MODES[currentPriceMode]){
      currentPriceMode = PRICE_MODE_KEYS.length ? PRICE_MODE_KEYS[0] : 'stock';
    }
    const LENGTH_FT = <?= json_encode($product['length_ft'] ?? null) ?>;
    const PIECES_PER_BOX = <?= json_encode($product['pieces_per_box'] ?? null) ?>;
    function getActivePriceMode(){
      return PRICE_MODES[currentPriceMode] || {};
    }
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
    function updateCalc(source){
      const boxesInput = document.getElementById('calcBoxes');
      const sqftInput = document.getElementById('calcSqft');
      const lenInput = document.getElementById('calcLen');
      const widInput = document.getElementById('calcWid');
      const unitsInputEl = document.getElementById('calcUnits');
      let boxes = parseInt(boxesInput?.value, 10);
      if(!Number.isFinite(boxes) || boxes < 0){
        boxes = 0;
      }
      if(source === 'calcBoxes'){
        boxes = Math.max(1, Math.round(boxes));
        if(boxesInput) boxesInput.value = boxes > 0 ? boxes : '';
      }
      const activePrice = getActivePriceMode();
      const pricePerUnitNum = Number(activePrice.unitValue);
      const lengthPerPiece = Number(LENGTH_FT) || 0;
      const piecesPerBox = Number(PIECES_PER_BOX) || 0;
      let coveragePerPackage = Number(COVERAGE_PER_PACKAGE);
      if(!Number.isFinite(coveragePerPackage) || coveragePerPackage <= 0){
        if(lengthPerPiece > 0 && piecesPerBox > 0){
          coveragePerPackage = lengthPerPiece * piecesPerBox;
        }else{
          coveragePerPackage = 0;
        }
      }
      let unitsNeeded = null;
      if(PRODUCT_TYPE === 'flooring'){
        const len = parseFloat(lenInput?.value);
        const wid = parseFloat(widInput?.value);
        const manualSqft = parseFloat(sqftInput?.value);
        let sqft = Number.isFinite(manualSqft) && manualSqft > 0 ? manualSqft : 0;
        if(Number.isFinite(len) && len > 0 && Number.isFinite(wid) && wid > 0){
          sqft = len * wid;
          if(sqftInput){
            const formatted = formatInputValue(sqft);
            if(formatted !== '') sqftInput.value = formatted; else sqftInput.value = '';
          }
        }else if(source === 'calcBoxes' && coveragePerPackage > 0 && boxes > 0){
          sqft = boxes * coveragePerPackage;
          if(sqftInput){
            const formatted = formatInputValue(sqft);
            sqftInput.value = formatted || '';
          }
        }else if(Number.isFinite(manualSqft) && manualSqft > 0){
          sqft = manualSqft;
        }else if(sqftInput && source !== 'calcSqft' && source !== 'calcLen' && source !== 'calcWid'){
          sqftInput.value = '';
        }
        if(coveragePerPackage > 0){
          if(source === 'calcBoxes'){
            if(boxes > 0){
              const sqftFromBoxes = boxes * coveragePerPackage;
              if(sqftInput){
                const formattedSqft = formatInputValue(sqftFromBoxes);
                sqftInput.value = formattedSqft || '';
              }
              sqft = sqftFromBoxes;
            }
          }else if(sqft > 0){
            boxes = Math.max(1, Math.ceil(sqft / coveragePerPackage));
            if(boxesInput) boxesInput.value = boxes;
          }else if(boxes > 0){
            const sqftFromBoxes = boxes * coveragePerPackage;
            if(sqftInput){
              const formattedSqft = formatInputValue(sqftFromBoxes);
              sqftInput.value = formattedSqft || '';
            }
            sqft = sqftFromBoxes;
          }
        }
        unitsNeeded = sqft > 0 ? sqft : null;
      }else{
        const unitsInput = parseFloat(unitsInputEl?.value);
        if(Number.isFinite(unitsInput) && unitsInput > 0){
          const piecesNeeded = lengthPerPiece > 0 ? unitsInput / lengthPerPiece : null;
          if(piecesPerBox > 0 && piecesNeeded){
            boxes = Math.max(1, Math.ceil(piecesNeeded / piecesPerBox));
          }else if(coveragePerPackage > 0){
            boxes = Math.max(1, Math.ceil(unitsInput / coveragePerPackage));
          }
          if(boxesInput && boxes > 0) boxesInput.value = boxes;
          unitsNeeded = unitsInput;
        }else if(boxes > 0){
          if(piecesPerBox > 0 && lengthPerPiece > 0){
            unitsNeeded = boxes * piecesPerBox * lengthPerPiece;
          }else if(coveragePerPackage > 0){
            unitsNeeded = boxes * coveragePerPackage;
          }
        }
      }
      if(unitsNeeded === null && boxes > 0 && coveragePerPackage > 0){
        unitsNeeded = boxes * coveragePerPackage;
      }
      let pricePerPackage = Number(activePrice.packageValue);
      if(!Number.isFinite(pricePerPackage) || pricePerPackage <= 0){
        const pricePerUnit = Number.isFinite(pricePerUnitNum) ? pricePerUnitNum : 0;
        pricePerPackage = coveragePerPackage > 0 && pricePerUnit > 0 ? coveragePerPackage * pricePerUnit : 0;
      }
      const totalPrice = boxes > 0 && pricePerPackage > 0 ? boxes * pricePerPackage : 0;
      const installSelected = document.getElementById('calcInstall')?.checked;
      const deliveryZone = document.getElementById('calcDelivery')?.value;
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
      const summaryParts = [];
      if(boxes > 0){
        const pkgLabel = boxes === 1 ? (PACKAGE_LABEL || 'box') : (PACKAGE_LABEL_PLURAL || ((PACKAGE_LABEL || 'box') + 'es'));
        summaryParts.push(`${boxes} ${pkgLabel}`);
      }
      if(unitsNeeded){
        summaryParts.push(`${formatUnits(unitsNeeded)} ${UNIT_LABEL}`);
      }
      const priceModeLabel = activePrice.label || (currentPriceMode === 'backorder' ? 'Backorder' : 'In stock');
      if(priceModeLabel && PRICE_MODE_KEYS.length > 1){
        summaryParts.push(priceModeLabel);
      }
      if(totalPrice > 0){
        summaryParts.push(`$${totalPrice.toFixed(2)}`);
      }
      if(installTotal > 0){
        summaryParts.push(`Install $${installTotal.toFixed(2)}`);
      }
      if(deliveryZone){
        summaryParts.push(`Delivery $${deliveryTotal.toFixed(2)}`);
      }
      if(grandTotal > totalPrice){
        summaryParts.push(`Est. total $${grandTotal.toFixed(2)}`);
      }
      document.getElementById('calcSummary').textContent = summaryParts.join(' — ');
      return boxes;
    }
    ['calcLen','calcWid','calcSqft','calcBoxes','calcUnits'].forEach(id=>{
      const el = document.getElementById(id);
      if(el) el.addEventListener('input', ()=>updateCalc(id));
    });
    document.getElementById('calcInstall')?.addEventListener('change', ()=>updateCalc());
    document.getElementById('calcDelivery')?.addEventListener('change', ()=>updateCalc());
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
        const deliveryZone = document.getElementById('calcDelivery')?.value || null;
        cart.addItem(SKU, boxes, priceType || 'stock', {install: installSelected, deliveryZone});
      }
    });
    updateCalc();
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
