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
    <?php
      $priceSqft = isset($product['price_sqft']) && $product['price_sqft'] !== null ? '$'.number_format($product['price_sqft'],2) : '';
      $priceBox = isset($product['price_box']) && $product['price_box'] !== null ? '$'.number_format($product['price_box'],2) : '';
    ?>
    <div class="store-price">
      <?php if($priceSqft): ?>
        <div><b><?= $priceSqft ?></b><span class="store-per">/sqft</span></div>
      <?php else: ?>
        <div><b>Call for price</b></div>
      <?php endif; ?>
      <?php if($priceBox): ?>
        <div><span class="store-per">≈ <?= $priceBox ?> / box</span></div>
      <?php endif; ?>
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
        <?php if($product['thickness_mm']): ?><li><strong>Thickness:</strong> <?= $product['thickness_mm'] ?> mm</li><?php endif; ?>
        <?php if($product['wear_layer_mil']): ?><li><strong>Wear layer:</strong> <?= $product['wear_layer_mil'] ?> mil</li><?php endif; ?>
        <?php if($product['width_in'] && $product['length_in']): ?><li><strong>Plank size:</strong> <?= $product['width_in'] ?>×<?= $product['length_in'] ?> in</li><?php endif; ?>
        <?php if($product['core']): ?><li><strong>Core:</strong> <?= htmlspecialchars($product['core']) ?></li><?php endif; ?>
        <?php if($product['pad']): ?><li><strong>Pad:</strong> <?= htmlspecialchars($product['pad']) ?> <?= htmlspecialchars($product['pad_material'] ?? '') ?></li><?php endif; ?>
        <?php if($product['installation']): ?><li><strong>Installation:</strong> <?= htmlspecialchars($product['installation']) ?></li><?php endif; ?>
        <?php if($product['waterproof']): ?><li><strong>Waterproof:</strong> <?= htmlspecialchars($product['waterproof']) ?></li><?php endif; ?>
        <?php if($product['scratch_resistant']): ?><li><strong>Scratch resistant:</strong> <?= htmlspecialchars($product['scratch_resistant']) ?></li><?php endif; ?>
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
