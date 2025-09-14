<?php
$products = json_decode(file_get_contents(__DIR__.'/../products.json'), true);
$base = '../';
$active = 'store';
$contact_source = 'website_store';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>B&S Floor Supply — Store</title>
  <link rel="stylesheet" href="<?=$base?>style.css" />
</head>
<body>
<?php include $base.'includes/header.php'; ?>

<section class="store-hero">
  <div class="container wrap">
    <h1>Store — Waterproof LVP</h1>
    <p>SPC/WPC core · Attached pad · Easy click install</p>
  </div>
</section>

<div class="container store-layout">
  <aside class="store-filters" aria-label="Filters">
    <h3>Filter</h3>
    <div class="f-sec">
      <label>Color family
        <select id="fColor">
          <option value="">All</option>
          <option>Gray</option>
          <option>Brown</option>
          <option>Beige</option>
        </select>
      </label>
      <label>Tone
        <select id="fTone">
          <option value="">All</option>
          <option>Light</option>
          <option>Medium</option>
          <option>Dark</option>
        </select>
      </label>
    </div>
    <div class="f-sec">
      <label>Thickness (mm) min
        <input id="fThkMin" type="number" step="0.1" placeholder="e.g., 5.0">
      </label>
      <label>Wear layer (mil) min
        <input id="fWearMin" type="number" step="1" placeholder="e.g., 12">
      </label>
    </div>
    <div class="f-sec">
      <button class="btn btn-ghost" id="clearFilters" type="button">Clear all</button>
    </div>
  </aside>

  <main>
    <div class="store-bar">
      <!--<div class="store-chip">Waterproof</div>
      <div class="store-chip">SPC Core</div>
      <div class="store-chip">Attached Pad</div>-->
      <div class="store-sort">
        <label for="sortSel" class="muted" style="margin-right:6px;">Sort</label>
        <select id="sortSel">
          <option value="relevance" selected>Relevance</option>
          <option value="price-asc">Price (low → high)</option>
          <option value="price-desc">Price (high → low)</option>
          <option value="rating-desc">Rating</option>
        </select>
      </div>
    </div>

    <section id="store-grid" class="store-grid" aria-live="polite"></section>
  </main>
</div>

<?php include $base.'includes/contact.php'; ?>
<?php include $base.'includes/footer.php'; ?>

<script>
const PRODUCTS = <?= json_encode($products) ?>;

function card(p){
  const inStock = (p.inventory_status || '').toLowerCase() === 'in stock';
  const priceSqft = p.price_sqft != null ? `$${p.price_sqft.toFixed(2)}` : '';
  const pb = p.price_box != null ? p.price_box : (p.price_sqft != null && p.sqft_per_box != null ? p.price_sqft * p.sqft_per_box : null);
  const priceBox = pb != null ? `$${pb.toFixed(2)}` : '';
  const width = (p.width_in && p.length_in) ? `${p.width_in}×${p.length_in} in` : '';
  const thk = p.thickness_mm ? `${p.thickness_mm} mm` : '';
  const wear = p.wear_layer_mil ? `${p.wear_layer_mil} mil wear` : '';
  const href = `product.php?sku=${encodeURIComponent(p.sku)}`;
  let priceHtml = priceSqft ? `<div class="store-price"><b>${priceSqft}</b><span class="store-per">/sqft</span></div>` : `<div class="store-price"><b>Call for price</b></div>`;
  if(priceBox){ priceHtml += `<div class="store-price"><span class="store-per">≈ ${priceBox} / box</span></div>`; }
  const badge = inStock ? '<span class="store-badge">In stock</span>' : '<span class="store-badge store-out">Backorder</span>';
  const img = p.image ? `../${p.image}` : '';
  return `
  <article class="store-card">
    <a href="${href}">
      <div class="store-img" style="background-image:url('${img}')">${badge}</div>
    </a>
    <div class="store-pad">
      <h3 class="store-title"><a href="${href}">${p.name}</a></h3>
      <div class="store-sub">${p.collection || ''}</div>
      <div class="store-specs">
        ${thk? `<span class="store-pill">${thk}</span>`:''}
        ${wear? `<span class="store-pill">${wear}</span>`:''}
        ${width? `<span class="store-pill">${width}</span>`:''}
        <span class="store-pill">${p.core || ''}</span>
        <span class="store-pill">${p.pad ? p.pad+' pad '+(p.pad_material||'') : ''}</span>
      </div>
      ${priceHtml}
      <div class="store-cta">
        <input type="number" class="qty" min="1" value="1" aria-label="Quantity">
        <button class="btn btn-primary add-cart" data-sku="${p.sku}" type="button">Add to cart</button>
        <a class="btn btn-ghost" href="${href}">View details</a>
      </div>
    </div>
  </article>`;
}

function applyFilters(list){
  const cf = (document.getElementById('fColor').value||'').toLowerCase();
  const tn = (document.getElementById('fTone').value||'').toLowerCase();
  const thkMin = parseFloat(document.getElementById('fThkMin').value) || 0;
  const wearMin = parseFloat(document.getElementById('fWearMin').value) || 0;
  return list.filter(p=>{
    const fam = (p.colorFamily||'').toLowerCase();
    const tone = (p.tone||'').toLowerCase();
    const thk = parseFloat(p.thickness_mm) || 0;
    const wear = parseFloat(p.wear_layer_mil) || 0;
    if(cf && fam !== cf) return false;
    if(tn && tone !== tn) return false;
    if(thkMin && thk < thkMin) return false;
    if(wearMin && wear < wearMin) return false;
    return true;
  });
}

function applySort(list){
  const v = document.getElementById('sortSel').value;
  const copy = [...list];
  if(v==='price-asc'){
    copy.sort((a,b)=>(a.price_sqft??1e9)-(b.price_sqft??1e9));
  }else if(v==='price-desc'){
    copy.sort((a,b) => (b.price_sqft??-1)-(a.price_sqft??-1));
  }else if(v==='rating-desc'){
    copy.sort((a,b)=>(b.rating??0)-(a.rating??0));
  }
  return copy;
}

function render(){
  let list = applyFilters(PRODUCTS);
  list = applySort(list);
  const grid = document.getElementById('store-grid');
  grid.innerHTML = list.map(card).join('');
  grid.querySelectorAll('.add-cart').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const qty = parseInt(btn.parentElement.querySelector('.qty').value) || 1;
      cart.addItem(btn.dataset.sku, qty);
    });
  });
}

['sortSel','fColor','fTone','fThkMin','fWearMin'].forEach(id=>{
  document.getElementById(id).addEventListener('change', render);
});
document.getElementById('clearFilters').addEventListener('click', ()=>{
  ['fColor','fTone','fThkMin','fWearMin'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('sortSel').value='relevance';
  render();
});
render();
</script>
</body>
</html>
