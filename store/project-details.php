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
    <div class="breadcrumb"><a href="<?=$base?>">Home</a> &gt; <a href="<?=$base?>store/index.php">Store</a> &gt; <a href="cart.php">Cart</a> &gt; Project details</div>
    <div class="checkout-title-row">
      <div>
        <h1>Project details &amp; quote request</h1>
        <p class="checkout-subtitle">Share your contact and project details so we can confirm stock, scheduling and final pricing.</p>
      </div>
      <div class="checkout-meta">Custom quote · No online payment · B&amp;S team will contact you</div>
    </div>
  </header>

  <main class="checkout-layout">
    <section aria-label="Project details form">
      <form id="project-form" class="checkout-form-card" action="<?=$base?>lead.php" method="POST">
        <section>
          <h2 class="section-title">1. Your contact information</h2>
          <p class="section-subtitle">We’ll use this information to send your quote and coordinate your project.</p>
          <div class="fieldset">
            <div class="form-field">
              <label class="form-label" for="name">Full name <span class="required">*</span></label>
              <input id="name" name="name" type="text" class="form-input" required />
            </div>
            <div class="form-field">
              <label class="form-label" for="email">Email <span class="required">*</span></label>
              <input id="email" name="email" type="email" class="form-input" required />
              <div class="form-helper">We’ll send your quote and any documents here.</div>
            </div>
            <div class="form-field">
              <label class="form-label" for="phone">WhatsApp / mobile <span class="required">*</span></label>
              <input id="phone" name="phone" type="tel" class="form-input" required />
              <div class="form-helper">This is our main channel for updates and questions.</div>
            </div>
            <div class="form-field">
              <span class="form-label">You are:</span>
              <div class="inline-radio-group">
                <label class="inline-radio"><input type="radio" name="client_type" value="homeowner" checked /> Homeowner</label>
                <label class="inline-radio"><input type="radio" name="client_type" value="contractor" /> Contractor / installer</label>
              </div>
            </div>
          </div>
        </section>

        <section>
          <h2 class="section-title">2. Project location &amp; space</h2>
          <p class="section-subtitle">Tell us where the project will be installed and what type of space it is.</p>
          <div class="fieldset full">
            <div class="form-field">
              <label class="form-label" for="address">Project address (city, area, ZIP) <span class="required">*</span></label>
              <input id="address" name="address" type="text" class="form-input" required />
              <div class="form-helper">Example: "Meadow Woods, Orlando FL 32824"</div>
            </div>
          </div>
          <div class="fieldset">
            <div class="form-field">
              <label class="form-label" for="space_type">Space type</label>
              <select id="space_type" name="space_type" class="form-select">
                <option value="">Select an option</option>
                <option value="apartment">Apartment</option>
                <option value="single-family">Single-family home</option>
                <option value="multi-family">Multi-family / condo</option>
                <option value="commercial">Commercial</option>
                <option value="hospitality">Hospitality</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div class="form-field">
              <label class="form-label" for="space_status">Property status</label>
              <select id="space_status" name="space_status" class="form-select">
                <option value="">Select an option</option>
                <option value="occupied">Occupied</option>
                <option value="vacant">Vacant</option>
                <option value="new-build">New construction</option>
                <option value="remodel">Remodel in progress</option>
              </select>
            </div>
            <div class="form-field">
              <label class="form-label" for="floor_level">Floor level</label>
              <input id="floor_level" name="floor_level" type="text" class="form-input" placeholder="E.g. 1st floor, 10th floor" />
            </div>
            <div class="form-field">
              <label class="form-label" for="access_notes">Building access</label>
              <input id="access_notes" name="access_notes" type="text" class="form-input" placeholder="Elevator access, stairs, parking instructions" />
            </div>
          </div>
        </section>

        <section>
          <h2 class="section-title">3. Services &amp; timing</h2>
          <p class="section-subtitle">Select the services you need and when you’d like to start.</p>
          <div class="fieldset full">
            <div class="service-card-grid" role="group" aria-label="Requested services">
              <label class="service-card">
                <input type="checkbox" name="service_supply" checked />
                <span class="service-card-check" aria-hidden="true"></span>
                <div class="service-card-body">
                  <div class="service-card-title">Material supply</div>
                  <div class="service-card-desc">Reserve flooring, trims and adhesives from our stock.</div>
                </div>
              </label>
              <label class="service-card">
                <input type="checkbox" name="service_install" />
                <span class="service-card-check" aria-hidden="true"></span>
                <div class="service-card-body">
                  <div class="service-card-title">Installation</div>
                  <div class="service-card-desc">Schedule B&amp;S installers to handle the full installation.</div>
                </div>
              </label>
              <label class="service-card">
                <input type="checkbox" name="service_removal" />
                <span class="service-card-check" aria-hidden="true"></span>
                <div class="service-card-body">
                  <div class="service-card-title">Removal of existing floor</div>
                  <div class="service-card-desc">We can remove and dispose of existing flooring materials.</div>
                </div>
              </label>
              <label class="service-card">
                <input type="checkbox" name="service_prep" />
                <span class="service-card-check" aria-hidden="true"></span>
                <div class="service-card-body">
                  <div class="service-card-title">Floor preparation / leveling</div>
                  <div class="service-card-desc">Surface prep, leveling or moisture barrier as needed.</div>
                </div>
              </label>
              <label class="service-card">
                <input type="checkbox" name="service_baseboards" />
                <span class="service-card-check" aria-hidden="true"></span>
                <div class="service-card-body">
                  <div class="service-card-title">Baseboards / transitions</div>
                  <div class="service-card-desc">Include matching baseboards, trims or transition pieces.</div>
                </div>
              </label>
            </div>
          </div>
          <div class="fieldset">
            <div class="form-field">
              <label class="form-label" for="start_date">Desired start date</label>
              <input id="start_date" name="start_date" type="date" class="form-input" />
            </div>
            <div class="form-field">
              <label class="form-label" for="timeframe">Project timing</label>
              <select id="timeframe" name="timeframe" class="form-select">
                <option value="">Select an option</option>
                <option value="urgent">As soon as possible</option>
                <option value="30-days">Within 30 days</option>
                <option value="60-days">Within 60 days</option>
                <option value="planning">Just planning / comparing options</option>
              </select>
            </div>
          </div>
          <div class="disclaimer-box">
            <strong>Delivery & installation notes:</strong> final pricing may adjust after verifying exact quantities, delivery zone and site access. Our team will confirm schedules with you.
          </div>
        </section>

        <section>
          <h2 class="section-title">4. Delivery &amp; access preferences</h2>
          <p class="section-subtitle">Let us know how you’d like to receive materials and any access details.</p>
          <div class="fieldset">
            <div class="form-field">
              <label class="form-label">Delivery preference</label>
              <div class="inline-radio-group">
                <label class="inline-radio"><input type="radio" name="delivery_preference" value="delivery" checked /> Delivery by B&amp;S</label>
                <label class="inline-radio"><input type="radio" name="delivery_preference" value="pickup" /> Warehouse pick-up</label>
              </div>
            </div>
            <div class="form-field">
              <label class="form-label" for="delivery_notes">Delivery / building notes</label>
              <input id="delivery_notes" name="delivery_notes" type="text" class="form-input" placeholder="Gate code, parking, preferred time window" />
            </div>
          </div>
        </section>

        <section>
          <h2 class="section-title">5. Project scope &amp; measurements</h2>
          <p class="section-subtitle">Add any measurements or room notes to help us prepare your quote.</p>
          <div class="fieldset">
            <div class="form-field">
              <label class="form-label" for="area_size">Approximate area / linear feet</label>
              <input id="area_size" name="area_size" type="text" class="form-input" placeholder="E.g. 850 sq ft, 120 lf baseboards" />
            </div>
            <div class="form-field">
              <label class="form-label" for="rooms">Rooms or zones</label>
              <input id="rooms" name="rooms" type="text" class="form-input" placeholder="Living room, bedrooms, hallway" />
            </div>
          </div>
          <div class="fieldset full">
            <div class="form-field">
              <label class="form-label" for="message">Project notes <span class="required">*</span></label>
              <textarea id="message" name="message" rows="4" class="form-textarea" placeholder="Tell us about subfloor condition, demolition needs, stairs, trims or any questions" required></textarea>
              <div class="form-helper">We’ll include these details with your project request.</div>
            </div>
          </div>
        </section>

        <section>
          <h2 class="section-title">6. Consent &amp; confirmation</h2>
          <div class="consent-card-grid" role="group" aria-label="Consents">
            <label class="service-card consent-card">
              <input type="checkbox" name="consent_custom_quote" required />
              <span class="service-card-check" aria-hidden="true"></span>
              <div class="service-card-body">
                <div class="service-card-title">Custom quote acknowledgment</div>
                <div class="service-card-desc">Pricing will be confirmed by B&amp;S Floor Supply based on stock, delivery zone and installation details.</div>
              </div>
            </label>
            <label class="service-card consent-card">
              <input type="checkbox" name="consent_whatsapp" required />
              <span class="service-card-check" aria-hidden="true"></span>
              <div class="service-card-body">
                <div class="service-card-title">Project updates</div>
                <div class="service-card-desc">I agree to receive updates and questions about this project via WhatsApp and email.</div>
              </div>
            </label>
          </div>
          <div class="submit-row">
            <button type="submit" class="btn btn-primary round" id="project-submit" disabled>Send my project &amp; request quote</button>
            <button type="button" class="btn-text" onclick="window.location.href='cart.php'">Back to cart</button>
            <div class="form-helper">You’ll see a confirmation screen after sending this form.</div>
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
          <div class="summary-title">Your project selection</div>
          <div class="summary-edit"><a href="cart.php">Edit cart</a></div>
        </div>
        <div class="summary-list" id="summary-items"></div>
        <div class="summary-subtotals">
          <div class="summary-row"><div class="summary-label">Materials subtotal</div><div class="summary-value" id="summary-material">$0.00</div></div>
          <div class="summary-row"><div class="summary-label">Truckload (molding)</div><div class="summary-value" id="summary-truckload">$0.00</div></div>
          <div class="summary-row"><div class="summary-label">Installation</div><div class="summary-value" id="summary-install">$0.00</div></div>
          <div class="summary-row"><div class="summary-label">Delivery</div><div class="summary-value" id="summary-delivery">$0.00</div></div>
          <div class="summary-row"><div class="summary-label">Taxes</div><div class="summary-value" id="summary-taxes">$0.00</div></div>
          <div class="summary-total" id="summary-total">Estimated total: $0.00</div>
        </div>
        <p class="summary-note" id="summary-note">Final quote may adjust after verifying actual quantities, site conditions and delivery zone.</p>
        <p class="summary-contact-note"><strong>We will contact you</strong> using the phone and email you provide in this form.</p>
      </div>
    </aside>
  </main>
</div>
<?php include $base.'includes/footer.php'; ?>
<script>
const STORE_PRODUCTS = <?= json_encode(array_values($products)) ?>;
const STORE_CONFIG = <?= json_encode($storeConfig) ?>;
const PROJECT_KEY = 'bs_project';

function formatCurrency(value){
  const num = Number(value);
  return Number.isFinite(num) ? `$${num.toFixed(2)}` : '$0.00';
}

function formatUnits(value){
  const num = Number(value);
  if(!Number.isFinite(num)) return '';
  return num.toLocaleString(undefined, {maximumFractionDigits: 2});
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
const totals = project.totals || {material:0, install:0, truckload:0, delivery:0, taxes:0, total:0};

if(Array.isArray(project.items)){
  itemsContainer.innerHTML = project.items.map(it=>{
    const p = it.product || {};
    const unit = p.measurementUnit === 'lf' ? 'lf' : p.measurementUnit === 'piece' ? 'piece' : 'sq ft';
    const pkgLabel = p.packageLabelPlural || p.packageLabel || 'boxes';
    const priceTypeLabel = it.priceType === 'backorder' ? 'Order-in' : 'In stock';
    const metaParts = [];
    if(p.category) metaParts.push(p.category);
    metaParts.push(`${it.quantity} ${pkgLabel}`);
    if(p.packageCoverage) metaParts.push(`≈ ${formatUnits(it.quantity * p.packageCoverage)} ${unit}`);
    if(it.install) metaParts.push('Install included');
    if(it.truckloadTotal) metaParts.push(`Truckload ${formatCurrency(it.truckloadTotal)}`);
    if(p.taxesOmit) metaParts.push('Tax exempt');
    else if(it.taxes) metaParts.push(`Taxes ${formatCurrency(it.taxes)}`);
    metaParts.push(priceTypeLabel);
    const itemTotal = (it.subtotal || 0) + (it.install || 0) + (it.delivery || 0) + (it.truckloadTotal || 0) + (it.taxes || 0);
    const taxesLabel = p.taxesOmit ? 'Tax exempt' : `Taxes ${formatCurrency(it.taxes || 0)}`;
    return `
      <div class="summary-item">
        <div>
          <div class="summary-item-title">${p.name || it.sku || 'Product'}</div>
          <div class="summary-item-meta">${metaParts.filter(Boolean).join(' · ')}</div>
        </div>
        <div class="summary-item-right">
          <div>${formatCurrency(itemTotal)}</div>
          <div class="summary-item-meta">${formatCurrency(it.subtotal || 0)} material · ${taxesLabel}</div>
        </div>
      </div>`;
  }).join('');
}

document.getElementById('summary-material').textContent = formatCurrency(totals.material);
document.getElementById('summary-truckload').textContent = formatCurrency(totals.truckload);
document.getElementById('summary-install').textContent = formatCurrency(totals.install);
document.getElementById('summary-delivery').textContent = formatCurrency(totals.delivery);
document.getElementById('summary-taxes').textContent = formatCurrency(totals.taxes);
document.getElementById('summary-total').textContent = `Estimated total: ${formatCurrency(totals.total)}`;
const summaryNote = document.getElementById('summary-note');
if(summaryNote && STORE_CONFIG.install?.notes){
  summaryNote.textContent = STORE_CONFIG.install.notes;
}

const deliveryPreferenceInputs = document.querySelectorAll('input[name="delivery_preference"]');
if(deliveryPreferenceInputs.length){
  const pickupSelected = project?.deliveryPreferences?.includeDelivery === false || project?.deliveryZone === 'pick-up';
  const defaultValue = pickupSelected ? 'pickup' : 'delivery';
  deliveryPreferenceInputs.forEach(input=>{
    input.checked = input.value === defaultValue;
  });
}

function composeMessage(form){
  const fd = new FormData(form);
  const lines = [];
  lines.push(`Contact: ${fd.get('name') || ''} (${fd.get('client_type') || 'client'})`);
  if(fd.get('email')) lines.push(`Email: ${fd.get('email')}`);
  if(fd.get('phone')) lines.push(`Phone: ${fd.get('phone')}`);
  if(fd.get('address')) lines.push(`Project address: ${fd.get('address')}`);
  if(fd.get('space_type')) lines.push(`Space type: ${fd.get('space_type')}`);
  if(fd.get('space_status')) lines.push(`Property status: ${fd.get('space_status')}`);
  if(fd.get('floor_level')) lines.push(`Floor level: ${fd.get('floor_level')}`);
  if(fd.get('access_notes')) lines.push(`Access: ${fd.get('access_notes')}`);
  const services = [];
  if(fd.get('service_supply')) services.push('Material supply');
  if(fd.get('service_install')) services.push('Installation');
  if(fd.get('service_removal')) services.push('Removal of existing floor');
  if(fd.get('service_prep')) services.push('Floor preparation / leveling');
  if(fd.get('service_baseboards')) services.push('Baseboards / transitions');
  if(services.length) lines.push(`Services: ${services.join(', ')}`);
  if(fd.get('start_date')) lines.push(`Desired start: ${fd.get('start_date')}`);
  if(fd.get('timeframe')) lines.push(`Project timing: ${fd.get('timeframe')}`);
  if(fd.get('delivery_preference')) lines.push(`Delivery preference: ${fd.get('delivery_preference')}`);
  if(fd.get('delivery_notes')) lines.push(`Delivery notes: ${fd.get('delivery_notes')}`);
  if(fd.get('area_size')) lines.push(`Area / LF: ${fd.get('area_size')}`);
  if(fd.get('rooms')) lines.push(`Rooms / zones: ${fd.get('rooms')}`);
  lines.push('Consents:');
  lines.push(`- Custom quote acknowledgment: ${fd.get('consent_custom_quote') ? 'Yes' : 'No'}`);
  lines.push(`- Project updates via WhatsApp/email: ${fd.get('consent_whatsapp') ? 'Yes' : 'No'}`);
  lines.push('');
  lines.push('Items:');
  if(Array.isArray(project.items)){
    project.items.forEach((it, idx)=>{
      const p = it.product || {};
      const unit = p.measurementUnit === 'lf' ? 'lf' : p.measurementUnit === 'piece' ? 'piece' : 'sq ft';
      const pkgLabel = p.packageLabelPlural || p.packageLabel || 'boxes';
      const qtyLabel = `${it.quantity || 0} ${pkgLabel}`;
      const coverage = p.packageCoverage ? ` (~${formatUnits((it.quantity || 0) * p.packageCoverage)} ${unit})` : '';
      const priceTypeLabel = it.priceType === 'backorder' ? 'Order-in' : 'In stock';
      lines.push(`${idx + 1}. ${p.name || it.sku || 'Product'} — ${qtyLabel}${coverage} | ${priceTypeLabel}`);
    });
  }
  const notes = fd.get('message') || '';
  lines.push('Project notes:');
  lines.push(notes);
  lines.push('---');
  lines.push(`Cart: ${project.items?.length || 0} items, estimated ${formatCurrency(totals.total)}`);
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
    status.textContent = enabled ? '' : 'Please confirm both consents to send your request.';
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
  status.textContent = 'Sending…';

  const messageField = document.getElementById('message');
  if(messageField){
    messageField.value = composeMessage(form);
  }

  const fd = new FormData(form);
  form.querySelectorAll('button,input,select,textarea').forEach(el=>el.disabled=true);
  try{
    const res = await fetch(form.action, {method:'POST', body: fd});
    const data = await res.json();
    status.textContent = data.data || 'Project sent';
    if(res.ok){
      localStorage.setItem(PROJECT_KEY, JSON.stringify(project));
      window.location.href = 'thank-you.php';
    }
  }catch(err){
    status.textContent = 'Something went wrong. Please try again.';
  }finally{
    form.querySelectorAll('button,input,select,textarea').forEach(el=>el.disabled=false);
  }
});
</script>
</body>
</html>
