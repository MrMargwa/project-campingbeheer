function haalAdresOpPerPostcode(postcode, huisnummer) {
    var genormaliseerd = postcode.replace(/\s+/g, '').toUpperCase();
    var apiSleutel = window.POSTCODE_API_SLEUTEL || '';

    if (!apiSleutel) {
        return valTerugAdresOphalen(genormaliseerd, huisnummer);
    }

    var url = 'https://postcode.tech/api/v1/postcode' +
        '?postcode=' + encodeURIComponent(genormaliseerd) +
        '&number=' + encodeURIComponent(huisnummer || '');

    return fetch(url, {
            headers: {
                'Authorization': 'Bearer ' + apiSleutel
            }
        })
        .then(function(r) {
            if (!r.ok) throw new Error();
            return r.json();
        })
        .then(function(json) {
            return {
                street: json.street || json.straatnaam || '',
                city: json.city || json.woonplaats || '',
                land: 'Nederland'
            };
        })
        .catch(function() {
            return valTerugAdresOphalen(genormaliseerd, huisnummer);
        });
}

function valTerugAdresOphalen(genormaliseerd, huisnummer) {
    function probeerPDOK() {
        var fqDelen = ['postcode:' + encodeURIComponent(genormaliseerd)];
        if (huisnummer) fqDelen.push('huisnummer:' + encodeURIComponent(huisnummer));
        var url = 'https://geodata.nationaalgeoregister.nl/locatieserver/v3/free' +
            '?q=*:*&rows=1&fq=' + fqDelen.join('&fq=');
        return fetch(url).then(function(r) {
                if (!r.ok) throw new Error();
                return r.json();
            })
            .then(function(json) {
                var doc = json.response?.docs?.[0];
                if (!doc) throw new Error();
                return {
                    street: doc.straatnaam || '',
                    city: doc.woonplaatsnaam || doc.city || '',
                    land: 'Nederland'
                };
            });
    }

    function probeerNominatim() {
        var zoekopdracht = genormaliseerd;
        if (huisnummer) zoekopdracht += '+' + huisnummer;
        var url = 'https://nominatim.openstreetmap.org/search' +
            '?q=' + encodeURIComponent(zoekopdracht) +
            '&format=json&addressdetails=1&countrycodes=nl&limit=1';
        return fetch(url, {
                headers: {
                    'User-Agent': 'Campingbeheer-App/1.0'
                }
            })
            .then(function(r) {
                if (!r.ok) throw new Error();
                return r.json();
            })
            .then(function(json) {
                if (!json || json.length === 0) throw new Error();
                var adres = json[0].address || {};
                return {
                    street: adres.road || adres.street || '',
                    city: adres.city || adres.town || adres.village || adres.place || '',
                    land: adres.country || 'Nederland'
                };
            });
    }

    function probeerZippopotam() {
        var url = 'https://api.zippopotam.us/NL/' + encodeURIComponent(genormaliseerd);
        return fetch(url).then(function(r) {
                if (!r.ok) throw new Error();
                return r.json();
            })
            .then(function(json) {
                var plaats = json.places?.[0];
                if (!plaats) return null;
                return {
                    street: '',
                    city: plaats['place name'] || plaats.city || '',
                    land: json.country || 'Netherlands'
                };
            });
    }

    return probeerPDOK().catch(probeerNominatim).catch(probeerZippopotam).catch(function() {
        return null;
    });
}

function toonAdresFout(bericht) {
    var el = document.getElementById('booking-error');
    if (el) {
        el.textContent = bericht;
        el.classList.remove('hidden');
    }
}

function initPostcodeZoek() {
    var postcodeInput = document.getElementById('postal-code-input');
    var zoekBtn = document.getElementById('postal-code-search');
    if (!postcodeInput || !zoekBtn) return;

    postcodeInput.addEventListener('input', function() {
        var waarde = this.value.trim().replace(/\s+/g, '');
        zoekBtn.disabled = waarde.length < 4;
        if (waarde.length >= 4) {
            zoekBtn.classList.remove('cursor-not-allowed', 'opacity-50');
        } else {
            zoekBtn.classList.add('cursor-not-allowed', 'opacity-50');
        }
    });

    zoekBtn.addEventListener('click', function() {
        var postcode = postcodeInput.value.trim();
        var huisnummer = document.getElementById('house-number-input')?.value.trim();
        if (!postcode) return;

        var btn = this;
        btn.disabled = true;
        btn.textContent = window.__('reserve.form.searching');

        haalAdresOpPerPostcode(postcode, huisnummer)
            .then(function(data) {
                if (data) {
                    var straatInput = document.getElementById('street-input');
                    var plaatsInput = document.getElementById('city-input');
                    var landInput = document.querySelector('input[name="country"]');
                    if (data.street && straatInput) straatInput.value = data.street;
                    if (data.city && plaatsInput) plaatsInput.value = data.city;
                    if (data.land && landInput) landInput.value = data.land;
                    var foutEl = document.getElementById('booking-error');
                    if (foutEl) foutEl.classList.add('hidden');
                } else {
                    toonAdresFout(window.__('reserve.form.address_not_found'));
                }
            })
            .catch(function() {
                toonAdresFout(window.__('reserve.form.address_fetch_error'));
            })
            .finally(function() {
                btn.disabled = false;
                btn.textContent = window.__('reserve.form.search');
            });
    });
}

function initBoekFormulier() {
    var formulier = document.getElementById('booking-form');
    if (!formulier) return;

    formulier.addEventListener('submit', function(e) {
        e.preventDefault();

        var formulierData = new FormData(formulier);
        var verzendBtn = document.getElementById('booking-submit');
        var foutEl = document.getElementById('booking-error');

        if (verzendBtn) {
            verzendBtn.disabled = true;
            verzendBtn.textContent = window.__('reserve.form.confirming');
        }
        if (foutEl) foutEl.classList.add('hidden');

        fetch('/reserveren', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                },
                body: formulierData,
            })
            .then(function(r) {
                if (!r.ok) {
                    return r.json().then(function(fout) {
                        throw fout;
                    });
                }
                return r.json();
            })
            .then(function(data) {
                if (data.success) {
                    if (typeof window.closeBookingModal === 'function') {
                        window.closeBookingModal();
                    }
                    alert(data.message || window.__('reserve.form.success'));
                    formulier.reset();
                }
            })
            .catch(function(fout) {
                var bericht = window.__('reserve.form.generic_error');
                if (fout.errors) {
                    var eersteSleutel = Object.keys(fout.errors)[0];
                    if (eersteSleutel) bericht = fout.errors[eersteSleutel][0];
                } else if (fout.message) {
                    bericht = fout.message;
                }
                if (foutEl) {
                    foutEl.textContent = bericht;
                    foutEl.classList.remove('hidden');
                }
            })
            .finally(function() {
                if (verzendBtn) {
                    verzendBtn.disabled = false;
                    verzendBtn.textContent = window.__('reserve.form.confirm');
                }
            });
    });
}

window.fetchAddressByPostcode = haalAdresOpPerPostcode;
window.showAddressError = toonAdresFout;
window.initPostcodeSearch = initPostcodeZoek;
window.initBookingForm = initBoekFormulier;

// Auto-initialize when module loads (deferred)
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
        initPostcodeZoek();
        initBoekFormulier();
    });
} else {
    initPostcodeZoek();
    initBoekFormulier();
}
