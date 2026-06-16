# Project Campingbeheer — Documentatie voor Studenten

## Inhoudsopgave

1. [Introductie](#1-introductie)
2. [Projectstructuur](#2-projectstructuur)
3. [Laravel Lifecycle — Hoe een request loopt](#3-laravel-lifecycle--hoe-een-request-loopt)
4. [Routing](#4-routing)
5. [Controllers](#5-controllers)
6. [Modellen (Eloquent ORM)](#6-modellen-eloquent-orm)
7. [Views (Blade Templates)](#7-views-blade-templates)
8. [Authenticatie & Middleware](#8-authenticatie--middleware)
9. [Boekingsflow — Complete Use Case](#9-boekingsflow--complete-use-case)
10. [Admin Functionaliteiten](#10-admin-functionaliteiten)
11. [Database Schema](#11-database-schema)
12. [Frontend](#12-frontend)
13. [Belangrijke Configuraties](#13-belangrijke-configuraties)

---

## 1. Introductie

Dit is een **Laravel 13** webapplicatie voor het beheren van een camping. Gasten kunnen accommodaties bekijken en reserveringen plaatsen. Beheerders kunnen reserveringen goedkeuren/afkeuren, accommodaties beheren (CRUD) en een planbord bekijken.

**Technische stack:**
- **Backend:** Laravel 13 (PHP 8.4+)
- **Frontend:** Blade, Tailwind CSS, Vite
- **Database:** SQLite
- **Kaarten:** Leaflet.js + OpenStreetMap
- **Internationalisatie:** Nederlands, Engels, Duits, Fries
- **E-mail:** Mailtrap (ontwikkeling)
- **Adreszoeker:** Postcode.tech API

---

## 2. Projectstructuur

```
project-campingbeheer/
├── campingbeheer-app/           <-- Dit is de Laravel applicatie
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/     <-- Alle controllers (logica per pagina)
│   │   │   └── Middleware/      <-- AdminMiddleware (role-check)
│   │   ├── Mail/                <-- E-mail klassen (Mailable)
│   │   ├── Models/              <-- Eloquent modellen (database tabellen)
│   │   └── Providers/           <-- Service Providers
│   ├── bootstrap/
│   │   └── app.php              <-- Kernel configuratie (Laravel 11+)
│   ├── config/                  <-- Configuratiebestanden
│   ├── database/
│   │   ├── factories/           <-- Test data generators
│   │   ├── migrations/          <-- Database schema (versiebeheer)
│   │   └── seeders/             <-- Test data (Zaaiers)
│   ├── resources/
│   │   ├── css/
│   │   ├── js/                  <-- JavaScript (Vite entry, i18n, adres)
│   │   └── views/               <-- Blade templates (HTML)
│   │       ├── layouts/         <-- Basis layouts (app, auth, admin)
│   │       ├── partials/        <-- Herbruikbare componenten
│   │       ├── admin/           <-- Admin pagina's
│   │       ├── auth/            <-- Login pagina
│   │       └── emails/          <-- E-mail templates
│   ├── routes/
│   │   └── web.php              <-- Alle web routes
│   ├── .env                     <-- Omgevingsvariabelen (DB, mail, API)
│   ├── composer.json
│   └── vite.config.js
├── documentatie-project.md      <-- Dit document
├── Projectplan Campingbeheer.docx
├── Techniesch Ontwerp - Campingbeheer.docx
└── Testcases voor Campingbeheer.xlsx
```

---

## 3. Laravel Lifecycle — Hoe een request loopt

Wanneer een gebruiker een URL bezoekt (bijv. `https://camping.nl/`), doorloopt Laravel een vaste reeks stappen:

```
Gebruiker typt URL in browser
        │
        ▼
   public/index.php           ← Entry point van Laravel
        │
        ▼
   bootstrap/app.php          ← Applicatie wordt opgestart
        │                        (middleware, providers laden)
        │
        ▼
   routes/web.php             ← Laravel zoekt naar een matching route
        │
        ▼
   Middleware                  ← Filters die vóór de controller draaien
        │                        (bv. 'auth' checkt of gebruiker is ingelogd,
        │                         'admin' checkt of rol = 'admin')
        │
        ▼
   Controller Method           ← De echte logica wordt uitgevoerd
        │                        (data ophalen uit DB, verwerken, etc.)
        │
        ▼
   Blade View                  ← Controller retourneert een view
        │                        (HTML template met data)
        │
        ▼
   Browser toont de pagina     ← Gebruiker ziet het resultaat
```

**Voorbeeld concreet:** Homepage (`/`)
1. Gebruiker typt `https://camping.nl/` in
2. `public/index.php` start Laravel op
3. Laravel leest `bootstrap/app.php` voor middleware configuratie
4. Laravel doorzoekt `routes/web.php` en vindt `Route::get('/', function () { ... })`
5. Er is **geen middleware** voor deze route (publiek toegankelijk)
6. De **Closure** (anonieme functie) wordt uitgevoerd:
   - Haalt alle accommodaties + bijbehorende kenmerken uit de database
   - Haalt alle types op (uniek, voor de filter dropdown)
   - Haalt alle kenmerken op
   - Bepaalt de taal via cookie
   - Retourneert `view('home', compact(...))`
7. Laravel rendert `resources/views/home.blade.php` die `layouts/app.blade.php` gebruikt
8. De browser toont de HTML-pagina

---

## 4. Routing

Alle routes staan in `routes/web.php`. Er zijn geen API routes.

### 4.1 Publieke Routes (geen login nodig)

| URL | Methode | Functie | Naam |
|-----|---------|---------|------|
| `/` | GET | Toont homepage met alle accommodaties en filters | `home` |
| `/reserveren` | GET | Toont de reserveringspagina met kaart | `reserveren` |
| `/reserveren` | POST | Verwerkt een nieuwe boeking (AJAX) | `reserveren.store` |
| `/login` | GET | Toont login formulier | `login` |
| `/login` | POST | Verwerkt inlogpoging | — |
| `/logout` | POST | Logt gebruiker uit | `logout` |

### 4.2 Admin Routes (auth + admin middleware vereist)

| URL | Methode | Functie | Naam |
|-----|---------|---------|------|
| `/admin/dashboard` | GET | Admin dashboard met overzicht | `admin.dashboard` |
| `/admin/planning-board` | GET | Weekplanning bord | `admin.planning-board.index` |
| `/admin/search-guests` | GET | AJAX zoeken naar gasten | `admin.search-guests` |
| `/admin/accommodations` | GET | Lijst van accommodaties | `admin.accommodation.index` |
| `/admin/accommodations/create` | GET | Formulier nieuwe accommodatie | `admin.accommodation.create` |
| `/admin/accommodations` | POST | Opslaan nieuwe accommodatie | `admin.accommodation.store` |
| `/admin/accommodations/{id}/edit` | GET | Formulier bewerken accommodatie | `admin.accommodation.edit` |
| `/admin/accommodations/{id}` | PUT | Bijwerken accommodatie | `admin.accommodation.update` |
| `/admin/accommodations/{id}` | DELETE | Verwijderen accommodatie | `admin.accommodation.destroy` |
| `/admin/bookings/{id}/approve` | POST | Boeking goedkeuren | `admin.bookings.approve` |
| `/admin/bookings/{id}/reject` | POST | Boeking afkeuren | `admin.bookings.reject` |

### 4.3 Hoe werkt route matching?

Laravel leest `routes/web.php` van boven naar onder. De eerste route die matcht wordt gebruikt.

```php
Route::get('/', function () {
    // ...
})->name('home');
```

- `Route::get()` betekent: alleen bij een GET-request
- `'/'` is het URL-pad
- De Closure is de "handler" (wat er moet gebeuren)
- `->name('home')` geeft de route een naam, zodat je in Blade `route('home')` kunt gebruiken

### 4.4 Helper: `getLocale()`

Bovenaan `web.php` staat een functie die de taal uitleest uit een cookie. Als er geen cookie is, wordt Nederlands (`nl`) gebruikt. Ondersteunde talen: `nl`, `en`, `de`, `fy` (Fries).

---

## 5. Controllers

Controllers bevatten de logica. Elke publieke methode in een controller is een **action** die aan een route hangt.

### 5.1 `AuthController` — `app/Http/Controllers/AuthController.php`

Verantwoordelijk voor inloggen en uitloggen.

| Methode | Wat doet het? |
|---------|---------------|
| `showLoginForm()` | Toont `auth.login` view (het loginformulier) |
| `login(Request $request)` | Valideert email + wachtwoord, probeert `Auth::attempt()`. Bij succes: sessie regenereren + redirect naar `/admin/dashboard`. Bij mislukken: terug met foutmelding (`->with('error', ...)`) |
| `logout(Request $request)` | Uitloggen, sessie invalidaten, redirect naar `/` |

**Belangrijk:** De User model gebruikt `wachtwoord` kolom i.p.v. `password`. Daarom overschrijft User de methode `getAuthPassword()`.

### 5.2 `ReservationController` — `app/Http/Controllers/ReservationController.php`

| Methode | Wat doet het? |
|---------|---------------|
| `index()` | Laadt alle accommodaties (met kenmerken), haalt de postcode API key op, bepaalt de locale. Retourneert `reserveren` view. |

### 5.3 `BookingController` — `app/Http/Controllers/BookingController.php`

| Methode | Wat doet het? |
|---------|---------------|
| `store(Request $request)` | Validatie van alle boekingsvelden (naam, email, telefoon, postcode, data, etc.). Checkt of aantal personen binnen min/max valt. Berekent totaalprijs. Maakt Booking aan met status `in_afwachting`. Stuurt `BookingConfirmation` email. Retourneert JSON. |
| `approve(Booking $booking)` | Zet status op `goedgekeurd`. Stuurt `BookingApproved` email. Redirect terug. |
| `reject(Request $request, Booking $booking)` | Zet status op `geannuleerd`. Voegt afkeurreden toe aan opmerking. Stuurt `BookingRejected` email. Redirect terug. |
| `searchGasten(Request $request)` | AJAX endpoint. Zoekt boekingen op naam (min 2 karakters). Retourneert unieke resultaten (max 10) voor de admin modal. |

### 5.4 `AdminDashboardController` — `app/Http/Controllers/AdminDashboardController.php`

| Methode | Wat doet het? |
|---------|---------------|
| `index()` | Laadt: accommodaties, postcode API key, alle `in_afwachting` boekingen (genagigeerd), en de boekingen voor vandaag (aankomst en vertrek met status `goedgekeurd` of `gereed`). |

### 5.5 `PlanningBoardController` — `app/Http/Controllers/PlanningBoardController.php`

| Methode | Wat doet het? |
|---------|---------------|
| `index(Request $request)` | Toont weekplanning. Accepteert `type` filter en `week` offset. Groepeert goedgekeurde/gereed boekingen per accommodatie voor een weekoverzicht. |

### 5.6 `AccommodationController` — `app/Http/Controllers/AccommodationController.php`

Volledige CRUD voor accommodaties.

| Methode | Wat doet het? |
|---------|---------------|
| `index()` | Lijst van alle accommodaties (gesorteerd op aanmaakdatum) |
| `create()` | Toont formulier met kaart (bestaande accommodaties als referentie) |
| `store(Request $request)` | Valideert en slaat nieuwe accommodatie op |
| `edit(Accommodation $accommodation)` | Toont formulier met ingevulde data + kaart |
| `update(Request $request, Accommodation $accommodation)` | Valideert en werkt accommodatie bij |
| `destroy(Accommodation $accommodation)` | Verwijdert accommodatie |

### 5.7 Base `Controller` — `app/Http/Controllers/Controller.php`

Abstracte basisklasse. Alle controllers erven hiervan.

---

## 6. Modellen (Eloquent ORM)

Modellen representeren database tabellen. Elke rij in de tabel is een **instantie** van het model.

### 6.1 `User` — `app/Models/User.php` (tabel: `users`)

```php
class User extends Model
{
    protected $fillable = ['naam', 'email', 'wachtwoord', 'rol'];
    // 'rol' kan 'admin' of 'gast' zijn
}
```

**Relaties:**
- `boekingen()` — `HasMany` naar `Booking` via `gebruiker_id`

**Authenticatie:**
- Overschrijft `getAuthPassword()` om `$this->wachtwoord` te returnen (i.p.v. `password`)
- `casts()` zet `wachtwoord` naar `hashed`, zodat Laravel automatisch hasht bij opslaan
- `CREATED_AT` = `aangemaakt_op`, `UPDATED_AT` = `bewerkt_op`

### 6.2 `Accommodation` — `app/Models/Accommodation.php` (tabel: `accommodaties`)

```php
class Accommodation extends Model
{
    protected $fillable = [
        'titel', 'type', 'beschrijving', 'min_personen', 'max_personen',
        'prijs_per_nacht', 'afbeelding', 'latitude', 'longitude', 'status'
    ];
}
```

**Relaties:**
- `bookings()` — `HasMany` naar `Booking` via `accommodatie_id`
- `features()` — `BelongsToMany` naar `Feature` via `accommodatie_kenmerk` (pivot-tabel)

**Vertaalmethodes:**
- `translatedTitle($locale)` — zoekt kolom `titel_nl`, `titel_en`, etc.
- `translatedDescription($locale)` — zoekt kolom `beschrijving_nl`, etc.
- `translatedType($locale)` — zoekt kolom `type_nl`, etc.
Valt terug op Nederlandse waarde als vertaling ontbreekt.

### 6.3 `Booking` — `app/Models/Booking.php` (tabel: `boekingen`)

```php
class Booking extends Model
{
    protected $fillable = [
        'gebruiker_id', 'accommodatie_id', 'naam', 'email', 'telefoon',
        'postcode', 'huisnummer', 'straat', 'plaats', 'land',
        'aankomst_datum', 'aankomst_tijd', 'vertrek_datum', 'vertrek_tijd',
        'aantal_personen', 'opmerking', 'totaal_prijs', 'status'
    ];
}
```

**Status mogelijkheden:** `in_afwachting`, `goedgekeurd`, `geannuleerd`, `gereed`

**Relaties:**
- `user()` — `BelongsTo` naar `User`
- `accommodation()` — `BelongsTo` naar `Accommodation`

### 6.4 `Feature` — `app/Models/Feature.php` (tabel: `kenmerken`)

```php
class Feature extends Model
{
    protected $fillable = ['naam', 'naam_en', 'naam_de', 'naam_fy'];
    public $timestamps = false;  // Geen 'bewerkt_op' kolom
}
```

**Vertaalmethode:** `translatedName($locale)` — zoekt `naam_nl`, `naam_en`, etc.

**Relaties:**
- `accommodations()` — `BelongsToMany` naar `Accommodation` via `accommodatie_kenmerk`

### 6.5 `AccommodationFeature` — `app/Models/AccommodationFeature.php` (tabel: `accommodatie_kenmerk`)

Pivot-model voor de many-to-many relatie tussen accommodaties en kenmerken.

```php
class AccommodationFeature extends Model
{
    protected $table = 'accommodatie_kenmerk';
    public $timestamps = false;
    protected $fillable = ['accommodatie_id', 'kenmerk_id'];
}
```

### 6.6 Hoe werkt Eloquent?

```php
// Alle accommodaties ophalen (met hun kenmerken)
$accommodaties = Accommodation::with('features')->get();

// Een accommodatie vinden op ID
$accommodatie = Accommodation::find(1);

// Boekingen filteren op status
$wachtend = Booking::where('status', 'in_afwachting')->get();

// Relatie gebruiken
$boekingen = $accommodatie->bookings;  // Alle boekingen voor deze accommodatie
```

---

## 7. Views (Blade Templates)

Blade is de templating engine van Laravel. Bestanden eindigen op `.blade.php` en staan in `resources/views/`.

### 7.1 Layouts (basisstructuren)

Elke pagina gebruikt een layout. De layout bevat de **HTML-skelet** (head, body, scripts) en een `@yield('content')` waar de pagina-inhoud komt.

**`layouts/app.blade.php`** — Publieke layout voor gasten:
- Inclusief navigatiebalk (`partials.nav`)
- `@yield('content')` — hier komt de paginaspecifieke inhoud
- Inclusief footer (`partials.footer`)
- Vite asset bundeling

**`layouts/auth.blade.php`** — Minimalistische layout voor login:
- Geen navigatie of footer
- Gecentreerde smalle kolom

**`layouts/admin.blade.php`** — Admin layout:
- Flex container met sidebar links (`partials.admin-sidebar`)
- Admin header bovenaan (`partials.admin-header`)
- `@yield('content')` in het midden

### 7.2 Partials (herbruikbare componenten)

| Partial | Beschrijving |
|---------|--------------|
| `nav.blade.php` | Navigatiebalk met logo, "Reserveer Nu" knop, taalmenu |
| `footer.blade.php` | Footer met copyright, credits aan Niek, Julian, Justin |
| `banner.blade.php` | Hero banner met achtergrondafbeelding en titel |
| `admin-sidebar.blade.php` | Zijbalk van 240px met links naar Dashboard, Accommodatie, Planbord |
| `admin-header.blade.php` | Admin bovenbalk met paginatitel, uitlogknop, taalmenu |
| `reserveer-modal.blade.php` | Boekingsformulier in een modal venster (herbruikbaar op meerdere pagina's) |

### 7.3 Publieke Pagina's

| View | Route | Wat zie je? |
|------|-------|-------------|
| `home.blade.php` | `/` | Filterpaneel (personen, type, data, kenmerken) + lijst met accommodatiekaarten |
| `reserveren.blade.php` | `/reserveren` | Leaflet kaart met markers, filters, detailkaart bij klikken + reserveer-modal |
| `auth/login.blade.php` | `/login` | Email + wachtwoord formulier |

### 7.4 Admin Pagina's

| View | Route | Wat zie je? |
|------|-------|-------------|
| `admin/dashboard.blade.php` | `/admin/dashboard` | Vandaag aankomst/vertrek tabellen, alle aanvragen met goedkeur/afkeur knoppen |
| `admin/accommodation/index.blade.php` | `/admin/accommodations` | Lijst van accommodaties met bewerk/verwijder acties |
| `admin/accommodation/create.blade.php` | `/admin/accommodations/create` | Formulier + kaart om accommodatie toe te voegen |
| `admin/accommodation/edit.blade.php` | `/admin/accommodations/{id}/edit` | Zelfde formulier maar vooraf ingevuld |
| `planbord/index.blade.php` | `/admin/planning-board` | Weekplanning met kleurgecodeerde cellen |

### 7.5 Hoe werkt `@extends` en `@section`?

```blade
{{-- home.blade.php --}}
@extends('layouts.app')           {{-- Deze pagina gebruikt de app layout --}}

@section('title', 'Home')          {{-- Vult de @yield('title') in de layout --}}

@section('content')
    <h1>Welkom</h1>                {{-- Dit komt op de plek van @yield('content') --}}
@endsection
```

---

## 8. Authenticatie & Middleware

### 8.1 Middleware Configuratie (`bootstrap/app.php`)

In Laravel 11+ wordt middleware geconfigureerd in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias(['admin' => AdminMiddleware::class]);
})
```

### 8.2 AdminMiddleware (`app/Http/Middleware/AdminMiddleware.php`)

```php
public function handle(Request $request, Closure $next): mixed
{
    if ($request->user()->rol !== 'admin') {
        abort(403);  // Geen toegang
    }
    return $next($request);
}
```

Deze middleware checkt of de ingelogde gebruiker de rol `admin` heeft. Zo niet: 403 Forbidden.

### 8.3 Hoe wordt middleware toegepast?

```php
Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
    ->middleware(['auth', 'admin']);  // Eerst ingelogd? Dan ook admin?
```

- `auth` middleware: checkt of gebruiker is ingelogd (redirect naar `/login` zo niet)
- `admin` middleware: checkt of rol = `admin` (403 zo niet)

### 8.4 Login Flow (stap voor stap)

1. **Gebruiker bezoekt** `/login` → `AuthController@showLoginForm()` → toont `auth/login.blade.php`
2. **Gebruiker vult email + wachtwoord in** en klikt op "Inloggen"
3. **Formulier POST** naar `/login` → `AuthController@login(Request $request)`
4. **Validatie:** `email` (required|email) en `password` (required|string)
5. **Auth::attempt()** probeert in te loggen:
   ```php
   Auth::attempt(['email' => $email, 'password' => $password])
   ```
   - Dit zoekt in de `users` tabel naar het emailadres
   - Vergelijkt het wachtwoord met de gehashte `wachtwoord` kolom
   - De User model overschrijft `getAuthPassword()` zodat Laravel de juiste kolom gebruikt
6. **Bij succes:**
   - Sessie wordt geregenereerd (veiligheid)
   - Redirect naar `admin.dashboard`
7. **Bij mislukken:**
   - Redirect terug naar `/login`
   - Flash message: `error` = "Ongeldige inloggegevens"

### 8.5 Uitloggen

```php
Auth::logout();                    // Sessie leegmaken
$request->session()->invalidate(); // Sessie ongeldig maken
$request->session()->regenerateToken(); // CSRF token vernieuwen
return redirect()->route('home');
```

### 8.6 Gebruikers (voorgezaaid door seeder)

| Naam | Email | Wachtwoord | Rol |
|------|-------|-----------|-----|
| Beheerder 01 | beheerder01@campingbeheer.nl | password123 | admin |
| Frans de Boer | frans@campingbeheer.nl | password123 | admin |
| (kijkinteams) | kijkinteams@gmail.com | jonge | admin |

Er is **geen registratieformulier**. Nieuwe gebruikers moeten handmatig in de database worden toegevoegd.

---

## 9. Boekingsflow — Complete Use Case

Dit is de belangrijkste flow van de applicatie. Hieronder staat elke stap, van begin tot eind.

### Fase 1: Homepage bezoeken

```
Gebruiker → / (GET)
    ↓
routes/web.php: Route::get('/', function () { ... })
    ↓
Haalt alle Accommodations + Features uit DB
    ↓
Retourneert view('home', compact('accommodations', 'types', 'features', 'locale'))
    ↓
home.blade.php wordt gerenderd met layouts/app.blade.php
    ↓
Gebruiker ziet: filter paneel + accommodatiekaarten
```

**Wat gebeurt er op de homepage?**
- De filterknoppen werken **client-side** (JavaScript).
- Gebruiker kan filteren op: aantal personen, type verblijf, aankomst/vertrek data, kenmerken.
- De accommodatiekaarten worden getoond/verborgen op basis van de filters.
- Elke kaart toont: titel, beschrijving, personen (min-max), kenmerken, prijs per nacht, "Reserveer" knop.

### Fase 2: Naar reserveringspagina

```
Gebruiker klikt "Reserveer" → /reserveren (GET)
    ↓
routes/web.php: Route::get('/reserveren', [ReservationController::class, 'index'])
    ↓
ReservationController@index()
    - Haalt accommodaties + features op
    - Haalt postcode API key uit config
    - Bepaalt locale
    ↓
Retourneert view('reserveren', compact(...))
    ↓
reserveren.blade.php wordt gerenderd
    ↓
Gebruiker ziet: Leaflet kaart met markers + filters + legenda
```

**Wat gebeurt er op de reserveringspagina?**
- Een interactieve kaart (Leaflet) toont alle accommodaties met gekleurde markers.
- Groen = beschikbaar, Rood = niet beschikbaar.
- Polygonen (uit `data.geojson`) tonen de zones (Chalets, Blokhutten, etc.).
- Gebruiker klikt op een marker → detailkaart verschijnt met info + "Reserveer Nu" knop.

### Fase 3: Boekingsformulier invullen

```
Gebruiker klikt "Reserveer Nu" op een accommodatie
    ↓
Modal opent: partials/reserveer-modal.blade.php
    ↓
Formulier velden:
    - accommodatie_id (verborgen)
    - naam, email, telefoon
    - postcode + huisnummer + "Zoek" knop
    - straat, plaats, land (automatisch ingevuld via postcode API)
    - aankomst_datum, aankomst_tijd (standaard: middag)
    - vertrek_datum, vertrek_tijd (standaard: ochtend)
    - aantal_personen
    - opmerking (optioneel)
```

**Adreszoeker:** De postcode lookup gebruikt de Postcode.tech API. Als die faalt, valt het terug op PDOK, Nominatim of Zippopotam.

### Fase 4: Boeking indienen

```
Gebruiker klikt "Verstuur" → POST /reserveren (AJAX)
    ↓
routes/web.php: Route::post('/reserveren', [BookingController::class, 'store'])
    ↓
BookingController@store(Request $request)
    ↓
Stap 1: Validatie
    - Alle velden worden gevalideerd (required, dates, numeric, etc.)
    ↓
Stap 2: Capaciteitscheck
    - Checkt of aantal_personen <= accommodatie->max_personen
    - Checkt of aantal_personen >= accommodatie->min_personen
    ↓
Stap 3: Boeking aanmaken
    $booking = Booking::create([
        'accommodatie_id' => $request->accommodatie_id,
        'naam' => $request->naam,
        'email' => $request->email,
        // ... alle andere velden
        'status' => 'in_afwachting',
        'totaal_prijs' => $accommodatie->prijs_per_nacht,
    ]);
    ↓
Stap 4: Bevestigingsmail versturen
    Mail::to($booking->email)->send(new BookingConfirmation($booking));
    (omringd door try-catch, zodat mailproblemen de boeking niet breken)
    ↓
Stap 5: JSON response
    return response()->json([
        'success' => true,
        'message' => 'Uw reservering is succesvol ontvangen!'
    ]);
    ↓
Gebruiker ziet: JavaScript alert met succesbericht
```

### Fase 5: Admin keurt goed of keurt af

```
Admin logt in → /admin/dashboard
    ↓
Ziet tabel "Alle aanvragen" met status = in_afwachting
    ↓
Klikt op ✓ (goedkeuren) of ✗ (afkeuren)
```

**Goedkeuren:**
```
POST /admin/bookings/{booking}/approve
    ↓
BookingController@approve(Booking $booking)
    $booking->update(['status' => 'goedgekeurd']);
    Mail::to($booking->email)->send(new BookingApproved($booking));
    ↓
Gast ontvangt email: "Uw reservering is goedgekeurd!"
```

**Afkeuren (met reden):**
```
POST /admin/bookings/{booking}/reject
    ↓
BookingController@reject(Request $request, Booking $booking)
    $booking->update([
        'status' => 'geannuleerd',
        'opmerking' => $booking->opmerking . "\nAfkeurreden: " . $request->reden
    ]);
    Mail::to($booking->email)->send(new BookingRejected($booking));
    ↓
Gast ontvangt email: "Uw reservering is helaas afgekeurd"
```

---

## 10. Admin Functionaliteiten

### 10.1 Dashboard (`/admin/dashboard`)

Het dashboard is het startpunt voor beheerders. Het toont:

1. **"Zelf boeking toevoegen"** — Opent de reserveer-modal met gastzoekfunctie (admins kunnen boekingen namens gasten plaatsen)
2. **"Vandaag aankomst"** — Lijst van gasten die vandaag aankomen (status `goedgekeurd` of `gereed`)
3. **"Vandaag vertrek"** — Lijst van gasten die vandaag vertrekken (zelfde statussen)
4. **"Alle aanvragen"** — Genagigeerde tabel met alle `in_afwachting` boekingen. Elke rij heeft goedkeur/afkeur knoppen.

### 10.2 Accommodaties Beheren (CRUD)

Volledig CRUD-systeem onder `/admin/accommodations`:

| Actie | URL | Wat gebeurt er? |
|-------|-----|-----------------|
| Lijst zien | `GET /admin/accommodations` | Tabel met alle accommodaties |
| Nieuwe toevoegen | `GET/POST /admin/accommodations/create` | Formulier met kaart om locatie te kiezen |
| Bewerken | `GET/PUT /admin/accommodations/{id}/edit` | Zelfde formulier, vooringevuld |
| Verwijderen | `DELETE /admin/accommodations/{id}` | Verwijdert accommodatie + bijbehorende boekingen (cascade) |

Velden: titel, type, beschrijving, min/max personen, prijs per nacht, afbeelding, latitude/longitude (via kaartklik), status (beschikbaar/niet beschikbaar).

### 10.3 Planbord (`/admin/planning-board`)

Een weekkalender die een overzicht geeft van alle boekingen per accommodatie.

- **Rijen:** Accommodaties (filterbaar op type)
- **Kolommen:** 7 dagen van de week
- **Kleuren:**
  - Groen: vrij
  - Rood: bezet
  - Diagonaal (oranje/geel): check-in, check-out of wisseldag
- **Tooltip:** bij hover zie je gastnaam, data, personen, notitie
- **Navigatie:** vorige/volgende week, "Deze week" knop

### 10.4 Gastzoeken (`/admin/search-guests`)

AJAX endpoint dat door de reserveer-modal wordt gebruikt. Admins kunnen typen in een zoekveld → suggesties krijgen van eerdere gasten → gegevens worden automatisch ingevuld.

---

## 11. Database Schema

De database is SQLite (`database/database.sqlite`). Hieronder staan alle tabellen.

### 11.1 `users`

| Kolom | Type | Bijzonderheden |
|-------|------|----------------|
| `id` | bigint (auto) | Primary Key |
| `naam` | string(255) | |
| `email` | string(255) | Unique |
| `wachtwoord` | string(255) | Gehasht (i.p.v. `password`) |
| `rol` | enum('admin','gast') | |
| `aangemaakt_op` | timestamp (nullable) | CREATED_AT |
| `bewerkt_op` | timestamp (nullable) | UPDATED_AT |

### 11.2 `accommodaties`

| Kolom | Type | Bijzonderheden |
|-------|------|----------------|
| `id` | bigint (auto) | Primary Key |
| `titel` | string(255) | |
| `type` | string(255) | bv. Chalet, Blokhut, Safaritent |
| `beschrijving` | text | |
| `min_personen` | bigint | |
| `max_personen` | bigint | |
| `prijs_per_nacht` | decimal(10,2) | |
| `afbeelding` | string(255) | nullable, alleen bestandsnaam |
| `latitude` | decimal(10,7) | nullable |
| `longitude` | decimal(10,7) | nullable |
| `status` | enum('beschikbaar','niet_beschikbaar') | |
| `aangemaakt_op` | timestamp | useCurrent |
| `bewerkt_op` | timestamp | nullable |

**Extra kolommen voor vertalingen (in model gebruikt, mogelijk later toegevoegd):**
`type_en`, `type_de`, `type_fy`, `titel_en`, `titel_de`, `titel_fy`, `beschrijving_en`, `beschrijving_de`, `beschrijving_fy`

### 11.3 `kenmerken`

| Kolom | Type | Bijzonderheden |
|-------|------|----------------|
| `id` | bigint (auto) | Primary Key |
| `naam` | string(255) | |
| `naam_en` | string(255) | nullable |
| `naam_de` | string(255) | nullable |
| `naam_fy` | string(255) | nullable |
| `aangemaakt_op` | timestamp | |

### 11.4 `accommodatie_kenmerk` (pivot)

| Kolom | Type | Bijzonderheden |
|-------|------|----------------|
| `id` | bigint (auto) | Primary Key |
| `accommodatie_id` | bigint | FK → `accommodaties` (`nullOnDelete`) |
| `kenmerk_id` | bigint | FK → `kenmerken` (`cascadeOnDelete`) |

Dit is de tussen-tabel voor de many-to-many relatie.

### 11.5 `boekingen`

| Kolom | Type | Bijzonderheden |
|-------|------|----------------|
| `id` | bigint (auto) | Primary Key |
| `gebruiker_id` | bigint | nullable, FK → `users` (cascadeOnDelete) |
| `naam` | string(255) | nullable (gastnaam) |
| `email` | string(255) | nullable |
| `telefoon` | string(255) | nullable |
| `postcode` | string(255) | nullable |
| `huisnummer` | string(255) | nullable |
| `straat` | string(255) | nullable |
| `plaats` | string(255) | nullable |
| `land` | string(255) | nullable |
| `accommodatie_id` | bigint | FK → `accommodaties` (cascadeOnDelete) |
| `aankomst_datum` | date | |
| `aankomst_tijd` | string(255) | nullable, default: 'middag' |
| `vertrek_datum` | date | |
| `vertrek_tijd` | string(255) | nullable, default: 'ochtend' |
| `aantal_personen` | integer | |
| `opmerking` | text | nullable |
| `totaal_prijs` | decimal(10,2) | |
| `status` | string(255) | 'in_afwachting', 'goedgekeurd', 'geannuleerd', 'gereed' |
| `aangemaakt_op` | timestamp | |
| `bewerkt_op` | timestamp | nullable |

### 11.6 Overige tabellen (Laravel intern)

- `cache` — Cache opslag
- `cache_locks` — Cache locks
- `jobs`, `job_batches`, `failed_jobs` — Queue systeem

### 11.7 Migraties

Migraties zijn versiebeheer voor de database. Ze staan in `database/migrations/` en worden uitgevoerd met `php artisan migrate`.

| Migratie | Tabel |
|----------|-------|
| `0001_01_01_000000_create_users_table.php` | `users` |
| `0001_01_01_000001_create_cache_table.php` | `cache` |
| `0001_01_01_000002_create_jobs_table.php` | `jobs` |
| `2026_06_02_000003_create_accommodaties_table.php` | `accommodaties` |
| `2026_06_02_000004_create_kenmerken_table.php` | `kenmerken` |
| `2026_06_02_000005_create_accommondatie_kenmerk_table.php` | `accommodatie_kenmerk` |
| `2026_06_02_000006_create_boekingen_table.php` | `boekingen` |

### 11.8 Seeders (testdata)

Seeders vullen de database met voorbeelddata.

**`UserSeeder`** — Maakt 3 admin gebruikers aan (zie [paragraaf 8.6](#86-gebruikers-voorgezaaid-door-seeder))

**`AccommodationSeeder`** — Zaait een groot aantal accommodaties:
- 6 Camperplaatsen (€27,50–€42,50/nacht)
- 6 Vakantiehuizen (€85–€150/nacht)
- 18 Blokhutten (€47,50–€77,50/nacht)
- 6 Chalets (€110–€165/nacht)
- 14 Safaritenten (€35–€65/nacht)
- Alle met GPS-coördinaten

Uitvoeren: `php artisan db:seed`

---

## 12. Frontend

### 12.1 Vite + Tailwind CSS

- **Vite** is de asset bundler (geconfigureerd in `vite.config.js`)
- **Tailwind CSS** wordt gebruikt voor styling
- Commando's:
  - `npm run dev` — Ontwikkelserver met hot reload
  - `npm run build` — Productie build

### 12.2 Leaflet.js (Kaarten)

Leaflet wordt gebruikt voor interactieve kaarten op:
- `/reserveren` — Accommodaties bekijken op de kaart
- `/admin/accommodations/create` en `/edit` — Locatie kiezen door te klikken

Belangrijke bestanden:
- `data.geojson` — Polygonen voor accommodatiezones
- `app.js` — Leaflet initialisatie en kaartlogica

### 12.3 Internationalisatie (i18n)

**Achterkant (PHP/Blade):**
- De functie `getLocale()` leest een cookie uit (default: `nl`)
- Ondersteunde talen: Nederlands (`nl`), Engels (`en`), Duits (`de`), Fries (`fy`)
- Modellen hebben vertaalmethodes: `translatedTitle($locale)`, `translatedDescription($locale)`, etc.
- In Blade worden vertalingen inline opgeslagen in datat attributen (`data-i18n="..."`)

**Voorkant (JavaScript):**
- `resources/js/i18n.js` — Client-side vertaalsysteem
- Vertalingen in JSON: `resources/js/lang/nl.json`, `en.json`, `de.json`, `fy.json`
- Locale opgeslagen in `localStorage` + cookie
- Functie `window.__('key', params)` voor vertalingen in JavaScript
- Event `locale-changed` zorgt dat pagina's herladen bij taalwissel

### 12.4 Adreszoeker

In `resources/js/address.js`:
1. Gebruiker vult postcode + huisnummer in
2. Klikt op "Zoek" → AJAX call naar Postcode.tech API
3. Bij succes: straat, plaats, land worden automatisch ingevuld
4. Bij falen: fallback naar PDOK API → Nominatim → Zippopotam

---

## 13. Belangrijke Configuraties

### 13.1 `.env` (omgevingsvariabelen)

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587

POSTCODE_API_KEY=   # API key voor postcode.tech
```

### 13.2 Queue (wachtrij)

- Driver: `database` (gebruikt `jobs` tabel)
- Gebruikt voor e-mail verzending (Mailable classes implementen `ShouldQueue`)
- Commando: `php artisan queue:work`

### 13.3 Cache

- Driver: `database` (gebruikt `cache` tabel)

### 13.4 Session

- Driver: `file` (opgeslagen in `storage/framework/sessions`)

### 13.5 E-mail (Mailable)

Drie e-mail klassen in `app/Mail/`:

| Klasse | Verzonden bij... | Template |
|--------|------------------|----------|
| `BookingConfirmation` | Nieuwe boeking (gast) | `emails/booking-confirmation.blade.php` |
| `BookingApproved` | Goedkeuring door admin | `emails/booking-approved.blade.php` |
| `BookingRejected` | Afkeuring door admin | `emails/booking-rejected.blade.php` |

Alle mail wordt in een try-catch verstuurd, zodat een mailfout de boeking niet blokkeert.

---

## Snelle Referentie — Veelgebruikte Commando's

```bash
# Laravel development server starten
php artisan serve

# Frontend assets (development)
npm run dev

# Frontend assets (production)
npm run build

# Database migraties uitvoeren
php artisan migrate

# Database leegmaken + opnieuw migreren + seeden
php artisan migrate:fresh --seed

# Seeders uitvoeren
php artisan db:seed

# Queue worker starten (voor e-mail)
php artisan queue:work

# Nieuwe controller maken
php artisan make:controller NaamController

# Nieuwe model + migration maken
php artisan make:model Naam -m

# Alle routes bekijken
php artisan route:list
```

---

*Gemaakt door het development team. Laatste update: juni 2026.*
