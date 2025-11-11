<?php
require_once __DIR__.'/utils.php';

$allProducts = load_store_products();
$type = $_GET['type'] ?? 'flooring';
if (!in_array($type, ['flooring', 'molding'], true)) {
  $type = 'flooring';
}
$products = array_values(array_filter($allProducts, function ($product) use ($type) {
  return ($product['product_type'] ?? 'flooring') === $type;
}));
$base = '../';
$active = 'store';
$contact_source = 'website_store';
$heroTitle = $type === 'molding' ? 'Store — Moldings' : 'Store — Waterproof LVP';
$heroSubtitle = $type === 'molding'
  ? 'Baseboards, casings, trims & more'
  : 'SPC/WPC core · Attached pad · Easy click install';
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
    <h1><?= htmlspecialchars($heroTitle) ?></h1>
    <p><?= htmlspecialchars($heroSubtitle) ?></p>
    <div class="store-type-switch" style="margin-top:1rem; display:flex; gap:0.5rem; flex-wrap:wrap;">
      <a class="btn <?= $type === 'flooring' ? 'btn-primary' : 'btn-ghost' ?>" href="?type=flooring">Flooring</a>
      <a class="btn <?= $type === 'molding' ? 'btn-primary' : 'btn-ghost' ?>" href="?type=molding">Moldings</a>
    </div>
  </div>
</section>

<div class="container store-layout <?= $type === 'molding' ? 'store-layout--single' : '' ?>">
  <?php if ($type === 'flooring'): ?>
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
  <?php endif; ?>

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
const PRODUCTS = <?= json_encode(array_values($products)) ?>;
const CURRENT_TYPE = <?= json_encode($type) ?>;
const getSortPrice = (product)=>{
  const raw = product?.computed_price_per_unit_stock ?? product?.computed_price_per_unit ?? product?.price_per_unit ?? product?.price_sqft;
  const num = Number(raw);
  return Number.isFinite(num) ? num : null;
};

function card(p){
  const inStock = (p.inventory_status || '').toLowerCase() === 'in stock';
  const isFlooring = (p.product_type || 'flooring') === 'flooring';
  const unit = (p.measurement_unit || '').toLowerCase() || (p.product_type === 'molding' ? 'lf' : 'sqft');
  const unitLabel = unit === 'lf' ? '/lf' : unit === 'piece' ? '/piece' : '/sqft';
  const unitName = unit === 'lf' ? 'lf' : unit === 'piece' ? 'pieces' : 'sqft';
  const formatNumber = (value)=>{
    const num = Number(value);
    if(!Number.isFinite(num)) return value ?? '';
    if(Math.abs(num) >= 1000 && num % 1 === 0){
      return num.toLocaleString();
    }
    return num % 1 === 0 ? num.toString() : num.toLocaleString(undefined, {maximumFractionDigits: 2});
  };
  const toNumber = (value)=>{
    const num = Number(value);
    return Number.isFinite(num) ? num : null;
  };
  const lengthFt = Number(p.length_ft);
  const piecesPerBox = Number(p.pieces_per_box);
  let coverage = p.computed_coverage_per_package ?? p.coverage_per_box ?? p.sqft_per_box;
  if(coverage == null || !Number.isFinite(Number(coverage)) || Number(coverage) <= 0){
    if(Number.isFinite(lengthFt) && lengthFt > 0 && Number.isFinite(piecesPerBox) && piecesPerBox > 0){
      coverage = lengthFt * piecesPerBox;
    }else{
      coverage = null;
    }
  }
  if(coverage != null){
    coverage = Number(coverage);
  }
  const stockPriceUnitValue = toNumber(p.computed_price_per_unit_stock ?? p.computed_price_per_unit ?? p.price_per_unit ?? p.price_sqft);
  const backorderPriceUnitValue = toNumber(p.computed_price_per_unit_backorder);
  const pricePackageStockValue = toNumber(p.computed_price_per_package_stock ?? (stockPriceUnitValue != null && coverage != null ? stockPriceUnitValue * coverage : null));
  const pricePackageBackorderValue = toNumber(p.computed_price_per_package_backorder ?? (backorderPriceUnitValue != null && coverage != null ? backorderPriceUnitValue * coverage : null));
  const width = (p.width_in && p.length_in) ? `${p.width_in}×${p.length_in} in` : '';
  const thk = p.thickness_mm ? `${p.thickness_mm} mm` : '';
  const wear = p.wear_layer_mil ? `${p.wear_layer_mil} mil wear` : '';
  const href = `product.php?sku=${encodeURIComponent(p.sku)}`;
  const packageLabel = p.package_label ?? 'box';
  const coverageLabel = coverage != null ? `${formatNumber(coverage)} ${unitName} / ${packageLabel}` : '';
  const formatPrice = (value)=> value != null ? `$${value.toFixed(2)}` : '';
  const priceOptions = [];
  if(stockPriceUnitValue != null){
    priceOptions.push({
      type: 'stock',
      label: 'In stock',
      unit: stockPriceUnitValue,
      package: pricePackageStockValue,
      checked: true
    });
  }
  if(backorderPriceUnitValue != null){
    priceOptions.push({
      type: 'backorder',
      label: 'Backorder',
      unit: backorderPriceUnitValue,
      package: pricePackageBackorderValue,
      checked: stockPriceUnitValue == null
    });
  }
  const renderOption = (option)=>{
    const unitHtml = option.unit != null
      ? `<span class="store-price-option-main"><b>${formatPrice(option.unit)}</b><span class="store-per">${unitLabel}</span></span>`
      : `<span class="store-price-option-main"><b>Call for price</b></span>`;
    const packageHtml = option.package != null
      ? `<span class="store-price-option-sub">≈ ${formatPrice(option.package)} / ${packageLabel}</span>`
      : '';
    return `<label class="store-price-option"><input type="radio" name="price-${p.sku}" value="${option.type}" ${option.checked ? 'checked' : ''}><div><span class="store-price-option-label">${option.label}</span>${unitHtml}${packageHtml}</div></label>`;
  };
  let priceHtml = '';
  if(isFlooring && priceOptions.length > 1){
    priceHtml = `<div class="store-price-options">${priceOptions.map(renderOption).join('')}</div>`;
  }else if(priceOptions.length){
    const option = priceOptions[0];
    const unitDisplay = option.unit != null
      ? `<b>${formatPrice(option.unit)}</b><span class="store-per">${unitLabel}</span>`
      : '<b>Call for price</b>';
    priceHtml = `<div class="store-price"><div>${unitDisplay}</div>${option.package != null ? `<div><span class="store-per">≈ ${formatPrice(option.package)} / ${packageLabel}</span></div>` : ''}</div>`;
  }else{
    priceHtml = `<div class="store-price"><b>Call for price</b></div>`;
  }
  const badge = inStock ? '<span class="store-badge">In stock</span>' : '<span class="store-badge store-out">Backorder</span>';
  const img = p.image ? `../${p.image}` : '';
  return `
  <article class="store-card">
    <a href="${href}">
      <div class="store-img" style="background-image:url('${img}')">${badge}</div>
    </a>
    <div class="store-pad">
      <h3 class="store-title"><a href="${href}">${p.name}</a></h3>
      <div class="store-sub">${p.collection || p.category || ''}</div>
      <div class="store-specs">
        ${thk? `<span class="store-pill">${thk}</span>`:''}
        ${wear? `<span class="store-pill">${wear}</span>`:''}
        ${width? `<span class="store-pill">${width}</span>`:''}
        ${p.core ? `<span class="store-pill">${p.core}</span>`:''}
        ${p.pad ? `<span class="store-pill">${p.pad} pad ${(p.pad_material||'').trim()}</span>`:''}
        ${coverageLabel ? `<span class="store-pill">${coverageLabel}</span>`:''}
      </div>
      ${priceHtml}
      <div class="store-cta">
        <div style="display:inline-block; width:100%; padding:0;">
          <div style="display:inline-block; width:auto;">Boxes</div>
          <div style="display:inline-block; width:50%;"><input type="number" class="qty" min="1" value="1" aria-label="Quantity"></div>
        </div>
        
        <button class="btn btn-primary add-cart" data-sku="${p.sku}" type="button">Add to cart</button>
        <a class="btn btn-ghost" href="${href}">View details</a>
      </div>
    </div>
  </article>`;
}

function applyFilters(list){
  if(CURRENT_TYPE !== 'flooring') return list;
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
    copy.sort((a,b)=>{
      const au = getSortPrice(a);
      const bu = getSortPrice(b);
      return (au ?? 1e9) - (bu ?? 1e9);
    });
  }else if(v==='price-desc'){
    copy.sort((a,b) => {
      const au = getSortPrice(a);
      const bu = getSortPrice(b);
      return (bu ?? -1) - (au ?? -1);
    });
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
      const cardEl = btn.closest('.store-card');
      const selectedPrice = cardEl?.querySelector('.store-price-option input:checked');
      const priceType = selectedPrice ? selectedPrice.value : 'stock';
      cart.addItem(btn.dataset.sku, qty, priceType);
    });
  });
}

['sortSel','fColor','fTone','fThkMin','fWearMin'].forEach(id=>{
  document.getElementById(id)?.addEventListener('change', render);
});
document.getElementById('clearFilters')?.addEventListener('click', ()=>{
  ['fColor','fTone','fThkMin','fWearMin'].forEach(id=>{ const el = document.getElementById(id); if(el) el.value=''; });
  const sortSel = document.getElementById('sortSel');
  if(sortSel) sortSel.value='relevance';
  render();
});
render();
</script>
</body>
</html>
