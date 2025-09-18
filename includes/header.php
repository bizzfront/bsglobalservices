<?php
$base = $base ?? '';
$active = $active ?? '';
?>
<!-- Top bilingual bar -->
<div id="topbar" class="topbar">
  <div class="container wrap">
    <div id="topbar-text">Available in English & Spanish · También atendemos en español</div>
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
          <a href="#" role="menuitem">Flooring Install</a>
        <?php else: ?>
          <a href="<?=$base?>services/flooring-install/" role="menuitem">Flooring Install</a>
        <?php endif; ?>
        <?php if ($active === 'store'): ?>
          <a href="#" role="menuitem">Store</a>
        <?php else: ?>
          <a href="<?=$base?>store/" role="menuitem">Store</a>
        <?php endif; ?>
        <?php if ($active === 'register'): ?>
          <a href="#" role="menuitem">Catalog &amp; Schedule</a>
        <?php else: ?>
          <a href="<?=$base?>register/" role="menuitem">Catalog &amp; Schedule</a>
        <?php endif; ?>
        <a id="cart-link" href="<?=$base?>store/cart.php" role="menuitem" hidden>Cart (<span id="cart-count">0</span>)</a>
      </div>
    </nav>
  </div>
</header>
<script src="<?=$base?>store/cart.js"></script>
<script>
  function updateCartCount(){
    const count = cart.getCount();
    const el = document.getElementById('cart-count');
    if(el) el.textContent = count;
    const link = document.getElementById('cart-link');
    if(link) link.hidden = count <= 0;
  }
  document.addEventListener('cartchange', updateCartCount);
  updateCartCount();
</script>
