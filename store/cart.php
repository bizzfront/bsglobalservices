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
    .cart-item { display:grid; grid-template-columns: 96px 1fr; gap:14px; padding:12px 0; border-bottom:1px solid #e5dbd3; }
    .cart-item:last-child { border-bottom:none; }
    .cart-item img { width:96px; height:96px; object-fit:cover; border-radius:12px; background:#f6f2ec; }
    .cart-item-meta { display:flex; flex-wrap:wrap; gap:6px; color:#6a605e; font-size:0.9rem; }
    .cart-actions { display:flex; gap:8px; flex-wrap:wrap; margin-top:6px; }
    .cart-actions input, .cart-actions select { padding:8px 10px; border-radius:10px; border:1px solid #d2c8c1; background:#fff; }
    .cart-totals { display:flex; flex-direction:column; gap:8px; }
    .cart-total-row { display:flex; justify-content:space-between; }
    .cart-total-row strong { color:#591320; }
    .cart-form label { display:block; margin-top:10px; font-weight:600; color:#4b4240; }
    .cart-form input, .cart-form textarea, .cart-form select { width:100%; padding:10px; border:1px solid #d2c8c1; border-radius:10px; margin-top:4px; }
    .cart-empty { padding:16px; text-align:center; color:#6a605e; }
    @media (max-width: 880px){
      .cart-board { grid-template-columns: 1fr; }
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
    <aside class="cart-card">
      <h3 style="margin-bottom:10px;">Totals</h3>
      <div id="cart-totals" class="cart-totals"></div>
      <button type="button" id="go-project" class="btn btn-primary" style="width:100%; margin:12px 0;">Continue to project details</button>
      <hr style="margin:12px 0; border:none; border-top:1px solid #e5dbd3;" />
      <form id="cart-form" action="<?=$base?>lead.php" method="POST" class="cart-form">
        <h4 style="color:var(--burgundy);">Quick send</h4>
        <label for="name-cart">Full name</label>
        <input id="name-cart" name="name" required />
        <label for="phone-cart">Phone / WhatsApp</label>
        <input id="phone-cart" name="phone" required />
        <label for="email-cart">Email</label>
        <input id="email-cart" type="email" name="email" required />
        <label for="project-type">Project type</label>
        <select id="project-type" name="project_type">
          <option value="">Select</option>
          <option>Residential</option>
          <option>Commercial</option>
          <option>Remodel</option>
        </select>
        <label for="message-cart">Notes</label>
        <textarea id="message-cart" name="message" rows="3" placeholder="Delivery address, timeline, stair count…"></textarea>
        <input type="hidden" name="service" value="order" />
        <input type="hidden" name="form_name" value="B&S – Cart order" />
        <input type="hidden" name="source" value="<?=$contact_source?>" />
        <input type="hidden" name="cart" id="cart-field" />
        <button type="submit" class="btn btn-ghost" style="width:100%; margin-top:10px;" id="send-cart">Send project now</button>
        <p id="cart-status" class="note" aria-live="polite"></p>
      </form>
    </aside>
  </div>
</main>
<?php include $base.'includes/footer.php'; ?>
<script src="cart.js"></script>
<script>
const PRODUCTS = <?= json_encode(array_values($products)) ?>;
const STORE_CONFIG = <?= json_encode($storeConfig) ?>;
const PROJECT_KEY = 'bs_project';

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
function getProduct(sku){
  return PRODUCTS.find(p=>p.sku===sku) || null;
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
function computeInstall(item, product, coverage){
  if(!item.install) return 0;
  const rate = product.services?.installRate ?? (product.productType === 'molding' ? STORE_CONFIG?.install?.defaultMoldingRate : STORE_CONFIG?.install?.defaultFlooringRate);
  if(!rate) return 0;
  const units = coverage > 0 ? coverage * item.quantity : 0;
  return units > 0 ? units * rate : 0;
}
function computeDelivery(item, product){
  const zones = product.delivery?.zones || STORE_CONFIG?.delivery?.zones || [];
  const zone = zones.find(z=>z.id === item.deliveryZone);
  if(zone && Number.isFinite(Number(zone.fee))) return Number(zone.fee);
  return 0;
}
function serializeProject(items){
  const enriched = [];
  let totals = {material:0, install:0, delivery:0, total:0};
  for(const item of items){
    const product = getProduct(item.sku);
    if(!product) continue;
    const pricing = computePricing(item, product);
    const install = computeInstall(item, product, pricing.coverage);
    const delivery = computeDelivery(item, product);
    totals.material += pricing.subtotal;
    totals.install += install;
    totals.delivery += delivery;
    enriched.push({
      ...item,
      priceType: pricing.priceType,
      unitPrice: pricing.unitPrice,
      packagePrice: pricing.packagePrice,
      subtotal: pricing.subtotal,
      install,
      delivery,
      product
    });
  }
  totals.total = totals.material + totals.install + totals.delivery;
  return {items: enriched, totals, createdAt: Date.now()};
}
function renderCart(){
  const items = cart.getItems();
  const container = document.getElementById('cart-items');
  const empty = document.getElementById('cart-empty');
  if(items.length === 0){
    container.innerHTML = '';
    empty.style.display = 'block';
    document.getElementById('cart-totals').innerHTML = '';
    return;
  }
  empty.style.display = 'none';
  const project = serializeProject(items);
  localStorage.setItem(PROJECT_KEY, JSON.stringify(project));
  document.getElementById('cart-field').value = JSON.stringify(project);
  container.innerHTML = project.items.map(it=>{
    const p = it.product;
    const unit = p.measurementUnit === 'lf' ? 'lf' : p.measurementUnit === 'piece' ? 'piece' : 'sqft';
    const coverLabel = it.product.packageCoverage ? `${formatUnits(it.product.packageCoverage)} ${unit} / ${p.packageLabel || 'box'}` : '';
    const image = p.images?.[0] ? `../${p.images[0]}` : '';
    const priceLabel = it.packagePrice ? `${formatCurrency(it.packagePrice)} / ${p.packageLabel || 'box'}` : 'Call for price';
    const priceTypeLabel = it.priceType === 'backorder' ? 'Order-in' : 'In stock';
    return `
      <article class="cart-item" data-sku="${it.sku}" data-price-type="${it.priceType}">
        <div>${image ? `<img src="${image}" alt="${p.name}">` : ''}</div>
        <div>
          <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px;">
            <div>
              <div style="font-weight:700; color:#1f1f1f;">${p.name}</div>
              <div class="cart-item-meta">${p.collection || ''} ${coverLabel ? '· '+coverLabel : ''}</div>
            </div>
            <button type="button" class="remove btn btn-ghost" style="padding:6px 10px;">Remove</button>
          </div>
          <div class="cart-item-meta" style="margin:6px 0;">${priceLabel} · ${priceTypeLabel}</div>
          <div class="cart-actions">
            <label>Qty
              <input type="number" class="qty" min="1" value="${it.quantity}">
            </label>
            <label>Install
              <input type="checkbox" class="install-toggle" ${it.install ? 'checked' : ''}>
            </label>
            <label>Delivery
              <select class="delivery-select">
                ${(p.delivery?.zones || STORE_CONFIG.delivery?.zones || []).map(z=>`<option value="${z.id}" ${it.deliveryZone===z.id?'selected':''}>${z.label}${z.fee!=null ? ' — '+formatCurrency(z.fee) : ''}</option>`).join('')}
              </select>
            </label>
          </div>
          <div class="cart-item-meta">Subtotal: ${formatCurrency(it.subtotal)} | Install: ${formatCurrency(it.install)} | Delivery: ${formatCurrency(it.delivery)}</div>
        </div>
      </article>
    `;
  }).join('');

  document.getElementById('cart-totals').innerHTML = `
    <div class="cart-total-row"><span>Material</span><strong>${formatCurrency(project.totals.material)}</strong></div>
    <div class="cart-total-row"><span>Install</span><strong>${formatCurrency(project.totals.install)}</strong></div>
    <div class="cart-total-row"><span>Delivery</span><strong>${formatCurrency(project.totals.delivery)}</strong></div>
    <div class="cart-total-row" style="border-top:1px solid #e5dbd3; padding-top:6px;"><span>Total</span><strong>${formatCurrency(project.totals.total)}</strong></div>
  `;

  container.querySelectorAll('.qty').forEach(input=>{
    input.addEventListener('change', ()=>{
      const article = input.closest('.cart-item');
      const sku = article?.dataset?.sku;
      const priceType = article?.dataset?.priceType;
      const install = article?.querySelector('.install-toggle')?.checked;
      const deliveryZone = article?.querySelector('.delivery-select')?.value;
      if(!sku) return;
      cart.setItem(sku, parseInt(input.value)||1, priceType, {install, deliveryZone});
      renderCart();
    });
  });
  container.querySelectorAll('.install-toggle').forEach(input=>{
    input.addEventListener('change', ()=>{
      const article = input.closest('.cart-item');
      const sku = article?.dataset?.sku;
      const priceType = article?.dataset?.priceType;
      const qty = parseInt(article?.querySelector('.qty')?.value)||1;
      const deliveryZone = article?.querySelector('.delivery-select')?.value;
      cart.setItem(sku, qty, priceType, {install: input.checked, deliveryZone});
      renderCart();
    });
  });
  container.querySelectorAll('.delivery-select').forEach(sel=>{
    sel.addEventListener('change', ()=>{
      const article = sel.closest('.cart-item');
      const sku = article?.dataset?.sku;
      const priceType = article?.dataset?.priceType;
      const qty = parseInt(article?.querySelector('.qty')?.value)||1;
      const install = article?.querySelector('.install-toggle')?.checked;
      cart.setItem(sku, qty, priceType, {install, deliveryZone: sel.value});
      renderCart();
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

renderCart();

['name-cart','phone-cart','email-cart'].forEach(id=>{
  const el = document.getElementById(id);
  if(!el) return;
  const saved = localStorage.getItem('cart_'+id);
  if(saved) el.value = saved;
  el.addEventListener('input', ()=>localStorage.setItem('cart_'+id, el.value));
});

document.getElementById('cart-form')?.addEventListener('submit', async (e)=>{
  e.preventDefault();
  const form = e.currentTarget;
  const status = document.getElementById('cart-status');
  status.textContent = 'Sending…';
  const formData = new FormData(form);
  form.querySelectorAll('button,input,select,textarea').forEach(el=>el.disabled=true);
  try{
    const res = await fetch(form.action, {method:'POST', body: formData});
    const data = await res.json();
    status.textContent = data.data || 'Request sent';
    if(res.ok){
      cart.clear();
    }
  }catch(err){
    status.textContent = 'Something went wrong. Please try again.';
  }finally{
    form.querySelectorAll('button,input,select,textarea').forEach(el=>el.disabled=false);
  }
});
</script>
</body>
</html>
