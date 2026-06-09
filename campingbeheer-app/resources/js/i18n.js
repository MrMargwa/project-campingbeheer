import nl from './lang/nl.json';
import en from './lang/en.json';
import de from './lang/de.json';
import fy from './lang/fy.json';

const translations = { nl, en, de, fy };

const SUPPORTED = ['nl', 'en', 'de', 'fy'];

function detectLocale() {
  const stored = localStorage.getItem('locale');
  if (stored && SUPPORTED.includes(stored)) return stored;

  const lang = (navigator.language || '').slice(0, 2);
  if (lang === 'nl' || lang === 'de') return lang;

  const fyMatch = (navigator.language || '').toLowerCase() === 'fy' ||
    ((navigator.languages || []).some(l => l.toLowerCase() === 'fy' || l.toLowerCase() === 'fy-nl'));
  if (fyMatch) return 'fy';

  return 'en';
}

let currentLocale = detectLocale();

function t(key, params = {}) {
  let text = translations[currentLocale]?.[key];
  if (text === undefined) {
    text = translations['en']?.[key] ?? key;
  }
  for (const [k, v] of Object.entries(params)) {
    text = text.replace(new RegExp(`\\{${k}\\}`, 'g'), v);
  }
  return text;
}

function translatePage() {
  document.querySelectorAll('[data-i18n]').forEach(el => {
    const key = el.dataset.i18n;
    el.textContent = t(key);
  });

  document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
    const key = el.dataset.i18nPlaceholder;
    el.placeholder = t(key);
  });
}

function setLocale(locale) {
  if (!SUPPORTED.includes(locale)) return;
  currentLocale = locale;
  localStorage.setItem('locale', locale);
  document.cookie = 'locale=' + locale + ';path=/;SameSite=Lax';
  translatePage();

  const sel = document.getElementById('language-select');
  if (sel) sel.value = locale;

  const btn = document.getElementById('lang-dropdown-btn');
  if (btn) {
    btn.innerHTML = `<img src="${getFlagUrl(locale)}" alt="${locale}" class="w-5 h-auto rounded-sm">` +
      '<svg class="w-4 h-4 text-primary transition-transform" id="lang-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>';
  }

  const menu = document.getElementById('lang-dropdown-menu');
  if (menu && typeof window.renderLangMenu === 'function') {
    window.renderLangMenu(locale);
  }

  document.dispatchEvent(new CustomEvent('locale-changed', { detail: locale }));
}

function getFlagUrl(locale) {
  return `/images/flags/${locale}.svg`;
}

function getFlagIcon(locale) {
  return `<img src="${getFlagUrl(locale)}" alt="${locale}" class="w-5 h-auto rounded-sm">`;
}

function getLanguageName(locale) {
  const names = { nl: 'Nederlands', en: 'English', de: 'Deutsch', fy: 'Frysk' };
  return names[locale] || locale;
}

window.__ = t;
window.i18nSetLocale = setLocale;
window.i18nFlagUrl = getFlagUrl;

document.addEventListener('DOMContentLoaded', translatePage);

export { t, setLocale, currentLocale, translatePage, getFlagIcon, getLanguageName };
