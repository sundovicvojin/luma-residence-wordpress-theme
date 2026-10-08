# Luma Residence, WordPress tema

Sajt stambeno-poslovnog kompleksa Luma Residence (investitor KESO INVEST doo, Novi Sad). Radi na čistoj instalaciji WordPress-a, bez plugina i page buildera. PHP 8.1+.

## Instalacija

1. Tema je u `wp-content/themes/luma-residence/`. Aktivirajte je u Izgled > Teme.
2. Pri aktivaciji tema sama pravi stranice (Početna, O nama, Prodaja stanova, Kontakt), podešava Početnu kao naslovnu stranu i uključuje lepe adrese (`/%postname%/`). Radi se jednom po verziji teme.
3. Slanje poruka iz forme koristi `wp_mail`. Na hostingu proverite da pošta stiže (po potrebi SMTP na nivou servera).

## Sajt

Meni: **Početna, O nama, Prodaja stanova, Keso Invest** (poslednja stavka vodi na sajt investitora, otvara se u novom tabu). Kontakt je dostupan preko dugmeta "Pošaljite upit" u zaglavlju i u podnožju. Meni se može menjati u Izgled > Meniji (lokacija "Glavni meni"); bez menija prikazuje se osnovni.

Stranica **Prodaja stanova** (i istoimena sekcija na početnoj) prikazuje samo koliko je stanova slobodno i koliko prodato, plus dugme "Pogledaj stanove" koje vodi na aplikaciju za prodaju: `https://lumaapp.kesoinvest.com`. Rezervisani stanovi se prikazuju samo ako ih ima.

Brojke se čitaju sa servera iz API-ja aplikacije (`/api/apartments`, JSON lista stanova sa poljem `status`: `available`, `reserved`, `sold`). Rezultat se kešira 5 minuta. Ako API ne odgovori, prikazuje se poslednja poznata vrednost, a ako je nema, samo poruka i dugme. Adrese aplikacije i API-ja menjaju se u podešavanjima.

## Kako se menja sadržaj

Sve u meniju administracije **Luma Residence** (jedna stranica sa poljima): kontakt, adresa, telefoni, email za poruke iz forme, linkovi (aplikacija, API, infobook, mapa, Keso Invest), brojke (m², garaže, rok) i tekstovi. Tekst i naslov stranica ne treba menjati u editoru.

## Struktura

```
inc/options.php       podešavanja (registar polja i podrazumevanih vrednosti)
inc/apartments.php    brojke prodato/slobodno iz API-ja aplikacije (keš)
inc/kontakt.php       kontakt forma (admin-post, nonce, honeypot, vreme, IP limit) i CPT "upit"
inc/mail.php          SMTP preko konstanti iz wp-config.php, log grešaka
inc/seed.php          početne stranice pri aktivaciji
inc/seo.php           naslovi, meta opis, Open Graph
inc/enqueue.php       CSS i JS, preload fonta i LCP slike
inc/images.php        <picture> sa AVIF/WebP/JPEG iz assets/img/manifest.json
template-parts/       sale (prodaja), phases, location, contact, wordmark
assets/js/core.js     nivo 0 i 1
assets/js/motion.js   nivo 2 (GSAP, ScrollTrigger, Lenis)
assets/js/vendor/     GSAP, ScrollTrigger i Lenis, lokalne kopije
```

Prefiks `luma_`, text domain `luma-residence`. Tekstovi u šablonima su srpski (jedan jezik, bez prevoda).

## Animacije

- **Nivo 0:** sav sadržaj vidljiv bez JS-a i bez animacija.
- **Nivo 1:** pojavljivanje elemenata (fade i pomak do 24 px), brojači, stanje zaglavlja (`IntersectionObserver`).
- **Nivo 2** (samo laptop/desktop sa jakim hardverom, bez reduced motion i saveData): Lenis glatki skrol vezan za GSAP ticker i ScrollTrigger, naslov početne reč po reč, maskirani naslovi sekcija, otkrivanje slika (clip-path), parallax, iscrtavanje linija i jedna pinovana sekcija (Prodaja stanova na početnoj: render se približava dok traje pin). Biblioteke se učitavaju tek posle provere uređaja. Čuvač performansi posle ~45 frejmova vraća na nivo 1 ako je prosek preko 24 ms, isto i pri širini ispod 1024 px (tada se gasi i Lenis).

## Pretpostavke

- Dizajn prati `SKILL.md` iz foldera teme (hladna paleta sa kobalt plavom `#233b79`, font Geist, oštri uglovi, svetla i tamna šema prema uređaju).
- Logotip: `plavi.png` sa starog sajta je znak "K" firme KESO, ne logotip Luma Residence, pa je napravljen tekstualni SVG wordmark.
- Mapa je statična slika sa starog sajta i link ka Google mapi, bez iframe-a.
- Infobook (PDF, oko 85 MB) ostaje na starom sajtu i otvara se linkom.
- Stranice spratova, osnove i tip sadržaja "Stan" uklonjeni su; sve o stanovima je u aplikaciji.
- "O nama" sadrži samo činjenice iz izvora: faze izgradnje, lokaciju i investitora.

## TODO za vlasnika (nesaglasnosti u izvoru)

1. **Rok završetka:** Luma stranica kaže kraj 2027, stari sajt na jednoj stranici kraj 2026. Koristi se **2027** (polja `rok` i `faza_1_status`, uskladiti oba).
2. **Raspon površina:** Luma stranica kaže 27 do 93 m², stari sajt na jednoj stranici 35 do 110 m². Koristi se **27 do 93 m²**.
3. Tabela "A1 do A6 po 58 m²", "L1 do L4" i zbirovi tipova (2+7+3+3) ne poklapaju se sa stanovima i **ne prikazuju se**.
4. "A+ energetska efikasnost" i "4 sprata plus galerija" prikazuju se samo ako se upišu u podešavanjima ("Tvrdnje za potvrdu"). Podrazumevano su prazne.
5. Treći telefon (+381 21 234 56 78) je placeholder i **izostavljen je**.
6. Slike sa starog sajta: `h4_BG_1` (žena u prodavnici) nije prikladna za garažu, a `4.png`, `4-2.png` i `4213213.png` su fotografije porodica, ne 3D prikazi. Treba ih zameniti pravim renderima. Neke fotografije nisu iskorišćene.
7. Ako postoji logotip Luma Residence u vektoru, zameniti wordmark u `template-parts/wordmark.php`.

## Stare stranice

Tema ne sadrži stare šablonske stranice (about-us, amenities, apartment, availability, blog, building, faqs, gallery, icons, neighborhood, residences, sample-page). Mogu se obrisati u administraciji. Stare adrese `/prvi-sprat/`, `/drugi-sprat/`, `/treci-sprat/`, `/cetvrti-sprat/` i `/luma-residence/` više ne postoje; po potrebi ih preusmerite na `/prodaja-stanova/` ili početnu na nivou servera.

## Licence

GSAP 3.15 (standardna GSAP licenca), Lenis 1.3 (MIT), font Geist (SIL OFL).
