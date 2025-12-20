(() => {
  const STORAGE_KEY = 'bs_lang';
  const DEFAULT_LANG = 'en';
  const state = {
    dict: null,
    lang: DEFAULT_LANG
  };
  let initPromise;

  const getPath = () => window.BS_I18N_PATH || 'i18n.json';

  const translate = (key) => {
    if(!state.dict) return '';
    return state.dict[state.lang]?.[key] ?? state.dict[DEFAULT_LANG]?.[key] ?? '';
  };

  const applyTranslations = (root = document) => {
    if(!state.dict) return;
    root.querySelectorAll('[data-i18n]').forEach((el) => {
      const key = el.dataset.i18n;
      const value = translate(key);
      if(value) el.textContent = value;
    });
    root.querySelectorAll('[data-i18n-html]').forEach((el) => {
      const key = el.dataset.i18nHtml;
      const value = translate(key);
      if(value) el.innerHTML = value;
    });
    root.querySelectorAll('[data-i18n-placeholder]').forEach((el) => {
      const key = el.dataset.i18nPlaceholder;
      const value = translate(key);
      if(value) el.setAttribute('placeholder', value);
    });
    root.querySelectorAll('[data-i18n-aria]').forEach((el) => {
      const key = el.dataset.i18nAria;
      const value = translate(key);
      if(value) el.setAttribute('aria-label', value);
    });
  };

  const updateToggleButtons = () => {
    const btnEN = document.getElementById('lang-en');
    const btnES = document.getElementById('lang-es');
    btnEN?.classList.toggle('active', state.lang === 'en');
    btnES?.classList.toggle('active', state.lang === 'es');
  };

  const setLang = (lang) => {
    if(!state.dict || !state.dict[lang]) return;
    state.lang = lang;
    localStorage.setItem(STORAGE_KEY, lang);
    document.documentElement.lang = lang;
    applyTranslations();
    updateToggleButtons();
    if(typeof window.trackEvent === 'function'){
      window.trackEvent('lang_toggle', {to: lang});
    }
    document.dispatchEvent(new CustomEvent('langchange', {detail: {lang}}));
  };

  const init = () => {
    if(initPromise) return initPromise;
    initPromise = (async () => {
      try{
        const response = await fetch(getPath(), {cache: 'no-store'});
        if(response.ok){
          state.dict = await response.json();
        }
      }catch(e){
        state.dict = null;
      }
      const stored = localStorage.getItem(STORAGE_KEY);
      if(stored && state.dict?.[stored]){
        state.lang = stored;
      }else{
        state.lang = DEFAULT_LANG;
      }
      document.documentElement.lang = state.lang;
      applyTranslations();
      updateToggleButtons();
    })();
    return initPromise;
  };

  window.bsI18n = {
    t: translate,
    setLang,
    getLang: () => state.lang,
    applyTranslations,
    init
  };

  document.addEventListener('DOMContentLoaded', async () => {
    await init();
    const btnEN = document.getElementById('lang-en');
    const btnES = document.getElementById('lang-es');
    btnEN?.addEventListener('click', () => setLang('en'));
    btnES?.addEventListener('click', () => setLang('es'));
  });
})();
