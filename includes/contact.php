<?php
$base = $base ?? '';
$contact_source = $contact_source ?? 'website_home';
?>
<section id="contact" class="sec--light">
  <div class="container">
    <div class="sec-head">
      <div>
        <div class="eyebrow" data-i18n="ey_contact">Let’s get your estimate</div>
        <h2 data-i18n="h_contact">Contact & free estimate</h2>
      </div>
      <a href="https://wa.me/16892968515?text=Hi%20B%26S%20Floor%20Supply%2C%20I'd%20like%20a%20free%20estimate." class="pill" id="cta-wa-pill" target="_blank" rel="noopener" data-i18n="cta_whatsapp">Chat on WhatsApp</a>
    </div>
    <div class="contact">
      <form class="form" id="lead-form-bottom" action="<?=$base?>lead.php" method="POST" aria-labelledby="contact-bottom">
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
        <input type="hidden" name="source" value="<?=$contact_source?>" />
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
