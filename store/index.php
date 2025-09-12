<?php $products = json_decode(file_get_contents(__DIR__.'/../products.json'), true); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>B&S Floor Supply — Store</title>
  <link rel="stylesheet" href="../style.css" />
</head>
<body>
  <!-- Top bilingual bar -->
  <div class="topbar">
    <div class="container wrap">
      <div id="topbar-text">Available in English & Spanish · También atendemos en español</div>
      <div class="lang-toggle" aria-label="Language switch">
        <button class="lang-btn active" id="lang-en">EN</button>
        <button class="lang-btn" id="lang-es">ES</button>
      </div>
    </div>
  </div>

  <!-- Header -->
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
          <a href="#" role="menuitem">Store</a>
        </div>
      </nav>
    </div>
  </header>

  <main class="container">
    <h1>Store</h1>
    <div class="grid-3">
    <?php foreach($products as $p): ?>
      <article class="card product-card">
        <a href="product.php?sku=<?= urlencode($p['sku']) ?>">
          <div class="img-wrapper">
            <img src="../<?= $p['image'] ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="default">
            <img src="../<?= $p['hoverImage'] ?>" alt="" class="hover">
            <?php if(!empty($p['promo'])): ?>
              <span class="promo-badge"><?= htmlspecialchars($p['promo']) ?></span>
            <?php endif; ?>
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

  <!-- Footer -->
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
    // Dynamic year
    document.getElementById('year').textContent = new Date().getFullYear();
  </script>
</body>
</html>
