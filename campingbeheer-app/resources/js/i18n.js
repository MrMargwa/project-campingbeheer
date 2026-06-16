import nl from './lang/nl.json';
import en from './lang/en.json';
import de from './lang/de.json';
import fy from './lang/fy.json';

const vertalingen = { nl, en, de, fy };

const ONDERSTEUND = ['nl', 'en', 'de', 'fy'];

function detecteerTaal() {
  const opgeslagen = localStorage.getItem('locale');
  if (opgeslagen && ONDERSTEUND.includes(opgeslagen)) return opgeslagen;

  const taal = (navigator.language || '').slice(0, 2);
  if (taal === 'nl' || taal === 'de') return taal;

  const fyMatch = (navigator.language || '').toLowerCase() === 'fy' ||
    ((navigator.languages || []).some(l => l.toLowerCase() === 'fy' || l.toLowerCase() === 'fy-nl'));
  if (fyMatch) return 'fy';

  return 'en';
}

let huidigeTaal = detecteerTaal();

function v(sleutel, params = {}) {
  let tekst = vertalingen[huidigeTaal]?.[sleutel];
  if (tekst === undefined) {
    tekst = vertalingen['en']?.[sleutel] ?? sleutel;
  }
  for (const [k, v] of Object.entries(params)) {
    tekst = tekst.replace(new RegExp(`\\{${k}\\}`, 'g'), v);
  }
  return tekst;
}

function vertaalPagina() {
  document.querySelectorAll('[data-i18n]').forEach(el => {
    const sleutel = el.dataset.i18n;
    el.textContent = v(sleutel);
  });

  document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
    const sleutel = el.dataset.i18nPlaceholder;
    el.placeholder = v(sleutel);
  });

  document.querySelectorAll('[data-i18n-title]').forEach(el => {
    const sleutel = el.dataset.i18nTitle;
    el.title = v(sleutel);
  });
}

function zetTaal(taal) {
  if (!ONDERSTEUND.includes(taal)) return;
  huidigeTaal = taal;
  localStorage.setItem('locale', taal);
  document.cookie = 'locale=' + taal + ';path=/;SameSite=Lax';
  vertaalPagina();

  const sel = document.getElementById('language-select');
  if (sel) sel.value = taal;

  const btn = document.getElementById('lang-dropdown-btn');
  if (btn) {
    btn.innerHTML = `<img src="${krijgVlagUrl(taal)}" alt="${taal}" class="w-5 h-auto rounded-sm">` +
      '<svg class="w-4 h-4 text-primary transition-transform" id="lang-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>';
  }

  const menu = document.getElementById('lang-dropdown-menu');
  if (menu && typeof window.renderLangMenu === 'function') {
    window.renderLangMenu(taal);
  }

  document.dispatchEvent(new CustomEvent('locale-changed', { detail: taal }));
}

function krijgVlagUrl(taal) {
  return `/images/flags/${taal}.svg`;
}

function krijgVlagIcoon(taal) {
  return `<img src="${krijgVlagUrl(taal)}" alt="${taal}" class="w-5 h-auto rounded-sm">`;
}

function krijgTaalNaam(taal) {
  const namen = { nl: 'Nederlands', en: 'English', de: 'Deutsch', fy: 'Frysk' };
  return namen[taal] || taal;
}

window.__ = v;
window.i18nSetLocale = zetTaal;
window.i18nFlagUrl = krijgVlagUrl;

window.renderLangMenu = function(actieveTaal) {
    var menu = document.getElementById('lang-dropdown-menu');
    if (!menu) return;
    var SEL = ['nl', 'en', 'de', 'fy'];
    var NAMEN = { nl: 'Nederlands', en: 'English', de: 'Deutsch', fy: 'Frysk' };
    var actief = actieveTaal || huidigeTaal;
    menu.innerHTML = SEL.map(function(code) {
        var vlag = krijgVlagUrl(code);
        var vink = code === actief ? '<span class="ml-auto text-accent text-xs">&#10003;</span>' : '';
        return '<button type="button" class="flex items-center gap-3 w-full px-3 py-2 text-sm text-left text-primary hover:bg-gray-50 transition border-b border-border last:border-b-0" data-lang="' + code + '" onclick="window.i18nSetLocale(\'' + code + '\');window.sluitTaalDropdown()">' +
            '<img src="' + vlag + '" alt="' + code + '" class="w-5 h-auto rounded-sm">' +
            '<span>' + NAMEN[code] + '</span>' + vink +
        '</button>';
    }).join('');
};

window.wisselTaalDropdown = function(e) {
    if (e) e.stopPropagation();
    var menu = document.getElementById('lang-dropdown-menu');
    var ch = document.getElementById('lang-chevron');
    if (!menu) return;
    var isOpen = !menu.classList.contains('hidden');
    menu.classList.toggle('hidden', isOpen);
    if (ch) ch.classList.toggle('rotate-180', !isOpen);
};

window.sluitTaalDropdown = function() {
    var menu = document.getElementById('lang-dropdown-menu');
    var ch = document.getElementById('lang-chevron');
    if (menu) menu.classList.add('hidden');
    if (ch) ch.classList.remove('rotate-180');
};

window.i18nInitDropdown = function() {
    var SEL = ['nl', 'en', 'de', 'fy'];
    var locale = (function() {
        var s = localStorage.getItem('locale');
        if (s && SEL.indexOf(s) !== -1) return s;
        var lang = (navigator.language || '').slice(0, 2);
        if (lang === 'nl' || lang === 'de') return lang;
        if ((navigator.language || '').toLowerCase() === 'fy' || (navigator.languages || []).some(function(l) { return l.toLowerCase() === 'fy' || l.toLowerCase() === 'fy-nl'; })) return 'fy';
        return 'en';
    })();

    document.cookie = 'locale=' + locale + ';path=/;SameSite=Lax';

    var sel = document.getElementById('language-select');
    if (sel) sel.value = locale;

    var btn = document.getElementById('lang-dropdown-btn');
    if (btn) {
        btn.innerHTML = '<img src="' + krijgVlagUrl(locale) + '" alt="' + locale + '" class="w-5 h-auto rounded-sm">' +
            '<svg class="w-4 h-4 text-primary transition-transform" id="lang-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>';
    }

    window.renderLangMenu(locale);

    document.addEventListener('click', function(e) {
        var wrapper = document.getElementById('lang-wrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            window.sluitTaalDropdown();
        }
    });
};

document.addEventListener('DOMContentLoaded', vertaalPagina);

export { v as t, zetTaal as setLocale, huidigeTaal as currentLocale, vertaalPagina as translatePage, krijgVlagIcoon as getFlagIcon, krijgTaalNaam as getLanguageName };
