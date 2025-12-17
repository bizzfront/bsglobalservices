<?php
$base = '';
$active = 'home';
$contact_source = 'website_home';
require_once __DIR__.'/store/utils.php';
$homeStoreProducts = array_map('normalize_store_product', load_store_products());
$homeFloorings = array_values(array_filter($homeStoreProducts, fn($p)=>($p['productType'] ?? 'flooring') === 'flooring'));
$homeFloorings = array_values(array_filter($homeFloorings, function($p){
  $price = $p['pricing']['activePricePerUnit'] ?? $p['pricing']['finalPriceStockPerUnit'] ?? $p['pricing']['finalPriceBackorderPerUnit'] ?? null;
  return $price !== null;
}));
$homeLatestProducts = array_reverse(array_slice($homeFloorings, -6));
$homeStoreConfig = load_store_config();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Waterproof LVP Flooring in Orlando | B&S Floor Supply – Sales & Installation</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="description" content="B&S Floor Supply sells waterproof LVP and installs laminate, vinyl and hardwood floors in Orlando. Fast, reliable installation and post-installation support. Get a free estimate." />
  <!-- Open Graph -->
  <meta property="og:title" content="B&S Floor Supply – Waterproof LVP | Sales & Installation" />
  <meta property="og:description" content="Waterproof LVP in Orlando. Plus installation of laminate, vinyl and hardwood. Free estimate by WhatsApp." />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="https://bsglobalservices.com/" />
  <meta property="og:image" content="https://bsglobalservices.com/newweb/assets/og-cover.jpg" />
  <!-- Twitter -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="B&S Floor Supply – Waterproof LVP | Sales & Installation" />
  <meta name="twitter:description" content="Waterproof LVP in Orlando. Plus installation of laminate, vinyl and hardwood." />
  <meta name="twitter:image" content="https://bsglobalservices.com/newweb/assets/og-cover.jpg" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?=$base?>style.css" />

  <!-- JSON-LD LocalBusiness -->
  <script type="application/ld+json">
  {
    "@context":"https://schema.org",
    "@type":"LocalBusiness",
    "name":"B&S Floor Supply",
    "description":"Waterproof LVP flooring in Orlando – sales and installation of laminate, vinyl and hardwood.",
    "url":"https://bsglobalservices.com/",
    "telephone":"+1-689-296-8515",
    "address":{"@type":"PostalAddress","addressLocality":"Orlando","addressRegion":"FL","addressCountry":"US"},
    "areaServed":"Orlando, Florida",
    "sameAs":["https://www.instagram.com/bsfloorsupply","https://www.facebook.com/BSGlobalServices"]
  }
  </script>

  <!-- GA4 / GTM (opcional) -->
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    // gtag('js', new Date());
    // gtag('config', 'G-XXXXXXXXXX');
  </script>
  <!-- GTM opcional -->
  <!--
  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-XXXXXXX');</script>
  -->
</head>

<body>
<?php include $base.'includes/header.php'; ?>

  <!-- Hero with form -->
  <main id="main" class="container hero">
   
    <div>
      <span class="badge" id="badge-hero">Waterproof LVP • Orlando, FL</span>

      <!-- Brand strip with logo -->
      <div class="brand-hero">
        <span class="logo-bs logo-bs--full" role="img" aria-label="B&S Floor Supply logo"></span>
        <div class="brand-hero-copy">
          <strong>B&S Floor Supply</strong>
          <small>Waterproof LVP • Installation of Laminate, Vinyl & Hardwood</small>
        </div>
      </div>

      <h1 class="h1" id="hero-title">Waterproof LVP Flooring — <br />Sales & Expert Installation</h1>
      <!--  style="color: white; text-shadow: 1px 1px 2px #000;" -->
      <p class="lead" id="hero-lead">
        Modern floors that stand up to daily life. We sell Waterproof LVP and install laminate, vinyl and hardwood with a bilingual team.
      </p>
      <div class="hero-cta">
        <a href="#contact" class="btn btn-primary" id="cta-hero-estimate" data-i18n="cta_estimate">Get a free estimate</a>
        <a href="#materials" class="btn btn-ghost" id="cta-hero-types" data-i18n="cta_types">Flooring Types</a>
      </div>
      <div class="stat">
        <div class="kpi"><strong>24–72h</strong><br><small id="kpi1">Typical install window</small></div>
        <div class="kpi"><strong id="kpi2a">Warranty</strong><br><small id="kpi2b">On installation</small></div>
        <div class="kpi"><strong>EN / ES</strong><br><small id="kpi3">We also speak Spanish</small></div>
      </div>
      <div class="pill" style="margin-top:.8rem" id="dual-focus">We sell Waterproof LVP & install Laminate, Vinyl and Hardwood</div>
    </div>

    <!-- Contact form (hero) -->
    <aside>
      <form class="form" id="lead-form-hero" action="<?=$base?>lead.php" method="POST" aria-labelledby="contact-hero">
        <h3 style="margin-top:0" data-i18n="form_title">Get your free estimate</h3>
        <div class="row">
          <div>
            <label for="name-hero" data-i18n="form_name">Full name</label>
            <input id="name-hero" name="name" placeholder="Your name" maxlength="255" required />
          </div>
          <div>
            <label for="phone-hero" data-i18n="form_phone">Phone / WhatsApp</label>
            <input id="phone-hero" name="phone" placeholder="+1 (689) 296-8515" maxlength="255" required />
          </div>
        </div>
        <div class="row">
          <div>
            <label for="email-hero">Email</label>
            <input id="email-hero" type="email" name="email" placeholder="info@globalservices.com" maxlength="255" required />
          </div>
          <div>
            <label for="service-hero" data-i18n="form_service">Service</label>
            <select id="service-hero" name="service">
              <option value="lvp" data-i18n="opt_lvp">Waterproof LVP – supply & install</option>
              <option value="install" data-i18n="opt_install">Installation only (Laminate / Vinyl / Hardwood)</option>
              <option value="other" data-i18n="opt_other">Other flooring</option>
            </select>
          </div>
        </div>
        <div class="row-1">
          <div>
            <label for="message-hero" data-i18n="form_details">Project details</label>
            <textarea id="message-hero" name="message" placeholder="Area size, rooms, preferred tone (e.g., Natural Maple / Northern Grey)…" maxlength="255" required></textarea>
          </div>
        </div>
        <input type="hidden" name="form_name" value="B&S – Web Lead (hero)" />
        <input type="hidden" name="source" value="website_home" />
        <p class="note" data-i18n="form_note">By sending, you agree to be contacted via WhatsApp, phone or email.</p>
        <div class="hero-cta">
          <button type="submit" class="btn btn-primary" id="send-btn-hero" data-i18n="cta_send">Send request</button>
          <a class="btn btn-ghost" href="https://wa.me/16892968515?text=Hi%20B%26S%20Floor%20Supply%2C%20I%20need%20a%20quote." target="_blank" rel="noopener" id="cta-wa-hero" data-i18n="cta_whatsapp">WhatsApp now</a>
        </div>
        <p id="form-status-hero" class="note hide" aria-live="polite"></p>
      </form>
    </aside>
  </main>

  <!-- Benefits -->
  <section id="benefits" class="sec--light">
    <div class="container">
      <div class="sec-head">
        <div>
          <div class="eyebrow" data-i18n="ey_benefits">Why homeowners choose B&S</div>
          <h2 data-i18n="h_benefits">Benefits that matter every day</h2>
        </div>
        <a href="#materials" class="pill" id="pill-benefits" data-i18n="pill_browse">Browse flooring types →</a>
      </div>
      <div class="grid-3">
        <article class="card">
          <div class="icon">💧</div>
          <h3 data-i18n="b1_t">Waterproof & easy to clean</h3>
          <p data-i18n="b1_d">Spills, pets and daily traffic aren’t a problem. Low-maintenance finishes that look great.</p>
        </article>
        <article class="card">
          <div class="icon">⚡</div>
          <h3 data-i18n="b2_t">Fast, neat installation</h3>
          <p data-i18n="b2_d">We protect your space, work clean and finish quickly so you can enjoy your home sooner.</p>
        </article>
        <article class="card">
          <div class="icon">🌐</div>
          <h3 data-i18n="b3_t">Bilingual team (EN/ES)</h3>
          <p data-i18n="b3_d">We attend in English and Spanish to make every step clear and comfortable.</p>
        </article>
      </div>
    </div>
  </section>

  <!-- Materials (white + translucent image) -->
  <section id="materials" class="sec--white sec--image" style="--bg:url('https://images.unsplash.com/photo-1519710164239-da123dc03ef4?q=80&w=1600&auto=format&fit=crop')">
    <div class="container">
      <div class="sec-head">
        <div>
          <div class="eyebrow" data-i18n="ey_materials">Flooring Materials We Install</div>
          <h2 data-i18n="h_materials">Laminate · Vinyl · Hardwood</h2>
        </div>
        <a href="#contact" class="pill" data-i18n="pill_quote">Get a quote</a>
      </div>
      <div class="materials">
        <article class="mat">
          <img src="images/floor_types/Laminate.png" alt="Laminate flooring in living room" />
          <div class="body">
            <strong data-i18n="m1_t">Laminate</strong>
            <p data-i18n="m1_d">Budget-friendly, scratch-resistant and modern looks. Great for high-traffic areas.</p>
            <a href="#contact" class="btn btn-ghost" data-i18n="m_cta">Request installation</a>
          </div>
        </article>
        <article class="mat">
          <img src="images/floor_types/Vinyl.png" alt="Vinyl flooring in bedroom" />
          <div class="body">
            <strong data-i18n="m2_t">Vinyl (LVP)</strong>
            <p data-i18n="m2_d">Water-resistant, quiet underfoot and easy to maintain. Ideal for kitchens & baths.</p>
            <a href="#contact" class="btn btn-ghost" data-i18n="m_cta">Request installation</a>
          </div>
        </article>
        <article class="mat">
          <img src="images/floor_types/Hardwood.png" alt="Hardwood flooring close-up" />
          <div class="body">
            <strong data-i18n="m3_t">Hardwood</strong>
            <p data-i18n="m3_d">Timeless aesthetics and long-term value. Professional prep and finishing required.</p>
            <a href="#contact" class="btn btn-ghost" data-i18n="m_cta">Request installation</a>
          </div>
        </article>
      </div>
    </div>
  </section>

  <!-- Popular Vinyl Plank Options (white + translucent image) -->
  <section id="popular" class="sec--white sec--image" style="--bg:url('https://images.unsplash.com/photo-1600566752355-35792bedcfea?q=80&w=1600&auto=format&fit=crop')">
    <div class="container">
      <div class="sec-head">
        <div>
          <div class="eyebrow" data-i18n="ey_popular">Popular Vinyl Plank Options</div>
          <h2 data-i18n="h_popular">Top picks our customers love</h2>
        </div>
        <span class="pill">Waterproof · Durable · Easy care</span>
      </div>

      <div class="carousel-wrap">
        <button class="carousel-btn prev" id="popular-prev" aria-label="Scroll left">&#10094;</button>
        <div class="popular-carousel" id="popular-carousel"></div>
          <button class="carousel-btn next" id="popular-next" aria-label="Scroll right">&#10095;</button>
      </div>
    </div>
  </section>

  <!-- Use cases (beige + translucent image) -->
<?php include $base.'includes/portfolio.php'; ?>

  <!-- Testimonials (dark inverse) -->
<?php include $base.'includes/reviews.php'; ?>

  <!-- FAQ -->
  <section id="faq" class="sec--white">
    <div class="container">
      <div class="sec-head">
        <div>
          <div class="eyebrow" data-i18n="ey_faq">Questions</div>
          <h2 data-i18n="h_faq">FAQ – Waterproof LVP</h2>
        </div>
        <a href="#contact" class="pill" data-i18n="pill_quote">Still unsure? →</a>
      </div>
      <details>
        <summary data-i18n="q1_t">Is LVP really waterproof?</summary>
        <p data-i18n="q1_d">Yes, premium LVP is designed to resist water and humidity. We help you choose the right product for bathrooms and kitchens.</p>
      </details>
      <details>
        <summary data-i18n="q2_t">How long does installation take?</summary>
        <p data-i18n="q2_d">Most home installs are completed within 24–72 hours, depending on prep and total area.</p>
      </details>
      <details>
        <summary data-i18n="q3_t">Do you remove old flooring?</summary>
        <p data-i18n="q3_d">Yes. We handle removal, disposal and subfloor prep so you don’t have to coordinate multiple crews.</p>
      </details>
    </div>
  </section>

  <!-- Contact / CTA (beige) -->
<?php include $base.'includes/contact.php'; ?>

  <!-- Footer -->
<?php include $base.'includes/footer.php'; ?>
<div class="bg-slider" style="--bg-opacity:.50">
      <div class="slide active" style="background-image:url('images/sliders/flooring_install.png')"></div>
    </div>
  <script>
    const HOME_PRODUCTS = <?= json_encode($homeLatestProducts) ?>;
    const STORE_CONFIG = <?= json_encode($homeStoreConfig) ?>;

    // Mobile menu toggle
    const burger = document.getElementById('burger');
    const menu = document.getElementById('menu');
    burger?.addEventListener('click', ()=>{
      const open = menu.classList.toggle('show');
      burger.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    // Background slider
    document.querySelectorAll('.bg-slider').forEach(slider => {
      const slides = slider.querySelectorAll('.slide');
      let i = 0;
      slides[0]?.classList.add('active');
      if (slides.length > 1) {
        setInterval(() => {
          slides[i].classList.remove('active');
          i = (i + 1) % slides.length;
          slides[i].classList.add('active');
        }, 5000);
      }
    });

    // Dynamic year
    document.getElementById('year').textContent = new Date().getFullYear();

    // WhatsApp links
    const waNumber = '16892968515';
    const waMsg   = encodeURIComponent("Hi B&S Floor Supply, I'd like a free estimate.");
    const waLink  = `https://wa.me/${waNumber}?text=${waMsg}`;
    ['cta-wa-header','cta-wa-pill','cta-wa-hero','cta-wa-form'].forEach(id=>{
      const el=document.getElementById(id); if(el) el.href = waLink;
    });

    // Analytics helpers
    function trackEvent(name, params){
      if (window.gtag) gtag('event', name, params || {});
      window.dataLayer && window.dataLayer.push({event: name, ...params});
    }
    document.getElementById('cta-hero-estimate')?.addEventListener('click', ()=>trackEvent('cta_click', {location:'hero', cta:'estimate'}));
    document.getElementById('cta-hero-types')?.addEventListener('click', ()=>trackEvent('cta_click', {location:'hero', cta:'flooring_types'}));

    // Scroll depth
    let sent50=false, sent75=false, sent100=false;
    window.addEventListener('scroll', ()=>{
      const sc = window.scrollY + window.innerHeight;
      const h = document.body.scrollHeight;
      const pct = sc / h;
      if (!sent50 && pct>=0.5){ trackEvent('scroll_depth', {percent:50}); sent50=true; }
      if (!sent75 && pct>=0.75){ trackEvent('scroll_depth', {percent:75}); sent75=true; }
      if (!sent100 && pct>=0.99){ trackEvent('scroll_depth', {percent:100}); sent100=true; }
    });

    // Forms (hero & bottom) optimistic UI + events
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
          const res = await fetch(form.action || 'lead.php', {method:'POST', body:formData});
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
    bindForm('lead-form-hero','send-btn-hero','form-status-hero','B&S – Web Lead (hero)');
    bindForm('lead-form-bottom','send-btn-bottom','form-status-bottom','B&S – Web Lead (bottom)');

    function bindPopularButtons(){
      document.querySelectorAll('.popular-check').forEach(el=>{
        el.addEventListener('click', ()=>{
          const sku = el.getAttribute('data-sku') || 'unknown';
          trackEvent('popular_check_store', {sku});
        });
      });
      document.querySelectorAll('.popular-quote').forEach(el=>{
        el.addEventListener('click', ()=>{
          const sku = el.getAttribute('data-sku') || 'unknown';
          trackEvent('popular_get_quote', {sku});
        });
      });
    }

    function formatCurrency(value){
      const num = Number(value);
      return Number.isFinite(num) ? `$${num.toFixed(2)}` : '';
    }

    function resolvePriceInfo(product){
      const badges = STORE_CONFIG?.ui?.badges || {};
      const preferred = (product?.pricing?.activePriceType || product?.availability?.activePriceType || product?.availability?.mode || 'stock').toLowerCase();
      const stockPrice = product?.pricing?.finalPriceStockPerUnit ?? null;
      const backorderPrice = product?.pricing?.finalPriceBackorderPerUnit ?? null;
      let type = preferred === 'backorder' ? 'backorder' : 'stock';
      let value = type === 'stock' ? stockPrice : backorderPrice;
      if(value == null){
        if(stockPrice != null){ type = 'stock'; value = stockPrice; }
        else if(backorderPrice != null){ type = 'backorder'; value = backorderPrice; }
      }
      const label = type === 'backorder' ? (badges.backorder || 'Order-in') : (badges.stock || 'In stock');
      const unitRaw = (product?.measurementUnit || '').toLowerCase();
      const unit = unitRaw === 'lf' ? 'lf' : (unitRaw === 'piece' ? 'piece' : 'sqft');
      return {type, value, label, unit};
    }

    function getHomeProducts(){
      if(!Array.isArray(HOME_PRODUCTS)) return [];
      return HOME_PRODUCTS.slice(0, 6);
    }

    function renderPopularProducts(){
      const carousel = document.getElementById('popular-carousel');
      if(!carousel) return;
      carousel.innerHTML = '';
      getHomeProducts().forEach(p=>{
        const art = document.createElement('article');
        art.className = 'card';
        const img = document.createElement('img');
        const primaryImg = p.images?.[0] || p.image || '';
        const hoverImg = p.images?.[1] || p.hoverImage || '';
        if(primaryImg) img.src = primaryImg;
        img.alt = (p.name || 'LVP option') + ' LVP';
        img.style = 'aspect-ratio:16/10;object-fit:cover;border-radius:10px';
        img.dataset.default = primaryImg;
        img.dataset.hover = hoverImg;
        if(hoverImg){
          img.addEventListener('mouseenter',()=>{img.src=img.dataset.hover;});
          img.addEventListener('mouseleave',()=>{img.src=img.dataset.default;});
        }
        const h3 = document.createElement('h3');
        h3.style = 'margin:.7rem 0 0';
        h3.textContent = p.name;
        const price = resolvePriceInfo(p);
        const priceRow = document.createElement('div');
        priceRow.className = 'popular-price';
        if(price.value != null){
          priceRow.innerHTML = `<span class="pill price-pill ${price.type==='backorder'?'price-pill--backorder':''}">${price.label}</span><strong>${formatCurrency(price.value)}</strong><span class="price-unit">/${price.unit}</span>`;
        } else {
          priceRow.innerHTML = `<span class="pill price-pill">${price.label}</span><strong>Call for price</strong>`;
        }
        const cta = document.createElement('div');
        cta.className = 'hero-cta';
        const storeLink = document.createElement('a');
        storeLink.href = 'store/product.php?sku='+encodeURIComponent(p.sku);
        storeLink.className = 'btn btn-ghost popular-check';
        storeLink.dataset.sku = p.sku;
        storeLink.textContent = 'Check Store';
        const quoteLink = document.createElement('a');
        quoteLink.href = '#contact';
        quoteLink.className = 'btn btn-primary popular-quote';
        quoteLink.dataset.sku = p.sku;
        quoteLink.textContent = 'Get install Quote';
        cta.append(storeLink, quoteLink);
        art.append(img, h3, priceRow, cta);
        carousel?.appendChild(art);
      });
      bindPopularButtons();
    }
    renderPopularProducts();

    // Popular carousel scrolling
    const popCarousel = document.getElementById('popular-carousel');
    document.getElementById('popular-prev')?.addEventListener('click', ()=>{
      popCarousel.scrollBy({left:-popCarousel.clientWidth, behavior:'smooth'});
    });
    document.getElementById('popular-next')?.addEventListener('click', ()=>{
      popCarousel.scrollBy({left:popCarousel.clientWidth, behavior:'smooth'});
    });

    // Simple EN/ES toggle
    const dict = {
      es: {
        nav_benefits:'Beneficios', nav_types:'Tipos de piso', nav_popular:'LVP popular',
        nav_use:'Usos', nav_reviews:'Opiniones', nav_faq:'Preguntas', nav_whatsapp:'WhatsApp',
        cta_estimate:'Pedir cotización', cta_types:'Tipos de piso', cta_whatsapp:'Escribir por WhatsApp', cta_send:'Enviar solicitud',
        ey_benefits:'Por qué nos eligen', h_benefits:'Beneficios que importan cada día',
        b1_t:'Impermeable y fácil de limpiar', b1_d:'Derrames, mascotas y tráfico diario no son problema. Bajo mantenimiento y gran apariencia.',
        b2_t:'Instalación rápida y prolija', b2_d:'Protegemos tu espacio y terminamos rápido para que disfrutes antes.',
        b3_t:'Equipo bilingüe (EN/ES)', b3_d:'Atendemos en inglés y español para que todo sea claro y cómodo.',
        ey_materials:'Materiales que instalamos', h_materials:'Laminate · Vinyl · Hardwood', pill_quote:'Solicitar cotización',
        m1_t:'Laminate', m1_d:'Económico, resistente a rayones y moderno. Ideal para alto tráfico.',
        m2_t:'Vinyl (LVP/LVT)', m2_d:'Resistente al agua, silencioso y fácil de mantener. Ideal para cocinas y baños.',
        m3_t:'Hardwood', m3_d:'Estética atemporal y valor a largo plazo. Requiere preparación y acabado profesional.',
        m_cta:'Solicitar instalación',
        ey_popular:'Opciones populares de LVP', h_popular:'Favoritos de nuestros clientes',
        p1_name:'Northern Grey',
        p1_desc:'LVP impermeable de tono frío y veta sutil. Ideal para interiores modernos y minimalistas.',
        p2_name:'Natural Maple',
        p2_desc:'Tono cálido que ilumina los ambientes. Resistente a rayones, ideal para familias y mascotas.',
        p3_name:'Coastal Oak',
        p3_desc:'Estética costera con textura suave. Equilibrio de tonos que combina con múltiples estilos.',
        btn_store:'Ver en la tienda',
        btn_quote:'Pedir instalación',
        ey_cases:'Dónde destaca el LVP', h_cases:'Usos y ambientes',
        u1_t:'Salas', u1_d:'Aspecto cálido y moderno, resistente a la vida en familia.',
        u2_t:'Dormitorios', u2_d:'Sensación acogedora, silencioso, fácil de cuidar.',
        u3_t:'Baños y cocinas', u3_d:'Superficies impermeables que manejan la humedad con estilo.',
        ey_reviews:'Qué dicen los clientes', h_reviews:'Opiniones y resultados',
        r1:'“Rápido y prolijo. Nuestra sala luce como nueva.”', r2:'“Explicaron opciones y terminaron antes de lo previsto.”', r3:'“Excelente valor. El LVP impermeable es perfecto para niños y mascotas.”',
        ey_faq:'Preguntas', h_faq:'FAQ – LVP Impermeable',
        q1_t:'¿El LVP es realmente impermeable?', q1_d:'Sí, el LVP premium resiste agua y humedad. Te ayudamos a elegir el adecuado para baños y cocinas.',
        q2_t:'¿Cuánto tarda la instalación?', q2_d:'La mayoría de instalaciones se completan en 24–72 horas, según preparación y metraje.',
        q3_t:'¿Retiran el piso antiguo?', q3_d:'Sí. Retiramos, disponemos e igualamos subpiso; no necesitas coordinar varios equipos.',
        ey_contact:'Vamos a tu cotización', h_contact:'Contacto y cotización gratis',
        pill_browse:'Ver tipos de piso →',
        info_t:'Datos de contacto',
      }
    };
    const i18nNodes = document.querySelectorAll('[data-i18n]');
    const btnEN = document.getElementById('lang-en');
    const btnES = document.getElementById('lang-es');
    function setLang(lang){
      document.documentElement.lang = lang;
      btnEN.classList.toggle('active', lang==='en');
      btnES.classList.toggle('active', lang==='es');
      if(lang==='es'){
        i18nNodes.forEach(n=>{
          const k = n.getAttribute('data-i18n');
          if(dict.es[k]) n.textContent = dict.es[k];
        });
        document.getElementById('topbar-text').textContent = 'Disponible en inglés y español · We also speak English';
        document.getElementById('kpi3').textContent = 'También atendemos en español';
        document.getElementById('footer_pitch').textContent = 'LVP impermeable — e instalación de laminate, vinyl y hardwood. Equipo bilingüe. Rápido, prolijo, confiable.';
        document.getElementById('dual-focus').textContent = 'Vendemos LVP impermeable e instalamos Laminate, Vinyl y Hardwood';
        trackEvent('lang_toggle', {to:'es'});
      }else{
        window.location.reload();
      }
    }
    btnEN?.addEventListener('click', ()=>setLang('en'));
    btnES?.addEventListener('click', ()=>setLang('es'));
  </script>
</body>
</html>
