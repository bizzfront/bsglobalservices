<?php
$base = '../../';
$active = 'services';
$contact_source = 'website_services';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>B&S Floor Supply — Flooring Installation</title>
  <meta name="description" content="Professional flooring installation in Orlando: Waterproof LVP (SPC/WPC), ceramic, laminate and wood. Clear quotes, quick turnaround, bilingual support.">
  <!-- Brand fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?=$base?>style.css" />
  <body>
<?php include $base.'includes/header.php'; ?>
  <nav class="quick" aria-label="Section navigation">
  <div class="container">
    <a href="#benefits">Benefits</a>
    <a href="#products">Floors we install</a>
    <a href="#process">How it works</a>
    <a href="#portfolio">Projects</a>
    <a href="#testimonials">Testimonials</a>
    <a href="#contact">Get a quote</a>
  </div>
</nav>

<main id="main">
  <div class="container hero">
    
    <div class="hero-copy">
      <span class="badge">Bilingual EN/ES · Orlando, FL</span>
      <h1>Professional Flooring Installation</h1>
      <p class="lead">Transform your space with precision, speed, and care. We install <strong>Waterproof LVP</strong>, laminate and wood. Clear quotes and human support.</p>
      <div class="cta">
        <!-- TODO: replace with your real WhatsApp link -->
        <a class="btn btn-primary" href="https://wa.me/16892968515?text=Hi%2C%20I%20want%20a%20flooring%20installation%20quote" target="_blank" rel="noopener">Request a WhatsApp quote</a>
        <a class="btn btn-ghost" href="#process">See how it works</a>
      </div>
    </div>

    <!-- Optional quick form -->
    <form class="form" id="lead-form-hero" action="<?=$base?>lead.php" method="POST" aria-label="Quick quote form">
      <div class="row">
        <div>
          <label for="name">Name</label>
          <input id="name" name="name" type="text" placeholder="Your name" maxlength="255" required />
        </div>
        <div>
          <label for="phone">Phone</label>
          <input id="phone" name="phone" type="tel" placeholder="+1 (___) ___-____" maxlength="255" required />
        </div>
      </div>
      <div class="row">
        <div>
          <label for="email">Email</label>
          <input id="email-hero" type="email" name="email" placeholder="info@globalservices.com" maxlength="255" required />
        </div>
        <div>
          <label for="city">City</label>
          <input id="city" name="city" type="text" placeholder="Orlando / Kissimmee / St. Cloud" maxlength="255" />
        </div>
      </div>
       <div class="row">
        <div>
          <label for="floor">Floor type</label>
          <input id="floor" name="service" type="text" placeholder="LVP, laminate, wood" maxlength="255" />
        </div>
      </div>
      <div class="row-1">
        <div>
          <label for="msg">Notes</label>
          <textarea id="message" name="message" placeholder="Approx. square footage, rooms, preferred dates…" maxlength="255"></textarea>
        </div>
      </div>
      <input type="hidden" name="form_name" value="B&S – Web Lead (quick)" />
      <input type="hidden" name="source" value="website_services" />
      <button class="btn btn-primary" id="send-btn-hero" type="submit">Get my quote</button>
      <p id="form-status-hero" class="note hide" aria-live="polite"></p>
    </form>
  </div>
</main>

<main>

  <!-- Benefits -->
  <section id="benefits" class="section">
    <div class="container">
      <h2>Why install with B&amp;S</h2>
      <p class="lead">Real outcomes focused on <strong>well-being</strong>, <strong>comfort</strong>, <strong>safety</strong> and <strong>coziness</strong>.</p>

      <div class="grid grid-4">
        <article class="card">
          <span class="pill">Well-being</span>
          <h3>Spaces that feel renewed</h3>
          <p>Immediate visual upgrade and a cared-for home vibe.</p>
        </article>
        <article class="card">
          <span class="pill">Comfort</span>
          <h3>Easy to clean</h3>
          <p>Surfaces built for everyday life with low maintenance.</p>
        </article>
        <article class="card">
          <span class="pill">Safety</span>
          <h3>Level & durable installation</h3>
          <p>Reduced tripping hazards and stability for high-traffic areas.</p>
        </article>
        <article class="card">
          <span class="pill">Cozy</span>
          <h3>Modern, warm aesthetics</h3>
          <p>Materials that add style and a welcoming atmosphere.</p>
        </article>
      </div>
    </div>
  </section>

  <!-- Floors we install -->
  <section id="products" class="section" style="background:var(--beige)">
    <div class="container">
      <h2>Floors we install (and sell)</h2>
      <p class="lead">Choose the right option for your home or business. We recommend <strong>Waterproof LVP</strong> for Florida living.</p>

      <div class="grid grid-3">
        <article class="card product">
          <div class="img" style="background-image:url('../../images/floor_types/Hardwood.png')" role="img" aria-label="Waterproof LVP flooring sample"></div>
          <h3>Waterproof LVP</h3>
          <p class="lead">Water-resistant, ideal for kitchens and bathrooms. Durable wood-look.</p>
          <div class="meta"><span>High traffic</span><span>Fast install</span></div>
          <a class="btn btn-ghost" href="../../store/">Open catalog</a>
        </article>

        <article class="card product">
          <div class="img" style="background-image:url('../../images/floor_types/Laminate.png')" role="img" aria-label="Laminate flooring"></div>
          <h3>Laminate</h3>
          <p class="lead">Modern and budget-friendly. Great look for less.</p>
          <div class="meta"><span>Quick install</span><span>Variety</span></div>
          <a class="btn btn-ghost" href="../../store/">Open catalog</a>
        </article>

        <article class="card product">
          <div class="img" style="background-image:url('../../images/floor_types/Vinyl.png')" role="img" aria-label="Engineered wood"></div>
          <h3>Wood (solid/engineered)</h3>
          <p class="lead">Premium finish and timeless warmth.</p>
          <div class="meta"><span>Premium</span><span>Home value</span></div>
          <a class="btn btn-ghost" href="../../store/">Open catalog</a>
        </article>
      </div>
    </div>
  </section>

  <!-- Process (with internal links) -->
  <section id="process" class="section">
    <div class="container">
      <h2>How it works</h2>
      <p class="lead">From start to finish, we’re with you at every step.</p>

      <div class="steps">
        <div class="step">
          <div class="num">1</div>
          <div>
            <h3>Choose your floor</h3>
            <p>Explore our options and pick the right material before your in-home visit.</p>
            <div class="links">
              <a class="btn btn-ghost" href="#products">See floors</a>
              <a class="btn btn-ghost" href="#catalog-lvp">LVP catalog</a>
            </div>
          </div>
        </div>

        <div class="step">
          <div class="num">2</div>
          <div>
            <h3>Schedule an in-home measurement</h3>
            <p>A specialist will measure your space and assess specific needs.</p>
            <a class="btn btn-ghost" href="#contact">Book a visit</a>
          </div>
        </div>

        <div class="step">
          <div class="num">3</div>
          <div>
            <h3>Get a clear quote</h3>
            <p>Materials + labor, no surprises. We’ll tailor options to your square footage.</p>
            <a class="btn btn-ghost" href="#contact">Request quote</a>
          </div>
        </div>

        <div class="step">
          <div class="num">4</div>
          <div>
            <h3>Enjoy your new floor</h3>
            <p>Clean and quick installation. We deliver your space ready to live in.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Use cases (beige + translucent image) -->
<?php include $base.'includes/portfolio.php'; ?>

  <!-- Testimonials -->
  <!-- Testimonials (dark inverse) -->
<?php include $base.'includes/reviews.php'; ?>

  <!-- Contact / CTA (beige) -->
<?php include $base.'includes/contact.php'; ?>
  </main>

  <!-- Footer -->
<?php include $base.'includes/footer.php'; ?>
  <div class="bg-slider" style="--bg-opacity:.25">
      <div class="slide active" style="background-image:url('../../images/sliders/flooring_install.png'); background-position: center bottom;"></div>
    </div>

  <script>
    const burger = document.getElementById('burger');
    const menu = document.getElementById('menu');
    burger?.addEventListener('click', () => {
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

    bindForm('lead-form-hero','send-btn-hero','form-status-hero','B&S – Web Lead Flooring Services (quick)');
    bindForm('lead-form-bottom','send-btn-bottom','form-status-bottom','B&S – Web Lead Flooring Services (bottom)');
  </script>

  </body>
  </html>
