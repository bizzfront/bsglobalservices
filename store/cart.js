(function(){
  const KEY = 'bs_cart';
  const TTL_MS = 12 * 60 * 60 * 1000; // 12 hours

  function load(){
    try{
      const data = JSON.parse(localStorage.getItem(KEY));
      if(!data || !Array.isArray(data.items) || (Date.now() - data.createdAt) > TTL_MS){
        localStorage.removeItem(KEY);
        return {createdAt: Date.now(), items: []};
      }
      return data;
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
    addItem(sku, qty){
      if(!sku) return;
      qty = parseInt(qty) || 1;
      const cart = load();
      const item = cart.items.find(i => i.sku === sku);
      if(item){
        item.quantity += qty;
      }else{
        cart.items.push({sku, quantity: qty});
      }
      cart.createdAt = Date.now();
      save(cart);
      notify();
    },
    setItem(sku, qty){
      qty = parseInt(qty) || 1;
      const cart = load();
      const item = cart.items.find(i => i.sku === sku);
      if(item){
        item.quantity = qty;
      }else{
        cart.items.push({sku, quantity: qty});
      }
      cart.createdAt = Date.now();
      save(cart);
      notify();
    },
    removeItem(sku){
      const cart = load();
      cart.items = cart.items.filter(i => i.sku !== sku);
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
