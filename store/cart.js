(function(){
  const KEY = 'bs_cart';
  const TTL_MS = 12 * 60 * 60 * 1000; // 12 hours

  function normalizePriceType(value){
    return value === 'backorder' ? 'backorder' : 'stock';
  }

  function sanitizeItems(items){
    if(!Array.isArray(items)) return [];
    const sanitized = [];
    for(const raw of items){
      if(!raw || typeof raw !== 'object') continue;
      const sku = raw.sku;
      const quantity = parseInt(raw.quantity) || 0;
      if(!sku || quantity <= 0) continue;
      const inventoryId = typeof raw.inventoryId === 'string' ? raw.inventoryId : null;
      sanitized.push({
        sku,
        quantity,
        priceType: normalizePriceType(raw.priceType),
        install: !!raw.install,
        inventoryId
      });
    }
    return sanitized;
  }

  function load(){
    try{
      const data = JSON.parse(localStorage.getItem(KEY));
      if(!data || !Array.isArray(data.items) || (Date.now() - data.createdAt) > TTL_MS){
        localStorage.removeItem(KEY);
        return {createdAt: Date.now(), items: []};
      }
      return {createdAt: data.createdAt || Date.now(), items: sanitizeItems(data.items)};
    }catch(e){
      return {createdAt: Date.now(), items: []};
    }
  }

  function save(data){
    localStorage.setItem(KEY, JSON.stringify(data));
  }

  function notify(){
    document.dispatchEvent(new CustomEvent('cartchange'));
  }

  function findMatchingIndex(items, sku, priceType, inventoryId){
    const exactIndex = items.findIndex(i => i.sku === sku && i.priceType === priceType && i.inventoryId === inventoryId);
    if(exactIndex >= 0) return exactIndex;
    return items.findIndex(i => i.sku === sku);
  }

  const api = {
    addItem(sku, qty, priceType, options){
      if(!sku) return;
      qty = parseInt(qty) || 1;
      const normalizedType = normalizePriceType(priceType);
      const opts = options && typeof options === 'object' ? options : {};
      const cart = load();
      const targetInventory = typeof opts.inventoryId === 'string' ? opts.inventoryId : null;
      const idx = findMatchingIndex(cart.items, sku, normalizedType, targetInventory);
      if(idx >= 0){
        const item = cart.items[idx];
        item.quantity += qty;
        item.priceType = normalizedType;
        if(targetInventory !== undefined) item.inventoryId = targetInventory;
        if(typeof opts.install === 'boolean') item.install = opts.install;
      }else{
        cart.items.push({
          sku,
          quantity: qty,
          priceType: normalizedType,
          install: !!opts.install,
          inventoryId: targetInventory || null
        });
      }
      cart.createdAt = Date.now();
      save(cart);
      notify();
    },
    setItem(sku, qty, priceType, options){
      qty = parseInt(qty) || 1;
      const normalizedType = normalizePriceType(priceType);
      const opts = options && typeof options === 'object' ? options : {};
      const cart = load();
      const targetInventory = typeof opts.inventoryId === 'string' ? opts.inventoryId : null;
      const idx = findMatchingIndex(cart.items, sku, normalizedType, targetInventory);
      if(idx >= 0){
        const item = cart.items[idx];
        item.quantity = qty;
        item.priceType = normalizedType;
        if(targetInventory !== undefined) item.inventoryId = targetInventory;
        if(typeof opts.install === 'boolean') item.install = opts.install;
      }else{
        cart.items.push({
          sku,
          quantity: qty,
          priceType: normalizedType,
          install: !!opts.install,
          inventoryId: targetInventory || null
        });
      }
      cart.createdAt = Date.now();
      save(cart);
      notify();
    },
    removeItem(sku, priceType, inventoryId){
      const normalizedType = normalizePriceType(priceType);
      const normalizedInventory = typeof inventoryId === 'string' ? inventoryId : null;
      const cart = load();
      cart.items = cart.items.filter(i => {
        if(i.sku !== sku || i.priceType !== normalizedType) return true;
        if(normalizedInventory && i.inventoryId !== normalizedInventory) return true;
        return false;
      });
      cart.createdAt = Date.now();
      save(cart);
      notify();
    },
    clear(){
      save({createdAt: Date.now(), items: []});
      notify();
    },
    getItems(){
      return load().items;
    },
    getCount(){
      return load().items.reduce((sum, i) => sum + (i.quantity || 0), 0);
    },
    clearExpired(){
      const data = load();
      if(Date.now() - data.createdAt > TTL_MS){
        localStorage.removeItem(KEY);
        notify();
      }
    }
  };

  window.cart = api;
  api.clearExpired();
})();
