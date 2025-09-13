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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($product['name']) ?> — B&S Floor Supply</title>
  <link rel="stylesheet" href="../style.css" />
</head>
<body>
  <div class="topbar">
    <div class="container wrap">
      <div id="topbar-text">Available in English & Spanish · También atendemos en español</div>
      <div class="lang-toggle" aria-label="Language switch">
        <button class="lang-btn active" id="lang-en">EN</button>
        <button class="lang-btn" id="lang-es">ES</button>
      </div>
    </div>
  </div>
  <header class="site-header" aria-label="Main">
    <div class="container nav">
      <a href="../" class="brand" aria-label="B&S Floor Supply">
        <span class="logo-bs logo-bs--full" role="img" aria-label="B&S Floor Supply logo"></span>
      </a>
      <nav aria-label="Primary">
        <button class="burger" aria-label="Toggle menu" aria-controls="menu" aria-expanded="false" id="burger">
          <span></span><span></span><span></span>
        </button>
        <div id="menu" class="menu" role="menu">
          <a href="../services/flooring-install/" role="menuitem">Flooring Install</a>
          <a href="./" role="menuitem">Store</a>
        </div>
      </nav>
    </div>
  </header>
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

  <section id="contact" class="sec--light">
    <div class="container">
      <div class="sec-head">
        <div>
          <div class="eyebrow" data-i18n="ey_contact">Let’s get your estimate</div>
          <h2 data-i18n="h_contact">Contact & free estimate</h2>
        </div>
        <a href="https://wa.me/16892968515?text=Hi%20B%26S%20Floor%20Supply%2C%20I%27d%20like%20a%20free%20estimate." class="pill" id="cta-wa-pill" target="_blank" rel="noopener" data-i18n="cta_whatsapp">Chat on WhatsApp</a>
      </div>
      <div class="contact">
        <form class="form" id="lead-form-bottom" action="../lead.php" method="POST" aria-labelledby="contact-bottom">
          <div class="row">
            <div>
              <label for="name-bottom" data-i18n="form_name">Full name</label>
              <input id="name-bottom" name="name" placeholder="Your name" maxlength="255" required />
            </div>
            <div>
              <label for="phone-bottom" data-i18n="form_phone">Phone / WhatsApp</label>
              <input id="phone-bottom" name="phone" placeholder="+1 (689) 296-8515" maxlength="255" required />
            </div>
          </div>
          <div class="row">
            <div>
              <label for="email-bottom">Email</label>
              <input id="email-bottom" type="email" name="email" placeholder="info@globalservices.com" maxlength="255" required />
            </div>
            <div>
              <label for="service-bottom" data-i18n="form_service">Service</label>
              <select id="service-bottom" name="service">
                <option value="lvp" data-i18n="opt_lvp">Waterproof LVP – supply & install</option>
                <option value="install" data-i18n="opt_install">Installation only (Laminate / Vinyl / Hardwood)</option>
                <option value="other" data-i18n="opt_other">Other flooring</option>
              </select>
            </div>
          </div>
          <div class="row-1">
            <div>
              <label for="message-bottom" data-i18n="form_details">Project details</label>
              <textarea id="message-bottom" name="message" placeholder="Area size, rooms, preferred tone…" maxlength="255" required></textarea>
            </div>
          </div>
          <input type="hidden" name="form_name" value="B&S – Web Lead (bottom)" />
          <input type="hidden" name="source" value="website_store" />
          <p class="note" data-i18n="form_note">By sending, you agree to be contacted via WhatsApp, phone or email.</p>
          <div class="hero-cta">
            <button type="submit" class="btn btn-primary" id="send-btn-bottom" data-i18n="cta_send">Send request</button>
            <a class="btn btn-ghost" href="https://wa.me/16892968515?text=Hi%20B%26S%20Floor%20Supply%2C%20I%20need%20a%20quote." target="_blank" rel="noopener" id="cta-wa-form" data-i18n="cta_whatsapp">WhatsApp now</a>
          </div>
          <p id="form-status-bottom" class="note hide" aria-live="polite"></p>
        </form>
        <aside class="card">
          <h3 data-i18n="info_t">Contact info</h3>
          <p><strong>Phone (WhatsApp):</strong> <a href="https://wa.me/16892968515" target="_blank" rel="noopener">+1 (689) 296-8515</a></p>
          <p><strong>Alt. phone:</strong> +1 (407) 225-1284</p>
          <p><strong>Email:</strong> <a href="mailto:info@globalservices.com">info@globalservices.com</a></p>
          <p><strong>Service area:</strong> Orlando, FL</p>
          <div class="hero-cta">
            <a href="https://instagram.com/bsfloorsupply" class="btn btn-ghost" target="_blank" rel="noopener">Instagram</a>
            <a href="https://facebook.com/BSGlobalServices" class="btn btn-ghost" target="_blank" rel="noopener">Facebook</a>
          </div>
          <hr />
          <p><small id="info_es">Prefer español? <em>También atendemos en español.</em></small></p>
        </aside>
      </div>
    </div>
  </section>
  <footer id="footer">
    <div class="container fgrid">
      <div>
        <div class="brand" style="margin-bottom:.6rem">
          <span class="logo-bs logo-bs--white" role="img" aria-label="B&S Floor Supply logo"></span>
          <span>B&S Floor Supply</span>
        </div>
        <p id="footer_pitch">Waterproof LVP — plus installation of laminate, vinyl and hardwood. Bilingual team. Fast, neat, reliable.</p>
      </div>
      <div>
        <h4 style="margin-top:0" data-i18n="foot_nav">Explore</h4>
        <nav>
          <a href="/#benefits" data-i18n="nav_benefits">Benefits</a><br/>
          <a href="/#materials" data-i18n="nav_types">Flooring Types</a><br/>
          <a href="/#popular" data-i18n="nav_popular">Popular LVP</a><br/>
          <a href="/#cases" data-i18n="nav_use">Use cases</a><br/>
          <a href="/#reviews" data-i18n="nav_reviews">Reviews</a><br/>
          <a href="/#faq" data-i18n="nav_faq">FAQ</a><br/>
          <a href="/terms.html">Terms & Conditions</a>
        </nav>
      </div>
      <div>
        <h4 style="margin-top:0" data-i18n="foot_contact">Get in touch</h4>
        <p>WhatsApp: <a href="https://wa.me/16892968515" target="_blank" rel="noopener">+1 (689) 296-8515</a></p>
        <p>Alt. phone: +1 (407) 225-1284</p>
        <p>Email: <a href="mailto:info@globalservices.com">info@globalservices.com</a></p>
        <p>Orlando, Florida</p>
        <p><a href="https://instagram.com/bsfloorsupply" target="_blank" rel="noopener">Instagram</a> · <a href="https://facebook.com/BSGlobalServices" target="_blank" rel="noopener">Facebook</a></p>
      </div>
    </div>
    <div class="container ft">
      © <span id="year"></span> B&S Floor Supply. All rights reserved.
    </div>
  </footer>
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
          const res = await fetch(form.action || '../lead.php', {method:'POST', body:formData});
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
