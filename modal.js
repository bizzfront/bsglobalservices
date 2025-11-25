(function(){
  if(window.bsModal) return;

  const overlay = document.createElement('div');
  overlay.className = 'bs-modal-overlay';
  overlay.innerHTML = `
    <div class="bs-modal" role="dialog" aria-modal="true">
      <button type="button" class="bs-modal__close" aria-label="Close modal">&times;</button>
      <div class="bs-modal__content">
        <h3 class="bs-modal__title"></h3>
        <p class="bs-modal__message"></p>
      </div>
      <div class="bs-modal__actions">
        <button type="button" class="btn btn-ghost bs-modal__cancel">Cancel</button>
        <button type="button" class="btn btn-primary bs-modal__confirm">Confirm</button>
      </div>
    </div>
  `;

  let resolveFn = null;
  let dismissible = true;
  const modal = overlay.querySelector('.bs-modal');
  const titleEl = overlay.querySelector('.bs-modal__title');
  const messageEl = overlay.querySelector('.bs-modal__message');
  const confirmBtn = overlay.querySelector('.bs-modal__confirm');
  const cancelBtn = overlay.querySelector('.bs-modal__cancel');
  const closeBtn = overlay.querySelector('.bs-modal__close');

  function close(result){
    overlay.classList.remove('is-active');
    document.body.classList.remove('bs-modal-open');
    resolveFn && resolveFn(result);
    resolveFn = null;
    dismissible = true;
  }

  function open(options){
    const {
      title = 'Mensaje',
      message = '',
      confirmText = 'Aceptar',
      cancelText = 'Cancelar',
      showCancel = true,
      mode = 'confirm',
      allowDismiss = true
    } = options || {};

    dismissible = allowDismiss !== false;

    titleEl.textContent = title;
    messageEl.textContent = message;
    confirmBtn.textContent = confirmText;
    cancelBtn.textContent = cancelText;
    cancelBtn.style.display = mode === 'alert' || !showCancel ? 'none' : 'inline-flex';

    return new Promise((resolve)=>{
      resolveFn = resolve;
      overlay.classList.add('is-active');
      document.body.classList.add('bs-modal-open');
      setTimeout(()=>{ confirmBtn?.focus(); }, 30);
    });
  }

  overlay.addEventListener('click', (e)=>{
    if(e.target === overlay && dismissible){
      close(false);
    }
  });

  confirmBtn?.addEventListener('click', ()=> close(true));
  cancelBtn?.addEventListener('click', ()=> close(false));
  closeBtn?.addEventListener('click', ()=> close(false));

  document.addEventListener('keydown', (e)=>{
    if(!overlay.classList.contains('is-active')) return;
    if(e.key === 'Escape' && dismissible){
      close(false);
    }
    if(e.key === 'Enter' && document.activeElement === confirmBtn){
      e.preventDefault();
      close(true);
    }
  });

  function attachOverlay(){
    if(!overlay.isConnected && document.body){
      document.body.appendChild(overlay);
    }
  }

  document.addEventListener('DOMContentLoaded', attachOverlay);
  if(document.readyState !== 'loading'){
    attachOverlay();
  }

  window.bsModal = {
    open,
    alert(opts){
      return open({
        title: opts?.title || 'Aviso',
        message: opts?.message || '',
        confirmText: opts?.confirmText || 'Entendido',
        cancelText: '',
        showCancel: false,
        mode: 'alert',
        allowDismiss: opts?.allowDismiss !== false
      }).then(()=>true);
    },
    confirm(opts){
      return open({
        title: opts?.title || 'Confirmar',
        message: opts?.message || '',
        confirmText: opts?.confirmText || 'Sí',
        cancelText: opts?.cancelText || 'No',
        showCancel: opts?.showCancel !== false,
        mode: 'confirm',
        allowDismiss: opts?.allowDismiss !== false
      }).then(result => !!result);
    }
  };
})();
