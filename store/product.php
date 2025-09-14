<?php
$products = json_decode(file_get_contents(__DIR__.'/../products.json'), true);
$sku = $_GET['sku'] ?? '';
$product = null;
foreach ($products as $p) {
  if ($p['sku'] === $sku) {
    $product = $p;
    break;
  }
}
if (!$product) {
  http_response_code(404);
  echo 'Product not found';
  exit;
}
$baseDir = dirname(dirname($product['image']));
$basePath = __DIR__ . '/../' . $baseDir;
$images = array_merge(glob($basePath.'/*.png'), glob($basePath.'/*/*.png'));
$images = array_map(function($p){return str_replace(__DIR__.'/../','',$p);}, $images);
$selected = array_unique([$product['image'], $product['hoverImage']]);
$others = array_diff($images, $selected);
shuffle($others);
$selected = array_merge($selected, array_slice($others, 0, max(0, 4 - count($selected))));
$base = '../';
$active = 'store';
$contact_source = 'website_store';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($product['name']) ?> — B&S Floor Supply</title>
  <link rel="stylesheet" href="<?=$base?>style.css" />
</head>
<body>
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
    <div class="tabs">
      <button class="tab-btn active" data-target="specs">Specifications</button>
      <button class="tab-btn" data-target="benefits">Benefits</button>
      <button class="tab-btn" data-target="use">Use Areas</button>
      <button class="tab-btn" data-target="logistics">Logistics</button>
    </div>
    <div id="specs" class="tab-content active"><?= $product['Technical_Specifications'] ?></div>
    <div id="benefits" class="tab-content"><?= $product['Ke_Benefits'] ?></div>
    <div id="use" class="tab-content"><?= $product['Recommended_Use_Areas'] ?></div>
    <div id="logistics" class="tab-content"><?= $product['Logistics'] ?></div>
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
