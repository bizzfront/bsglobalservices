<?php
$base = '../';
$active = 'cart';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thank you — B&S Floor Supply</title>
  <link rel="stylesheet" href="<?=$base?>style.css" />
  <style>
    .thanks-shell { background:#f6f2ec; min-height:100vh; }
    .thank-card { background:#fff; max-width:720px; margin:32px auto; padding:20px; border-radius:18px; box-shadow:0 14px 40px rgba(0,0,0,0.08); }
    .proj-item { display:flex; justify-content:space-between; margin-bottom:8px; color:#4f4542; }
    .proj-total { border-top:1px solid #e5dbd3; padding-top:8px; font-weight:700; color:#591320; display:flex; justify-content:space-between; }
  </style>
</head>
<body class="thanks-shell">
<?php include $base.'includes/header.php'; ?>
<main class="container">
  <div class="thank-card">
    <div class="eyebrow" data-i18n="store_project_submitted">Project submitted</div>
    <h2 style="color:var(--burgundy);" data-i18n="store_thank_you">Thank you!</h2>
    <p data-i18n="store_thank_you_message">We received your project. A B&S specialist will confirm stock, delivery windows and installation scheduling shortly.</p>
    <div id="project-summary"></div>
    <div style="margin-top:14px;">
      <a class="btn btn-primary" href="index.php" data-i18n="store_back_to_store">Back to store</a>
    </div>
  </div>
</main>
<?php include $base.'includes/footer.php'; ?>
<script>
const project = (()=>{ try { return JSON.parse(localStorage.getItem('bs_project') || '{}'); } catch(e){ return {}; }})();
function formatCurrency(value){ const num = Number(value); return Number.isFinite(num) ? `$${num.toFixed(2)}` : '$0.00'; }
const container = document.getElementById('project-summary');
if(!project.items || !project.items.length){
  const t = (key, fallback = '') => window.bsI18n?.t?.(key) || fallback;
  container.innerHTML = `<p class="note">${t('store_no_project_data', 'No project data found.')}</p>`;
}else{
  container.innerHTML = `
    <div class="proj-list">${project.items.map(it=>`<div class="proj-item"><span>${it.product?.name || it.sku} × ${it.quantity}</span><span>${formatCurrency((it.subtotal||0)+(it.install||0)+(it.delivery||0))}</span></div>`).join('')}</div>
    <div class="proj-total"><span data-i18n="store_total_label">Total</span><span>${formatCurrency(project.totals?.total)}</span></div>
  `;
  window.bsI18n?.applyTranslations?.(container);
}
</script>
</body>
</html>
