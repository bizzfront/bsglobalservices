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
      sanitized.push({
        sku,
        quantity,
        priceType: normalizePriceType(raw.priceType)
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

  const api = {
    addItem(sku, qty, priceType){
      if(!sku) return;
      qty = parseInt(qty) || 1;
      const normalizedType = normalizePriceType(priceType);
      const cart = load();
      const item = cart.items.find(i => i.sku === sku && i.priceType === normalizedType);
      if(item){
        item.quantity += qty;
      }else{
        cart.items.push({sku, quantity: qty, priceType: normalizedType});
      }
      cart.createdAt = Date.now();
      save(cart);
      notify();
    },
    setItem(sku, qty, priceType){
      qty = parseInt(qty) || 1;
      const normalizedType = normalizePriceType(priceType);
      const cart = load();
      const item = cart.items.find(i => i.sku === sku && i.priceType === normalizedType);
      if(item){
        item.quantity = qty;
      }else{
        cart.items.push({sku, quantity: qty, priceType: normalizedType});
      }
      cart.createdAt = Date.now();
      save(cart);
      notify();
    },
    removeItem(sku, priceType){
      const normalizedType = normalizePriceType(priceType);
      const cart = load();
      cart.items = cart.items.filter(i => !(i.sku === sku && i.priceType === normalizedType));
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
