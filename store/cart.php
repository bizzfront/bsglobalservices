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
          <!--<div class="summary-row">
            <span>Truckload (molding)</span>
            <strong id="summary-truckload">$0.00</strong>
          </div>-->
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
              <select id="delivery-zone" class="cart-actions-select"></select>
            </div>
            <p class="summary-note" id="delivery-note" style="margin:2px 0 0 0;"></p>
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
  return zones[0]?.id || null;
}
function loadDeliveryPreferences(){
  try{
    const saved = JSON.parse(localStorage.getItem(DELIVERY_PREF_KEY));
    return {
      includeDelivery: saved?.includeDelivery !== false,
      zone: saved?.zone || getDefaultDeliveryZone()
    };
  }catch(e){
    return {includeDelivery: true, zone: getDefaultDeliveryZone()};
  }
}
function saveDeliveryPreferences(prefs){
  localStorage.setItem(DELIVERY_PREF_KEY, JSON.stringify(prefs));
}
let deliveryPreferences = loadDeliveryPreferences();
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
  const priceType = item.priceType === 'backorder' ? 'backorder' : 'stock';
  const coverage = Number(product.packageCoverage) || 0;
  const packagePrice = priceType === 'backorder'
    ? (product.pricing?.pricePerPackageBackorder ?? (coverage>0 && product.pricing?.finalPriceBackorderPerUnit ? product.pricing.finalPriceBackorderPerUnit * coverage : null))
    : (product.pricing?.pricePerPackageStock ?? (coverage>0 && product.pricing?.finalPriceStockPerUnit ? product.pricing.finalPriceStockPerUnit * coverage : null));
  const unitPrice = priceType === 'backorder'
    ? (product.pricing?.finalPriceBackorderPerUnit ?? product.pricing?.finalPriceStockPerUnit)
    : (product.pricing?.finalPriceStockPerUnit ?? product.pricing?.finalPriceBackorderPerUnit);
  const subtotal = packagePrice ? packagePrice * item.quantity : 0;
  return {priceType, unitPrice, packagePrice, subtotal, coverage};
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
    .sort((a,b)=>a.maxBoxes - b.maxBoxes);
  const ratio = pieces / piecesPerPackage;
  const fallbackPrice = sortedTiers.length ? sortedTiers[sortedTiers.length - 1].pricePerPiece : 0;
  let pricePerPiece = Number(truckloadConfig.defaultPricePerPiece);
  if(!Number.isFinite(pricePerPiece) || pricePerPiece <= 0){
    pricePerPiece = fallbackPrice;
  }
  for(const tier of sortedTiers){
    if(ratio <= tier.maxBoxes){
      pricePerPiece = tier.pricePerPiece;
      break;
    }
  }
  return pricePerPiece;
}

function computeTruckload(item, product){
  if(product.productType !== 'molding') return {pricePerPiece: 0, total: 0};
  const piecesPerPackage = (product.packageLabel === 'piece' || product.packageLabelPlural === 'pieces')
    ? 1
    : Number(product.pieces_per_box ?? product.piecesPerBox);
  const packages = Number(item.quantity);
  if(!Number.isFinite(packages) || packages <= 0 || !Number.isFinite(piecesPerPackage) || piecesPerPackage <= 0){
    return {pricePerPiece: 0, total: 0};
  }
  const totalPieces = packages * piecesPerPackage;
  const pricePerPiece = getMoldingTruckloadPricePerPiece(totalPieces, piecesPerPackage);
  const total = Number.isFinite(pricePerPiece) && pricePerPiece > 0 ? pricePerPiece * totalPieces : 0;
  return {pricePerPiece, total};
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
  let totals = {material:0, install:0, truckload:0, delivery:0, taxes:0, total:0};
  const deliveryZones = getAvailableDeliveryZones();
  const deliveryZone = resolveProjectDeliveryZone();
  const deliveryFee = deliveryPreferences.includeDelivery === false ? 0 : computeDelivery(deliveryZone, deliveryZones);
  for(const item of items){
    const product = getProduct(item.sku);
    if(!product) continue;
    const pricing = computePricing(item, product);
    const truckload = computeTruckload(item, product);
    const install = computeInstall(item, product, pricing.coverage);
    const taxes = computeTaxes((pricing.subtotal || 0) + (truckload.total || 0) + (install || 0), product);
    const unitPriceWithTruckload = product.productType === 'molding'
      ? (Number(pricing.unitPrice) || 0) + (Number(truckload.pricePerPiece) || 0)
      : pricing.unitPrice;
    totals.material += pricing.subtotal;
    totals.install += install;
    totals.truckload += truckload.total;
    totals.taxes += taxes;
    enriched.push({
      ...item,
      priceType: pricing.priceType,
      unitPrice: pricing.unitPrice,
      unitPriceWithTruckload,
      packagePrice: pricing.packagePrice,
      subtotal: pricing.subtotal,
      truckloadPricePerPiece: truckload.pricePerPiece,
      truckloadTotal: truckload.total,
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
  totals.total = totals.material + totals.install + totals.delivery + totals.truckload + totals.taxes;
  return {items: enriched, totals, createdAt: Date.now(), deliveryPreferences: {...deliveryPreferences, zone: deliveryZone}, deliveryZone};
}
function refreshDeliveryControls(){
  const toggle = document.getElementById('delivery-toggle');
  const select = document.getElementById('delivery-zone');
  const note = document.getElementById('delivery-note');
  const zones = getAvailableDeliveryZones();
  if(toggle){
    toggle.checked = deliveryPreferences.includeDelivery !== false;
  }
  if(select){
    const preferred = zones.some(z=>z.id === deliveryPreferences.zone) ? deliveryPreferences.zone : zones[0]?.id || '';
    if(preferred && preferred !== deliveryPreferences.zone){
      deliveryPreferences = {...deliveryPreferences, zone: preferred};
      saveDeliveryPreferences(deliveryPreferences);
    }
    select.innerHTML = zones.map(z=>`<option value="${z.id}">${z.label}${z.fee!=null ? ' — '+formatCurrency(z.fee) : ''}</option>`).join('') || '<option value="" disabled>No delivery zones</option>';
    select.value = preferred;
    select.disabled = !deliveryPreferences.includeDelivery || zones.length === 0;
  }
  if(note){
    note.textContent = STORE_CONFIG.delivery?.notes || 'Delivery cost may vary based on access conditions.';
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
      //document.getElementById('summary-truckload').textContent = '$0.00';
      document.getElementById('summary-install').textContent = '$0.00';
      document.getElementById('summary-delivery').textContent = '$0.00';
      document.getElementById('summary-taxes').textContent = '$0.00';
      document.getElementById('summary-total').textContent = '$0.00';
      refreshDeliveryControls();
      return;
    }
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
      const truckloadLabel = p.productType === 'molding' ? ` | Truckload: ${formatCurrency(it.truckloadTotal || 0)}` : '';
      return `
        <article class="cart-item" data-sku="${it.sku}" data-price-type="${it.priceType}">
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
            <div class="cart-item-meta">Subtotal: ${formatCurrency(it.subtotal)}${truckloadLabel} | Install: ${formatCurrency(it.install)}</div>
            <div class="cart-item-meta">Taxes: ${formatCurrency(it.taxes || 0)}</div>
          </div>
        </article>
      `;
    }).join('');

  document.getElementById('summary-material').textContent = formatCurrency(project.totals.material+project.totals.truckload);
  //document.getElementById('summary-truckload').textContent = formatCurrency(project.totals.truckload);
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
      const install = article?.querySelector('.install-toggle')?.checked;
      if(!sku) return;
      cart.setItem(sku, parseInt(input.value)||1, priceType, {install});
      renderCart();
    });
  });
  container.querySelectorAll('.install-toggle').forEach(input=>{
    input.addEventListener('change', ()=>{
      const article = input.closest('.cart-item');
      const sku = article?.dataset?.sku;
      const priceType = article?.dataset?.priceType;
      const qty = parseInt(article?.querySelector('.qty')?.value)||1;
      cart.setItem(sku, qty, priceType, {install: input.checked});
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
      cart.removeItem(article?.dataset?.sku, article?.dataset?.priceType);
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
  deliveryPreferences = {...deliveryPreferences, includeDelivery: e.target.checked};
  if(!e.target.checked){
    const pickup = getAvailableDeliveryZones().find(z=>z.id === 'pick-up');
    if(pickup){
      deliveryPreferences = {...deliveryPreferences, zone: pickup.id};
    }
  }
  saveDeliveryPreferences(deliveryPreferences);
  renderCart();
});
document.getElementById('delivery-zone')?.addEventListener('change', (e)=>{
  deliveryPreferences = {...deliveryPreferences, zone: e.target.value};
  saveDeliveryPreferences(deliveryPreferences);
  renderCart();
});

refreshDeliveryControls();
renderCart();

</script>
</body>
</html>
