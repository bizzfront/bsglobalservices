<?php
function load_store_products(): array {
  $floorings = json_decode(@file_get_contents(__DIR__.'/../floorings.json'), true) ?: [];
  $moldings = json_decode(@file_get_contents(__DIR__.'/../moldings.json'), true) ?: [];
  return array_merge($floorings, $moldings);
}

$products = load_store_products();
$base = '../';
$active = 'cart';
$contact_source = 'website_store';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Cart — B&S Floor Supply</title>
  <link rel="stylesheet" href="<?=$base?>style.css" />
</head>
<body>
<?php include $base.'includes/header.php'; ?>
<main class="container" style="padding-bottom:3vw; padding-top:3vw;">
  <h2 style="color:var(--burgundy);">Your cart</h2>
  <div class="cart-layout">
    <div class="cart-items">
      <div class="cart-items-head">
        <h3>Shopping cart</h3>
        <div id="cart-summary" class="cart-summary"></div>
      </div>
      <div id="cart-items" class="cart-items-list"></div>
      <p id="cart-empty" class="note">Your cart is empty.</p>
    </div>
    <form id="cart-form" action="<?=$base?>lead.php" method="POST" class="form cart-form">
      <div class="row">
        <div>
          <label for="name-cart">Full name</label>
          <input id="name-cart" name="name" required />
      </div>
      <div>
        <label for="phone-cart">Phone / WhatsApp</label>
        <input id="phone-cart" name="phone" required />
      </div>
    </div>
    <div class="row">
      <div>
        <label for="email-cart">Email</label>
        <input id="email-cart" type="email" name="email" required />
      </div>
    </div>
    <div class="row-1">
      <div>
        <label for="message-cart">Message</label>
        <textarea id="message-cart" name="message"></textarea>
      </div>
    </div>
    <input type="hidden" name="service" value="order" />
    <input type="hidden" name="form_name" value="B&S – Cart order" />
    <input type="hidden" name="source" value="<?=$contact_source?>" />
    <input type="hidden" name="cart" id="cart-field" />
    <div class="hero-cta">
      <button type="submit" class="btn btn-primary" id="send-cart">Send order</button>
    </div>
    <p id="cart-status" class="note" aria-live="polite"></p>
    </form>
  </div>
</main>
<?php include $base.'includes/footer.php'; ?>
<script>
const PRODUCTS = <?= json_encode(array_values($products)) ?>;
let previousCount = cart.getItems().length;
function formatCurrency(value){
  value = Number(value);
  if(!Number.isFinite(value) || value <= 0){
    return '';
  }
  return `$${value.toFixed(2)}`;
}

function formatUnits(value){
  const num = Number(value);
  if(!Number.isFinite(num)) return value ?? '';
  if(Math.abs(num) >= 1000 && Number.isInteger(num)){
    return num.toLocaleString();
  }
  return Number.isInteger(num) ? num.toString() : num.toLocaleString(undefined, {maximumFractionDigits: 2});
}

function renderCart(){
  const items = cart.getItems();
  const container = document.getElementById('cart-items');
  const empty = document.getElementById('cart-empty');
  const summary = document.getElementById('cart-summary');
  if(items.length === 0){
    container.innerHTML = '';
    empty.style.display = 'block';
    if(summary){
      summary.textContent = '';
    }
    if(previousCount > 0){
      previousCount = 0;
      window.location.href = 'index.php';
      return;
    }
    previousCount = 0;
    return;
  }
  empty.style.display = 'none';
  container.innerHTML = items.map(it=>{
    const p = PRODUCTS.find(pr=>pr.sku===it.sku) || {};
    const unit = (p.measurement_unit || (p.product_type === 'molding' ? 'lf' : 'sqft')).toLowerCase();
    const unitLabel = unit === 'lf' ? 'lf' : unit === 'piece' ? 'piece' : 'sqft';
    const coverageValue = p.coverage_per_box ?? p.sqft_per_box;
    const pricePerUnit = Number(p.price_per_unit ?? p.price_sqft);
    const pricePerBoxValue = Number(p.price_box ?? (pricePerUnit && coverageValue ? pricePerUnit * coverageValue : 0));
    const subtotal = pricePerBoxValue * it.quantity;
    const priceEach = formatCurrency(pricePerBoxValue);
    const priceUnit = pricePerUnit ? `${formatCurrency(pricePerUnit)} / ${unitLabel}` : '';
    const coverage = coverageValue != null ? `${formatUnits(coverageValue)} ${unitLabel} / box` : '';
    const callForPrice = !priceEach ? '<span class="cart-item-call">Call for price</span>' : '';
    const image = p.hoverImage ? `../${p.hoverImage}` : '';
    return `<div class="cart-item" data-sku="${it.sku}">
      <div class="cart-item-media">${image ? `<img src="${image}" alt="${(p.name || it.sku).replace(/"/g,'&quot;')}">` : ''}</div>
      <div class="cart-item-details">
        <div class="cart-item-title">${p.name || it.sku}</div>
        <div class="cart-item-meta">
          ${p.brand ? `<span>${p.brand}</span>` : ''}
          ${p.collection ? `<span>${p.collection}</span>` : ''}
          ${p.category ? `<span>${p.category}</span>` : ''}
          ${coverage ? `<span>${coverage}</span>` : ''}
        </div>
        <div class="cart-item-secondary">
          ${priceUnit ? `<span>${priceUnit}</span>` : ''}
          ${callForPrice}
        </div>
        <div class="cart-item-actions">
          <label class="cart-item-qty">Qty
            <input type="number" class="qty" min="1" value="${it.quantity}">
          </label>
          <button type="button" class="remove">Remove</button>
        </div>
      </div>
      <div class="cart-item-pricing">
        ${priceEach ? `<div class="cart-item-price">${priceEach}</div>` : ''}
        <div class="cart-item-subtotal">Subtotal: ${formatCurrency(subtotal) || '—'}</div>
      </div>
    </div>`;
  }).join('');
  if(summary){
    const total = items.reduce((sum, it)=>{
      const p = PRODUCTS.find(pr=>pr.sku===it.sku) || {};
      const coverageValue = p.coverage_per_box ?? p.sqft_per_box;
      const pricePerUnit = Number(p.price_per_unit ?? p.price_sqft);
      const pricePerBoxValue = Number(p.price_box ?? (pricePerUnit && coverageValue ? pricePerUnit * coverageValue : 0));
      return sum + (pricePerBoxValue * it.quantity);
    }, 0);
    summary.textContent = `Subtotal (${items.length} item${items.length !== 1 ? 's' : ''}): ${formatCurrency(total) || 'Call for price'}`;
  }
  previousCount = items.length;
  container.querySelectorAll('.qty').forEach(input=>{
    input.addEventListener('change', ()=>{
      const wrapper = input.closest('.cart-item');
      const sku = wrapper?.dataset.sku;
      if(!sku) return;
      cart.setItem(sku, parseInt(input.value) || 1);
      renderCart();
    });
  });
  container.querySelectorAll('.remove').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const wrapper = btn.closest('.cart-item');
      const sku = wrapper?.dataset.sku;
      if(!sku) return;
      cart.removeItem(sku);
      renderCart();
    });
  });
}

document.addEventListener('cartchange', renderCart);
renderCart();

document.getElementById('cart-form')?.addEventListener('submit', async e=>{
  e.preventDefault();
  const form = e.target;
  const status = document.getElementById('cart-status');
  const items = cart.getItems();
  if(items.length === 0){
    status.textContent = 'Cart is empty.';
    return;
  }
  form.cart.value = JSON.stringify(items);
  const summary = items.map(it=>{
    const p = PRODUCTS.find(pr=>pr.sku===it.sku) || {};
    return `${p.name || it.sku} x ${it.quantity}`;
  }).join(', ');
  const msg = document.getElementById('message-cart');
  msg.value = `Order request: ${summary}\n` + (msg.value || '');
  status.textContent = 'Sending your request…';
  const formData = new FormData(form);
  Array.from(form.elements).forEach(el=>el.disabled=true);
  try{
    const res = await fetch(form.action, {method:'POST', body:formData});
    const data = await res.json();
    status.textContent = data.data || 'Request sent.';
    if(res.ok && data.code === '01'){
      cart.clear();
      form.reset();
      renderCart();
    }
  }catch(err){
    status.textContent = 'An error occurred. Please try again later.';
  }finally{
    Array.from(form.elements).forEach(el=>el.disabled=false);
  }
});
</script>
</body>
</html>
