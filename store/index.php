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
  <title>B&S Floor Supply — Store</title>
  <link rel="stylesheet" href="<?=$base?>style.css" />
</head>
<body>
<?php include $base.'includes/header.php'; ?>

  <main class="container" style="margin-bottom:2vw;">
    <div class="sec-head">
        <div style="margin-top:1.6vw;" >
          <div class="eyebrow" data-i18n="ey_popular">Popular Vinyl Plank Options</div>
          <h2 data-i18n="h_popular">Store</h2>
        </div>
        <span class="pill">Store</span>
    </div>
    <div class="grid-3">
    <?php foreach($products as $p): ?>
      <article class="card product-card">
        <?php if(!empty($p['promo'])): ?>
          <div class="store-promo"><span><?= htmlspecialchars($p['promo']) ?></span></div>
        <?php endif; ?>
        <a href="product.php?sku=<?= urlencode($p['sku']) ?>">
          <div class="img-wrapper">
            <img src="../<?= $p['image'] ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="default">
            <img src="../<?= $p['hoverImage'] ?>" alt="" class="hover">
            
          </div>
        </a>
        <h3 style="margin:.7rem 0 0"><?= htmlspecialchars($p['name']) ?></h3>
        <div class="hero-cta">
          <a href="product.php?sku=<?= urlencode($p['sku']) ?>" class="btn btn-ghost">View Details</a>
          <a href="#contact" class="btn btn-primary">Get install Quote</a>
        </div>
      </article>
    <?php endforeach; ?>
    </div>
  </main>

<?php include $base.'includes/contact.php'; ?>

  <!-- Footer -->
<?php include $base.'includes/footer.php'; ?>

  <script>
    const burger = document.getElementById('burger');
    const menu = document.getElementById('menu');
    burger?.addEventListener('click', () => {
      const open = menu.classList.toggle('show');
      burger.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
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
    // Dynamic year
    document.getElementById('year').textContent = new Date().getFullYear();
  </script>
</body>
</html>
