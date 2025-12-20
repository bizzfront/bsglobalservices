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
    <a href="#benefits" data-i18n="flooring_install_nav_benefits">Benefits</a>
    <a href="#products" data-i18n="flooring_install_nav_products">Floors we install</a>
    <a href="#process" data-i18n="flooring_install_nav_process">How it works</a>
    <a href="#portfolio" data-i18n="flooring_install_nav_portfolio">Projects</a>
    <a href="#testimonials" data-i18n="flooring_install_nav_testimonials">Testimonials</a>
    <a href="#contact" data-i18n="flooring_install_nav_contact">Get a quote</a>
  </div>
</nav>

<main id="main">
  <div class="container hero flooring-install--hero">
    
    <div class="hero-copy">
      <span class="badge" data-i18n="flooring_install_badge">Bilingual EN/ES · Orlando, FL</span>
      <h1 data-i18n="flooring_install_hero_title">Professional Flooring Installation</h1>
      <p class="lead" data-i18n-html="flooring_install_hero_lead">Transform your space with precision, speed, and care. We install <strong>Waterproof LVP</strong>, laminate and wood. Clear quotes and human support.</p>
      <div class="cta">
        <!-- TODO: replace with your real WhatsApp link -->
        <a class="btn btn-primary" href="https://wa.me/16892968515?text=Hi%2C%20I%20want%20a%20flooring%20installation%20quote" target="_blank" rel="noopener" data-i18n="flooring_install_hero_cta_primary">Request a WhatsApp quote</a>
        <a class="btn btn-ghost" href="#process" data-i18n="flooring_install_hero_cta_secondary">See how it works</a>
      </div>
    </div>

    <!-- Professional Flooring Installation form -->
    <section class="bs-section bs-section--light" aria-labelledby="installation-quote-heading">
      <div class="bs-container">
        <div class="bs-section-header">
          <h2 class="bs-heading" id="installation-quote-heading" data-i18n="flooring_install_form_heading">Flooring Installation – Detailed quote</h2>
          <p class="bs-subheading" data-i18n="flooring_install_form_subheading">
            Tell us a bit about your space so we can give you a clear, no-surprise estimate.
          </p>
          <p class="bs-language-note" data-i18n-html="flooring_install_form_language_note">
            Prefer to speak Spanish? We also answer in <strong>español</strong>.
          </p>
        </div>

        <div class="install-form-steps" aria-hidden="true">
          <span class="install-step-indicator is-active" data-step="1" data-i18n="flooring_install_step_1">1. Your info</span>
          <span class="install-step-indicator" data-step="2" data-i18n="flooring_install_step_2">2. Your space</span>
          <span class="install-step-indicator" data-step="3" data-i18n="flooring_install_step_3">3. Existing floor</span>
          <span class="install-step-indicator" data-step="4" data-i18n="flooring_install_step_4">4. Floor &amp; timing</span>
        </div>

        <form id="lead-form-hero" class="install-form" action="<?=$base?>lead.php" method="POST" novalidate>
          <fieldset class="install-step is-active" data-step="1">
            <!--<legend class="install-step-title">Your info</legend>-->

            <div class="install-field">
              <label for="full_name_hero"><span data-i18n="flooring_install_label_full_name">Full name</span><span class="required">*</span></label>
              <input type="text" id="full_name_hero" name="name" required maxlength="255">
            </div>

            <div class="install-field">
              <label for="phone_hero"><span data-i18n="flooring_install_label_phone">Phone / WhatsApp</span><span class="required">*</span></label>
              <input type="tel" id="phone_hero" name="phone" placeholder="+1 (689) 000-0000" data-i18n-placeholder="flooring_install_placeholder_phone" required maxlength="255">
            </div>

            <div class="install-field">
              <label for="email_hero"><span data-i18n="flooring_install_label_email">Email</span><span class="required">*</span></label>
              <input type="email" id="email_hero" name="email" placeholder="you@example.com" data-i18n-placeholder="flooring_install_placeholder_email" maxlength="255" required>
            </div>

            <div class="install-field">
              <span class="install-label"><span data-i18n="flooring_install_label_language">Preferred language</span><span class="required">*</span></span>
              <div class="install-options">
                <label>
                  <input type="radio" class="ratio-correct-display" name="preferred_language" value="english" checked required>
                  <span data-i18n="flooring_install_option_language_english">English</span>
                </label>
                <label>
                  <input type="radio" class="ratio-correct-display" name="preferred_language" value="spanish" required>
                  <span data-i18n="flooring_install_option_language_spanish">Español</span>
                </label>
              </div>
            </div>

            <div class="install-field">
              <label for="zip_hero"><span data-i18n="flooring_install_label_zip">ZIP Code</span><span class="required">*</span></label>
              <input type="text" id="zip_hero" name="zip" list="zip_list_hero" placeholder="Select your ZIP Code" data-i18n-placeholder="flooring_install_placeholder_zip" inputmode="numeric" pattern="\d*" required>
              <datalist id="zip_list_hero" data-zip-list></datalist>
              <small class="install-help" data-i18n="flooring_install_help_zip">Service area ZIPs supported by B&amp;S.</small>
            </div>

            <div class="install-form-nav">
              <button type="button" class="button install-btn-next" data-next-step="2">
                <span data-i18n="flooring_install_btn_next">Next</span>
              </button>
            </div>
          </fieldset>

          <fieldset class="install-step" data-step="2">
            <!--<legend class="install-step-title">Your space</legend>-->

            <div class="install-field">
              <label for="property_type_hero"><span data-i18n="flooring_install_label_property_type">Property type</span><span class="required">*</span></label>
              <select id="property_type_hero" name="property_type" required>
                <option value="" data-i18n="flooring_install_option_select">Select an option</option>
                <option value="house" data-i18n="flooring_install_option_house">House</option>
                <option value="apartment" data-i18n="flooring_install_option_apartment">Apartment / Condo</option>
                <option value="townhouse" data-i18n="flooring_install_option_townhouse">Townhouse</option>
                <option value="business" data-i18n="flooring_install_option_business">Business / Commercial</option>
                <option value="other" data-i18n="flooring_install_option_other">Other</option>
              </select>
            </div>

            <div class="install-field">
              <span class="install-label"><span data-i18n="flooring_install_label_occupancy">Is the property currently…?</span><span class="required">*</span></span>
              <div class="install-options">
                <label>
                  <input type="radio" class="ratio-correct-display" name="occupancy" value="occupied" required>
                  <span data-i18n="flooring_install_option_occupied">Occupied (we live/work here)</span>
                </label>
                <label>
                  <input type="radio" class="ratio-correct-display" name="occupancy" value="vacant" required>
                  <span data-i18n="flooring_install_option_vacant">Vacant</span>
                </label>
              </div>
            </div>

            <div class="install-field">
              <span class="install-label" data-i18n="flooring_install_label_areas">Areas you want to install</span>
              <div class="install-options install-options-multi">
                <label><input type="checkbox" class="ratio-correct-display"name="areas[]" value="living_room"> <span data-i18n="flooring_install_option_area_living_room">Living room / Family room</span></label>
                <label><input type="checkbox" class="ratio-correct-display"name="areas[]" value="bedrooms"> <span data-i18n="flooring_install_option_area_bedrooms">Bedrooms</span></label>
                <label><input type="checkbox" class="ratio-correct-display"name="areas[]" value="hallways"> <span data-i18n="flooring_install_option_area_hallways">Hallways</span></label>
                <label><input type="checkbox" class="ratio-correct-display"name="areas[]" value="kitchen"> <span data-i18n="flooring_install_option_area_kitchen">Kitchen</span></label>
                <label><input type="checkbox" class="ratio-correct-display"name="areas[]" value="bathrooms"> <span data-i18n="flooring_install_option_area_bathrooms">Bathrooms</span></label>
                <label><input type="checkbox" class="ratio-correct-display"name="areas[]" value="stairs"> <span data-i18n="flooring_install_option_area_stairs">Stairs</span></label>
                <label><input type="checkbox" class="ratio-correct-display"name="areas[]" value="entire_home"> <span data-i18n="flooring_install_option_area_entire_home">Entire home</span></label>
                <label><input type="checkbox" class="ratio-correct-display"name="areas[]" value="other"> <span data-i18n="flooring_install_option_area_other">Other</span></label>
              </div>
            </div>

            <div class="install-field">
              <label for="square_footage_hero" data-i18n="flooring_install_label_square_footage">Approximate square footage</label>
              <input type="number" id="square_footage_hero" name="square_footage" min="0" step="1" placeholder="Example: 900" data-i18n-placeholder="flooring_install_placeholder_square_footage">
              <small class="install-help" data-i18n="flooring_install_help_square_footage">Example: 900 sq ft (3 bedrooms + hallway).</small>
            </div>

            <div class="install-field">
              <span class="install-label"><span data-i18n="flooring_install_label_has_stairs">Do you have stairs to cover?</span><span class="required">*</span></span>
              <div class="install-options">
                <label><input type="radio" class="ratio-correct-display" name="has_stairs" value="yes" required> <span data-i18n="flooring_install_option_yes">Yes</span></label>
                <label><input type="radio" class="ratio-correct-display" name="has_stairs" value="no" required> <span data-i18n="flooring_install_option_no">No</span></label>
                <label><input type="radio" class="ratio-correct-display" name="has_stairs" value="not_sure" required> <span data-i18n="flooring_install_option_not_sure_yet">Not sure yet</span></label>
              </div>
            </div>

            <div class="install-form-nav">
              <button type="button" class="button button--ghost install-btn-back" data-prev-step="1">
                <span data-i18n="flooring_install_btn_back">Back</span>
              </button>
              <button type="button" class="button install-btn-next" data-next-step="3">
                <span data-i18n="flooring_install_btn_next">Next</span>
              </button>
            </div>
          </fieldset>

          <fieldset class="install-step" data-step="3">
            <!--<legend class="install-step-title">Existing floor &amp; preparation</legend>-->

            <div class="install-field">
              <label for="current_floor_hero"><span data-i18n="flooring_install_label_current_floor">Current flooring (what you have now)</span><span class="required">*</span></label>
              <select id="current_floor_hero" name="current_floor" required>
                <option value="" data-i18n="flooring_install_option_select">Select an option</option>
                <option value="tile" data-i18n="flooring_install_option_current_tile">Tile</option>
                <option value="carpet" data-i18n="flooring_install_option_current_carpet">Carpet</option>
                <option value="laminate" data-i18n="flooring_install_option_current_laminate">Laminate</option>
                <option value="vinyl_lvp" data-i18n="flooring_install_option_current_vinyl_lvp">Vinyl / LVP</option>
                <option value="wood" data-i18n="flooring_install_option_current_wood">Wood (solid/engineered)</option>
                <option value="concrete" data-i18n="flooring_install_option_current_concrete">Bare concrete</option>
                <option value="not_sure" data-i18n="flooring_install_option_current_not_sure">I’m not sure</option>
              </select>
            </div>

            <div class="install-field">
              <span class="install-label"><span data-i18n="flooring_install_label_need_removal">Do you need removal &amp; disposal of old floor?</span><span class="required">*</span></span>
              <div class="install-options">
                <label><input type="radio" class="ratio-correct-display" name="need_removal" value="yes" required> <span data-i18n="flooring_install_option_need_removal_yes">Yes, I need removal &amp; disposal</span></label>
                <label><input type="radio" class="ratio-correct-display" name="need_removal" value="no" required> <span data-i18n="flooring_install_option_need_removal_no">No, floor is already removed</span></label>
                <label><input type="radio" class="ratio-correct-display" name="need_removal" value="not_sure" required> <span data-i18n="flooring_install_option_need_removal_not_sure">Not sure, I need advice</span></label>
              </div>
            </div>

            <div class="install-field">
              <span class="install-label" data-i18n="flooring_install_label_subfloor">Subfloor / condition</span>
              <div class="install-options install-options-multi">
                <label><input type="checkbox" class="ratio-correct-display"name="subfloor_condition[]" value="cracks_uneven"> <span data-i18n="flooring_install_option_subfloor_cracks">I know there are cracks / uneven areas</span></label>
                <label><input type="checkbox" class="ratio-correct-display"name="subfloor_condition[]" value="moisture_issues"> <span data-i18n="flooring_install_option_subfloor_moisture">I’ve had moisture issues before</span></label>
                <label><input type="checkbox" class="ratio-correct-display"name="subfloor_condition[]" value="dont_know"> <span data-i18n="flooring_install_option_subfloor_unknown">I don’t know, please check during visit</span></label>
              </div>
            </div>

            <div class="install-form-nav">
              <button type="button" class="button button--ghost install-btn-back" data-prev-step="2">
                <span data-i18n="flooring_install_btn_back">Back</span>
              </button>
              <button type="button" class="button install-btn-next" data-next-step="4">
                <span data-i18n="flooring_install_btn_next">Next</span>
              </button>
            </div>
          </fieldset>

          <fieldset class="install-step" data-step="4">
            <!--<legend class="install-step-title">Floor &amp; timing</legend>-->

            <div class="install-field">
              <span class="install-label"><span data-i18n="flooring_install_label_has_material">Do you already have the flooring material?</span><span class="required">*</span></span>
              <div class="install-options">
                <label><input type="radio" class="ratio-correct-display" name="has_material" value="need_supply" required> <span data-i18n="flooring_install_option_has_material_need_supply">No, I need you to supply it</span></label>
                <label><input type="radio" class="ratio-correct-display" name="has_material" value="already_have" required> <span data-i18n="flooring_install_option_has_material_already">Yes, I already bought the material</span></label>
                <label><input type="radio" class="ratio-correct-display" name="has_material" value="need_guidance" required> <span data-i18n="flooring_install_option_has_material_guidance">I’m not sure, need guidance</span></label>
              </div>
            </div>

            <div class="install-field">
              <label for="floor_type_hero"><span data-i18n="flooring_install_label_floor_type">Floor type you’re interested in</span><span class="required">*</span></label>
              <select id="floor_type_hero" name="floor_type" required>
                <option value="" data-i18n="flooring_install_option_select">Select an option</option>
                <option value="waterproof_lvp" data-i18n="flooring_install_option_floor_type_lvp">Waterproof LVP</option>
                <option value="laminate" data-i18n="flooring_install_option_floor_type_laminate">Laminate</option>
                <option value="wood" data-i18n="flooring_install_option_floor_type_wood">Wood (solid/engineered)</option>
                <option value="deciding" data-i18n="flooring_install_option_floor_type_deciding">Still deciding / Need recommendation</option>
              </select>
            </div>

            <div class="install-field">
              <span class="install-label"><span data-i18n="flooring_install_label_service_type">Service type</span><span class="required">*</span></span>
              <div class="install-options">
                <label>
                  <input type="radio" class="ratio-correct-display" name="service_type" value="lvp_supply_install" required>
                  <span data-i18n="flooring_install_option_service_lvp">Waterproof LVP – supply &amp; install</span>
                </label>
                <label>
                  <input type="radio" class="ratio-correct-display" name="service_type" value="installation_only" required>
                  <span data-i18n="flooring_install_option_service_install_only">Installation only (Laminate / Vinyl / Hardwood)</span>
                </label>
                <label>
                  <input type="radio" class="ratio-correct-display" name="service_type" value="other_service" required>
                  <span data-i18n="flooring_install_option_service_other">Other flooring service</span>
                </label>
              </div>
            </div>

            <div class="install-field">
              <span class="install-label" data-i18n="flooring_install_label_extras">Extras you might need</span>
              <div class="install-options install-options-multi">
                <label><input type="checkbox" class="ratio-correct-display"name="extras[]" value="baseboards"> <span data-i18n="flooring_install_option_extra_baseboards">Baseboards / trims</span></label>
                <label><input type="checkbox" class="ratio-correct-display"name="extras[]" value="quarter_round"> <span data-i18n="flooring_install_option_extra_quarter_round">Quarter round / transitions</span></label>
                <label><input type="checkbox" class="ratio-correct-display"name="extras[]" value="underlayment"> <span data-i18n="flooring_install_option_extra_underlayment">Underlayment / moisture barrier</span></label>
                <label><input type="checkbox" class="ratio-correct-display"name="extras[]" value="old_floor_removal"> <span data-i18n="flooring_install_option_extra_removal">Old floor removal &amp; disposal</span></label>
                <label><input type="checkbox" class="ratio-correct-display"name="extras[]" value="furniture_moving"> <span data-i18n="flooring_install_option_extra_furniture">Furniture moving</span></label>
                <label><input type="checkbox" class="ratio-correct-display"name="extras[]" value="other"> <span data-i18n="flooring_install_option_area_other">Other</span></label>
              </div>
            </div>

            <div class="install-field">
              <label for="install_timing_hero"><span data-i18n="flooring_install_label_install_timing">When are you planning to install?</span><span class="required">*</span></label>
              <select id="install_timing_hero" name="install_timing" required>
                <option value="" data-i18n="flooring_install_option_select">Select an option</option>
                <option value="asap" data-i18n="flooring_install_option_timing_asap">As soon as possible (1–2 weeks)</option>
                <option value="1_month" data-i18n="flooring_install_option_timing_1_month">Within 1 month</option>
                <option value="2_3_months" data-i18n="flooring_install_option_timing_2_3_months">In 2–3 months</option>
                <option value="exploring" data-i18n="flooring_install_option_timing_exploring">Just exploring options</option>
              </select>
            </div>

            <div class="install-field">
              <label for="budget_range_hero" data-i18n="flooring_install_label_budget">Approximate budget range</label>
              <select id="budget_range_hero" name="budget_range">
                <option value="" data-i18n="flooring_install_option_select">Select an option</option>
                <option value="under_2000" data-i18n="flooring_install_option_budget_under_2000">Under $2,000</option>
                <option value="2000_5000" data-i18n="flooring_install_option_budget_2000_5000">$2,000 – $5,000</option>
                <option value="5000_10000" data-i18n="flooring_install_option_budget_5000_10000">$5,000 – $10,000</option>
                <option value="over_10000" data-i18n="flooring_install_option_budget_over_10000">More than $10,000</option>
                <option value="not_sure" data-i18n="flooring_install_option_budget_not_sure">I’m not sure yet</option>
              </select>
            </div>

            <div class="install-field">
              <label for="how_hear_hero" data-i18n="flooring_install_label_how_hear">How did you hear about us?</label>
              <select id="how_hear_hero" name="how_hear">
                <option value="" data-i18n="flooring_install_option_select">Select an option</option>
                <option value="google" data-i18n="flooring_install_option_hear_google">Google search</option>
                <option value="social" data-i18n="flooring_install_option_hear_social">Instagram / Facebook</option>
                <option value="referral" data-i18n="flooring_install_option_hear_referral">Referral / Friend</option>
                <option value="contractor" data-i18n="flooring_install_option_hear_contractor">Contractor</option>
                <option value="other" data-i18n="flooring_install_option_other">Other</option>
              </select>
            </div>

            <div class="install-field">
              <label for="project_notes_hero" data-i18n="flooring_install_label_project_notes">Project details / Notes</label>
              <textarea id="project_notes_hero" name="project_notes" rows="4" placeholder="Tell us anything important: pets, kids, deadlines, special rooms, HOA rules…" data-i18n-placeholder="flooring_install_placeholder_project_notes"></textarea>
            </div>

            <div class="install-form-nav">
              <button type="button" class="button button--ghost install-btn-back" data-prev-step="3">
                <span data-i18n="flooring_install_btn_back">Back</span>
              </button>
              <button type="submit" class="button button--primary" id="send-btn-hero">
                <span data-i18n="flooring_install_btn_submit_detail">Get my installation quote</span>
              </button>
            </div>

            <p class="install-legal" data-i18n="flooring_install_legal_detail">
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
      <h2 data-i18n="flooring_install_benefits_title">Why install with B&amp;S</h2>
      <p class="lead" data-i18n-html="flooring_install_benefits_lead">Real outcomes focused on <strong>well-being</strong>, <strong>comfort</strong>, <strong>safety</strong> and <strong>coziness</strong>.</p>

      <div class="grid grid-4">
        <article class="card">
          <span class="pill" data-i18n="flooring_install_benefit_1_pill">Well-being</span>
          <h3 data-i18n="flooring_install_benefit_1_title">Spaces that feel renewed</h3>
          <p data-i18n="flooring_install_benefit_1_desc">Immediate visual upgrade and a cared-for home vibe.</p>
        </article>
        <article class="card">
          <span class="pill" data-i18n="flooring_install_benefit_2_pill">Comfort</span>
          <h3 data-i18n="flooring_install_benefit_2_title">Easy to clean</h3>
          <p data-i18n="flooring_install_benefit_2_desc">Surfaces built for everyday life with low maintenance.</p>
        </article>
        <article class="card">
          <span class="pill" data-i18n="flooring_install_benefit_3_pill">Safety</span>
          <h3 data-i18n="flooring_install_benefit_3_title">Level & durable installation</h3>
          <p data-i18n="flooring_install_benefit_3_desc">Reduced tripping hazards and stability for high-traffic areas.</p>
        </article>
        <article class="card">
          <span class="pill" data-i18n="flooring_install_benefit_4_pill">Cozy</span>
          <h3 data-i18n="flooring_install_benefit_4_title">Modern, warm aesthetics</h3>
          <p data-i18n="flooring_install_benefit_4_desc">Materials that add style and a welcoming atmosphere.</p>
        </article>
      </div>
    </div>
  </section>

  <!-- Floors we install -->
  <section id="products" class="section" style="background:var(--beige)">
    <div class="container">
      <h2 data-i18n="flooring_install_products_title">Floors we install (and sell)</h2>
      <p class="lead" data-i18n-html="flooring_install_products_lead">Choose the right option for your home or business. We recommend <strong>Waterproof LVP</strong> for Florida living.</p>

      <div class="grid grid-3">
        <article class="card product">
          <div class="img" style="background-image:url('../../images/floor_types/Hardwood.png')" role="img" aria-label="Waterproof LVP flooring sample" data-i18n-aria="flooring_install_product_lvp_aria"></div>
          <h3 data-i18n="flooring_install_product_lvp_title">Waterproof LVP</h3>
          <p class="lead" data-i18n="flooring_install_product_lvp_desc">Water-resistant, ideal for kitchens and bathrooms. Durable wood-look.</p>
          <div class="meta"><span data-i18n="flooring_install_product_lvp_meta_1">High traffic</span><span data-i18n="flooring_install_product_lvp_meta_2">Fast install</span></div>
          <a class="btn btn-ghost" href="../../store/" data-i18n="flooring_install_product_cta">Open catalog</a>
        </article>

        <article class="card product">
          <div class="img" style="background-image:url('../../images/floor_types/Laminate.png')" role="img" aria-label="Laminate flooring" data-i18n-aria="flooring_install_product_laminate_aria"></div>
          <h3 data-i18n="flooring_install_product_laminate_title">Laminate</h3>
          <p class="lead" data-i18n="flooring_install_product_laminate_desc">Modern and budget-friendly. Great look for less.</p>
          <div class="meta"><span data-i18n="flooring_install_product_laminate_meta_1">Quick install</span><span data-i18n="flooring_install_product_laminate_meta_2">Variety</span></div>
          <a class="btn btn-ghost" href="../../store/" data-i18n="flooring_install_product_cta">Open catalog</a>
        </article>

        <article class="card product">
          <div class="img" style="background-image:url('../../images/floor_types/Vinyl.png')" role="img" aria-label="Engineered wood" data-i18n-aria="flooring_install_product_wood_aria"></div>
          <h3 data-i18n="flooring_install_product_wood_title">Wood (solid/engineered)</h3>
          <p class="lead" data-i18n="flooring_install_product_wood_desc">Premium finish and timeless warmth.</p>
          <div class="meta"><span data-i18n="flooring_install_product_wood_meta_1">Premium</span><span data-i18n="flooring_install_product_wood_meta_2">Home value</span></div>
          <a class="btn btn-ghost" href="../../store/" data-i18n="flooring_install_product_cta">Open catalog</a>
        </article>
      </div>
    </div>
  </section>

  <!-- Process (with internal links) -->
  <section id="process" class="section">
    <div class="container">
      <h2 data-i18n="flooring_install_process_title">How it works</h2>
      <p class="lead" data-i18n="flooring_install_process_lead">From start to finish, we’re with you at every step.</p>

      <div class="steps">
        <div class="step">
          <div class="num">1</div>
          <div>
            <h3 data-i18n="flooring_install_process_step_1_title">Choose your floor</h3>
            <p data-i18n="flooring_install_process_step_1_desc">Explore our options and pick the right material before your in-home visit.</p>
            <div class="links">
              <a class="btn btn-ghost" href="#products" data-i18n="flooring_install_process_step_1_link_products">See floors</a>
              <a class="btn btn-ghost" href="#catalog-lvp" data-i18n="flooring_install_process_step_1_link_catalog">LVP catalog</a>
            </div>
          </div>
        </div>

        <div class="step">
          <div class="num">2</div>
          <div>
            <h3 data-i18n="flooring_install_process_step_2_title">Schedule an in-home measurement</h3>
            <p data-i18n="flooring_install_process_step_2_desc">A specialist will measure your space and assess specific needs.</p>
            <a class="btn btn-ghost" href="#contact" data-i18n="flooring_install_process_step_2_link">Book a visit</a>
          </div>
        </div>

        <div class="step">
          <div class="num">3</div>
          <div>
            <h3 data-i18n="flooring_install_process_step_3_title">Get a clear quote</h3>
            <p data-i18n="flooring_install_process_step_3_desc">Materials + labor, no surprises. We’ll tailor options to your square footage.</p>
            <a class="btn btn-ghost" href="#contact" data-i18n="flooring_install_process_step_3_link">Request quote</a>
          </div>
        </div>

        <div class="step">
          <div class="num">4</div>
          <div>
            <h3 data-i18n="flooring_install_process_step_4_title">Enjoy your new floor</h3>
            <p data-i18n="flooring_install_process_step_4_desc">Clean and quick installation. We deliver your space ready to live in.</p>
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
        <h2 class="bs-heading" data-i18n="flooring_install_contact_heading">Contact &amp; free estimate</h2>
        <p class="bs-subheading" data-i18n="flooring_install_contact_subheading">
          Tell us about your flooring project and we’ll get back with a clear estimate.
        </p>
        <p class="bs-language-note" data-i18n-html="flooring_install_contact_language_note">
          Available in English &amp; Spanish · También atendemos en <strong>español</strong>.
        </p>

        <p style="margin: 0 0 1rem;">
          <a class="button button--ghost" href="https://wa.me/16892968515" target="_blank" rel="noopener">
            <span data-i18n="cta_whatsapp_chat">Chat on WhatsApp</span>
          </a>
        </p>

        <form id="lead-form-bottom" class="install-compact-form" action="<?=$base?>lead.php" method="POST" novalidate>
          <div class="install-field">
            <label for="full_name_bottom"><span data-i18n="flooring_install_label_full_name">Full name</span><span class="required">*</span></label>
            <input type="text" id="full_name_bottom" name="name" required maxlength="255">
          </div>

          <div class="install-field">
            <label for="phone_bottom"><span data-i18n="flooring_install_label_phone">Phone / WhatsApp</span><span class="required">*</span></label>
            <input type="tel" id="phone_bottom" name="phone" required placeholder="+1 (689) 000-0000" data-i18n-placeholder="flooring_install_placeholder_phone" maxlength="255">
          </div>

          <div class="install-field">
            <label for="email_bottom"><span data-i18n="flooring_install_label_email">Email</span><span class="required">*</span></label>
            <input type="email" id="email_bottom" name="email" placeholder="you@example.com" data-i18n-placeholder="flooring_install_placeholder_email" maxlength="255" required>
          </div>

          <div class="install-field">
            <label for="zip_bottom"><span data-i18n="flooring_install_label_zip">ZIP Code</span><span class="required">*</span></label>
            <input type="text" id="zip_bottom" name="zip" list="zip_list_bottom" placeholder="Select your ZIP Code" data-i18n-placeholder="flooring_install_placeholder_zip" inputmode="numeric" pattern="\d*" required>
            <datalist id="zip_list_bottom" data-zip-list></datalist>
          </div>

          <div class="install-field">
            <label for="service_bottom"><span data-i18n="flooring_install_label_service">Service</span><span class="required">*</span></label>
            <select id="service_bottom" name="service" required>
              <option value="" data-i18n="flooring_install_option_select">Select an option</option>
              <option value="lvp_supply_install" data-i18n="flooring_install_option_service_lvp">Waterproof LVP – supply &amp; install</option>
              <option value="installation_only" data-i18n="flooring_install_option_service_install_only">Installation only (Laminate / Vinyl / Hardwood)</option>
              <option value="other_flooring" data-i18n="flooring_install_option_service_other_short">Other flooring</option>
            </select>
          </div>

          <div class="install-field">
            <label for="project_details_bottom" data-i18n="flooring_install_label_project_details">Project details</label>
            <textarea id="project_details_bottom" name="project_details" rows="3" placeholder="Rooms, deadlines, special conditions, kids/pets, HOA rules…" data-i18n-placeholder="flooring_install_placeholder_project_details"></textarea>
          </div>

          <div class="install-extra">
            <button type="button" class="install-extra-toggle" aria-expanded="false">
              <span data-i18n="flooring_install_extra_toggle">More project details (optional)</span>
            </button>

            <div class="install-extra-panel" hidden>
              <div class="install-field">
                <label for="property_type_bottom" data-i18n="flooring_install_label_property_type">Property type</label>
                <select id="property_type_bottom" name="property_type">
                  <option value="" data-i18n="flooring_install_option_select">Select an option</option>
                  <option value="house" data-i18n="flooring_install_option_house">House</option>
                  <option value="apartment" data-i18n="flooring_install_option_apartment">Apartment / Condo</option>
                  <option value="townhouse" data-i18n="flooring_install_option_townhouse">Townhouse</option>
                  <option value="business" data-i18n="flooring_install_option_business">Business / Commercial</option>
                  <option value="other" data-i18n="flooring_install_option_other">Other</option>
                </select>
              </div>

              <div class="install-field">
                <span class="install-label" data-i18n="flooring_install_label_areas">Areas you want to install</span>
                <div class="install-options install-options-multi">
                  <label><input type="checkbox" class="ratio-correct-display"name="areas[]" value="living_room"> <span data-i18n="flooring_install_option_area_living_room">Living room / Family room</span></label>
                  <label><input type="checkbox" class="ratio-correct-display"name="areas[]" value="bedrooms"> <span data-i18n="flooring_install_option_area_bedrooms">Bedrooms</span></label>
                  <label><input type="checkbox" class="ratio-correct-display"name="areas[]" value="hallways"> <span data-i18n="flooring_install_option_area_hallways">Hallways</span></label>
                  <label><input type="checkbox" class="ratio-correct-display"name="areas[]" value="kitchen"> <span data-i18n="flooring_install_option_area_kitchen">Kitchen</span></label>
                  <label><input type="checkbox" class="ratio-correct-display"name="areas[]" value="bathrooms"> <span data-i18n="flooring_install_option_area_bathrooms">Bathrooms</span></label>
                  <label><input type="checkbox" class="ratio-correct-display"name="areas[]" value="stairs"> <span data-i18n="flooring_install_option_area_stairs">Stairs</span></label>
                  <label><input type="checkbox" class="ratio-correct-display"name="areas[]" value="entire_home"> <span data-i18n="flooring_install_option_area_entire_home">Entire home</span></label>
                </div>
              </div>

              <div class="install-field install-field--inline">
                <div>
                  <label for="square_footage_bottom" data-i18n="flooring_install_label_square_footage_short">Approx. square footage</label>
                  <input type="number" id="square_footage_bottom" name="square_footage" min="0" step="1" placeholder="Example: 900" data-i18n-placeholder="flooring_install_placeholder_square_footage">
                </div>
                <div>
                  <label for="has_stairs_bottom" data-i18n="flooring_install_label_has_stairs_short">Stairs to cover</label>
                  <select id="has_stairs_bottom" name="has_stairs">
                    <option value="" data-i18n="flooring_install_option_select_short">Select</option>
                    <option value="yes" data-i18n="flooring_install_option_yes">Yes</option>
                    <option value="no" data-i18n="flooring_install_option_no">No</option>
                    <option value="not_sure" data-i18n="flooring_install_option_not_sure">Not sure</option>
                  </select>
                </div>
              </div>

              <div class="install-field">
                <span class="install-label" data-i18n="flooring_install_label_has_material">Do you already have the flooring material?</span>
                <div class="install-options">
                  <label><input type="radio" class="ratio-correct-display" name="has_material" value="need_supply"> <span data-i18n="flooring_install_option_has_material_need_supply">No, I need you to supply it</span></label>
                  <label><input type="radio" class="ratio-correct-display" name="has_material" value="already_have"> <span data-i18n="flooring_install_option_has_material_already_short">Yes, I already bought it</span></label>
                  <label><input type="radio" class="ratio-correct-display" name="has_material" value="need_guidance"> <span data-i18n="flooring_install_option_has_material_guidance">I’m not sure, need guidance</span></label>
                </div>
              </div>

              <div class="install-field install-field--inline">
                <div>
                  <label for="floor_type_bottom" data-i18n="flooring_install_label_floor_type_short">Floor type interested in</label>
                  <select id="floor_type_bottom" name="floor_type">
                    <option value="" data-i18n="flooring_install_option_select_short">Select</option>
                    <option value="waterproof_lvp" data-i18n="flooring_install_option_floor_type_lvp">Waterproof LVP</option>
                    <option value="laminate" data-i18n="flooring_install_option_floor_type_laminate">Laminate</option>
                    <option value="wood" data-i18n="flooring_install_option_floor_type_wood">Wood (solid/engineered)</option>
                    <option value="deciding" data-i18n="flooring_install_option_floor_type_deciding">Still deciding / Need recommendation</option>
                  </select>
                </div>
                <div>
                  <label for="install_timing_bottom" data-i18n="flooring_install_label_install_timing">When are you planning to install?</label>
                  <select id="install_timing_bottom" name="install_timing">
                    <option value="" data-i18n="flooring_install_option_select_short">Select</option>
                    <option value="asap" data-i18n="flooring_install_option_timing_asap">As soon as possible (1–2 weeks)</option>
                    <option value="1_month" data-i18n="flooring_install_option_timing_1_month">Within 1 month</option>
                    <option value="2_3_months" data-i18n="flooring_install_option_timing_2_3_months">In 2–3 months</option>
                    <option value="exploring" data-i18n="flooring_install_option_timing_exploring">Just exploring options</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <p class="install-legal" data-i18n="flooring_install_legal_contact">
            By sending, you agree to be contacted via WhatsApp, phone or email.
          </p>

          <input type="hidden" name="message" value="">
          <input type="hidden" name="service_detail" value="Flooring installation free estimate">
          <input type="hidden" name="city" value="" id="city_bottom">
          <input type="hidden" name="form_name" value="B&S – Web Lead Flooring Services (contact)" />
          <input type="hidden" name="source" value="<?=$contact_source?>" />
          <button type="submit" class="button button--primary" id="send-btn-bottom">
            <span data-i18n="cta_send">Send request</span>
          </button>
          <p id="form-status-bottom" class="note hide" aria-live="polite"></p>
        </form>
      </div>

      <aside class="contact-estimate-info">
        <h3 data-i18n="info_t">Contact info</h3>
        <p><strong data-i18n="info_phone_label">Phone (WhatsApp):</strong> <a href="https://wa.me/16892968515" target="_blank" rel="noopener">+1 (689) 296-8515</a></p>
        <p><strong data-i18n="info_alt_phone_label">Alt. phone:</strong> +1 (407) 225-1284</p>
        <p><strong data-i18n="info_email_label">Email:</strong> info@globalservices.com</p>
        <p><strong data-i18n="info_service_area_label">Service area:</strong> Orlando, FL</p>
        <div class="hero-cta">
          <a href="https://instagram.com/bsfloorsupply" class="button button--ghost" target="_blank" rel="noopener" data-i18n="flooring_install_social_instagram">Instagram</a>
          <a href="https://facebook.com/BSGlobalServices" class="button button--ghost" target="_blank" rel="noopener" data-i18n="flooring_install_social_facebook">Facebook</a>
        </div>
        <p><small data-i18n-html="info_spanish_note">Prefer español? <em>También atendemos en español.</em></small></p>
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

    const t = (key, fallback) => window.bsI18n?.t?.(key) || fallback;

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
        if(status) status.textContent = t('flooring_install_form_status_sending_request', 'Sending your request…');
        const formData = new FormData(form);
        Array.from(form.elements).forEach(el=>el.disabled=true);
        if(sendBtn) sendBtn.textContent = t('flooring_install_form_status_sending', 'Sending…');
        try{
          const res = await fetch(form.action || 'lead.php', {method:'POST', body:formData});
          const data = await res.json();
          if(status) status.textContent = data.data || t('flooring_install_form_status_sent', 'Request sent.');
          if(res.ok && data.code === '01') form.reset();
        }catch(err){
          if(status) status.textContent = t('flooring_install_form_status_error', 'An error occurred. Please try again later.');
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
