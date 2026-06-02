(() => {
  const COOKIE_NAME = 'site_theme';
  const ONE_YEAR_IN_DAYS = 365;

  function getTheme() {
    return document.body?.dataset.theme === 'dark' ? 'dark' : 'light';
  }

  function getNextTheme(theme) {
    return theme === 'dark' ? 'light' : 'dark';
  }

  function writeCookie(theme) {
    const expires = new Date(Date.now() + ONE_YEAR_IN_DAYS * 24 * 60 * 60 * 1000).toUTCString();
    document.cookie = `${COOKIE_NAME}=${encodeURIComponent(theme)}; expires=${expires}; path=/; SameSite=Lax`;
  }

  function paintTheme(theme) {
    const isDark = theme === 'dark';
    document.body.dataset.theme = theme;
    document.documentElement.style.colorScheme = theme;

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
      button.setAttribute('aria-pressed', isDark ? 'true' : 'false');

      const mark = button.querySelector('[data-theme-toggle-mark]');
      if (mark) {
        mark.textContent = isDark ? 'DK' : 'LT';
      }

      const state = button.querySelector('[data-theme-toggle-state]');
      if (state) {
        state.textContent = isDark ? 'Sombre actif' : 'Clair actif';
      }

      const label = button.querySelector('[data-theme-toggle-label]');
      if (label) {
        label.textContent = isDark ? 'Mode clair' : 'Mode sombre';
      }
    });
  }

  function bindToggles() {
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
      button.addEventListener('click', () => {
        const nextTheme = getNextTheme(getTheme());
        paintTheme(nextTheme);
        writeCookie(nextTheme);
      });
    });
  }

  if (document.body) {
    paintTheme(getTheme());
    bindToggles();
  }
})();
