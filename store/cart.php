<?php
$products = json_decode(file_get_contents(__DIR__.'/../products.json'), true);
$base = '../';
$active = 'store';
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
      <div id="cart-items"></div>
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
const PRODUCTS = <?= json_encode($products) ?>;
function renderCart(){
  const items = cart.getItems();
  const container = document.getElementById('cart-items');
  const empty = document.getElementById('cart-empty');
  if(items.length === 0){
    container.innerHTML = '';
    empty.style.display = 'block';
    return;
  }
  empty.style.display = 'none';
  container.innerHTML = items.map(it=>{
    const p = PRODUCTS.find(pr=>pr.sku===it.sku) || {};
    const pricePerBox = p.price_box || (p.price_sqft && p.sqft_per_box ? p.price_sqft * p.sqft_per_box : 0);
    const subtotal = pricePerBox * it.quantity;
    return `<div class="cart-item" data-sku="${it.sku}">
      <span>${p.name || it.sku}</span>
      <input type="number" class="qty" min="1" value="${it.quantity}">
      <span class="sub">$${subtotal.toFixed(2)}</span>
      <button type="button" class="remove">Remove</button>
    </div>`;
  }).join('');
  container.querySelectorAll('.qty').forEach(input=>{
    input.addEventListener('change', ()=>{
      const sku = input.parentElement.getAttribute('data-sku');
      cart.setItem(sku, parseInt(input.value) || 1);
      renderCart();
    });
  });
  container.querySelectorAll('.remove').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const sku = btn.parentElement.getAttribute('data-sku');
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
