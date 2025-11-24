<?php
require_once __DIR__.'/utils.php';
$products = array_map('normalize_store_product', load_store_products());
$storeConfig = load_store_config();
$base = '../';
$active = 'cart';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Project details — B&S Floor Supply</title>
  <link rel="stylesheet" href="<?=$base?>style.css" />
  <style>
    .project-shell { background:#f6f2ec; }
    .project-grid { display:grid; grid-template-columns: 1.4fr 1fr; gap:18px; }
    .card { background:#fff; border-radius:16px; padding:16px; box-shadow:0 10px 30px rgba(0,0,0,0.08); }
    .proj-item { display:flex; justify-content:space-between; margin-bottom:10px; color:#504642; }
    .proj-total { border-top:1px solid #e5dbd3; padding-top:10px; font-weight:700; color:#591320; display:flex; justify-content:space-between; }
    .form label { display:block; margin-top:10px; font-weight:600; color:#4b4240; }
    .form input, .form textarea, .form select { width:100%; padding:10px; border:1px solid #d2c8c1; border-radius:10px; margin-top:4px; }
    @media (max-width: 880px){ .project-grid { grid-template-columns: 1fr; } }
  </style>
</head>
<body class="project-shell">
<?php include $base.'includes/header.php'; ?>
<main class="container" style="padding:32px 0 40px;">
  <div class="sec-head">
    <div>
      <div class="eyebrow">Checkout</div>
      <h2 style="color:var(--burgundy);">Project details</h2>
    </div>
  </div>
  <div class="project-grid">
    <section class="card">
      <h3 style="margin-bottom:12px;">Your project</h3>
      <div id="project-items"></div>
      <div id="project-totals" style="margin-top:10px;"></div>
    </section>
    <section class="card">
      <h3 style="margin-bottom:12px;">Contact & scheduling</h3>
      <form id="project-form" class="form" action="<?=$base?>lead.php" method="POST">
        <label>Name<input name="name" required></label>
        <label>Phone / WhatsApp<input name="phone" required></label>
        <label>Email<input type="email" name="email" required></label>
        <label>Project type
          <select name="project_type">
            <option value="">Select</option>
            <option>Residential</option>
            <option>Commercial</option>
            <option>Remodel</option>
          </select>
        </label>
        <label>Desired start date<input type="date" name="start_date"></label>
        <label>Notes<textarea name="message" rows="3" placeholder="Delivery address, units, building instructions…"></textarea></label>
        <input type="hidden" name="service" value="project_request" />
        <input type="hidden" name="form_name" value="B&S – Project checkout" />
        <input type="hidden" name="source" value="website_store" />
        <input type="hidden" name="cart" id="project-cart-field" />
        <button type="submit" class="btn btn-primary" style="width:100%; margin-top:10px;">Send project</button>
        <p id="project-status" class="note" aria-live="polite"></p>
      </form>
    </section>
  </div>
</main>
<?php include $base.'includes/footer.php'; ?>
<script>
const STORE_PRODUCTS = <?= json_encode(array_values($products)) ?>;
const STORE_CONFIG = <?= json_encode($storeConfig) ?>;
const PROJECT_KEY = 'bs_project';
function formatCurrency(value){
  const num = Number(value);
  return Number.isFinite(num) ? `$${num.toFixed(2)}` : '$0.00';
}
const project = (()=>{
  try{ return JSON.parse(localStorage.getItem(PROJECT_KEY) || '{}'); }catch(e){ return {}; }
})();
if(!project.items || !project.items.length){
  window.location.href = 'cart.php';
}
document.getElementById('project-cart-field').value = JSON.stringify(project);
const itemsContainer = document.getElementById('project-items');
itemsContainer.innerHTML = project.items.map(it=>{
  const p = it.product;
  return `<div class="proj-item"><span>${p.name} × ${it.quantity} ${p.packageLabelPlural || 'boxes'} (${it.priceType})</span><span>${formatCurrency(it.subtotal + it.install + it.delivery)}</span></div>`;
}).join('');
const totals = project.totals || {material:0,install:0,delivery:0,total:0};
document.getElementById('project-totals').innerHTML = `
  <div class="proj-item"><span>Material</span><span>${formatCurrency(totals.material)}</span></div>
  <div class="proj-item"><span>Install</span><span>${formatCurrency(totals.install)}</span></div>
  <div class="proj-item"><span>Delivery</span><span>${formatCurrency(totals.delivery)}</span></div>
  <div class="proj-total"><span>Total</span><span>${formatCurrency(totals.total)}</span></div>
`;
document.getElementById('project-form')?.addEventListener('submit', async (e)=>{
  e.preventDefault();
  const form = e.currentTarget;
  const status = document.getElementById('project-status');
  status.textContent = 'Sending…';
  const fd = new FormData(form);
  form.querySelectorAll('button,input,select,textarea').forEach(el=>el.disabled=true);
  try{
    const res = await fetch(form.action, {method:'POST', body: fd});
    const data = await res.json();
    status.textContent = data.data || 'Project sent';
    if(res.ok){
      localStorage.setItem(PROJECT_KEY, JSON.stringify(project));
      window.location.href = 'thank-you.php';
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
