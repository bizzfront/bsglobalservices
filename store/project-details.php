<?php
require_once __DIR__.'/utils.php';

$products = array_map('normalize_store_product', load_store_products());
$storeConfig = load_store_config();
$base = '../';
$active = 'cart';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Project details — B&S Floor Supply</title>
  <link rel="stylesheet" href="<?=$base?>style.css" />
  <style>
    :root {
      --bs-bg: #f5f2ec;
      --bs-card-bg: #ffffff;
      --bs-border: #e0ddd6;
      --bs-text-main: #2b2623;
      --bs-text-muted: #7a7168;
      --bs-primary: #4a1f1c;
      --bs-primary-soft: #6a3931;
      --bs-accent: #c9a646;
      --bs-accent-soft: #e3cf8a;
    }

    body.project-shell {
      background: var(--bs-bg);
      color: var(--bs-text-main);
    }

    .page-wrapper {
      max-width: 1200px;
      margin: 0 auto;
      padding: 1.5rem 1rem 3rem;
    }

    header.checkout-header { margin-bottom: 1.25rem; }
    .breadcrumb { font-size: 0.85rem; color: var(--bs-text-muted); margin-bottom: 0.35rem; }
    .breadcrumb a { color: var(--bs-text-muted); }
    .checkout-title-row { display:flex; flex-wrap:wrap; justify-content:space-between; gap:0.75rem; align-items:flex-end; }
    .checkout-title-row h1 { margin:0; color: var(--bs-primary); font-size:1.6rem; }
    .checkout-subtitle { color: var(--bs-text-muted); margin:0.35rem 0 0; font-size:0.95rem; }
    .checkout-meta { font-size:0.85rem; color: var(--bs-text-muted); text-align:right; }

    .checkout-layout { display:grid; grid-template-columns: minmax(0, 2.05fr) minmax(280px, 1fr); gap:1.3rem; align-items:flex-start; }
    .checkout-form-card { background: var(--bs-card-bg); border-radius: 0.9rem; border:1px solid var(--bs-border); box-shadow:0 4px 18px rgba(0,0,0,0.03); padding:1.2rem 1.25rem 1.4rem; display:flex; flex-direction:column; gap:1.2rem; }
    .section-title { font-size:1rem; font-weight:700; color:var(--bs-primary); margin:0 0 0.2rem; }
    .section-subtitle { font-size:0.88rem; color:var(--bs-text-muted); margin:0; }
    .fieldset { display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap:0.9rem 1rem; margin-top:0.75rem; }
    .fieldset.full { grid-template-columns: 1fr; }
    .form-field { display:flex; flex-direction:column; gap:0.25rem; }
    .form-label { font-size:0.86rem; font-weight:600; color:var(--bs-text-main); }
    .required { color:#b0303c; margin-left:0.08rem; }
    .form-input, .form-select, .form-textarea { padding:0.5rem 0.65rem; border-radius:0.55rem; border:1px solid var(--bs-border); font-size:0.9rem; font-family:inherit; background:#fdfbf7; color:var(--bs-text-main); }
    .form-input:focus, .form-select:focus, .form-textarea:focus { outline:none; border-color: var(--bs-primary-soft); box-shadow:0 0 0 1px rgba(74,31,28,0.07); background:#fff; }
    .form-helper { font-size:0.8rem; color:var(--bs-text-muted); }
    .inline-radio-group { display:flex; flex-wrap:wrap; gap:0.75rem; font-size:0.88rem; }
    .inline-radio { display:inline-flex; align-items:center; gap:0.35rem; cursor:pointer; }
    .inline-radio input { accent-color: var(--bs-primary); }
    .service-card-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:0.8rem; margin-top:0.4rem; }
    .service-card { position:relative; display:flex; gap:0.55rem; align-items:flex-start; padding:0.85rem 0.95rem; border-radius:0.85rem; border:1px solid var(--bs-border); background:#fff; cursor:pointer; transition:border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease; }
    .service-card:hover { border-color: var(--bs-primary-soft); box-shadow:0 10px 20px rgba(74,31,28,0.08); }
    .service-card input { position:absolute; opacity:0; pointer-events:none; }
    .service-card-check { width:18px; height:18px; border:1px solid #c9beba; border-radius:6px; background:#fdfbf7; display:grid; place-items:center; flex-shrink:0; transition:border-color 0.2s ease, background-color 0.2s ease; }
    .service-card-check::after { content:""; width:8px; height:8px; border-radius:2px; background:#fff; opacity:0; transition:opacity 0.2s ease; }
    .service-card-body { display:flex; flex-direction:column; gap:0.25rem; min-width:0; }
    .service-card-title { font-weight:700; color:var(--bs-text-main); font-size:0.95rem; }
    .service-card-desc { font-size:0.85rem; color:var(--bs-text-muted); line-height:1.35; }
    .service-card input:checked + .service-card-check { background:var(--bs-primary); border-color:var(--bs-primary); }
    .service-card input:checked + .service-card-check::after { opacity:1; }
    .service-card input:checked ~ .service-card-body .service-card-title { color:var(--bs-primary); }
    .service-card input:focus-visible + .service-card-check { outline:2px solid var(--bs-primary); outline-offset:2px; }
    .disclaimer-box { margin-top:0.75rem; padding:0.7rem 0.8rem; border-radius:0.6rem; background:#fdf9f0; border:1px dashed var(--bs-accent-soft); font-size:0.82rem; color:var(--bs-text-main); }
    .disclaimer-box strong { color:var(--bs-primary); }
    .consent-group { display:flex; flex-direction:column; gap:0.4rem; margin-top:0.5rem; font-size:0.82rem; color:var(--bs-text-main); }
    .consent-option { display:inline-flex; gap:0.3rem; align-items:flex-start; }
    .consent-option input { margin-top:0.1rem; accent-color:var(--bs-primary); }
    .consent-card-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap:0.8rem; margin-top:0.6rem; }
    .consent-card .service-card-body { gap:0.35rem; }
    .consent-card .service-card-desc { font-size:0.88rem; }
    .submit-row { margin-top:0.8rem; display:flex; flex-wrap:wrap; gap:0.6rem; align-items:center; }
    .btn-primary.round { border-radius:999px; padding:0.65rem 1.1rem; font-weight:650; }
    .btn-text { border:none; background:none; color:var(--bs-text-muted); font-size:0.85rem; cursor:pointer; text-decoration:underline; padding:0; }

    .summary-card { position:sticky; top:1rem; padding:1rem; background:var(--bs-card-bg); border-radius:0.9rem; border:1px solid var(--bs-border); box-shadow:0 4px 18px rgba(0,0,0,0.03); display:flex; flex-direction:column; gap:0.75rem; font-size:0.9rem; }
    .summary-header { display:flex; justify-content:space-between; gap:0.5rem; align-items:baseline; }
    .summary-title { font-size:1rem; font-weight:700; color:var(--bs-primary); }
    .summary-edit { font-size:0.82rem; }
    .summary-list { border-top:1px dashed var(--bs-border); padding-top:0.45rem; display:flex; flex-direction:column; gap:0.4rem; max-height:220px; overflow:auto; }
    .summary-item { display:flex; justify-content:space-between; gap:0.45rem; }
    .summary-item-title { font-weight:600; }
    .summary-item-meta { font-size:0.8rem; color:var(--bs-text-muted); }
    .summary-item-right { text-align:right; white-space:nowrap; }
    .summary-subtotals { border-top:1px dashed var(--bs-border); padding-top:0.4rem; display:flex; flex-direction:column; gap:0.25rem; }
    .summary-row { display:flex; justify-content:space-between; gap:0.35rem; align-items:baseline; }
    .summary-label { color:var(--bs-text-muted); }
    .summary-value { font-weight:600; }
    .summary-total { font-size:1.05rem; font-weight:700; color:var(--bs-primary); margin-top:0.2rem; }
    .summary-note { font-size:0.8rem; color:var(--bs-text-muted); line-height:1.4; }
    .summary-contact-note { font-size:0.8rem; color:var(--bs-text-muted); }
    .summary-contact-note strong { color:var(--bs-primary); }

    @media (max-width: 880px){
      .checkout-layout { grid-template-columns: 1fr; }
      .summary-card { position:static; }
    }
    @media (max-width: 640px){
      .fieldset { grid-template-columns: 1fr; }
      .checkout-title-row { align-items:flex-start; }
    }
    @media print {
      body { background:#fff; }
      .checkout-layout { grid-template-columns: 1fr; }
      .summary-card { box-shadow:none; border:1px solid #ccc; position:static; }
      .btn-primary, .btn-text, header.checkout-header { display:none !important; }
    }
  </style>
</head>
<body class="project-shell">
<?php include $base.'includes/header.php'; ?>
<div class="page-wrapper">
  <header class="checkout-header">
    <div class="breadcrumb"><a href="<?=$base?>" data-i18n="store_breadcrumb_home">Home</a> &gt; <a href="<?=$base?>store/index.php" data-i18n="store_breadcrumb_store">Store</a> &gt; <a href="cart.php" data-i18n="store_breadcrumb_cart">Cart</a> &gt; <span data-i18n="store_breadcrumb_project_details">Project details</span></div>
    <div class="checkout-title-row">
      <div>
        <h1 data-i18n="store_project_details_title">Project details &amp; quote request</h1>
        <p class="checkout-subtitle" data-i18n="store_project_details_subtitle">Share your contact and project details so we can confirm stock, scheduling and final pricing.</p>
      </div>
      <div class="checkout-meta" data-i18n="store_project_details_meta">Custom quote · No online payment · B&amp;S team will contact you</div>
    </div>
  </header>

  <main class="checkout-layout">
    <section aria-label="Project details form">
      <form id="project-form" class="checkout-form-card" action="<?=$base?>lead.php" method="POST">
        <section>
          <h2 class="section-title" data-i18n="store_section_contact_title">1. Your contact information</h2>
          <p class="section-subtitle" data-i18n="store_section_contact_subtitle">We’ll use this information to send your quote and coordinate your project.</p>
          <div class="fieldset">
            <div class="form-field">
              <label class="form-label" for="name"><span data-i18n="store_label_full_name">Full name</span> <span class="required">*</span></label>
              <input id="name" name="name" type="text" class="form-input" required />
            </div>
            <div class="form-field">
              <label class="form-label" for="email"><span data-i18n="store_label_email">Email</span> <span class="required">*</span></label>
              <input id="email" name="email" type="email" class="form-input" required />
              <div class="form-helper" data-i18n="store_helper_email">We’ll send your quote and any documents here.</div>
            </div>
            <div class="form-field">
              <label class="form-label" for="phone"><span data-i18n="store_label_phone">WhatsApp / mobile</span> <span class="required">*</span></label>
              <input id="phone" name="phone" type="tel" class="form-input" required />
              <div class="form-helper" data-i18n="store_helper_phone">This is our main channel for updates and questions.</div>
            </div>
            <div class="form-field">
              <span class="form-label" data-i18n="store_label_client_type">You are:</span>
              <div class="inline-radio-group">
                <label class="inline-radio"><input type="radio" name="client_type" value="homeowner" checked /> <span data-i18n="store_option_homeowner">Homeowner</span></label>
                <label class="inline-radio"><input type="radio" name="client_type" value="contractor" /> <span data-i18n="store_option_contractor">Contractor / installer</span></label>
              </div>
            </div>
          </div>
        </section>

        <section>
          <h2 class="section-title" data-i18n="store_section_location_title">2. Project location &amp; space</h2>
          <p class="section-subtitle" data-i18n="store_section_location_subtitle">Tell us where the project will be installed and what type of space it is.</p>
          <div class="fieldset full">
            <div class="form-field">
              <label class="form-label" for="address"><span data-i18n="store_label_address">Project address (city, area, ZIP)</span> <span class="required">*</span></label>
              <input id="address" name="address" type="text" class="form-input" required />
              <div class="form-helper" data-i18n="store_helper_address">Example: "Meadow Woods, Orlando FL 32824"</div>
            </div>
          </div>
          <div class="fieldset">
            <div class="form-field">
              <label class="form-label" for="space_type" data-i18n="store_label_space_type">Space type</label>
              <select id="space_type" name="space_type" class="form-select">
                <option value="" data-i18n="store_option_select">Select an option</option>
                <option value="apartment" data-i18n="store_option_apartment">Apartment</option>
                <option value="single-family" data-i18n="store_option_single_family">Single-family home</option>
                <option value="multi-family" data-i18n="store_option_multi_family">Multi-family / condo</option>
                <option value="commercial" data-i18n="store_option_commercial">Commercial</option>
                <option value="hospitality" data-i18n="store_option_hospitality">Hospitality</option>
                <option value="other" data-i18n="store_option_other">Other</option>
              </select>
            </div>
            <div class="form-field">
              <label class="form-label" for="space_status" data-i18n="store_label_property_status">Property status</label>
              <select id="space_status" name="space_status" class="form-select">
                <option value="" data-i18n="store_option_select">Select an option</option>
                <option value="occupied" data-i18n="store_option_occupied">Occupied</option>
                <option value="vacant" data-i18n="store_option_vacant">Vacant</option>
                <option value="new-build" data-i18n="store_option_new_build">New construction</option>
                <option value="remodel" data-i18n="store_option_remodel">Remodel in progress</option>
              </select>
            </div>
            <div class="form-field">
              <label class="form-label" for="floor_level" data-i18n="store_label_floor_level">Floor level</label>
              <input id="floor_level" name="floor_level" type="text" class="form-input" placeholder="E.g. 1st floor, 10th floor" data-i18n-placeholder="store_placeholder_floor_level" />
            </div>
            <div class="form-field">
              <label class="form-label" for="access_notes" data-i18n="store_label_building_access">Building access</label>
              <input id="access_notes" name="access_notes" type="text" class="form-input" placeholder="Elevator access, stairs, parking instructions" data-i18n-placeholder="store_placeholder_access_notes" />
            </div>
          </div>
        </section>

        <section>
          <h2 class="section-title" data-i18n="store_section_services_title">3. Services &amp; timing</h2>
          <p class="section-subtitle" data-i18n="store_section_services_subtitle">Select the services you need and when you’d like to start.</p>
          <div class="fieldset full">
            <div class="service-card-grid" role="group" aria-label="Requested services">
              <label class="service-card">
                <input type="checkbox" name="service_supply" checked />
                <span class="service-card-check" aria-hidden="true"></span>
                <div class="service-card-body">
                  <div class="service-card-title" data-i18n="store_service_material_supply">Material supply</div>
                  <div class="service-card-desc" data-i18n="store_service_material_supply_desc">Reserve flooring, trims and adhesives from our stock.</div>
                </div>
              </label>
              <label class="service-card">
                <input type="checkbox" name="service_install" />
                <span class="service-card-check" aria-hidden="true"></span>
                <div class="service-card-body">
                  <div class="service-card-title" data-i18n="store_service_installation">Installation</div>
                  <div class="service-card-desc" data-i18n="store_service_installation_desc">Schedule B&amp;S installers to handle the full installation.</div>
                </div>
              </label>
              <label class="service-card">
                <input type="checkbox" name="service_removal" />
                <span class="service-card-check" aria-hidden="true"></span>
                <div class="service-card-body">
                  <div class="service-card-title" data-i18n="store_service_removal">Removal of existing floor</div>
                  <div class="service-card-desc" data-i18n="store_service_removal_desc">We can remove and dispose of existing flooring materials.</div>
                </div>
              </label>
              <label class="service-card">
                <input type="checkbox" name="service_prep" />
                <span class="service-card-check" aria-hidden="true"></span>
                <div class="service-card-body">
                  <div class="service-card-title" data-i18n="store_service_prep">Floor preparation / leveling</div>
                  <div class="service-card-desc" data-i18n="store_service_prep_desc">Surface prep, leveling or moisture barrier as needed.</div>
                </div>
              </label>
              <label class="service-card">
                <input type="checkbox" name="service_baseboards" />
                <span class="service-card-check" aria-hidden="true"></span>
                <div class="service-card-body">
                  <div class="service-card-title" data-i18n="store_service_baseboards">Baseboards / transitions</div>
                  <div class="service-card-desc" data-i18n="store_service_baseboards_desc">Include matching baseboards, trims or transition pieces.</div>
                </div>
              </label>
            </div>
          </div>
          <div class="fieldset">
            <div class="form-field">
              <label class="form-label" for="start_date" data-i18n="store_label_start_date">Desired start date</label>
              <input id="start_date" name="start_date" type="date" class="form-input" />
            </div>
            <div class="form-field">
              <label class="form-label" for="timeframe" data-i18n="store_label_project_timing">Project timing</label>
              <select id="timeframe" name="timeframe" class="form-select">
                <option value="" data-i18n="store_option_select">Select an option</option>
                <option value="urgent" data-i18n="store_option_asap">As soon as possible</option>
                <option value="30-days" data-i18n="store_option_30_days">Within 30 days</option>
                <option value="60-days" data-i18n="store_option_60_days">Within 60 days</option>
                <option value="planning" data-i18n="store_option_planning">Just planning / comparing options</option>
              </select>
            </div>
          </div>
          <div class="disclaimer-box">
            <strong data-i18n="store_delivery_install_notes_title">Delivery & installation notes:</strong> <span data-i18n="store_delivery_install_notes">final pricing may adjust after verifying exact quantities, delivery zone and site access. Our team will confirm schedules with you.</span>
          </div>
        </section>

        <section>
          <h2 class="section-title" data-i18n="store_section_delivery_title">4. Delivery &amp; access preferences</h2>
          <p class="section-subtitle" data-i18n="store_section_delivery_subtitle">Let us know how you’d like to receive materials and any access details.</p>
          <div class="fieldset">
            <div class="form-field">
              <label class="form-label" data-i18n="store_label_delivery_preference">Delivery preference</label>
              <div class="inline-radio-group">
                <label class="inline-radio"><input type="radio" name="delivery_preference" value="delivery" checked /> <span data-i18n="store_option_delivery_bs">Delivery by B&amp;S</span></label>
                <label class="inline-radio"><input type="radio" name="delivery_preference" value="pickup" /> <span data-i18n="store_option_pickup">Warehouse pick-up</span></label>
              </div>
            </div>
            <div class="form-field">
              <label class="form-label" for="delivery_zip" data-i18n="store_delivery_zip_label">Delivery ZIP Code</label>
              <input id="delivery_zip" name="delivery_zip" type="text" class="form-input" list="delivery_zip_list" placeholder="Enter ZIP Code or leave blank for warehouse" data-i18n-placeholder="store_delivery_pickup_placeholder" inputmode="numeric" pattern="\\d*" />
              <datalist id="delivery_zip_list"></datalist>
              <div class="form-helper" id="delivery_zip_note"></div>
            </div>
            <div class="form-field">
              <label class="form-label" for="delivery_notes" data-i18n="store_label_delivery_notes">Delivery / building notes</label>
              <input id="delivery_notes" name="delivery_notes" type="text" class="form-input" placeholder="Gate code, parking, preferred time window" data-i18n-placeholder="store_placeholder_delivery_notes" />
            </div>
          </div>
        </section>

        <section>
          <h2 class="section-title" data-i18n="store_section_scope_title">5. Project scope &amp; measurements</h2>
          <p class="section-subtitle" data-i18n="store_section_scope_subtitle">Add any measurements or room notes to help us prepare your quote.</p>
          <div class="fieldset">
            <div class="form-field">
              <label class="form-label" for="area_size" data-i18n="store_label_area_size">Approximate area / linear feet</label>
              <input id="area_size" name="area_size" type="text" class="form-input" placeholder="E.g. 850 sq ft, 120 lf baseboards" data-i18n-placeholder="store_placeholder_area_size" />
            </div>
            <div class="form-field">
              <label class="form-label" for="rooms" data-i18n="store_label_rooms">Rooms or zones</label>
              <input id="rooms" name="rooms" type="text" class="form-input" placeholder="Living room, bedrooms, hallway" data-i18n-placeholder="store_placeholder_rooms" />
            </div>
          </div>
          <div class="fieldset full">
            <div class="form-field">
              <label class="form-label" for="message"><span data-i18n="store_label_project_notes">Project notes</span> <span class="required">*</span></label>
              <textarea id="message" name="message" rows="4" class="form-textarea" placeholder="Tell us about subfloor condition, demolition needs, stairs, trims or any questions" data-i18n-placeholder="store_placeholder_project_notes" required></textarea>
              <div class="form-helper" data-i18n="store_helper_project_notes">We’ll include these details with your project request.</div>
            </div>
          </div>
        </section>

        <section>
          <h2 class="section-title" data-i18n="store_section_consent_title">6. Consent &amp; confirmation</h2>
          <div class="consent-card-grid" role="group" aria-label="Consents">
            <label class="service-card consent-card">
              <input type="checkbox" name="consent_custom_quote" required />
              <span class="service-card-check" aria-hidden="true"></span>
              <div class="service-card-body">
                <div class="service-card-title" data-i18n="store_consent_quote_title">Custom quote acknowledgment</div>
                <div class="service-card-desc" data-i18n="store_consent_quote_desc">Pricing will be confirmed by B&amp;S Floor Supply based on stock, delivery zone and installation details.</div>
              </div>
            </label>
            <label class="service-card consent-card">
              <input type="checkbox" name="consent_whatsapp" required />
              <span class="service-card-check" aria-hidden="true"></span>
              <div class="service-card-body">
                <div class="service-card-title" data-i18n="store_consent_updates_title">Project updates</div>
                <div class="service-card-desc" data-i18n="store_consent_updates_desc">I agree to receive updates and questions about this project via WhatsApp and email.</div>
              </div>
            </label>
          </div>
          <div class="submit-row">
            <button type="submit" class="btn btn-primary round" id="project-submit" disabled data-i18n="store_send_project">Send my project &amp; request quote</button>
            <button type="button" class="btn-text" onclick="window.location.href='cart.php'" data-i18n="store_back_to_cart">Back to cart</button>
            <div class="form-helper" data-i18n="store_project_confirmation_note">You’ll see a confirmation screen after sending this form.</div>
          </div>
          <p id="project-status" class="note" aria-live="polite" style="margin:0;"></p>
        </section>

        <input type="hidden" name="service" value="project_request" />
        <input type="hidden" name="form_name" value="B&S – Project checkout" />
        <input type="hidden" name="source" value="website_store" />
        <input type="hidden" name="cart" id="project-cart-field" />
        <input type="hidden" name="cart_totals" id="project-cart-totals" />
      </form>
    </section>

    <aside class="checkout-sidebar" aria-label="Cart summary">
      <div class="summary-card">
        <div class="summary-header">
          <div class="summary-title" data-i18n="store_project_selection_title">Your project selection</div>
          <div class="summary-edit"><a href="cart.php" data-i18n="store_edit_cart">Edit cart</a></div>
        </div>
        <div class="summary-list" id="summary-items"></div>
        <div class="summary-subtotals">
          <div class="summary-row"><div class="summary-label" data-i18n="store_summary_materials_subtotal">Materials subtotal</div><div class="summary-value" id="summary-material">$0.00</div></div>
          <div class="summary-row"><div class="summary-label" data-i18n="store_summary_installation">Installation</div><div class="summary-value" id="summary-install">$0.00</div></div>
          <div class="summary-row"><div class="summary-label" data-i18n="store_summary_delivery">Delivery</div><div class="summary-value" id="summary-delivery">$0.00</div></div>
          <div class="summary-row"><div class="summary-label" data-i18n="store_summary_taxes">Taxes</div><div class="summary-value" id="summary-taxes">$0.00</div></div>
          <div class="summary-total" id="summary-total" data-i18n="store_summary_estimated_total_label">Estimated total: $0.00</div>
        </div>
        <p class="summary-note" id="summary-note" data-i18n="store_summary_final_note">Final quote may adjust after verifying actual quantities, site conditions and delivery zone.</p>
        <p class="summary-contact-note"><strong data-i18n="store_summary_contact_strong">We will contact you</strong> <span data-i18n="store_summary_contact_note">using the phone and email you provide in this form.</span></p>
      </div>
    </aside>
  </main>
</div>
<?php include $base.'includes/footer.php'; ?>
<script>
const STORE_PRODUCTS = <?= json_encode(array_values($products)) ?>;
const STORE_CONFIG = <?= json_encode($storeConfig) ?>;
const PROJECT_KEY = 'bs_project';
const ZIP_ZONE_FILE = 'zip_zones.json';
const ZIP_ZONE_MAPPING = {A: 'meadow', B: 'orlando', C: 'orlando'};
let ZIP_DATA = [];
let zipLoadPromise = null;
const t = (key, fallback = '') => window.bsI18n?.t?.(key) || fallback;
const formatTemplate = (template, data) => template.replace(/\{\{(\w+)\}\}/g, (_, key) => data[key] ?? '');

function formatCurrency(value){
  const num = Number(value);
  return Number.isFinite(num) ? `$${num.toFixed(2)}` : '$0.00';
}

function formatUnits(value){
  const num = Number(value);
  if(!Number.isFinite(num)) return '';
  return num.toLocaleString(undefined, {maximumFractionDigits: 2});
}
function mapZoneFromLetter(letter){
  return ZIP_ZONE_MAPPING[letter?.toString().trim().toUpperCase()] || null;
}
function ensureZipData(){
  if(zipLoadPromise) return zipLoadPromise;
  zipLoadPromise = fetch(ZIP_ZONE_FILE)
    .then(res=>res.ok ? res.json() : [])
    .then(data=>{ ZIP_DATA = Array.isArray(data) ? data : []; return ZIP_DATA; })
    .catch(()=>{ ZIP_DATA = []; return ZIP_DATA; });
  return zipLoadPromise;
}
function resolveZipEntry(zip){
  const normalized = (zip || '').toString().trim();
  if(!normalized || !ZIP_DATA.length) return null;
  const entry = ZIP_DATA.find(z=>z.zip === normalized);
  if(!entry) return null;
  const zoneId = mapZoneFromLetter(entry.zone);
  return {...entry, zoneId};
}

function loadProject(){
  try{
    return JSON.parse(localStorage.getItem(PROJECT_KEY) || '{}');
  }catch(e){
    return {};
  }
}

const project = loadProject();
if(!project.items || !project.items.length){
  window.location.href = 'cart.php';
}

document.getElementById('project-cart-field').value = JSON.stringify(project.items || []);
document.getElementById('project-cart-totals').value = JSON.stringify(project.totals || {});

const itemsContainer = document.getElementById('summary-items');
const totals = project.totals || {material:0, install:0, delivery:0, taxes:0, total:0};

if(Array.isArray(project.items)){
  itemsContainer.innerHTML = project.items.map(it=>{
    const p = it.product || {};
    const unit = p.measurementUnit === 'lf' ? 'lf' : p.measurementUnit === 'piece' ? 'piece' : 'sq ft';
    const pkgLabel = p.packageLabelPlural || p.packageLabel || 'boxes';
    const priceTypeLabel = it.priceType === 'backorder' ? t('store_label_order_in', 'Order-in') : t('store_label_in_stock', 'In stock');
    const metaParts = [];
    if(p.category) metaParts.push(p.category);
    metaParts.push(`${it.quantity} ${pkgLabel}`);
    if(p.packageCoverage) metaParts.push(`≈ ${formatUnits(it.quantity * p.packageCoverage)} ${unit}`);
    if(it.install) metaParts.push(t('store_install_included', 'Install included'));
    if(p.taxesOmit) metaParts.push(t('store_tax_exempt', 'Tax exempt'));
    else if(it.taxes) metaParts.push(`${t('store_summary_taxes', 'Taxes')} ${formatCurrency(it.taxes)}`);
    metaParts.push(priceTypeLabel);
    const itemTotal = (it.subtotal || 0) + (it.install || 0) + (it.delivery || 0) + (it.taxes || 0);
    const taxesLabel = p.taxesOmit ? t('store_tax_exempt', 'Tax exempt') : `${t('store_summary_taxes', 'Taxes')} ${formatCurrency(it.taxes || 0)}`;
    return `
      <div class="summary-item">
        <div>
          <div class="summary-item-title">${p.name || it.sku || t('store_product_fallback', 'Product')}</div>
          <div class="summary-item-meta">${metaParts.filter(Boolean).join(' · ')}</div>
        </div>
        <div class="summary-item-right">
          <div>${formatCurrency(itemTotal)}</div>
          <div class="summary-item-meta">${formatCurrency(it.subtotal || 0)} ${t('store_material_label', 'material')} · ${taxesLabel}</div>
        </div>
      </div>`;
  }).join('');
}

document.getElementById('summary-material').textContent = formatCurrency(totals.material);
document.getElementById('summary-install').textContent = formatCurrency(totals.install);
document.getElementById('summary-delivery').textContent = formatCurrency(totals.delivery);
document.getElementById('summary-taxes').textContent = formatCurrency(totals.taxes);
document.getElementById('summary-total').textContent = `${t('store_summary_estimated_total', 'Estimated total')}: ${formatCurrency(totals.total)}`;
const summaryNote = document.getElementById('summary-note');
if(summaryNote && STORE_CONFIG.install?.notes){
  summaryNote.textContent = STORE_CONFIG.install.notes;
}

const deliveryPreferenceInputs = document.querySelectorAll('input[name="delivery_preference"]');
let defaultDeliveryPreference = 'delivery';
if(deliveryPreferenceInputs.length){
  const pickupSelected = project?.deliveryPreferences?.includeDelivery === false || project?.deliveryZone === 'pick-up';
  defaultDeliveryPreference = pickupSelected ? 'pickup' : 'delivery';
  deliveryPreferenceInputs.forEach(input=>{
    input.checked = input.value === defaultDeliveryPreference;
  });
}
const deliveryZipInput = document.getElementById('delivery_zip');
const deliveryZipNote = document.getElementById('delivery_zip_note');
const deliveryZipList = document.getElementById('delivery_zip_list');
function renderDeliveryZipNote(entry, includeDelivery){
  if(!deliveryZipNote) return;
  const zones = STORE_CONFIG?.delivery?.zones || [];
  const pickup = zones.find(z=>z.id === 'pick-up');
  const zone = zones.find(z=>z.id === (entry?.zoneId || project?.deliveryZone));
  const pickupLabel = pickup ? `${pickup.label}${pickup.fee!=null ? ' — '+formatCurrency(pickup.fee) : ''}` : t('store_pickup_default', 'Warehouse pick-up (default)');
  if(includeDelivery === false){
    deliveryZipNote.textContent = pickupLabel;
    return;
  }
  const zoneLabel = zone ? `${zone.label}${zone.fee!=null ? ' — '+formatCurrency(zone.fee) : ''}` : '';
  const cityLabel = entry?.city && entry?.zip ? `${entry.city} (${entry.zip})` : '';
  deliveryZipNote.textContent = [cityLabel || t('store_delivery_zip_pending', 'Delivery ZIP pending'), zoneLabel].filter(Boolean).join(' · ');
}
ensureZipData().then(()=>{
  if(deliveryZipList){
    deliveryZipList.innerHTML = ZIP_DATA.map(z=>`<option value="${z.zip}">${z.city}</option>`).join('');
  }
  const storedZip = project?.deliveryInfo?.zip || '';
  const entry = resolveZipEntry(storedZip);
  if(deliveryZipInput && storedZip){
    deliveryZipInput.value = storedZip;
  }
  renderDeliveryZipNote(entry, project?.deliveryInfo?.includeDelivery !== false && defaultDeliveryPreference !== 'pickup');
});
deliveryZipInput?.addEventListener('change', (evt)=>{
  const raw = (evt.target.value || '').trim();
  ensureZipData().then(()=>{
    const entry = resolveZipEntry(raw);
    const deliveryRadio = Array.from(deliveryPreferenceInputs).find(r=>r.value === 'delivery');
    const pickupRadio = Array.from(deliveryPreferenceInputs).find(r=>r.value === 'pickup');
    if(entry){
      if(deliveryRadio) deliveryRadio.checked = true;
      renderDeliveryZipNote(entry, true);
    }else{
      if(pickupRadio) pickupRadio.checked = true;
      renderDeliveryZipNote(null, false);
    }
  });
});
deliveryPreferenceInputs.forEach(input=>{
  input.addEventListener('change', ()=>{
    const includeDelivery = input.value === 'delivery' && input.checked;
    if(!includeDelivery && deliveryZipInput){
      deliveryZipInput.value = '';
    }
    renderDeliveryZipNote(resolveZipEntry(deliveryZipInput?.value || ''), includeDelivery);
  });
});

function composeMessage(form){
  const fd = new FormData(form);
  const lines = [];
  lines.push(`${t('store_msg_contact', 'Contact')}: ${fd.get('name') || ''} (${fd.get('client_type') || t('store_client_label', 'client')})`);
  if(fd.get('email')) lines.push(`${t('store_msg_email', 'Email')}: ${fd.get('email')}`);
  if(fd.get('phone')) lines.push(`${t('store_msg_phone', 'Phone')}: ${fd.get('phone')}`);
  if(fd.get('address')) lines.push(`${t('store_msg_address', 'Project address')}: ${fd.get('address')}`);
  if(fd.get('space_type')) lines.push(`${t('store_msg_space_type', 'Space type')}: ${fd.get('space_type')}`);
  if(fd.get('space_status')) lines.push(`${t('store_msg_property_status', 'Property status')}: ${fd.get('space_status')}`);
  if(fd.get('floor_level')) lines.push(`${t('store_msg_floor_level', 'Floor level')}: ${fd.get('floor_level')}`);
  if(fd.get('access_notes')) lines.push(`${t('store_msg_access', 'Access')}: ${fd.get('access_notes')}`);
  const services = [];
  if(fd.get('service_supply')) services.push(t('store_service_material_supply', 'Material supply'));
  if(fd.get('service_install')) services.push(t('store_service_installation', 'Installation'));
  if(fd.get('service_removal')) services.push(t('store_service_removal', 'Removal of existing floor'));
  if(fd.get('service_prep')) services.push(t('store_service_prep', 'Floor preparation / leveling'));
  if(fd.get('service_baseboards')) services.push(t('store_service_baseboards', 'Baseboards / transitions'));
  if(services.length) lines.push(`${t('store_msg_services', 'Services')}: ${services.join(', ')}`);
  if(fd.get('start_date')) lines.push(`${t('store_msg_desired_start', 'Desired start')}: ${fd.get('start_date')}`);
  if(fd.get('timeframe')) lines.push(`${t('store_msg_project_timing', 'Project timing')}: ${fd.get('timeframe')}`);
  if(fd.get('delivery_preference')) lines.push(`${t('store_msg_delivery_preference', 'Delivery preference')}: ${fd.get('delivery_preference')}`);
  if(fd.get('delivery_zip')) lines.push(`${t('store_msg_delivery_zip', 'Delivery ZIP')}: ${fd.get('delivery_zip')}`);
  if(fd.get('delivery_notes')) lines.push(`${t('store_msg_delivery_notes', 'Delivery notes')}: ${fd.get('delivery_notes')}`);
  if(fd.get('area_size')) lines.push(`${t('store_msg_area', 'Area / LF')}: ${fd.get('area_size')}`);
  if(fd.get('rooms')) lines.push(`${t('store_msg_rooms', 'Rooms / zones')}: ${fd.get('rooms')}`);
  lines.push(`${t('store_msg_consents', 'Consents')}:`);
  lines.push(`- ${t('store_consent_quote_title', 'Custom quote acknowledgment')}: ${fd.get('consent_custom_quote') ? t('store_yes', 'Yes') : t('store_no', 'No')}`);
  lines.push(`- ${t('store_consent_updates_title', 'Project updates')}: ${fd.get('consent_whatsapp') ? t('store_yes', 'Yes') : t('store_no', 'No')}`);
  lines.push('');
  lines.push(`${t('store_msg_items', 'Items')}:`);
  if(Array.isArray(project.items)){
    project.items.forEach((it, idx)=>{
      const p = it.product || {};
      const unit = p.measurementUnit === 'lf' ? t('store_unit_lf', 'lf') : p.measurementUnit === 'piece' ? t('store_unit_piece', 'piece') : t('store_unit_sq_ft', 'sq ft');
      const pkgLabel = p.packageLabelPlural || p.packageLabel || t('store_package_boxes', 'boxes');
      const qtyLabel = `${it.quantity || 0} ${pkgLabel}`;
      const coverage = p.packageCoverage ? ` (~${formatUnits((it.quantity || 0) * p.packageCoverage)} ${unit})` : '';
      const priceTypeLabel = it.priceType === 'backorder' ? t('store_label_order_in', 'Order-in') : t('store_label_in_stock', 'In stock');
      lines.push(`${idx + 1}. ${p.name || it.sku || t('store_product_fallback', 'Product')} — ${qtyLabel}${coverage} | ${priceTypeLabel}`);
    });
  }
  const notes = fd.get('message') || '';
  lines.push(`${t('store_msg_project_notes', 'Project notes')}:`);
  lines.push(notes);
  lines.push('---');
  lines.push(`${t('store_msg_cart', 'Cart')}: ${project.items?.length || 0} ${t('store_items_label', 'items')}, ${t('store_msg_estimated', 'estimated')} ${formatCurrency(totals.total)}`);
  return lines.filter(Boolean).join('\n');
}

function updateConsentState(){
  const consent1 = document.querySelector('input[name="consent_custom_quote"]');
  const consent2 = document.querySelector('input[name="consent_whatsapp"]');
  const submitBtn = document.getElementById('project-submit');
  const status = document.getElementById('project-status');
  const enabled = consent1?.checked && consent2?.checked;
  if(submitBtn){
    submitBtn.disabled = !enabled;
  }
  if(status){
    status.textContent = enabled ? '' : t('store_confirm_consents', 'Please confirm both consents to send your request.');
  }
}

['consent_custom_quote','consent_whatsapp'].forEach(name=>{
  const el = document.querySelector(`input[name="${name}"]`);
  if(el){
    el.addEventListener('change', updateConsentState);
  }
});
updateConsentState();

document.getElementById('project-form')?.addEventListener('submit', async (e)=>{
  e.preventDefault();
  const form = e.currentTarget;
  const status = document.getElementById('project-status');
  status.textContent = t('store_sending', 'Sending…');

  const messageField = document.getElementById('message');
  if(messageField){
    messageField.value = composeMessage(form);
  }

  const fd = new FormData(form);
  form.querySelectorAll('button,input,select,textarea').forEach(el=>el.disabled=true);
  try{
    const res = await fetch(form.action, {method:'POST', body: fd});
    const data = await res.json();
    status.textContent = data.data || t('store_project_sent', 'Project sent');
    if(res.ok){
      localStorage.setItem(PROJECT_KEY, JSON.stringify(project));
      window.location.href = 'thank-you.php';
    }
  }catch(err){
    status.textContent = t('store_error_generic', 'Something went wrong. Please try again.');
  }finally{
    form.querySelectorAll('button,input,select,textarea').forEach(el=>el.disabled=false);
  }
});
</script>
</body>
</html>
