<?php
$base = $base ?? '';
$active = $active ?? '';
?>
<link rel="stylesheet" href="<?=$base?>modal.css" />

<!-- Top bilingual bar -->
<div id="topbar" class="topbar">
  <div class="container wrap">
    <div id="topbar-text" data-i18n="topbar_text">Available in English & Spanish · También atendemos en español</div>
    <div class="lang-toggle" aria-label="Language switch">
      <button class="lang-btn active" id="lang-en">EN</button>
      <button class="lang-btn" id="lang-es">ES</button>
    </div>
  </div>
</div>
<!-- Header -->
<header class="site-header" aria-label="Main">
  <div class="container nav">
    <a href="<?=$base?>" class="brand" aria-label="B&S Floor Supply">
      <span class="logo-bs logo-bs--full" role="img" aria-label="B&S Floor Supply logo"></span>
    </a>
    <nav aria-label="Primary">
      <button class="burger" aria-label="Toggle menu" aria-controls="menu" aria-expanded="false" id="burger">
        <span></span><span></span><span></span>
      </button>
      <div id="menu" class="menu" role="menu">
        <?php if ($active === 'services'): ?>
          <a href="#" role="menuitem" data-i18n="nav_install">Flooring Install</a>
        <?php else: ?>
          <a href="<?=$base?>services/flooring-install/" role="menuitem" data-i18n="nav_install">Flooring Install</a>
        <?php endif; ?>
        <?php if ($active === 'store'): ?>
          <a href="#" role="menuitem" data-i18n="nav_store">Store</a>
        <?php else: ?>
          <a href="<?=$base?>store/" role="menuitem" data-i18n="nav_store">Store</a>
        <?php endif; ?>
        <?php if ($active === 'register'): ?>
          <a href="#" role="menuitem" data-i18n="nav_catalog">Catalog &amp; Schedule</a>
        <?php else: ?>
          <a href="<?=$base?>register/" role="menuitem" data-i18n="nav_catalog">Catalog &amp; Schedule</a>
        <?php endif; ?>
        <div class="cart-actions" id="cart-actions" aria-label="Cart actions">
          <a id="cart-link" href="<?=$base?>store/cart.php" role="menuitem" hidden>
            <span data-i18n="cart_label">Cart</span> (<span id="cart-count">0</span>)
          </a>
          <button type="button" id="cart-reset" class="cart-reset" data-i18n-aria="cart_reset_label" aria-label="Reset cart" hidden>&times;</button>
        </div>
      </div>
    </nav>
  </div>
</header>
<script>
  window.BS_I18N_PATH = "<?=$base?>i18n.json";
</script>
<script src="<?=$base?>i18n.js"></script>
<script src="<?=$base?>modal.js"></script>
<script src="<?=$base?>store/cart.js"></script>
<script>
  function getStoredCartCount(){
    try{
      const raw = localStorage.getItem('bs_cart');
      if(!raw) return 0;
      const data = JSON.parse(raw);
      if(!data || !Array.isArray(data.items)) return 0;
      return data.items.reduce((sum, item) => sum + (parseInt(item?.quantity) || 0), 0);
    }catch(e){
      return 0;
    }
  }

  function updateCartCount(){
    const count = (window.cart && typeof cart.getCount === 'function') ? cart.getCount() : getStoredCartCount();
    const el = document.getElementById('cart-count');
    const hasItems = count > 0;
    if(el) el.textContent = count;
    const link = document.getElementById('cart-link');
    const reset = document.getElementById('cart-reset');
    const actions = document.getElementById('cart-actions');
    if(link) link.hidden = !hasItems;
    if(reset) reset.hidden = !hasItems;
    if(actions) actions.hidden = !hasItems;
  }
  document.addEventListener('cartchange', updateCartCount);
  window.addEventListener('storage', (evt) => {
    if(evt.key === 'bs_cart') updateCartCount();
  });
  document.getElementById('cart-reset')?.addEventListener('click', async () => {
    const t = (key, fallback) => window.bsI18n?.t?.(key) || fallback;
    const confirmed = await bsModal.confirm({
      title: t('cart_reset_title', 'Empty cart'),
      message: t('cart_reset_message', 'Do you want to remove all items from your cart?'),
      confirmText: t('cart_reset_confirm', 'Yes, remove'),
      cancelText: t('cart_reset_cancel', 'No, keep')
    });
    if(confirmed){
      cart.clear();
    }
  });
  updateCartCount();
</script>
