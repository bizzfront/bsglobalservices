<?php
require_once __DIR__.'/utils.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$products = load_store_products();
$sku = $_GET['sku'] ?? '';
$product = null;
foreach ($products as $p) {
  if (($p['sku'] ?? '') === $sku) {
    $product = $p;
    break;
  }
}
if (!$product) {
  http_response_code(404);
  echo 'Product not found';
  exit;
}
$imagePaths = array_filter([$product['image'] ?? '', $product['hoverImage'] ?? '']);
$baseDir = $imagePaths ? dirname(dirname($imagePaths[0])) : null;
$basePath = $baseDir ? __DIR__ . '/../' . $baseDir : null;
$images = [];
if ($basePath && is_dir($basePath)) {
  $images = array_merge(glob($basePath.'/*.png') ?: [], glob($basePath.'/*/*.png') ?: []);
  $images = array_map(function($p){return str_replace(__DIR__.'/../','',$p);}, $images);
}
$selected = array_unique(array_filter([$product['image'] ?? '', $product['hoverImage'] ?? '']));
$others = array_diff($images, $selected);
shuffle($others);
$selected = array_merge($selected, array_slice($others, 0, max(0, 4 - count($selected))));
$base = '../';
$active = 'store';
$contact_source = 'website_store';
$productType = $product['product_type'] ?? 'flooring';
$isFlooring = $productType === 'flooring';
$measurementUnit = strtolower($product['measurement_unit'] ?? ($isFlooring ? 'sqft' : 'lf'));
$unitLabels = [
  'sqft' => 'sqft',
  'lf' => 'linear feet',
  'piece' => 'pieces'
];
$packageLabelSingular = $product['package_label'] ?? ($isFlooring ? 'box' : 'package');
$packageLabelPlural = $product['package_label_plural'] ?? ($isFlooring ? 'boxes' : 'packages');
$unitLabel = $unitLabels[$measurementUnit] ?? $measurementUnit;
$unitSuffix = $measurementUnit === 'lf' ? '/lf' : ($measurementUnit === 'piece' ? '/piece' : '/sqft');
$pricePerUnitValue = $product['computed_price_per_unit'] ?? $product['price_per_unit'] ?? $product['price_sqft'] ?? null;
$coveragePerBoxValue = $product['computed_coverage_per_package'] ?? $product['coverage_per_box'] ?? $product['sqft_per_box'] ?? null;
$lengthFtValue = isset($product['length_ft']) ? (float)$product['length_ft'] : null;
$piecesPerBoxValue = isset($product['pieces_per_box']) ? (float)$product['pieces_per_box'] : null;
if($coveragePerBoxValue === null && $lengthFtValue && $piecesPerBoxValue){
  $coveragePerBoxValue = $lengthFtValue * $piecesPerBoxValue;
}
if($coveragePerBoxValue !== null){
  $coveragePerBoxValue = (float)$coveragePerBoxValue;
}
$pricePackageNum = $product['computed_price_per_package'] ?? (($pricePerUnitValue !== null && $coveragePerBoxValue) ? $pricePerUnitValue * $coveragePerBoxValue : null);
$pricePerUnitDisplayValue = $pricePerUnitValue !== null ? round((float)$pricePerUnitValue, 2) : null;
$pricePackageDisplayValue = $pricePackageNum !== null ? round((float)$pricePackageNum, 2) : null;
$pricePerUnit = $pricePerUnitDisplayValue !== null ? '$'.number_format($pricePerUnitDisplayValue, 2) : '';
$pricePackage = $pricePackageDisplayValue !== null ? '$'.number_format($pricePackageDisplayValue, 2) : '';
$formatNumber = static function($value) {
  if($value === null) return '';
  $formatted = number_format((float)$value, 2, '.', '');
  return rtrim(rtrim($formatted, '0'), '.');
};
$coveragePerBoxLabel = $coveragePerBoxValue ? $formatNumber($coveragePerBoxValue) . ' ' . ($unitLabels[$measurementUnit] ?? $measurementUnit) . ' / ' . $packageLabelSingular : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($product['name']) ?> — B&S Floor Supply</title>
  <link rel="stylesheet" href="<?=$base?>style.css" />
</head>
<body>
<?php include $base.'includes/header.php'; ?>
  <main class="container" style="padding-bottom:3vw;">
    <div class="sec-head">
        <div style="margin-top:1.6vw;" >
          <div class="eyebrow" data-i18n="ey_popular">Popular Vinyl Plank Options</div>
          <h1 data-i18n="h_popular"><?= htmlspecialchars($product['name']) ?></h2>
        </div>
        <span class="pill"><?= htmlspecialchars($product['name']) ?></span>
    </div>
    <h1></h1>
    <div class="product-top">
      <div class="slider">
        <div>
          <?php foreach($selected as $i => $img): ?>
            <img src="../<?= $img ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="<?= $i===0 ? 'active' : '' ?>">
          <?php endforeach; ?>
          <button class="prev" type="button">Prev</button>
          <button class="next" type="button">Next</button>
          <?php if(!empty($product['promo'])): ?>
            <div class="store-promo"><span><?= htmlspecialchars($product['promo']) ?></span></div>
          <?php endif; ?>
        </div>
      </div>
      <div class="product-cart">
        <?php
        ?>
        <div class="store-price">
          <?php if($pricePerUnit): ?>
            <div><b><?= $pricePerUnit ?></b><span class="store-per"><?= htmlspecialchars($unitSuffix) ?></span></div>
          <?php else: ?>
            <div><b>Call for price</b></div>
          <?php endif; ?>
          <?php if($pricePackage): ?>
            <div><span class="store-per">≈ <?= $pricePackage ?> / <?= htmlspecialchars($packageLabelSingular) ?></span></div>
          <?php endif; ?>
          <?php if($coveragePerBoxLabel): ?>
            <div><span class="store-per"><?= htmlspecialchars($coveragePerBoxLabel) ?></span></div>
          <?php endif; ?>
        </div>

        <div id="calc" class="calc">
          <h3 style="color: var(--burgundy);">Material calculator</h3>
          <form id="calcForm" class="form">
            <?php if($isFlooring): ?>
              <label>
                Length (ft)
                <input type="number" id="calcLen" step="0.1" min="0">
              </label>
              <label>
                Width (ft)
                <input type="number" id="calcWid" step="0.1" min="0">
              </label>
              <div class="full or">or</div>
            <?php else: ?>
              <label class="full">
                <?= htmlspecialchars(ucfirst($unitLabel)) ?> needed
                <input type="number" id="calcUnits" step="0.1" min="0">
              </label>
              <div class="full or">or</div>
            <?php endif; ?>
            <label class="full">
              <?= htmlspecialchars(ucfirst($packageLabelPlural)) ?>
              <input type="number" id="calcBoxes" min="1" value="1">
            </label>
            <p id="calcSummary" class="note full"></p>
            <button type="button" id="addToCart" class="btn btn-primary full">Add to cart</button>
          </form>
        </div>
      </div>
    </div>

    <div class="tabs">
      <button class="tab-btn active" data-target="overview">Overview</button>
      <button class="tab-btn" data-target="specs">Specifications</button>
    </div>
    <div id="overview" class="tab-content active">
      <?php if(!empty($product['short_desc'])): ?>
        <ul>
          <?php foreach (explode(';', $product['short_desc']) as $b): if(trim($b)): ?>
            <li><?= htmlspecialchars(trim($b)) ?></li>
          <?php endif; endforeach; ?>
        </ul>
      <?php endif; ?>
      <?php if(!empty($product['long_desc'])): ?>
        <p><?= htmlspecialchars($product['long_desc']) ?></p>
      <?php endif; ?>
    </div>
    <div id="specs" class="tab-content">
      <ul>
        <?php if($isFlooring): ?>
          <?php if($product['thickness_mm']): ?><li><strong>Thickness:</strong> <?= $product['thickness_mm'] ?> mm</li><?php endif; ?>
          <?php if($product['wear_layer_mil']): ?><li><strong>Wear layer:</strong> <?= $product['wear_layer_mil'] ?> mil</li><?php endif; ?>
          <?php if($product['width_in'] && $product['length_in']): ?><li><strong>Plank size:</strong> <?= $product['width_in'] ?>×<?= $product['length_in'] ?> in</li><?php endif; ?>
          <?php if($product['core']): ?><li><strong>Core:</strong> <?= htmlspecialchars($product['core']) ?></li><?php endif; ?>
          <?php if($product['pad']): ?><li><strong>Pad:</strong> <?= htmlspecialchars($product['pad']) ?> <?= htmlspecialchars($product['pad_material'] ?? '') ?></li><?php endif; ?>
          <?php if($product['installation']): ?><li><strong>Installation:</strong> <?= htmlspecialchars($product['installation']) ?></li><?php endif; ?>
          <?php if($product['waterproof']): ?><li><strong>Waterproof:</strong> <?= htmlspecialchars($product['waterproof']) ?></li><?php endif; ?>
          <?php if($product['scratch_resistant']): ?><li><strong>Scratch resistant:</strong> <?= htmlspecialchars($product['scratch_resistant']) ?></li><?php endif; ?>
        <?php else: ?>
          <?php if($product['category']): ?><li><strong>Category:</strong> <?= htmlspecialchars($product['category']) ?></li><?php endif; ?>
          <?php if($product['nominal_size']): ?><li><strong>Nominal size:</strong> <?= htmlspecialchars($product['nominal_size']) ?></li><?php endif; ?>
          <?php if($product['actual_size']): ?><li><strong>Actual size:</strong> <?= htmlspecialchars($product['actual_size']) ?></li><?php endif; ?>
          <?php if($product['length_ft']): ?><li><strong>Length per piece:</strong> <?= $product['length_ft'] ?> ft</li><?php endif; ?>
          <?php if($product['pieces_per_box']): ?><li><strong>Pieces per box:</strong> <?= $product['pieces_per_box'] ?></li><?php endif; ?>
          <?php if($coveragePerBoxValue): ?><li><strong>Coverage per box:</strong> <?= $coveragePerBoxLabel ?></li><?php endif; ?>
          <?php if($product['packaging_notes']): ?><li><strong>Packaging:</strong> <?= htmlspecialchars($product['packaging_notes']) ?></li><?php endif; ?>
          <?php if($product['comments']): ?><li><strong>Comments:</strong> <?= htmlspecialchars($product['comments']) ?></li><?php endif; ?>
        <?php endif; ?>
      </ul>
    </div>
    <div class="hero-cta" style="margin-top:1rem;">
      <a href="#contact" class="btn btn-primary">Get install Quote</a>
    </div>
  </main>

<?php include $base.'includes/contact.php'; ?>
<?php include $base.'includes/footer.php'; ?>
  <script>
    const burger = document.getElementById('burger');
    const menu = document.getElementById('menu');
    burger?.addEventListener('click', () => {
      const open = menu.classList.toggle('show');
      burger.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    const imgs = document.querySelectorAll('.slider img');
    let idx = 0;
    function showSlide(n){
      if(!imgs.length) return;
      imgs[idx].classList.remove('active');
      idx = (n + imgs.length) % imgs.length;
      imgs[idx].classList.add('active');
    }
    document.querySelector('.next')?.addEventListener('click', ()=>showSlide(idx+1));
    document.querySelector('.prev')?.addEventListener('click', ()=>showSlide(idx-1));
    document.querySelectorAll('.tab-btn').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c=>c.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById(btn.dataset.target).classList.add('active');
      });
    });
    const SKU = <?= json_encode($product['sku']) ?>;
    const PRODUCT_TYPE = <?= json_encode($productType) ?>;
    const MEASUREMENT_UNIT = <?= json_encode($measurementUnit) ?>;
    const UNIT_LABEL = <?= json_encode($unitLabel) ?>;
    const PACKAGE_LABEL = <?= json_encode($packageLabelSingular) ?>;
    const PACKAGE_LABEL_PLURAL = <?= json_encode($packageLabelPlural) ?>;
    const COVERAGE_PER_PACKAGE = <?= json_encode($coveragePerBoxValue) ?>;
    const PRICE_PER_UNIT = <?= json_encode($pricePerUnitValue) ?>;
    const PRICE_PER_UNIT_DISPLAY = <?= json_encode($pricePerUnitDisplayValue) ?>;
    const PRICE_PER_PACKAGE = <?= json_encode($pricePackageNum) ?>;
    const PRICE_PER_PACKAGE_DISPLAY = <?= json_encode($pricePackageDisplayValue) ?>;
    const LENGTH_FT = <?= json_encode($product['length_ft'] ?? null) ?>;
    const PIECES_PER_BOX = <?= json_encode($product['pieces_per_box'] ?? null) ?>;
    function formatUnits(value){
      const num = Number(value);
      if(!Number.isFinite(num)) return '';
      if(Math.abs(num) >= 1000 && Number.isInteger(num)){
        return num.toLocaleString();
      }
      return num.toLocaleString(undefined, {maximumFractionDigits: 2});
    }
    function updateCalc(){
      let boxes = parseInt(document.getElementById('calcBoxes')?.value) || 0;
      let unitsNeeded = null;
      const pricePerUnitDisplay = Number(PRICE_PER_UNIT_DISPLAY);
      const pricePerUnitNum = Number.isFinite(pricePerUnitDisplay) ? pricePerUnitDisplay : (Number(PRICE_PER_UNIT) || 0);
      const lengthPerPiece = Number(LENGTH_FT) || 0;
      const piecesPerBox = Number(PIECES_PER_BOX) || 0;
      let coveragePerPackage = Number(COVERAGE_PER_PACKAGE);
      if(!Number.isFinite(coveragePerPackage) || coveragePerPackage <= 0){
        if(lengthPerPiece > 0 && piecesPerBox > 0){
          coveragePerPackage = lengthPerPiece * piecesPerBox;
        }else{
          coveragePerPackage = 0;
        }
      }
      if(PRODUCT_TYPE === 'flooring'){
        const len = parseFloat(document.getElementById('calcLen')?.value);
        const wid = parseFloat(document.getElementById('calcWid')?.value);
        if(len && wid){
          const sqft = len * wid;
          if(COVERAGE_PER_PACKAGE){
            boxes = Math.ceil(sqft / COVERAGE_PER_PACKAGE);
            document.getElementById('calcBoxes').value = boxes;
          }
          unitsNeeded = sqft;
        }
      }else{
        const unitsInput = parseFloat(document.getElementById('calcUnits')?.value);
        if(unitsInput){
          const piecesNeeded = lengthPerPiece > 0 ? unitsInput / lengthPerPiece : null;
          if(piecesPerBox > 0 && piecesNeeded){
            boxes = Math.ceil(piecesNeeded / piecesPerBox);
            document.getElementById('calcBoxes').value = boxes;
          }else if(coveragePerPackage > 0){
            boxes = Math.ceil(unitsInput / coveragePerPackage);
            document.getElementById('calcBoxes').value = boxes;
          }
          unitsNeeded = unitsInput;
        }else if(boxes){
          if(piecesPerBox > 0 && lengthPerPiece > 0){
            unitsNeeded = boxes * piecesPerBox * lengthPerPiece;
          }else if(coveragePerPackage > 0){
            unitsNeeded = boxes * coveragePerPackage;
          }
        }
      }
      if(!unitsNeeded && boxes && coveragePerPackage > 0){
        unitsNeeded = boxes * coveragePerPackage;
      }
      const pricePerPackageDisplay = Number(PRICE_PER_PACKAGE_DISPLAY);
      const pricePerPackage = Number.isFinite(pricePerPackageDisplay)
        ? pricePerPackageDisplay
        : (Number(PRICE_PER_PACKAGE) || (coveragePerPackage > 0 && pricePerUnitNum > 0 ? coveragePerPackage * pricePerUnitNum : 0));
      const totalPrice = unitsNeeded && pricePerUnitNum > 0 ? unitsNeeded * pricePerUnitNum : (boxes && pricePerPackage ? boxes * pricePerPackage : 0);
      const summaryParts = [];
      if(boxes){
        const pkgLabel = boxes === 1 ? (PACKAGE_LABEL || 'box') : (PACKAGE_LABEL_PLURAL || ((PACKAGE_LABEL || 'box') + 'es'));
        summaryParts.push(`${boxes} ${pkgLabel}`);
      }
      if(unitsNeeded){
        summaryParts.push(`${formatUnits(unitsNeeded)} ${UNIT_LABEL}`);
      }
      if(totalPrice){
        summaryParts.push(`$${totalPrice.toFixed(2)}`);
      }
      document.getElementById('calcSummary').textContent = summaryParts.join(' — ');
      return boxes;
    }
    ['calcLen','calcWid','calcBoxes','calcUnits'].forEach(id=>document.getElementById(id)?.addEventListener('input', updateCalc));
    document.getElementById('addToCart')?.addEventListener('click', ()=>{
      const boxes = updateCalc();
      if(boxes>0) cart.addItem(SKU, boxes);
    });
    updateCalc();
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
          const res = await fetch(form.action || '<?=$base?>lead.php', {method:'POST', body:formData});
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
    document.getElementById('year').textContent = new Date().getFullYear();
  </script>
</body>
</html>
