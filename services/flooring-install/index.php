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
  <body class="flooring-install-forms">
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

    <!-- Professional Flooring Installation form -->
    <section class="bs-section bs-section--light" aria-labelledby="installation-quote-heading">
      <div class="bs-container">
        <div class="bs-section-header">
          <h2 class="bs-heading" id="installation-quote-heading">Flooring Installation – Detailed quote</h2>
          <p class="bs-subheading">
            Tell us a bit about your space so we can give you a clear, no-surprise estimate.
          </p>
          <p class="bs-language-note">
            Prefer to speak Spanish? We also answer in <strong>español</strong>.
          </p>
        </div>

        <div class="install-form-steps" aria-hidden="true">
          <span class="install-step-indicator is-active" data-step="1">1. Your info</span>
          <span class="install-step-indicator" data-step="2">2. Your space</span>
          <span class="install-step-indicator" data-step="3">3. Existing floor</span>
          <span class="install-step-indicator" data-step="4">4. Floor &amp; timing</span>
        </div>

        <form id="lead-form-hero" class="install-form" action="<?=$base?>lead.php" method="POST" novalidate>
          <fieldset class="install-step is-active" data-step="1">
            <legend class="install-step-title">Your info</legend>

            <div class="install-field">
              <label for="full_name_hero">Full name<span class="required">*</span></label>
              <input type="text" id="full_name_hero" name="name" required maxlength="255">
            </div>

            <div class="install-field">
              <label for="phone_hero">Phone / WhatsApp<span class="required">*</span></label>
              <input type="tel" id="phone_hero" name="phone" placeholder="+1 (689) 000-0000" required maxlength="255">
            </div>

            <div class="install-field">
              <label for="email_hero">Email<span class="required">*</span></label>
              <input type="email" id="email_hero" name="email" placeholder="you@example.com" maxlength="255" required>
            </div>

            <div class="install-field">
              <span class="install-label">Preferred language<span class="required">*</span></span>
              <div class="install-options">
                <label>
                  <input type="radio" name="preferred_language" value="english" checked required>
                  English
                </label>
                <label>
                  <input type="radio" name="preferred_language" value="spanish" required>
                  Español
                </label>
              </div>
            </div>

            <div class="install-field">
              <label for="zip_hero">ZIP Code<span class="required">*</span></label>
              <input type="text" id="zip_hero" name="zip" list="zip_list_hero" placeholder="Select your ZIP Code" inputmode="numeric" pattern="\d*" required>
              <datalist id="zip_list_hero" data-zip-list></datalist>
              <small class="install-help">Service area ZIPs supported by B&amp;S.</small>
            </div>

            <div class="install-form-nav">
              <button type="button" class="button install-btn-next" data-next-step="2">
                Next
              </button>
            </div>
          </fieldset>

          <fieldset class="install-step" data-step="2">
            <legend class="install-step-title">Your space</legend>

            <div class="install-field">
              <label for="property_type_hero">Property type<span class="required">*</span></label>
              <select id="property_type_hero" name="property_type" required>
                <option value="">Select an option</option>
                <option value="house">House</option>
                <option value="apartment">Apartment / Condo</option>
                <option value="townhouse">Townhouse</option>
                <option value="business">Business / Commercial</option>
                <option value="other">Other</option>
              </select>
            </div>

            <div class="install-field">
              <span class="install-label">Is the property currently…?<span class="required">*</span></span>
              <div class="install-options">
                <label>
                  <input type="radio" name="occupancy" value="occupied" required>
                  Occupied (we live/work here)
                </label>
                <label>
                  <input type="radio" name="occupancy" value="vacant" required>
                  Vacant
                </label>
              </div>
            </div>

            <div class="install-field">
              <span class="install-label">Areas you want to install</span>
              <div class="install-options install-options-multi">
                <label><input type="checkbox" name="areas[]" value="living_room"> Living room / Family room</label>
                <label><input type="checkbox" name="areas[]" value="bedrooms"> Bedrooms</label>
                <label><input type="checkbox" name="areas[]" value="hallways"> Hallways</label>
                <label><input type="checkbox" name="areas[]" value="kitchen"> Kitchen</label>
                <label><input type="checkbox" name="areas[]" value="bathrooms"> Bathrooms</label>
                <label><input type="checkbox" name="areas[]" value="stairs"> Stairs</label>
                <label><input type="checkbox" name="areas[]" value="entire_home"> Entire home</label>
                <label><input type="checkbox" name="areas[]" value="other"> Other</label>
              </div>
            </div>

            <div class="install-field">
              <label for="square_footage_hero">Approximate square footage</label>
              <input type="number" id="square_footage_hero" name="square_footage" min="0" step="1" placeholder="Example: 900">
              <small class="install-help">Example: 900 sq ft (3 bedrooms + hallway).</small>
            </div>

            <div class="install-field">
              <span class="install-label">Do you have stairs to cover?<span class="required">*</span></span>
              <div class="install-options">
                <label><input type="radio" name="has_stairs" value="yes" required> Yes</label>
                <label><input type="radio" name="has_stairs" value="no" required> No</label>
                <label><input type="radio" name="has_stairs" value="not_sure" required> Not sure yet</label>
              </div>
            </div>

            <div class="install-form-nav">
              <button type="button" class="button button--ghost install-btn-back" data-prev-step="1">
                Back
              </button>
              <button type="button" class="button install-btn-next" data-next-step="3">
                Next
              </button>
            </div>
          </fieldset>

          <fieldset class="install-step" data-step="3">
            <legend class="install-step-title">Existing floor &amp; preparation</legend>

            <div class="install-field">
              <label for="current_floor_hero">Current flooring (what you have now)<span class="required">*</span></label>
              <select id="current_floor_hero" name="current_floor" required>
                <option value="">Select an option</option>
                <option value="tile">Tile</option>
                <option value="carpet">Carpet</option>
                <option value="laminate">Laminate</option>
                <option value="vinyl_lvp">Vinyl / LVP</option>
                <option value="wood">Wood (solid/engineered)</option>
                <option value="concrete">Bare concrete</option>
                <option value="not_sure">I’m not sure</option>
              </select>
            </div>

            <div class="install-field">
              <span class="install-label">Do you need removal &amp; disposal of old floor?<span class="required">*</span></span>
              <div class="install-options">
                <label><input type="radio" name="need_removal" value="yes" required> Yes, I need removal &amp; disposal</label>
                <label><input type="radio" name="need_removal" value="no" required> No, floor is already removed</label>
                <label><input type="radio" name="need_removal" value="not_sure" required> Not sure, I need advice</label>
              </div>
            </div>

            <div class="install-field">
              <span class="install-label">Subfloor / condition</span>
              <div class="install-options install-options-multi">
                <label><input type="checkbox" name="subfloor_condition[]" value="cracks_uneven"> I know there are cracks / uneven areas</label>
                <label><input type="checkbox" name="subfloor_condition[]" value="moisture_issues"> I’ve had moisture issues before</label>
                <label><input type="checkbox" name="subfloor_condition[]" value="dont_know"> I don’t know, please check during visit</label>
              </div>
            </div>

            <div class="install-form-nav">
              <button type="button" class="button button--ghost install-btn-back" data-prev-step="2">
                Back
              </button>
              <button type="button" class="button install-btn-next" data-next-step="4">
                Next
              </button>
            </div>
          </fieldset>

          <fieldset class="install-step" data-step="4">
            <legend class="install-step-title">Floor &amp; timing</legend>

            <div class="install-field">
              <span class="install-label">Do you already have the flooring material?<span class="required">*</span></span>
              <div class="install-options">
                <label><input type="radio" name="has_material" value="need_supply" required> No, I need you to supply it</label>
                <label><input type="radio" name="has_material" value="already_have" required> Yes, I already bought the material</label>
                <label><input type="radio" name="has_material" value="need_guidance" required> I’m not sure, need guidance</label>
              </div>
            </div>

            <div class="install-field">
              <label for="floor_type_hero">Floor type you’re interested in<span class="required">*</span></label>
              <select id="floor_type_hero" name="floor_type" required>
                <option value="">Select an option</option>
                <option value="waterproof_lvp">Waterproof LVP</option>
                <option value="laminate">Laminate</option>
                <option value="wood">Wood (solid/engineered)</option>
                <option value="deciding">Still deciding / Need recommendation</option>
              </select>
            </div>

            <div class="install-field">
              <span class="install-label">Service type<span class="required">*</span></span>
              <div class="install-options">
                <label>
                  <input type="radio" name="service_type" value="lvp_supply_install" required>
                  Waterproof LVP – supply &amp; install
                </label>
                <label>
                  <input type="radio" name="service_type" value="installation_only" required>
                  Installation only (Laminate / Vinyl / Hardwood)
                </label>
                <label>
                  <input type="radio" name="service_type" value="other_service" required>
                  Other flooring service
                </label>
              </div>
            </div>

            <div class="install-field">
              <span class="install-label">Extras you might need</span>
              <div class="install-options install-options-multi">
                <label><input type="checkbox" name="extras[]" value="baseboards"> Baseboards / trims</label>
                <label><input type="checkbox" name="extras[]" value="quarter_round"> Quarter round / transitions</label>
                <label><input type="checkbox" name="extras[]" value="underlayment"> Underlayment / moisture barrier</label>
                <label><input type="checkbox" name="extras[]" value="old_floor_removal"> Old floor removal &amp; disposal</label>
                <label><input type="checkbox" name="extras[]" value="furniture_moving"> Furniture moving</label>
                <label><input type="checkbox" name="extras[]" value="other"> Other</label>
              </div>
            </div>

            <div class="install-field">
              <label for="install_timing_hero">When are you planning to install?<span class="required">*</span></label>
              <select id="install_timing_hero" name="install_timing" required>
                <option value="">Select an option</option>
                <option value="asap">As soon as possible (1–2 weeks)</option>
                <option value="1_month">Within 1 month</option>
                <option value="2_3_months">In 2–3 months</option>
                <option value="exploring">Just exploring options</option>
              </select>
            </div>

            <div class="install-field">
              <label for="budget_range_hero">Approximate budget range</label>
              <select id="budget_range_hero" name="budget_range">
                <option value="">Select an option</option>
                <option value="under_2000">Under $2,000</option>
                <option value="2000_5000">$2,000 – $5,000</option>
                <option value="5000_10000">$5,000 – $10,000</option>
                <option value="over_10000">More than $10,000</option>
                <option value="not_sure">I’m not sure yet</option>
              </select>
            </div>

            <div class="install-field">
              <label for="how_hear_hero">How did you hear about us?</label>
              <select id="how_hear_hero" name="how_hear">
                <option value="">Select an option</option>
                <option value="google">Google search</option>
                <option value="social">Instagram / Facebook</option>
                <option value="referral">Referral / Friend</option>
                <option value="contractor">Contractor</option>
                <option value="other">Other</option>
              </select>
            </div>

            <div class="install-field">
              <label for="project_notes_hero">Project details / Notes</label>
              <textarea id="project_notes_hero" name="project_notes" rows="4" placeholder="Tell us anything important: pets, kids, deadlines, special rooms, HOA rules…"></textarea>
            </div>

            <div class="install-form-nav">
              <button type="button" class="button button--ghost install-btn-back" data-prev-step="3">
                Back
              </button>
              <button type="submit" class="button button--primary" id="send-btn-hero">
                Get my installation quote
              </button>
            </div>

            <p class="install-legal">
              By sending, you agree to be contacted via WhatsApp, phone or email about your flooring project. No spam.
            </p>
          </fieldset>

          <input type="hidden" name="message" value="">
          <input type="hidden" name="service" value="">
          <input type="hidden" name="service_detail" value="Flooring installation detailed quote">
          <input type="hidden" name="city" value="" id="city_hero">
          <input type="hidden" name="form_name" value="B&S – Web Lead Flooring Services (detailed)" />
          <input type="hidden" name="source" value="website_services" />
          <p id="form-status-hero" class="note hide" aria-live="polite"></p>
        </form>
      </div>
    </section>
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
  <section id="contact" class="bs-section bs-section--light">
    <div class="bs-container contact-estimate-layout">
      <div class="contact-estimate-main">
        <h2 class="bs-heading">Contact &amp; free estimate</h2>
        <p class="bs-subheading">
          Tell us about your flooring project and we’ll get back with a clear estimate.
        </p>
        <p class="bs-language-note">
          Available in English &amp; Spanish · También atendemos en <strong>español</strong>.
        </p>

        <p style="margin: 0 0 1rem;">
          <a class="button button--ghost" href="https://wa.me/16892968515" target="_blank" rel="noopener">
            Chat on WhatsApp
          </a>
        </p>

        <form id="lead-form-bottom" class="install-compact-form" action="<?=$base?>lead.php" method="POST" novalidate>
          <div class="install-field">
            <label for="full_name_bottom">Full name<span class="required">*</span></label>
            <input type="text" id="full_name_bottom" name="name" required maxlength="255">
          </div>

          <div class="install-field">
            <label for="phone_bottom">Phone / WhatsApp<span class="required">*</span></label>
            <input type="tel" id="phone_bottom" name="phone" required placeholder="+1 (689) 000-0000" maxlength="255">
          </div>

          <div class="install-field">
            <label for="email_bottom">Email<span class="required">*</span></label>
            <input type="email" id="email_bottom" name="email" placeholder="you@example.com" maxlength="255" required>
          </div>

          <div class="install-field">
            <label for="zip_bottom">ZIP Code<span class="required">*</span></label>
            <input type="text" id="zip_bottom" name="zip" list="zip_list_bottom" placeholder="Select your ZIP Code" inputmode="numeric" pattern="\d*" required>
            <datalist id="zip_list_bottom" data-zip-list></datalist>
          </div>

          <div class="install-field">
            <label for="service_bottom">Service<span class="required">*</span></label>
            <select id="service_bottom" name="service" required>
              <option value="">Select an option</option>
              <option value="lvp_supply_install">Waterproof LVP – supply &amp; install</option>
              <option value="installation_only">Installation only (Laminate / Vinyl / Hardwood)</option>
              <option value="other_flooring">Other flooring</option>
            </select>
          </div>

          <div class="install-field">
            <label for="project_details_bottom">Project details</label>
            <textarea id="project_details_bottom" name="project_details" rows="3" placeholder="Rooms, deadlines, special conditions, kids/pets, HOA rules…"></textarea>
          </div>

          <div class="install-extra">
            <button type="button" class="install-extra-toggle" aria-expanded="false">
              More project details (optional)
            </button>

            <div class="install-extra-panel" hidden>
              <div class="install-field">
                <label for="property_type_bottom">Property type</label>
                <select id="property_type_bottom" name="property_type">
                  <option value="">Select an option</option>
                  <option value="house">House</option>
                  <option value="apartment">Apartment / Condo</option>
                  <option value="townhouse">Townhouse</option>
                  <option value="business">Business / Commercial</option>
                  <option value="other">Other</option>
                </select>
              </div>

              <div class="install-field">
                <span class="install-label">Areas you want to install</span>
                <div class="install-options install-options-multi">
                  <label><input type="checkbox" name="areas[]" value="living_room"> Living room / Family room</label>
                  <label><input type="checkbox" name="areas[]" value="bedrooms"> Bedrooms</label>
                  <label><input type="checkbox" name="areas[]" value="hallways"> Hallways</label>
                  <label><input type="checkbox" name="areas[]" value="kitchen"> Kitchen</label>
                  <label><input type="checkbox" name="areas[]" value="bathrooms"> Bathrooms</label>
                  <label><input type="checkbox" name="areas[]" value="stairs"> Stairs</label>
                  <label><input type="checkbox" name="areas[]" value="entire_home"> Entire home</label>
                </div>
              </div>

              <div class="install-field install-field--inline">
                <div>
                  <label for="square_footage_bottom">Approx. square footage</label>
                  <input type="number" id="square_footage_bottom" name="square_footage" min="0" step="1" placeholder="Example: 900">
                </div>
                <div>
                  <label for="has_stairs_bottom">Stairs to cover</label>
                  <select id="has_stairs_bottom" name="has_stairs">
                    <option value="">Select</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                    <option value="not_sure">Not sure</option>
                  </select>
                </div>
              </div>

              <div class="install-field">
                <span class="install-label">Do you already have the flooring material?</span>
                <div class="install-options">
                  <label><input type="radio" name="has_material" value="need_supply"> No, I need you to supply it</label>
                  <label><input type="radio" name="has_material" value="already_have"> Yes, I already bought it</label>
                  <label><input type="radio" name="has_material" value="need_guidance"> I’m not sure, need guidance</label>
                </div>
              </div>

              <div class="install-field install-field--inline">
                <div>
                  <label for="floor_type_bottom">Floor type interested in</label>
                  <select id="floor_type_bottom" name="floor_type">
                    <option value="">Select</option>
                    <option value="waterproof_lvp">Waterproof LVP</option>
                    <option value="laminate">Laminate</option>
                    <option value="wood">Wood (solid/engineered)</option>
                    <option value="deciding">Still deciding / Need recommendation</option>
                  </select>
                </div>
                <div>
                  <label for="install_timing_bottom">When are you planning to install?</label>
                  <select id="install_timing_bottom" name="install_timing">
                    <option value="">Select</option>
                    <option value="asap">As soon as possible (1–2 weeks)</option>
                    <option value="1_month">Within 1 month</option>
                    <option value="2_3_months">In 2–3 months</option>
                    <option value="exploring">Just exploring options</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <p class="install-legal">
            By sending, you agree to be contacted via WhatsApp, phone or email.
          </p>

          <input type="hidden" name="message" value="">
          <input type="hidden" name="service_detail" value="Flooring installation free estimate">
          <input type="hidden" name="city" value="" id="city_bottom">
          <input type="hidden" name="form_name" value="B&S – Web Lead Flooring Services (contact)" />
          <input type="hidden" name="source" value="<?=$contact_source?>" />
          <button type="submit" class="button button--primary" id="send-btn-bottom">
            Send request
          </button>
          <p id="form-status-bottom" class="note hide" aria-live="polite"></p>
        </form>
      </div>

      <aside class="contact-estimate-info">
        <h3>Contact info</h3>
        <p><strong>Phone (WhatsApp):</strong> <a href="https://wa.me/16892968515" target="_blank" rel="noopener">+1 (689) 296-8515</a></p>
        <p><strong>Alt. phone:</strong> +1 (407) 225-1284</p>
        <p><strong>Email:</strong> info@globalservices.com</p>
        <p><strong>Service area:</strong> Orlando, FL</p>
        <div class="hero-cta">
          <a href="https://instagram.com/bsfloorsupply" class="button button--ghost" target="_blank" rel="noopener">Instagram</a>
          <a href="https://facebook.com/BSGlobalServices" class="button button--ghost" target="_blank" rel="noopener">Facebook</a>
        </div>
        <p><small>Prefer español? <em>También atendemos en español.</em></small></p>
      </aside>
    </div>
  </section>
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

    const ZIP_ZONE_FILE = '<?=$base?>store/zip_zones.json';
    let zipLoadPromise = null;
    let ZIP_DATA = [];

    function loadZipData(){
      if(zipLoadPromise) return zipLoadPromise;
      zipLoadPromise = fetch(ZIP_ZONE_FILE)
        .then(res => res.ok ? res.json() : [])
        .then(data => {
          ZIP_DATA = Array.isArray(data) ? data : [];
          return ZIP_DATA;
        })
        .catch(() => {
          ZIP_DATA = [];
          return ZIP_DATA;
        });
      return zipLoadPromise;
    }

    function resolveZipEntry(zip){
      const normalized = (zip || '').toString().trim();
      return ZIP_DATA.find(z => z.zip === normalized);
    }

    function setupZipInputs(){
      const zipInputs = [
        {input: document.getElementById('zip_hero'), city: document.getElementById('city_hero')},
        {input: document.getElementById('zip_bottom'), city: document.getElementById('city_bottom')},
      ];
      loadZipData().then(() => {
        document.querySelectorAll('[data-zip-list]').forEach(list => {
          list.innerHTML = ZIP_DATA.map(z => `<option value="${z.zip}">${z.city || ''}</option>`).join('');
        });
        zipInputs.forEach(({input, city}) => {
          if (!input) return;
          const entry = resolveZipEntry(input.value);
          if (entry && city) city.value = entry.city || '';
          input.addEventListener('change', () => {
            const selected = resolveZipEntry(input.value);
            if (city) city.value = selected?.city || '';
          });
        });
      });
    }

    function stepIsValid(step){
      const fields = Array.from(step.querySelectorAll('input, select, textarea'));
      const checkedRadioGroups = new Set();

      for (const field of fields) {
        if (field.type === 'radio' && field.name) {
          if (checkedRadioGroups.has(field.name)) continue;
          checkedRadioGroups.add(field.name);
          const groupChecked = step.querySelector(`input[name="${field.name}"]:checked`);
          if (!groupChecked && field.required) {
            field.reportValidity();
            return false;
          }
          continue;
        }
        if (!field.checkValidity()) {
          field.reportValidity();
          return false;
        }
      }
      return true;
    }

    function setupMultiStepForm(form){
      if (!form) return;
      const steps = Array.from(form.querySelectorAll('.install-step'));
      const scope = form.closest('section') || form;
      const indicators = Array.from(scope.querySelectorAll('.install-step-indicator'));

      function showStep(stepNumber){
        steps.forEach(step => {
          step.classList.toggle('is-active', step.dataset.step === String(stepNumber));
        });
        indicators.forEach(ind => {
          ind.classList.toggle('is-active', ind.dataset.step === String(stepNumber));
        });
      }

      form.addEventListener('click', (e) => {
        const nextBtn = e.target.closest('.install-btn-next');
        const backBtn = e.target.closest('.install-btn-back');
        if (nextBtn) {
          const currentStep = form.querySelector('.install-step.is-active');
          if (currentStep && !stepIsValid(currentStep)) return;
          showStep(nextBtn.getAttribute('data-next-step'));
        }
        if (backBtn) {
          showStep(backBtn.getAttribute('data-prev-step'));
        }
      });

      showStep(1);
    }

    function setupExtraDetailsToggle(form){
      if (!form) return;
      const extraToggle = form.querySelector('.install-extra-toggle');
      const extraPanel = form.querySelector('.install-extra-panel');
      if (extraToggle && extraPanel) {
        extraToggle.addEventListener('click', () => {
          const expanded = extraToggle.getAttribute('aria-expanded') === 'true';
          extraToggle.setAttribute('aria-expanded', String(!expanded));
          extraPanel.hidden = expanded;
        });
      }
    }

    function getCheckedLabels(form, selector){
      return Array.from(form.querySelectorAll(selector))
        .map(el => el.parentElement?.textContent?.trim() || el.value)
        .filter(Boolean)
        .join(', ') || '-';
    }

    function getSelectedLabel(form, selector){
      const input = form.querySelector(selector);
      if (!input) return '-';
      return input.parentElement?.textContent?.trim() || input.value || '-';
    }

    function buildHeroMessage(form){
      const messageLines = [
        '📋 New flooring installation request',
        '',
        'Client info',
        `Name: ${form.name?.value || '-'}`,
        `Phone: ${form.phone?.value || '-'}`,
        `Email: ${form.email?.value || '-'}`,
        `ZIP Code: ${form.zip?.value || '-'}`,
        `City: ${form.city?.value || '-'}`,
        `Preferred language: ${form.preferred_language?.value || '-'}`,
        '',
        'Property & space',
        `Property type: ${form.property_type?.value || '-'}`,
        `Occupancy: ${form.occupancy?.value || '-'}`,
        `Areas to install: ${getCheckedLabels(form, 'input[name="areas[]"]:checked')}`,
        `Approx. square footage: ${form.square_footage?.value || '-'}`,
        `Stairs to cover: ${form.has_stairs?.value || '-'}`,
        '',
        'Existing floor & condition',
        `Current flooring: ${form.current_floor?.value || '-'}`,
        `Need removal/disposal: ${form.need_removal?.value || '-'}`,
        `Subfloor / condition: ${getCheckedLabels(form, 'input[name="subfloor_condition[]"]:checked')}`,
        '',
        'Floor & service',
        `Has material: ${form.has_material?.value || '-'}`,
        `Floor type interested in: ${form.floor_type?.value || '-'}`,
        `Service type: ${form.service_type?.value || '-'}`,
        `Extras: ${getCheckedLabels(form, 'input[name="extras[]"]:checked')}`,
        '',
        'Timing & budget',
        `When to install: ${form.install_timing?.value || '-'}`,
        `Budget range: ${form.budget_range?.value || '-'}`,
        `How did you hear about us?: ${form.how_hear?.value || '-'}`,
        '',
        'Project details / Notes',
        form.project_notes?.value || '-',
        '',
        '— Form submitted from Flooring Install page.'
      ];

      const serviceLabel = getSelectedLabel(form, 'input[name="service_type"]:checked');
      const floorType = form.floor_type?.value || '';
      const service = ['Flooring installation', serviceLabel, floorType].filter(Boolean).join(' - ');

      form.querySelector('input[name="service"]').value = service;
      form.querySelector('input[name="message"]').value = messageLines.join('\n');
    }

    function buildBottomMessage(form){
      const lines = [
        '📋 New flooring estimate request',
        '',
        'Client info',
        `Name: ${form.name?.value || '-'}`,
        `Phone: ${form.phone?.value || '-'}`,
        `Email: ${form.email?.value || '-'}`,
        `ZIP Code: ${form.zip?.value || '-'}`,
        `City: ${form.city?.value || '-'}`,
        '',
        'Service',
        `Requested service: ${form.service?.value || '-'}`,
        '',
        'Project details',
        form.project_details?.value || '-',
        '',
        'Extra project info',
        `Property type: ${form.property_type?.value || '-'}`,
        `Areas to install: ${getCheckedLabels(form, 'input[name="areas[]"]:checked')}`,
        `Approx. square footage: ${form.square_footage?.value || '-'}`,
        `Stairs to cover: ${form.has_stairs?.value || '-'}`,
        `Has material: ${form.has_material?.value || '-'}`,
        `Floor type interested in: ${form.floor_type?.value || '-'}`,
        `When to install: ${form.install_timing?.value || '-'}`,
        '',
        '— Submitted from Flooring Install – Contact & free estimate section.'
      ];

      form.querySelector('input[name="message"]').value = lines.join('\n');
    }

    function bindForm(fid, sendBtnId, statusId, formName, beforeSend){
      const form = document.getElementById(fid);
      const status = document.getElementById(statusId);
      const sendBtn = document.getElementById(sendBtnId);
      form?.addEventListener('submit', async (e)=>{
        e.preventDefault();
        if (beforeSend) beforeSend(form);
        if (!form.reportValidity()) return;
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

    setupZipInputs();
    setupMultiStepForm(document.getElementById('lead-form-hero'));
    setupExtraDetailsToggle(document.getElementById('lead-form-bottom'));
    bindForm('lead-form-hero','send-btn-hero','form-status-hero','B&S – Web Lead Flooring Services (detailed)', buildHeroMessage);
    bindForm('lead-form-bottom','send-btn-bottom','form-status-bottom','B&S – Web Lead Flooring Services (contact)', buildBottomMessage);
  </script>

  </body>
  </html>
