<?php
require_once __DIR__.'/utils.php';

$products = array_map('normalize_store_product', load_store_products());
$storeConfig = load_store_config();
$base = '../';
$active = 'cart';
$contact_source = 'website_store';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Project cart — B&S Floor Supply</title>
  <link rel="stylesheet" href="<?=$base?>style.css" />
  <style>
    .cart-shell { background:#f6f2ec; }
    .cart-board { display:grid; grid-template-columns: 2fr 1fr; gap:18px; align-items:flex-start; }
    .cart-card { background:#fff; border-radius:16px; padding:16px; box-shadow:0 10px 30px rgba(0,0,0,0.08); }
    .cart-stack { display:flex; flex-direction:column; gap:14px; }
    .cart-item { display:grid; grid-template-columns: 96px 1fr; gap:14px; padding:12px 0; border-bottom:1px solid #e5dbd3; }
    .cart-item:last-child { border-bottom:none; }
    .cart-item img { width:96px; height:96px; object-fit:cover; border-radius:12px; background:#f6f2ec; }
    .cart-item-meta { display:flex; flex-wrap:wrap; gap:6px; color:#6a605e; font-size:0.9rem; }
    .cart-actions { display:flex; flex-direction:column; gap:12px; margin-top:6px; }
    .cart-actions input, .cart-actions select { padding:8px 10px; border-radius:10px; border:1px solid #d2c8c1; background:#fff; }
    .cart-actions label { display:flex; flex-direction:column; gap:4px; color:#4b4240; font-weight:600; font-size:0.9rem; }
    .cart-service-cards { display:flex; flex-direction:column; gap:10px; }
    .cart-service-card { display:flex; justify-content:space-between; gap:12px; align-items:center; padding:12px; border:1px solid #e6dcd9; border-radius:12px; background:#fff; transition:border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease; }
    .cart-service-card:hover { border-color:#c3b6b2; box-shadow:0 8px 16px rgba(89,19,32,0.08); }
    .cart-service-card.selected { border-color:#591320; box-shadow:0 10px 20px rgba(89,19,32,0.14); background:#fbf7f5; }
    .cart-service-card__text { display:flex; flex-direction:column; gap:4px; color:#4b4240; }
    .cart-service-card__title { font-weight:700; color:#2f2523; }
    .cart-service-card__desc { color:#6a605e; font-size:0.88rem; }
    .cart-service-card__action input { width:20px; height:20px; }
    .cart-service-card--install { cursor:pointer; }
    .cart-service-card--delivery { align-items:flex-start; }
    .cart-service-card--delivery .cart-service-card__control { flex:1; display:flex; flex-direction:column; gap:6px; align-items:flex-start; }
    .cart-service-card--delivery .cart-service-card__title { color:#5a1620; }
    .cart-service-card--delivery .cart-service-card__desc { color:#6a605e; font-size:0.9rem; }
    .cart-service-card--delivery .cart-service-card__select { width:100%; }
    .cart-service-card--delivery select { width:100%; min-width:180px; padding:10px; border-radius:10px; border:1px solid #d2c8c1; }
    .cart-service-card--delivery .summary-toggle { gap:10px; }
    .badge { display:inline-flex; align-items:center; gap:6px; padding:4px 10px; border-radius:999px; font-size:0.78rem; font-weight:650; background:#f3e7dc; color:#5c3a28; }
    .badge.stock { background:#eef9f0; color:#2d7a42; }
    .badge.backorder { background:#f6e6e7; color:#5a1620; }
    .cart-summary-title { font-size:1.05rem; font-weight:700; color:#5a1620; margin-bottom:2px; }
    .cart-summary-sub { color:#746a66; margin-bottom:10px; font-size:0.92rem; }
    .cart-summary-block { border:1px solid #e5dbd3; border-radius:12px; padding:12px; background:#fdfbf8; margin-bottom:10px; }
    .summary-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:6px; color:#4b4240; }
    .summary-row strong { color:#5a1620; }
    .summary-divider { border-top:1px solid #e5dbd3; margin:10px 0; }
    .summary-note { color:#7a6a62; font-size:0.88rem; line-height:1.4; margin-bottom:8px; }
    .summary-toggle { display:flex; gap:10px; align-items:center; color:#4b4240; padding:10px 12px; border:1px solid #e6dcd9; border-radius:12px; background:#fff; cursor:pointer; transition:border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease; }
    .summary-toggle:hover { border-color:#c3b6b2; box-shadow:0 8px 16px rgba(89,19,32,0.08); }
    .summary-toggle:focus-within { outline:2px solid #591320; outline-offset:2px; }
    .summary-toggle input { appearance:none; width:18px; height:18px; border:1px solid #c9beba; border-radius:6px; display:grid; place-items:center; background:#fff; flex-shrink:0; }
    .summary-toggle input:checked { background:#591320; border-color:#591320; box-shadow:0 3px 8px rgba(89,19,32,0.15); }
    .summary-toggle input:checked::after { content:""; width:8px; height:8px; display:block; border-radius:2px; background:#fff; }
    .summary-toggle__content { display:flex; flex-direction:column; gap:2px; }
    .summary-toggle__title { font-weight:650; color:#2f2523; }
    .summary-toggle__desc { font-size:0.9rem; color:#6a605e; line-height:1.35; }
    .cart-empty { padding:16px; text-align:center; color:#6a605e; }
    .cart-actions .price-note { color:#7a6a62; font-size:0.84rem; margin-top:2px; display:block; }
    .cart-footer-actions { display:flex; gap:8px; flex-wrap:wrap; }
    @media (max-width: 1024px){
      .cart-board { grid-template-columns: 1fr; }
      .cart-stack { flex-direction:column; }
    }
    @media (max-width: 640px){
      .cart-item { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body class="cart-shell">
<?php include $base.'includes/header.php'; ?>
<main class="container" style="padding:32px 0 40px;">
  <div class="sec-head">
    <div>
      <div class="eyebrow">Project builder</div>
      <h2 style="color:var(--burgundy);">Your cart</h2>
    </div>
  </div>
  <div class="cart-board">
    <section class="cart-card">
      <div id="cart-items"></div>
      <p id="cart-empty" class="cart-empty">Your project list is empty.</p>
    </section>
    <aside class="cart-stack">
      <section class="cart-card cart-summary-card">
        <div class="cart-summary-title">Project summary</div>
        <div class="cart-summary-sub" id="summary-items">No items yet</div>
        <div class="cart-summary-block">
          <div class="summary-row">
            <span>Materials</span>
            <strong id="summary-material">$0.00</strong>
          </div>
          <div class="summary-row">
            <span>Installation</span>
            <strong id="summary-install">$0.00</strong>
          </div>
          <div class="summary-row">
            <span>Delivery (estimate)</span>
            <strong id="summary-delivery">$0.00</strong>
          </div>
          <div class="summary-row">
            <span>Taxes</span>
            <strong id="summary-taxes">$0.00</strong>
          </div>
          <div class="summary-divider"></div>
          <div class="summary-row">
            <span><strong>Estimated total</strong></span>
            <strong id="summary-total">$0.00</strong>
          </div>
        </div>
        <p class="summary-note" id="summary-note">Final quote may adjust based on exact measurements, delivery zone and scheduling.</p>
        <div class="summary-divider"></div>
        <div class="cart-service-card cart-service-card--delivery" role="group" aria-labelledby="delivery-title">
            <div class="cart-service-card__control">
              <div class="cart-service-card__title" id="delivery-title">Delivery & zone</div>
              <div class="cart-service-card__desc">Choose delivery or warehouse pick-up for this project.</div>
            <label class="summary-toggle" for="delivery-toggle">
              <input type="checkbox" id="delivery-toggle" checked />
              <span>I’d like B&S to handle delivery for this project.</span>
            </label>
            <div class="cart-service-card__select">
              <label style="width:100%; display:block;">
                <span style="display:block; margin-bottom:6px; color:#4b4240; font-weight:600;">Delivery ZIP Code</span>
                <input type="text" id="delivery-zip" list="delivery-zip-list" placeholder="Enter ZIP Code" style="width:100%; min-width:180px; padding:10px; border-radius:10px; border:1px solid #d2c8c1;" inputmode="numeric" pattern="\\d*" />
                <datalist id="delivery-zip-list"></datalist>
              </label>
            </div>
            <p class="summary-note" id="delivery-note" style="margin:2px 0 0 0;"></p>
            <p class="summary-note" id="delivery-city" style="margin:2px 0 0 0;"></p>
            </div>
          </div>
        <div class="cart-footer-actions">
          <button type="button" id="go-project" class="btn btn-primary" style="flex:1;">Continue to project details</button>
          <button type="button" class="btn btn-ghost" style="flex:1;" onclick="window.location.href='index.php'">Back to store</button>
        </div>
        <p class="summary-note" style="margin-top:8px;">By continuing, you’ll send this project to our team. No payment is made on this page.</p>
      </section>
    </aside>
  </div>
</main>
<?php include $base.'includes/footer.php'; ?>
<script src="cart.js"></script>
<script>
const PRODUCTS = <?= json_encode(array_values($products)) ?>;
const STORE_CONFIG = <?= json_encode($storeConfig) ?>;
const PROJECT_KEY = 'bs_project';
const DELIVERY_PREF_KEY = 'bs_delivery_pref';
const ZIP_ZONE_FILE = 'zip_zones.json';
const ZIP_ZONE_MAPPING = {A: 'meadow', B: 'orlando', C: 'orlando'};
let ZIP_DATA = [];
let zipLoadPromise = null;
let hasRenderedCartWithItems = (cart.getItems()?.length || 0) > 0;

function formatCurrency(value){
  const num = Number(value);
  if(!Number.isFinite(num)) return '$0.00';
  return `$${num.toFixed(2)}`;
}
function formatUnits(value){
  const num = Number(value);
  if(!Number.isFinite(num)) return '';
  return num.toLocaleString(undefined, {maximumFractionDigits: 2});
}
function getDefaultDeliveryZone(){
  const zones = STORE_CONFIG.delivery?.zones || [];
  const pickup = zones.find(z=>z.id === 'pick-up');
  return pickup?.id || zones[0]?.id || null;
}
function loadDeliveryPreferences(){
  try{
    const saved = JSON.parse(localStorage.getItem(DELIVERY_PREF_KEY));
    const defaults = {includeDelivery: false, zone: getDefaultDeliveryZone(), zip: '', city: '', zoneSource: null};
    const merged = {...defaults, ...(saved || {})};
    if(!merged.zone) merged.zone = getDefaultDeliveryZone();
    return merged;
  }catch(e){
    return {includeDelivery: false, zone: getDefaultDeliveryZone(), zip: '', city: '', zoneSource: null};
  }
}
function saveDeliveryPreferences(prefs){
  localStorage.setItem(DELIVERY_PREF_KEY, JSON.stringify(prefs));
}
let deliveryPreferences = loadDeliveryPreferences();
function mapZoneFromLetter(letter){
  return ZIP_ZONE_MAPPING[letter?.toString().trim().toUpperCase()] || getDefaultDeliveryZone();
}
function ensureZipData(){
  if(zipLoadPromise) return zipLoadPromise;
  zipLoadPromise = fetch(ZIP_ZONE_FILE)
    .then(res=>res.ok ? res.json() : [])
    .then(data=>{ ZIP_DATA = Array.isArray(data) ? data : []; return ZIP_DATA; })
    .catch(()=>{ ZIP_DATA = []; return ZIP_DATA; });
  return zipLoadPromise;
}
function resolveZipEntry(zip){
  const normalized = (zip || '').toString().trim();
  if(!normalized || !ZIP_DATA.length) return null;
  const entry = ZIP_DATA.find(z=>z.zip === normalized);
  if(!entry) return null;
  const zoneId = mapZoneFromLetter(entry.zone);
  const zones = getAvailableDeliveryZones();
  const validZone = zones.some(z=>z.id === zoneId) ? zoneId : getDefaultDeliveryZone();
  return {...entry, zoneId: validZone};
}
function getProduct(sku){
  return PRODUCTS.find(p=>p.sku===sku) || null;
}
function getAvailableDeliveryZones(){
  return STORE_CONFIG?.delivery?.zones || [];
}
function resolveProjectDeliveryZone(){
  const zones = getAvailableDeliveryZones();
  if(!zones.length) return null;
  if(deliveryPreferences.includeDelivery === false){
    const pickup = zones.find(z=>z.id === 'pick-up');
    if(pickup) return pickup.id;
  }
  if(deliveryPreferences.zone && zones.some(z=>z.id === deliveryPreferences.zone)) return deliveryPreferences.zone;
  return zones[0].id;
}
function computePricing(item, product){
  let priceType = item.priceType === 'backorder' ? 'backorder' : 'stock';
  const coverage = Number(product.packageCoverage) || 0;
  const availableStock = Number(product.availability?.stockAvailable ?? NaN);
  const allowBackorder = product.availability?.allowBackorder !== false;
  const hasStock = ((product.availability?.activePriceType || product.availability?.mode) === 'stock') && Number.isFinite(availableStock) && availableStock > 0;
  if(priceType === 'stock' && (!hasStock || (allowBackorder && Number(item.quantity) > availableStock))){
    priceType = 'backorder';
  }
  const sqftRequested = coverage > 0 && Number.isFinite(Number(item.quantity)) ? Number(item.quantity) * coverage : null;
  const backorderUnit = priceType === 'backorder'
    ? (product.productType === 'flooring'
      ? computeFlooringBackorderUnitPrice(sqftRequested, product)
      : computeMoldingBackorderUnitPrice(sqftRequested, product, coverage))
    : null;
  const packagePrice = priceType === 'backorder'
    ? (backorderUnit !== null && coverage>0 ? backorderUnit * coverage : (product.pricing?.pricePerPackageBackorder ?? (coverage>0 && product.pricing?.finalPriceBackorderPerUnit ? product.pricing.finalPriceBackorderPerUnit * coverage : null)))
    : (product.pricing?.pricePerPackageStock ?? (coverage>0 && product.pricing?.finalPriceStockPerUnit ? product.pricing.finalPriceStockPerUnit * coverage : null));
  const unitPrice = priceType === 'backorder'
    ? (backorderUnit !== null ? backorderUnit : (product.pricing?.finalPriceBackorderPerUnit ?? product.pricing?.finalPriceStockPerUnit))
    : (product.pricing?.finalPriceStockPerUnit ?? product.pricing?.finalPriceBackorderPerUnit);
  const subtotal = packagePrice ? packagePrice * item.quantity : 0;
  const inventoryId = priceType === 'stock' ? (item.inventoryId || product.availability?.activeInventoryId || null) : null;
  return {priceType, unitPrice, packagePrice, subtotal, coverage, inventoryId};
}

function getFlooringTruckloadPricePerPiece(boxRatio){
  const truckloadConfig = STORE_CONFIG?.flooring?.truckload || {};
  const tiers = Array.isArray(truckloadConfig.tiers) ? truckloadConfig.tiers : [];
  let price = Number(tiers.find(t=>t && t.default)?.pricePerPiece ?? truckloadConfig.defaultPricePerPiece);
  price = Number.isFinite(price) ? price : 0;
  const sortedTiers = tiers
    .map(t=>({maxBoxes: Number(t.maxBoxes), pricePerPiece: Number(t.pricePerPiece)}))
    .filter(t=>Number.isFinite(t.maxBoxes) && t.maxBoxes > 0 && Number.isFinite(t.pricePerPiece))
    .sort((a,b)=>b.maxBoxes - a.maxBoxes);
  for(const tier of sortedTiers){
    if(Number.isFinite(boxRatio) && boxRatio >= tier.maxBoxes){
      price = tier.pricePerPiece;
      break;
    }
  }
  return price;
}

function computeMoldingBackorderUnitPrice(lfRequested, product, coverage){
  const providerPrice = Number(product?.pricing?.providerPrice);
  if(!Number.isFinite(providerPrice) || providerPrice <= 0){
    return null;
  }
  const lengthFt = Number(product?.length_ft ?? product?.lengthFt ?? coverage);
  const piecesPerBox = Number(product?.pieces_per_box ?? product?.piecesPerBox ?? 1);
  const totalLf = Number(lfRequested);
  if(!Number.isFinite(lengthFt) || lengthFt <= 0 || !Number.isFinite(totalLf) || totalLf <= 0){
    return null;
  }
  const piecesNeeded = totalLf / lengthFt;
  const truckloadPrice = getMoldingTruckloadPricePerPiece(piecesNeeded, piecesPerBox);
  const gain = Number(product?.pricing?.gainPercent);
  const discount = Number(product?.pricing?.discountPercent);
  const gainFactor = Number.isFinite(gain) ? (1 + (gain/100)) : 1;
  const discountFactor = Number.isFinite(discount) ? (1 - (discount/100)) : 1;
  const basePrice = providerPrice + (Number.isFinite(truckloadPrice) ? truckloadPrice : 0);
  const adjusted = basePrice * gainFactor * discountFactor;
  return Number.isFinite(adjusted) ? adjusted : null;
}

function computeFlooringBackorderUnitPrice(sqftRequested, product){
  const providerPrice = Number(product?.pricing?.providerPrice);
  if(!Number.isFinite(providerPrice) || providerPrice <= 0){
    return null;
  }
  const truckloadSqft = Number(product?.pricing?.truckLoadPallets);
  const boxRatio = Number.isFinite(truckloadSqft) && truckloadSqft > 0 && Number.isFinite(Number(sqftRequested))
    ? Number(sqftRequested) / truckloadSqft
    : null;
  const truckloadPrice = getFlooringTruckloadPricePerPiece(boxRatio);
  const gain = Number(product?.pricing?.gainPercent);
  const discount = Number(product?.pricing?.discountPercent);
  const gainFactor = Number.isFinite(gain) ? (1 + (gain/100)) : 1;
  const discountFactor = Number.isFinite(discount) ? (1 - (discount/100)) : 1;
  const basePrice = providerPrice + (Number.isFinite(truckloadPrice) ? truckloadPrice : 0);
  const adjusted = basePrice * gainFactor * discountFactor;
  return Number.isFinite(adjusted) ? adjusted : null;
}

function getMoldingTruckloadPricePerPiece(piecesCount, piecesPerBox){
  const pieces = Number(piecesCount);
  const piecesPerPackage = Number.isFinite(Number(piecesPerBox)) && Number(piecesPerBox) > 0 ? Number(piecesPerBox) : 1;
  if(!Number.isFinite(pieces) || pieces <= 0){
    return 0;
  }
  const truckloadConfig = STORE_CONFIG?.molding?.truckload || {};
  const tiers = Array.isArray(truckloadConfig.tiers) ? truckloadConfig.tiers : [];
  const sortedTiers = tiers
    .map(t=>({maxBoxes: Number(t.maxBoxes), pricePerPiece: Number(t.pricePerPiece)}))
    .filter(t=>Number.isFinite(t.maxBoxes) && t.maxBoxes > 0 && Number.isFinite(t.pricePerPiece) && t.pricePerPiece >= 0)
    .sort((a,b)=>b.maxBoxes - a.maxBoxes);
  const ratio = pieces / piecesPerPackage;
  let pricePerPiece = Number(tiers.find(t=>t && t.default)?.pricePerPiece ?? truckloadConfig.defaultPricePerPiece);
  pricePerPiece = Number.isFinite(pricePerPiece) ? pricePerPiece : (sortedTiers.length ? sortedTiers[sortedTiers.length - 1].pricePerPiece : 0);
  for(const tier of sortedTiers){
    if(Number.isFinite(ratio) && ratio >= tier.maxBoxes){
      pricePerPiece = tier.pricePerPiece;
      break;
    }
  }
  return pricePerPiece;
}

function computeInstall(item, product, coverage){
  if(!item.install) return 0;
  const rate = product.services?.installRate ?? (product.productType === 'molding' ? STORE_CONFIG?.install?.defaultMoldingRate : STORE_CONFIG?.install?.defaultFlooringRate);
  if(!rate) return 0;
  const units = coverage > 0 ? coverage * item.quantity : 0;
  return units > 0 ? units * rate : 0;
}
function computeDelivery(zoneId, zones){
  const zone = zones.find(z=>z.id === zoneId);
  if(zone && Number.isFinite(Number(zone.fee))) return Number(zone.fee);
  return 0;
}
function getTaxRate(){
  const rate = Number(STORE_CONFIG?.taxes);
  return Number.isFinite(rate) && rate > 0 ? rate : 0;
}
function computeTaxes(taxableBase, product){
  if(product?.taxesOmit) return 0;
  const rate = getTaxRate();
  const base = Number(taxableBase);
  if(!Number.isFinite(base) || base <= 0 || rate <= 0) return 0;
  return base * rate;
}
function serializeProject(items){
  const enriched = [];
  let totals = {material:0, install:0, delivery:0, taxes:0, total:0};
  const deliveryZones = getAvailableDeliveryZones();
  const deliveryZone = resolveProjectDeliveryZone();
  const deliveryFee = deliveryPreferences.includeDelivery === false ? 0 : computeDelivery(deliveryZone, deliveryZones);
  const deliveryInfo = {
    includeDelivery: deliveryPreferences.includeDelivery !== false,
    zone: deliveryZone,
    zip: deliveryPreferences.zip || '',
    city: deliveryPreferences.city || '',
    zoneSource: deliveryPreferences.zoneSource || null
  };
  for(const item of items){
    const product = getProduct(item.sku);
    if(!product) continue;
    const pricing = computePricing(item, product);
    const install = computeInstall(item, product, pricing.coverage);
    const taxes = computeTaxes((pricing.subtotal || 0) + (install || 0), product);
    const unitPriceWithTruckload = pricing.unitPrice;
    if(item.priceType !== pricing.priceType || item.inventoryId !== pricing.inventoryId){
      cart.setItem(item.sku, item.quantity, pricing.priceType, {install: item.install, inventoryId: pricing.inventoryId});
    }
    totals.material += pricing.subtotal;
    totals.install += install;
    totals.taxes += taxes;
    enriched.push({
      ...item,
      priceType: pricing.priceType,
      unitPrice: pricing.unitPrice,
      unitPriceWithTruckload,
      packagePrice: pricing.packagePrice,
      inventoryId: pricing.inventoryId,
      subtotal: pricing.subtotal,
      install,
      taxes,
      taxRate: getTaxRate(),
      delivery: deliveryFee,
      deliveryZone,
      deliveryZones,
      product
    });
  }
  totals.delivery = deliveryFee;
  totals.total = totals.material + totals.install + totals.delivery + totals.taxes;
  return {items: enriched, totals, createdAt: Date.now(), deliveryPreferences: {...deliveryPreferences, zone: deliveryZone}, deliveryZone, deliveryInfo};
}
function refreshDeliveryControls(){
  const toggle = document.getElementById('delivery-toggle');
  const zipInput = document.getElementById('delivery-zip');
  const zipList = document.getElementById('delivery-zip-list');
  const note = document.getElementById('delivery-note');
  const cityNote = document.getElementById('delivery-city');
  const zones = getAvailableDeliveryZones();
  if(toggle){
    toggle.checked = deliveryPreferences.includeDelivery !== false;
  }
  if(zipInput){
    zipInput.value = deliveryPreferences.zip || '';
    zipInput.disabled = !deliveryPreferences.includeDelivery;
  }
  if(note){
    note.textContent = STORE_CONFIG.delivery?.notes || 'Delivery cost may vary based on access conditions.';
  }
  if(cityNote){
    const zoneId = resolveProjectDeliveryZone();
    const zone = zones.find(z=>z.id === zoneId);
    const pickup = zones.find(z=>z.id === 'pick-up');
    const cityLabel = deliveryPreferences.zip && deliveryPreferences.city ? `${deliveryPreferences.city} (${deliveryPreferences.zip})` : '';
    const zoneLabel = zone ? `${zone.label}${zone.fee!=null ? ' — '+formatCurrency(zone.fee) : ''}` : '';
    const pickupLabel = pickup ? `${pickup.label}${pickup.fee!=null ? ' — '+formatCurrency(pickup.fee) : ''}` : 'Warehouse pick-up (default)';
    cityNote.textContent = deliveryPreferences.includeDelivery !== false
      ? [cityLabel || 'Delivery ZIP pending', zoneLabel].filter(Boolean).join(' · ')
      : pickupLabel;
  }
  if(zipList){
    ensureZipData().then(()=>{
      zipList.innerHTML = ZIP_DATA.map(z=>`<option value="${z.zip}">${z.city}</option>`).join('');
    });
  }
  const summaryNote = document.getElementById('summary-note');
  if(summaryNote && STORE_CONFIG.install?.notes){
    summaryNote.textContent = STORE_CONFIG.install.notes;
  }
}
  function renderCart(){
    const items = cart.getItems();
    const container = document.getElementById('cart-items');
    const empty = document.getElementById('cart-empty');
    if(items.length === 0){
      container.innerHTML = '';
      empty.style.display = 'block';
      document.getElementById('summary-items').textContent = 'No items yet';
      document.getElementById('summary-material').textContent = '$0.00';
      document.getElementById('summary-install').textContent = '$0.00';
      document.getElementById('summary-delivery').textContent = '$0.00';
      document.getElementById('summary-taxes').textContent = '$0.00';
      document.getElementById('summary-total').textContent = '$0.00';
      refreshDeliveryControls();
      if(hasRenderedCartWithItems){
        setTimeout(()=>{ window.location.href = 'index.php'; }, 150);
      }
      hasRenderedCartWithItems = false;
      return;
    }
  hasRenderedCartWithItems = true;
  empty.style.display = 'none';
  const project = serializeProject(items);
  localStorage.setItem(PROJECT_KEY, JSON.stringify(project));
  container.innerHTML = project.items.map(it=>{
    const p = it.product;
    const unit = p.measurementUnit === 'lf' ? 'lf' : p.measurementUnit === 'piece' ? 'piece' : 'sqft';
    const coverLabel = it.product.packageCoverage ? `${formatUnits(it.product.packageCoverage)} ${unit} / ${p.packageLabel || 'box'}` : '';
    const image = p.images?.[0] ? `../${p.images[0]}` : '';
    const unitPriceLabel = Number.isFinite(Number(it.unitPriceWithTruckload))
      ? `${formatCurrency(Number(it.unitPriceWithTruckload))} / ${unit}`
      : '';
    const packagePriceLabel = it.packagePrice ? `${formatCurrency(it.packagePrice)} / ${p.packageLabel || 'box'}` : '';
      const priceLabel = [unitPriceLabel, packagePriceLabel].filter(Boolean).join(' · ') || 'Call for price';
      const priceTypeLabel = it.priceType === 'backorder' ? 'Order-in' : 'In stock';
      const taxLabel = p.taxesOmit ? '<span class="badge">Tax exempt</span>' : '';
      const installRate = p.services?.installRate ?? (p.productType === 'molding' ? STORE_CONFIG?.install?.defaultMoldingRate : STORE_CONFIG?.install?.defaultFlooringRate);
      const installUnitLabel = p.measurementUnit === 'lf' ? 'lf' : (p.measurementUnit === 'piece' ? 'piece' : 'sq ft');
      const installRateLabel = installRate ? ` (${formatCurrency(installRate)} / ${installUnitLabel})` : '';
      return `
        <article class="cart-item" data-sku="${it.sku}" data-price-type="${it.priceType}" data-inventory-id="${it.inventoryId || ''}">
          <div>${image ? `<img src="${image}" alt="${p.name}">` : ''}</div>
          <div>
            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px;">
              <div>
                <div style="font-weight:700; color:#1f1f1f;">${p.name}</div>
                <div class="cart-item-meta">${p.collection || ''} ${coverLabel ? '· '+coverLabel : ''}</div>
                <div class="cart-item-meta"><span class="badge ${it.priceType === 'backorder' ? 'backorder' : 'stock'}">${priceTypeLabel}</span>${taxLabel ? ' · '+taxLabel : ''}</div>
              </div>
              <button type="button" class="remove btn btn-ghost" style="padding:6px 10px;">Remove</button>
            </div>
            <div class="cart-item-meta" style="margin:6px 0;">${priceLabel}</div>
          <div class="cart-actions">
            <label class="cart-qty-control">Qty
              <input type="number" class="qty" min="1" value="${it.quantity}">
            </label>
            <div class="cart-service-cards">
              <div class="cart-service-card cart-service-card--install ${it.install ? 'selected' : ''}" role="button" tabindex="0" aria-pressed="${it.install ? 'true' : 'false'}">
                <div class="cart-service-card__text">
                  <div class="cart-service-card__title">Installation${installRateLabel}</div>
                  <div class="cart-service-card__desc">${it.install ? 'Installation added to this item.' : 'Add an installation estimate based on your area.'}</div>
                </div>
                <div class="cart-service-card__action">
                  <input type="checkbox" class="install-toggle" ${it.install ? 'checked' : ''} aria-label="Toggle installation">
                </div>
              </div>
            </div>
          </div>
            <div class="cart-item-meta">Subtotal: ${formatCurrency(it.subtotal)} | Install: ${formatCurrency(it.install)}</div>
            <div class="cart-item-meta">Taxes: ${formatCurrency(it.taxes || 0)}</div>
          </div>
        </article>
      `;
    }).join('');

  document.getElementById('summary-material').textContent = formatCurrency(project.totals.material);
  document.getElementById('summary-install').textContent = formatCurrency(project.totals.install);
  document.getElementById('summary-delivery').textContent = formatCurrency(project.totals.delivery);
  document.getElementById('summary-taxes').textContent = formatCurrency(project.totals.taxes);
  document.getElementById('summary-total').textContent = formatCurrency(project.totals.total);
  const totalUnits = project.items.reduce((sum, it)=> sum + (it.quantity || 0), 0);
  document.getElementById('summary-items').textContent = `${project.items.length} item${project.items.length === 1 ? '' : 's'} · ${formatUnits(totalUnits)} package${totalUnits === 1 ? '' : 's'}`;
  refreshDeliveryControls();

  container.querySelectorAll('.qty').forEach(input=>{
    input.addEventListener('change', ()=>{
      const article = input.closest('.cart-item');
      const sku = article?.dataset?.sku;
      const priceType = article?.dataset?.priceType;
      const inventoryId = article?.dataset?.inventoryId;
      const install = article?.querySelector('.install-toggle')?.checked;
      if(!sku) return;
      cart.setItem(sku, parseInt(input.value)||1, priceType, {install, inventoryId});
      renderCart();
    });
  });
  container.querySelectorAll('.install-toggle').forEach(input=>{
    input.addEventListener('change', ()=>{
      const article = input.closest('.cart-item');
      const sku = article?.dataset?.sku;
      const priceType = article?.dataset?.priceType;
      const inventoryId = article?.dataset?.inventoryId;
      const qty = parseInt(article?.querySelector('.qty')?.value)||1;
      cart.setItem(sku, qty, priceType, {install: input.checked, inventoryId});
      renderCart();
    });
  });
  container.querySelectorAll('.cart-service-card--install').forEach(card=>{
    card.addEventListener('click', (evt)=>{
      if(evt.target.closest('input,select,button,label')) return;
      const checkbox = card.querySelector('.install-toggle');
      if(!checkbox) return;
      checkbox.checked = !checkbox.checked;
      checkbox.dispatchEvent(new Event('change', {bubbles:true}));
    });
    card.addEventListener('keydown', (evt)=>{
      if(evt.key === 'Enter' || evt.key === ' '){
        evt.preventDefault();
        card.click();
      }
    });
  });
  container.querySelectorAll('.remove').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const article = btn.closest('.cart-item');
      cart.removeItem(article?.dataset?.sku, article?.dataset?.priceType, article?.dataset?.inventoryId);
      renderCart();
    });
  });
}

document.getElementById('go-project')?.addEventListener('click', ()=>{
  const project = serializeProject(cart.getItems());
  localStorage.setItem(PROJECT_KEY, JSON.stringify(project));
  window.location.href = 'project-details.php';
});

document.getElementById('delivery-toggle')?.addEventListener('change', (e)=>{
  const pickup = getAvailableDeliveryZones().find(z=>z.id === 'pick-up');
  deliveryPreferences = {
    ...deliveryPreferences,
    includeDelivery: e.target.checked,
    zone: e.target.checked ? deliveryPreferences.zone || getDefaultDeliveryZone() : (pickup?.id || getDefaultDeliveryZone()),
    zip: e.target.checked ? deliveryPreferences.zip : '',
    city: e.target.checked ? deliveryPreferences.city : '',
    zoneSource: e.target.checked ? deliveryPreferences.zoneSource : null
  };
  saveDeliveryPreferences(deliveryPreferences);
  renderCart();
});
document.getElementById('delivery-zip')?.addEventListener('change', (e)=>{
  const raw = (e.target.value || '').trim();
  ensureZipData().then(()=>{
    const entry = resolveZipEntry(raw);
    if(entry){
      deliveryPreferences = {
        ...deliveryPreferences,
        includeDelivery: true,
        zone: entry.zoneId,
        zip: entry.zip,
        city: entry.city,
        zoneSource: entry.zone
      };
    }else{
      const pickup = getAvailableDeliveryZones().find(z=>z.id === 'pick-up');
      deliveryPreferences = {
        ...deliveryPreferences,
        includeDelivery: false,
        zone: pickup?.id || getDefaultDeliveryZone(),
        zip: '',
        city: '',
        zoneSource: null
      };
    }
    saveDeliveryPreferences(deliveryPreferences);
    renderCart();
  });
});

refreshDeliveryControls();
renderCart();

</script>
</body>
</html>
