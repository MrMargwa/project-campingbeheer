function fetchAddressByPostcode(postalCode, houseNumber) {
    var normalized = postalCode.replace(/\s+/g, '').toUpperCase();
    var apiKey = window.POSTCODE_API_KEY || '';

    if (!apiKey) {
        return fallbackFetchAddress(normalized, houseNumber);
    }

    var url = 'https://postcode.tech/api/v1/postcode' +
        '?postcode=' + encodeURIComponent(normalized) +
        '&number=' + encodeURIComponent(houseNumber || '');

    return fetch(url, {
            headers: {
                'Authorization': 'Bearer ' + apiKey
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
            return fallbackFetchAddress(normalized, houseNumber);
        });
}

function fallbackFetchAddress(normalized, houseNumber) {
    function tryPDOK() {
        var fqParts = ['postcode:' + encodeURIComponent(normalized)];
        if (houseNumber) fqParts.push('huisnummer:' + encodeURIComponent(houseNumber));
        var url = 'https://geodata.nationaalgeoregister.nl/locatieserver/v3/free' +
            '?q=*:*&rows=1&fq=' + fqParts.join('&fq=');
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

    function tryNominatim() {
        var query = normalized;
        if (houseNumber) query += '+' + houseNumber;
        var url = 'https://nominatim.openstreetmap.org/search' +
            '?q=' + encodeURIComponent(query) +
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
                var addr = json[0].address || {};
                return {
                    street: addr.road || addr.street || '',
                    city: addr.city || addr.town || addr.village || addr.place || '',
                    land: addr.country || 'Nederland'
                };
            });
    }

    function tryZippopotam() {
        var url = 'https://api.zippopotam.us/NL/' + encodeURIComponent(normalized);
        return fetch(url).then(function(r) {
                if (!r.ok) throw new Error();
                return r.json();
            })
            .then(function(json) {
                var place = json.places?.[0];
                if (!place) return null;
                return {
                    street: '',
                    city: place['place name'] || place.city || '',
                    land: json.country || 'Netherlands'
                };
            });
    }

    return tryPDOK().catch(tryNominatim).catch(tryZippopotam).catch(function() {
        return null;
    });
}

function showAddressError(msg) {
    var el = document.getElementById('booking-error');
    if (el) {
        el.textContent = msg;
        el.classList.remove('hidden');
    }
}

function initPostcodeSearch() {
    var postcodeInput = document.getElementById('postal-code-input');
    var searchBtn = document.getElementById('postal-code-search');
    if (!postcodeInput || !searchBtn) return;

    postcodeInput.addEventListener('input', function() {
        var val = this.value.trim().replace(/\s+/g, '');
        searchBtn.disabled = val.length < 4;
        if (val.length >= 4) {
            searchBtn.classList.remove('cursor-not-allowed', 'opacity-50');
        } else {
            searchBtn.classList.add('cursor-not-allowed', 'opacity-50');
        }
    });

    searchBtn.addEventListener('click', function() {
        var postcode = postcodeInput.value.trim();
        var houseNumber = document.getElementById('house-number-input')?.value.trim();
        if (!postcode) return;

        var btn = this;
        btn.disabled = true;
        btn.textContent = window.__('reserve.form.searching');

        fetchAddressByPostcode(postcode, houseNumber)
            .then(function(data) {
                if (data) {
                    var streetInput = document.getElementById('street-input');
                    var cityInput = document.getElementById('city-input');
                    var landInput = document.querySelector('input[name="country"]');
                    if (data.street && streetInput) streetInput.value = data.street;
                    if (data.city && cityInput) cityInput.value = data.city;
                    if (data.land && landInput) landInput.value = data.land;
                    var errorEl = document.getElementById('booking-error');
                    if (errorEl) errorEl.classList.add('hidden');
                } else {
                    showAddressError(window.__('reserve.form.address_not_found'));
                }
            })
            .catch(function() {
                showAddressError(window.__('reserve.form.address_fetch_error'));
            })
            .finally(function() {
                btn.disabled = false;
                btn.textContent = window.__('reserve.form.search');
            });
    });
}

function initBookingForm() {
    var form = document.getElementById('booking-form');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(form);
        var submitBtn = document.getElementById('booking-submit');
        var errorEl = document.getElementById('booking-error');

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = window.__('reserve.form.confirming');
        }
        if (errorEl) errorEl.classList.add('hidden');

        fetch('/reserveren', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                },
                body: formData,
            })
            .then(function(r) {
                if (!r.ok) {
                    return r.json().then(function(err) {
                        throw err;
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
                    form.reset();
                }
            })
            .catch(function(err) {
                var msg = window.__('reserve.form.generic_error');
                if (err.errors) {
                    var firstKey = Object.keys(err.errors)[0];
                    if (firstKey) msg = err.errors[firstKey][0];
                } else if (err.message) {
                    msg = err.message;
                }
                if (errorEl) {
                    errorEl.textContent = msg;
                    errorEl.classList.remove('hidden');
                }
            })
            .finally(function() {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = window.__('reserve.form.confirm');
                }
            });
    });
}

window.fetchAddressByPostcode = fetchAddressByPostcode;
window.showAddressError = showAddressError;
window.initPostcodeSearch = initPostcodeSearch;
window.initBookingForm = initBookingForm;

// Auto-initialize when module loads (deferred)
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
        initPostcodeSearch();
        initBookingForm();
    });
} else {
    initPostcodeSearch();
    initBookingForm();
}
