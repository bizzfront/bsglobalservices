(function(){
  const grid = document.getElementById('store-grid');
  const resultSummary = document.getElementById('resultSummary');
  const typeSwitch = document.getElementById('typeSwitch');
  if(!grid || !Array.isArray(BS_PRODUCTS)) return;

  function formatCurrency(value){
    const num = Number(value);
    if(!Number.isFinite(num)) return '';
    return `$${num.toFixed(2)}`;
  }

  function formatNumber(value){
    const num = Number(value);
    if(!Number.isFinite(num)) return '';
    if(Math.abs(num) >= 1000 && Number.isInteger(num)) return num.toLocaleString();
    return Number.isInteger(num) ? num.toString() : num.toLocaleString(undefined, {maximumFractionDigits: 2});
  }

  function computePriceType(product, qty){
    const stockAvailable = Number(product?.availability?.stockAvailable ?? NaN);
    const allowBackorder = product?.availability?.allowBackorder !== false;
    const baseType = product?.pricing?.activePriceType || product?.availability?.activePriceType || product?.availability?.mode || 'stock';
    let priceType = baseType === 'backorder' ? 'backorder' : 'stock';

    if(priceType === 'stock' && allowBackorder && Number.isFinite(stockAvailable)){
      if(stockAvailable <= 0) return 'backorder';
      if(Number.isFinite(qty) && qty > stockAvailable) return 'backorder';
    }

    return priceType;
  }

  function buildPriceHtml(product, priceType, unit){
    const coverage = Number(product.packageCoverage ?? NaN);
    const pkgLabel = product.packageLabel || 'box';
    const badgeStock = STORE_CONFIG?.ui?.badges?.stock || 'In stock';
    const badgeBackorder = STORE_CONFIG?.ui?.badges?.backorder || 'Order-in';

    const getPackagePrice = (mode)=>{
      if(mode === 'stock'){
        const pkgPrice = product.pricing?.pricePerPackageStock;
        if(pkgPrice != null) return pkgPrice;
        const unitPrice = Number(product.pricing?.finalPriceStockPerUnit ?? NaN);
        if(Number.isFinite(unitPrice) && Number.isFinite(coverage) && coverage > 0) return unitPrice * coverage;
      }
      if(mode === 'backorder'){
        const pkgPrice = product.pricing?.pricePerPackageBackorder;
        if(pkgPrice != null) return pkgPrice;
        const unitPrice = Number(product.pricing?.finalPriceBackorderPerUnit ?? NaN);
        if(Number.isFinite(unitPrice) && Number.isFinite(coverage) && coverage > 0) return unitPrice * coverage;
      }
      const unitPrice = Number(product.pricing?.activePricePerPackage ?? NaN);
      return Number.isFinite(unitPrice) ? unitPrice : null;
    };

    const priceModes = {};
    const stockUnit = Number(product.pricing?.finalPriceStockPerUnit ?? NaN);
    const backorderUnit = Number(product.pricing?.finalPriceBackorderPerUnit ?? NaN);
    const activeUnit = Number(product.pricing?.activePricePerUnit ?? NaN);

    if(Number.isFinite(stockUnit)){
      priceModes.stock = {
        label: badgeStock,
        unit: stockUnit,
        pkg: getPackagePrice('stock')
      };
    }
    if(Number.isFinite(backorderUnit)){
      priceModes.backorder = {
        label: badgeBackorder,
        unit: backorderUnit,
        pkg: getPackagePrice('backorder')
      };
    }

    if(!Object.keys(priceModes).length && Number.isFinite(activeUnit)){
      priceModes.stock = {
        label: badgeStock,
        unit: activeUnit,
        pkg: getPackagePrice('stock')
      };
    }

    const targetMode = priceModes[priceType] ? priceType : (priceModes.stock ? 'stock' : Object.keys(priceModes)[0]);
    const modeData = targetMode ? priceModes[targetMode] : null;
    if(!modeData){
      return '<div class="store-price-line"><b>Call for price</b></div>';
    }

    const badge = targetMode === 'stock' ? badgeStock : badgeBackorder;
    const pkgPrice = modeData.pkg;

    const pkgLine = pkgPrice != null
      ? `<div class="store-price-line" style="color:#6a605e;"><span class="store-per">≈ ${formatCurrency(pkgPrice)} / ${pkgLabel}</span></div>`
      : '';

    return `
      <div class="store-price-line"><span class="store-badge-new">${badge}</span><b>${formatCurrency(modeData.unit)}</b><span>/${unit}</span></div>
      ${pkgLine}
    `;
  }

  function renderCard(p){
    const unit = p.measurementUnit === 'lf' ? 'lf' : p.measurementUnit === 'piece' ? 'piece' : 'sqft';
    const priceType = computePriceType(p, 1);
    const badgeLabel = priceType === 'stock' ? (STORE_CONFIG?.ui?.badges?.stock || 'In stock') : (STORE_CONFIG?.ui?.badges?.backorder || 'Order-in');
    const badgeClass = priceType === 'stock' ? '' : 'backorder';
    const stockAvailable = Number(p.availability?.stockAvailable ?? null);
    const hasStock = (p.availability?.mode || '').toLowerCase() === 'stock' && Number.isFinite(stockAvailable) && stockAvailable > 0;
    const pkgLabel = p.packageLabel || 'box';
    const coverLabel = p.packageCoverage ? `${formatNumber(p.packageCoverage)} ${unit} / ${pkgLabel}` : '';
    const href = `product.php?sku=${encodeURIComponent(p.sku)}`;
    const img = p.images?.[0] ? `../${p.images[0]}` : '';
    let stockLabel = '';
    if(hasStock){
      const pkgLabelPlural = p.packageLabelPlural || `${pkgLabel}es`;
      const unitLabel = unit === 'lf' ? 'linear feet' : unit === 'piece' ? 'pieces' : 'sqft';
      let approxPackages = '';
      if(p.packageCoverage){
        const packagesCount = stockAvailable / p.packageCoverage;
        if(Number.isFinite(packagesCount) && packagesCount > 0){
          const chosenPkgLabel = packagesCount === 1 ? pkgLabel : pkgLabelPlural;
          approxPackages = ` (≈ ${formatNumber(packagesCount)} ${chosenPkgLabel})`;
        }
      }
      stockLabel = `<div class="store-meta">In stock: ${formatNumber(stockAvailable)} ${unitLabel}${approxPackages}</div>`;
    } else if(Number.isFinite(stockAvailable)) {
      stockLabel = `<div class="store-meta">Next batch: ${formatNumber(stockAvailable)} coming</div>`;
    }

    const priceHtml = buildPriceHtml(p, priceType, unit);

    return `
      <article class="store-card-new" data-sku="${p.sku}">
        <a href="${href}" class="store-card-img" style="background-image:url('${img}')">
          ${badgeLabel ? `<span class="store-tag ${badgeClass}">${badgeLabel}</span>` : ''}
        </a>
        <div class="store-card-body">
          <div>
            <h3><a href="${href}">${p.name}</a></h3>
            <div class="store-meta">${p.collection || ''} ${p.category ? '· '+p.category : ''}</div>
          </div>
          <div class="store-prices">${priceHtml || '<div class="store-price-line"><b>Call for price</b></div>'}</div>
          ${stockLabel || (coverLabel ? `<div class="store-meta">${coverLabel}</div>` : '')}
          ${stockLabel && coverLabel ? `<div class="store-meta">${coverLabel}</div>` : ''}
          <div class="store-badges">
            ${p.thickness ? `<span class="store-badge-new">${p.thickness} mm</span>` : ''}
            ${p.wearLayer ? `<span class="store-badge-new">${p.wearLayer} mil wear</span>` : ''}
            ${p.widthIn && p.lengthIn ? `<span class="store-badge-new">${p.widthIn}×${p.lengthIn} in</span>` : ''}
            ${p.colorFamily ? `<span class="store-badge-new">${p.colorFamily}</span>` : ''}
          </div>
          <div class="store-cta-row">
            <label style="display:flex; align-items:center; gap:6px;">
              <span style="font-size:0.9rem; color:#6a605e;">${p.packageLabelPlural || 'Qty'}</span>
              <input type="number" class="qty" min="1" value="1" ${p.availability?.maxPurchaseQuantity ? `max="${p.availability.maxPurchaseQuantity}"` : ''}>
            </label>
            <button class="btn btn-primary add-cart" type="button">Add to project</button>
            <a class="btn btn-ghost" href="${href}">View details</a>
          </div>
        </div>
      </article>
    `;
  }

  function getCheckedValues(selector){
    return Array.from(document.querySelectorAll(selector)).filter(el=>el.checked).map(el=>el.value.toLowerCase());
  }

  function matchesAvailability(p, filters){
    if(filters.length === 0) return true;
    const mode = (p.availability?.mode || '').toLowerCase();
    const lead = Number(p.availability?.backorderLeadTimeDays ?? NaN);
    return filters.some(f=>{
      if(f === 'stock') return mode === 'stock';
      if(f === 'backorder') return mode !== 'stock';
      if(f === 'nextday') return mode === 'stock' || (Number.isFinite(lead) && lead <= 7);
      return true;
    });
  }

  function applyFilters(list){
    const colorFilters = getCheckedValues('.filter-color');
    const availabilityFilters = getCheckedValues('.filter-availability');
    const toneFilters = colorFilters.filter(v=>['light','medium','dark'].includes(v));
    const familyFilters = colorFilters.filter(v=>!['light','medium','dark'].includes(v));
    const thkMin = parseFloat(document.getElementById('fThkMin')?.value || '') || 0;
    const wearMin = parseFloat(document.getElementById('fWearMin')?.value || '') || 0;

    let filtered = [...list];
    if(CURRENT_TYPE === 'flooring'){
      filtered = filtered.filter(p=>{
        const tone = (p.tone || '').toLowerCase();
        const family = (p.colorFamily || '').toLowerCase();
        if(toneFilters.length && !toneFilters.includes(tone)) return false;
        if(familyFilters.length && !familyFilters.includes(family)) return false;
        if(thkMin && (parseFloat(p.thickness) || 0) < thkMin) return false;
        if(wearMin && (parseFloat(p.wearLayer) || 0) < wearMin) return false;
        return true;
      });
    }
    filtered = filtered.filter(p=>matchesAvailability(p, availabilityFilters));
    return filtered;
  }

  function getSortPrice(p){
    const raw = p.pricing?.activePricePerUnit ?? p.pricing?.finalPriceStockPerUnit ?? p.pricing?.finalPriceBackorderPerUnit;
    const num = Number(raw);
    return Number.isFinite(num) ? num : null;
  }

  function applySort(list){
    const v = document.getElementById('sortSel')?.value || 'relevance';
    const copy = [...list];
    if(v==='price-asc'){
      copy.sort((a,b)=> (getSortPrice(a) ?? 1e9) - (getSortPrice(b) ?? 1e9));
    }else if(v==='price-desc'){
      copy.sort((a,b)=> (getSortPrice(b) ?? -1) - (getSortPrice(a) ?? -1));
    }
    return copy;
  }

  function render(){
    const list = applySort(applyFilters(BS_PRODUCTS));
    grid.innerHTML = list.map(renderCard).join('');
    if(resultSummary){
      resultSummary.textContent = `Showing ${list.length} of ${BS_PRODUCTS.length} products`;
    }
    grid.querySelectorAll('.store-card-new').forEach(card=>{
      const sku = card.dataset?.sku;
      const product = list.find(p=>p.sku === sku);
      const qtyInput = card.querySelector('.qty');
      const badge = card.querySelector('.store-tag');
      const priceContainer = card.querySelector('.store-prices');
      if(!product) return;
      const unit = product.measurementUnit === 'lf' ? 'lf' : product.measurementUnit === 'piece' ? 'piece' : 'sqft';
      const updateCardPricing = ()=>{
        let qty = parseInt(qtyInput?.value || '1', 10) || 1;
        const maxQty = Number(qtyInput?.max || '');
        if(Number.isFinite(maxQty) && maxQty > 0 && qty > maxQty){
          qty = maxQty;
          if(qtyInput) qtyInput.value = qty;
        }
        if(qty < 1){
          qty = 1;
          if(qtyInput) qtyInput.value = qty;
        }

        const priceType = computePriceType(product, qty);
        const badgeLabel = priceType === 'stock' ? (STORE_CONFIG?.ui?.badges?.stock || 'In stock') : (STORE_CONFIG?.ui?.badges?.backorder || 'Order-in');
        badge?.classList.toggle('backorder', priceType !== 'stock');
        if(badge) badge.textContent = badgeLabel;
        if(priceContainer) priceContainer.innerHTML = buildPriceHtml(product, priceType, unit);
      };

      qtyInput?.addEventListener('input', updateCardPricing);
      qtyInput?.addEventListener('change', updateCardPricing);
      updateCardPricing();
    });
    grid.querySelectorAll('.add-cart').forEach(btn=>{
      btn.addEventListener('click', async ()=>{
        const card = btn.closest('.store-card-new');
        const qtyInput = card.querySelector('.qty');
        const maxQty = Number(qtyInput?.max || '');
        let qty = parseInt(qtyInput?.value || '1', 10) || 1;
        if(Number.isFinite(maxQty) && maxQty > 0){
          qty = Math.min(qty, maxQty);
          if(qtyInput) qtyInput.value = qty;
        }
        const sku = card?.dataset?.sku;
        if(!sku) return;
        const product = list.find(p=>p.sku===sku);
        const priceType = computePriceType(product, qty);
        const inventoryId = priceType === 'stock' ? (product?.availability?.activeInventoryId || null) : null;
        cart.addItem(sku, qty, priceType, {inventoryId});
        btn.textContent = 'Added';
        setTimeout(()=>{btn.textContent='Add to project';}, 1200);
        if(CURRENT_TYPE === 'flooring'){
          const goToMolding = await bsModal.confirm({
            title: 'Añadir moldings',
            message: '¿Quieres añadir moldings para este piso? Te llevaremos a la sección de moldings.',
            confirmText: 'Sí, llévame',
            cancelText: 'No por ahora'
          });
          if(goToMolding){
            const url = new URL(window.location.href);
            url.searchParams.set('type', 'molding');
            window.location.href = url.toString();
          }
        }
      });
    });
  }

  ['sortSel','fThkMin','fWearMin'].forEach(id=>{
    document.getElementById(id)?.addEventListener('change', render);
  });
  document.querySelectorAll('.filter-color,.filter-availability').forEach(el=>{
    el.addEventListener('change', render);
  });
  document.getElementById('clearFilters')?.addEventListener('click', ()=>{
    ['fThkMin','fWearMin'].forEach(id=>{
      const el = document.getElementById(id);
      if(el) el.value = '';
    });
    document.querySelectorAll('.filter-color,.filter-availability').forEach(el=>{
      el.checked = false;
    });
    render();
  });

  typeSwitch?.addEventListener('change', ()=>{
    const val = typeSwitch.value === 'molding' ? 'molding' : 'flooring';
    const url = new URL(window.location.href);
    url.searchParams.set('type', val);
    window.location.href = url.toString();
  });

  render();
})();
