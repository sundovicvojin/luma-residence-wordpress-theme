<?php
/**
 * Podešavanja teme: jedna stranica u administraciji, jedan niz u bazi.
 * Svi tekstovi, kontakt podaci i brojke menjaju se ovde, bez koda.
 *
 * @package luma-residence
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Definicija polja: grupa => [ ključ => [ labela, tip, podrazumevana vrednost, pomoć ] ].
 * Tipovi: text, textarea, url, email.
 *
 * @return array<string, array<string, array>>
 */
function luma_option_fields(): array {
	return array(
		'Kontakt i investitor'     => array(
			'investitor'  => array( 'Investitor', 'text', 'KESO INVEST doo' ),
			'adresa'      => array( 'Adresa investitora', 'text', 'Danila Kiša 14a, Novi Sad' ),
			'telefon_1'   => array( 'Telefon 1', 'text', '+381 21 30 10 250' ),
			'telefon_2'   => array( 'Telefon 2', 'text', '+381 64 657 9655' ),
			'email'       => array( 'Email za prikaz na sajtu', 'email', 'kesoinvestdoo@gmail.com' ),
			'forma_email' => array( 'Email na koji stižu poruke iz forme', 'email', 'kesoinvestdoo@gmail.com' ),
			'mapa_url'    => array( 'Link ka mapi (otvara se u novom tabu)', 'url', 'https://www.google.com/maps/search/?api=1&query=Obla%C4%8Di%C4%87a+Rada+i+Bo%C5%A1ka+Vrebalova+Novi+Sad' ),
			'infobook'    => array( 'Link ka infobook PDF-u (prazno = PDF iz teme)', 'url', get_theme_file_uri( 'assets/files/Luma-Residence-Infobook.pdf' ) ),
		),
		'Prodaja stanova (aplikacija)' => array(
			'app_url'    => array( 'Link ka aplikaciji za prodaju stanova', 'url', 'https://lumaapp.kesoinvest.com' ),
			'app_api'    => array( 'API aplikacije (spisak stanova sa statusima)', 'url', 'https://lumaresidenceapplication.vercel.app/api/apartments', 'Brojevi prodatih i slobodnih stanova se čitaju odavde (osvežavanje na 5 minuta).' ),
			'prodaja_tekst' => array( 'Opis uz brojeve prodaje', 'textarea', 'Raspored, osnove i dostupnost svakog stana nalaze se u aplikaciji za prodaju stanova.' ),
			'keso_url'   => array( 'Link ka sajtu investitora (stavka "Keso Invest" u meniju)', 'url', 'https://kesoinvest.com/' ),
		),
		'Projekat: brojke'         => array(
			'status'    => array( 'Status gradnje', 'text', 'U izgradnji' ),
			'rok'       => array( 'Završetak I faze (godina)', 'text', '2027', 'TODO: stranica prvog sprata na starom sajtu navodi 2026. Potvrditi godinu.' ),
			'stan_od'   => array( 'Najmanji stan (m²)', 'text', '27', 'TODO: stranica prvog sprata na starom sajtu navodi 35 do 110 m². Potvrditi raspon.' ),
			'stan_do'   => array( 'Najveći stan (m²)', 'text', '93' ),
			'posl_od'   => array( 'Najmanji poslovni prostor (m²)', 'text', '90' ),
			'posl_do'   => array( 'Najveći poslovni prostor (m²)', 'text', '560' ),
			'garaze'    => array( 'Broj garažnih mesta', 'text', '98' ),
		),
		'Projekat: tekstovi'       => array(
			'hero_naslov'    => array( 'Naslov na početnoj', 'text', 'Život kakav zaslužujete' ),
			'hero_podnaslov' => array( 'Podnaslov na početnoj (do 20 reči)', 'textarea', 'Stambeno-poslovni kompleks na uglu ulica Oblačića Rada i Boška Vrebalova u Novom Sadu.' ),
			'o_naslov'       => array( 'Naslov sekcije o projektu', 'text', 'Nova adresa kvaliteta, sigurnosti i mira' ),
			'o_tekst_1'      => array( 'Opis projekta, prvi pasus', 'textarea', 'Luma Residence je moderan stambeno-poslovni kompleks koji se gradi na uglu ulica Oblačića Rada i Boška Vrebalova. Osmišljen je kao mesto za kvalitetan život, sa funkcionalnim stanovima, modernim poslovnim prostorima, podzemnom garažom i okruženjem koje pruža mir, sigurnost i dobru povezanost sa gradom.' ),
			'o_tekst_2'      => array( 'Opis projekta, drugi pasus', 'textarea', 'Kompletan kompleks gradi se u dve faze.' ),
			'faza_1_naziv'   => array( 'I faza: lamele', 'text', 'Lamela G i Lamela A' ),
			'faza_1_status'  => array( 'I faza: status', 'text', 'Završetak do kraja 2027. godine.', 'TODO: godina se ovde unosi ručno, uskladiti sa poljem iznad.' ),
			'faza_2_naziv'   => array( 'II faza: lamela', 'text', 'Lamela B' ),
			'faza_2_status'  => array( 'II faza: status', 'text', 'U pripremi.' ),
			'poslovni_tekst' => array( 'Poslovni prostori, opis', 'textarea', 'Poslovni prostori u prizemlju dostupni su u površinama od 90 do 560 m², uz mogućnost prilagođavanja rasporeda potrebama budućeg zakupca ili vlasnika. Veliki stakleni portali i direktan pristup sa ulice čine ih pogodnim za maloprodaju, apoteke, ordinacije, kancelarije, kozmetičke i frizerske salone i druge uslužne delatnosti.' ),
			'garaza_tekst'   => array( 'Garaža, opis', 'textarea', 'Luma Residence ima ukupno 98 garažnih parking mesta na dve podzemne etaže. Parking mesta se mogu kupiti uz stan ili kao samostalna jedinica.' ),
			'blizina_naslov' => array( 'Naslov sekcije o lokaciji', 'text', 'Sve na nekoliko koraka od doma' ),
			'blizina'        => array( 'Blizina: jedan red po stavci, oblik "Naziv | udaljenost"', 'textarea', "Park za pse | 50 m\nProdavnica | 100 m\nIgralište | 100 m\nAutobuska stanica | 100 m\nVrtić | 400 m\nPolicijska stanica | 450 m" ),
			'infobook_tekst' => array( 'Infobook, opis', 'textarea', 'Ceo projekat, osnove i tehnički podaci u jednom PDF dokumentu.' ),
			'kontakt_naslov' => array( 'Naslov kontakt sekcije', 'text', 'Zakažite obilazak' ),
		),
		'Tvrdnje za potvrdu (prazno = ne prikazuje se)' => array(
			'tvrdnja_energija' => array( 'Tvrdnja o energetskoj efikasnosti', 'text', '', 'TODO: stari sajt navodi "A+ energetska efikasnost" samo na stranici prvog sprata. Upisati tek kad vlasnik potvrdi.' ),
			'tvrdnja_spratnost' => array( 'Tvrdnja o spratnosti', 'text', '', 'TODO: stari sajt navodi "4 sprata plus galerija". Upisati tek kad vlasnik potvrdi.' ),
		),
		'Optimizacija pretrage'    => array(
			'meta_opis' => array( 'Meta opis početne strane', 'textarea', 'Luma Residence u Novom Sadu: stanovi od 27 do 93 m², poslovni prostori i garaže. Stambeno-poslovni kompleks u izgradnji, investitor KESO INVEST.' ),
		),
	);
}

/**
 * Vrednost jednog podešavanja (sa podrazumevanom vrednošću iz definicije).
 */
function luma_opt( string $key ): string {
	static $saved = null;
	static $defaults = null;
	if ( null === $saved ) {
		$saved = get_option( 'luma_options', array() );
		$saved = is_array( $saved ) ? $saved : array();
	}
	if ( null === $defaults ) {
		$defaults = array();
		foreach ( luma_option_fields() as $fields ) {
			foreach ( $fields as $k => $def ) {
				$defaults[ $k ] = $def[2];
			}
		}
	}
	if ( isset( $saved[ $key ] ) && '' !== $saved[ $key ] ) {
		return (string) $saved[ $key ];
	}
	// Tvrdnje se namerno čuvaju prazne, ostalo pada na podrazumevano.
	if ( isset( $saved[ $key ] ) && 0 === strpos( $key, 'tvrdnja_' ) ) {
		return '';
	}
	return $defaults[ $key ] ?? '';
}

/**
 * Telefon u obliku pogodnom za tel: link.
 */
function luma_tel( string $phone ): string {
	return 'tel:' . preg_replace( '/[^0-9+]/', '', $phone );
}

add_action(
	'admin_menu',
	static function (): void {
		add_menu_page(
			'Luma Residence',
			'Luma Residence',
			'manage_options',
			'luma-podesavanja',
			'luma_render_options_page',
			'dashicons-building',
			58
		);
	}
);

add_action(
	'admin_init',
	static function (): void {
		register_setting(
			'luma_options_group',
			'luma_options',
			array(
				'type'              => 'array',
				'sanitize_callback' => 'luma_sanitize_options',
				'default'           => array(),
			)
		);
	}
);

/**
 * Sanitizacija na ulazu, po tipu polja.
 *
 * @param mixed $input Ulaz iz forme.
 */
function luma_sanitize_options( $input ): array {
	$out   = array();
	$input = is_array( $input ) ? $input : array();
	foreach ( luma_option_fields() as $fields ) {
		foreach ( $fields as $key => $def ) {
			$raw = isset( $input[ $key ] ) ? wp_unslash( $input[ $key ] ) : '';
			switch ( $def[1] ) {
				case 'url':
					$out[ $key ] = esc_url_raw( trim( (string) $raw ) );
					break;
				case 'email':
					$out[ $key ] = sanitize_email( (string) $raw );
					break;
				case 'textarea':
					$out[ $key ] = sanitize_textarea_field( (string) $raw );
					break;
				default:
					$out[ $key ] = sanitize_text_field( (string) $raw );
			}
		}
	}
	return $out;
}

/**
 * Prikaz stranice podešavanja.
 */
function luma_render_options_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	echo '<div class="wrap"><h1>Luma Residence: podešavanja</h1>';
	settings_errors();
	echo '<p>Ovde se menjaju tekstovi, kontakt podaci i brojke na sajtu. Stanovi se prodaju kroz aplikaciju, a brojke prodatih i slobodnih stanova se čitaju iz nje automatski.</p>';
	echo '<form method="post" action="options.php">';
	settings_fields( 'luma_options_group' );
	foreach ( luma_option_fields() as $group => $fields ) {
		echo '<h2>' . esc_html( $group ) . '</h2><table class="form-table" role="presentation">';
		foreach ( $fields as $key => $def ) {
			$id    = 'luma_' . $key;
			$name  = 'luma_options[' . $key . ']';
			$value = luma_opt( $key );
			echo '<tr><th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $def[0] ) . '</label></th><td>';
			if ( 'textarea' === $def[1] ) {
				echo '<textarea class="large-text" rows="4" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '">' . esc_textarea( $value ) . '</textarea>';
			} else {
				$type = in_array( $def[1], array( 'url', 'email' ), true ) ? $def[1] : 'text';
				echo '<input class="large-text" type="' . esc_attr( $type ) . '" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '">';
			}
			if ( ! empty( $def[3] ) ) {
				echo '<p class="description">' . esc_html( $def[3] ) . '</p>';
			}
			echo '</td></tr>';
		}
		echo '</table>';
	}
	submit_button();
	echo '</form></div>';
}
