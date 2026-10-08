<?php
/**
 * Kontakt forma bez plugina: admin-post obrada, CPT "upit" kao rezervna kopija, zaštita od spama.
 *
 * Zaštite: nonce, honeypot, potpisan vremenski žig, ograničenje po IP adresi, sanitizacija.
 * Nonce i vremenski žig se pri učitavanju stranice osvežavaju malim fetch-om (vidi luma_contact_script),
 * pa forma radi i na keširanoj stranici. Bez JS-a važe vrednosti upisane u HTML.
 *
 * @package luma-residence
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const LUMA_FORM_MIN_AGE  = 3;      // Sekunde: brže od ovoga je bot.
const LUMA_FORM_MAX_AGE  = 86400;  // Sekunde: žig stariji od dana je istekao.
const LUMA_FORM_RATE_MAX = 5;      // Najviše upita po IP adresi na sat.

/**
 * CPT "upit": rezervna kopija svake poruke. Vidljiv samo u administraciji.
 */
function luma_register_upit(): void {
	register_post_type(
		'upit',
		array(
			'labels'              => array(
				'name'          => __( 'Upiti', 'luma-residence' ),
				'singular_name' => __( 'Upit', 'luma-residence' ),
				'menu_name'     => __( 'Upiti', 'luma-residence' ),
				'all_items'     => __( 'Svi upiti', 'luma-residence' ),
				'edit_item'     => __( 'Pogledaj upit', 'luma-residence' ),
				'not_found'     => __( 'Nema upita.', 'luma-residence' ),
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => false,
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => false,
			'menu_icon'           => 'dashicons-email-alt',
			'menu_position'       => 59,
			'supports'            => array( 'title', 'editor' ),
			'capability_type'     => 'post',
		)
	);
}
add_action( 'init', 'luma_register_upit' );

/**
 * Kolone u listi upita: email i datum.
 *
 * @param array $columns Podrazumevane kolone.
 */
function luma_upit_columns( array $columns ): array {
	return array(
		'cb'         => $columns['cb'] ?? '',
		'title'      => __( 'Ime i prezime', 'luma-residence' ),
		'luma_email' => __( 'Email', 'luma-residence' ),
		'date'       => __( 'Datum', 'luma-residence' ),
	);
}
add_filter( 'manage_upit_posts_columns', 'luma_upit_columns' );

/**
 * Sadržaj kolone email.
 *
 * @param string $column  Ključ kolone.
 * @param int    $post_id ID upita.
 */
function luma_upit_column_content( string $column, int $post_id ): void {
	if ( 'luma_email' !== $column ) {
		return;
	}
	$email = (string) get_post_meta( $post_id, '_luma_email', true );
	if ( is_email( $email ) ) {
		printf( '<a href="mailto:%1$s">%1$s</a>', esc_attr( $email ) );
	}
}
add_action( 'manage_upit_posts_custom_column', 'luma_upit_column_content', 10, 2 );

/**
 * Svež nonce i potpisan vremenski žig.
 *
 * @return array{n: string, t: string}
 */
function luma_form_tokens(): array {
	$t = (string) time();
	return array(
		'n' => wp_create_nonce( 'luma_contact' ),
		't' => $t . '.' . wp_hash( 'luma_form|' . $t ),
	);
}

/**
 * Provera potpisanog žiga: ispravan potpis i starost u dozvoljenom opsegu.
 *
 * @param string $token Vrednost iz skrivenog polja.
 */
function luma_form_time_ok( string $token ): bool {
	$parts = explode( '.', $token );
	if ( 2 !== count( $parts ) || ! ctype_digit( $parts[0] ) || ! hash_equals( wp_hash( 'luma_form|' . $parts[0] ), $parts[1] ) ) {
		return false;
	}
	$age = time() - (int) $parts[0];
	return $age >= LUMA_FORM_MIN_AGE && $age <= LUMA_FORM_MAX_AGE;
}

/**
 * Adresa stranice na kojoj se forma nalazi, bez rezultata prethodnog slanja.
 */
function luma_form_return_url(): string {
	$url = is_singular() ? get_permalink() : home_url( '/' );
	return remove_query_arg( array( 'poslato', 'k' ), (string) $url );
}

/**
 * Preusmerava nazad na stranicu sa rezultatom i sidrom #kontakt.
 *
 * @param string $back  Adresa stranice.
 * @param bool   $ok    Da li je slanje uspelo.
 * @param string $key   Ključ privremenog zapisa sa greškama i unetim poljima.
 */
function luma_form_redirect( string $back, bool $ok, string $key = '' ): void {
	$back = wp_validate_redirect( $back, home_url( '/' ) );
	$args = array( 'poslato' => $ok ? '1' : '0' );
	if ( '' !== $key ) {
		$args['k'] = $key;
	}
	wp_safe_redirect( add_query_arg( $args, remove_query_arg( array( 'poslato', 'k' ), $back ) ) . '#kontakt' );
	exit;
}

/**
 * Greška: unete vrednosti i poruke čuvaju se 10 minuta u transientu (bez PHP sesija).
 *
 * @param string $back   Adresa stranice.
 * @param array  $errors Poruke po poljima.
 * @param array  $values Unete vrednosti.
 */
function luma_form_fail( string $back, array $errors, array $values ): void {
	$key = wp_generate_password( 16, false );
	set_transient( 'luma_f_' . $key, compact( 'errors', 'values' ), 10 * MINUTE_IN_SECONDS );
	luma_form_redirect( $back, false, $key );
}

/**
 * Obrada slanja (admin-post.php, za posetioce i prijavljene korisnike).
 */
function luma_contact_submit(): void {
	$back = isset( $_POST['luma_back'] ) ? esc_url_raw( wp_unslash( $_POST['luma_back'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing

	$values = array(
		'ime'    => str_replace( array( "\r", "\n" ), '', sanitize_text_field( wp_unslash( $_POST['ime'] ?? '' ) ) ), // phpcs:ignore WordPress.Security.NonceVerification.Missing
		'email'  => str_replace( array( "\r", "\n" ), '', sanitize_email( wp_unslash( $_POST['email'] ?? '' ) ) ), // phpcs:ignore WordPress.Security.NonceVerification.Missing
		'poruka' => sanitize_textarea_field( wp_unslash( $_POST['poruka'] ?? '' ) ), // phpcs:ignore WordPress.Security.NonceVerification.Missing
	);

	$nonce = isset( $_POST['luma_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['luma_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'luma_contact' ) ) {
		luma_form_fail( $back, array( 'forma' => __( 'Sigurnosna provera nije prošla. Osvežite stranicu i pokušajte ponovo.', 'luma-residence' ) ), $values );
	}

	// Honeypot: botu se vraća "uspeh", a ništa se ne čuva ni šalje.
	if ( ! empty( $_POST['website'] ) ) {
		luma_form_redirect( $back, true );
	}

	$errors = array();
	$token  = isset( $_POST['luma_t'] ) ? sanitize_text_field( wp_unslash( $_POST['luma_t'] ) ) : '';
	if ( ! luma_form_time_ok( $token ) ) {
		$errors['forma'] = __( 'Forma je poslata prebrzo ili je istekla. Pokušajte ponovo.', 'luma-residence' );
	}
	if ( mb_strlen( $values['ime'] ) < 2 ) {
		$errors['ime'] = __( 'Upišite ime i prezime.', 'luma-residence' );
	}
	if ( ! is_email( $values['email'] ) ) {
		$errors['email'] = __( 'Upišite ispravnu email adresu.', 'luma-residence' );
	}
	if ( mb_strlen( $values['poruka'] ) < 5 ) {
		$errors['poruka'] = __( 'Upišite poruku.', 'luma-residence' );
	} elseif ( mb_strlen( $values['poruka'] ) > 5000 ) {
		$errors['poruka'] = __( 'Poruka je predugačka (najviše 5000 znakova).', 'luma-residence' );
	}
	if ( empty( $_POST['saglasnost'] ) ) {
		$errors['saglasnost'] = __( 'Potrebna je saglasnost za obradu podataka.', 'luma-residence' );
	}

	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key = 'luma_rl_' . md5( $ip );
	$cnt = (int) get_transient( $key );
	if ( $cnt >= LUMA_FORM_RATE_MAX ) {
		/* translators: %s: broj telefona. */
		$errors['forma'] = sprintf( __( 'Poslato je previše poruka. Pokušajte kasnije ili nas pozovite na %s.', 'luma-residence' ), luma_opt( 'telefon_1' ) );
	}

	if ( $errors ) {
		luma_form_fail( $back, $errors, $values );
	}

	set_transient( $key, $cnt + 1, HOUR_IN_SECONDS );

	// Rezervna kopija pre slanja: upit se ne gubi ako mejl ne ode.
	$post_id = wp_insert_post(
		wp_slash(
			array(
				'post_type'    => 'upit',
				'post_status'  => 'publish',
				'post_title'   => $values['ime'],
				'post_content' => $values['poruka'],
				'meta_input'   => array(
					'_luma_email' => $values['email'],
					'_luma_mail'  => 'cekanje',
				),
			)
		)
	);

	$to = luma_opt( 'forma_email' );
	$to = is_email( $to ) ? $to : 'kesoinvestdoo@gmail.com';

	/* translators: %s: ime posetioca. */
	$subject = sprintf( __( 'Novi upit sa sajta – %s', 'luma-residence' ), $values['ime'] );
	$body    = sprintf( "Ime i prezime: %s\nEmail: %s\n\nPoruka:\n%s\n", $values['ime'], $values['email'], $values['poruka'] );
	$reply   = trim( str_replace( array( '"', '<', '>', ',', ';' ), '', $values['ime'] ) );
	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $reply . ' <' . $values['email'] . '>',
	);

	$sent = wp_mail( $to, $subject, $body, $headers );
	if ( $post_id && ! is_wp_error( $post_id ) ) {
		update_post_meta( $post_id, '_luma_mail', $sent ? 'poslato' : 'neuspelo' );
	}

	if ( $sent ) {
		luma_form_redirect( $back, true );
	}
	/* translators: %s: broj telefona. */
	luma_form_fail( $back, array( 'forma' => sprintf( __( 'Poruku trenutno nije moguće poslati. Pozovite nas na %s.', 'luma-residence' ), luma_opt( 'telefon_1' ) ) ), $values );
}
add_action( 'admin_post_nopriv_luma_contact', 'luma_contact_submit' );
add_action( 'admin_post_luma_contact', 'luma_contact_submit' );

/**
 * Sveži nonce i žig za keširane stranice (JSON, bez keširanja).
 */
function luma_contact_tokens(): void {
	nocache_headers();
	wp_send_json( luma_form_tokens() );
}
add_action( 'admin_post_nopriv_luma_tokens', 'luma_contact_tokens' );
add_action( 'admin_post_luma_tokens', 'luma_contact_tokens' );

/**
 * Ispisuje formu. $id prefiks sprečava dupliran id ako se forma pojavi dvaput.
 */
function luma_contact_form( string $id = 'forma' ): void {
	$errors = array();
	$v      = array(
		'ime'    => '',
		'email'  => '',
		'poruka' => '',
	);
	$sent   = false;
	$failed = false;

	// Samo čitanje rezultata prethodnog slanja; ništa se ne menja.
	if ( isset( $_GET['poslato'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$sent   = '1' === $_GET['poslato']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$failed = ! $sent;
		$k      = isset( $_GET['k'] ) ? preg_replace( '/[^A-Za-z0-9]/', '', wp_unslash( $_GET['k'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$saved  = $k ? get_transient( 'luma_f_' . $k ) : false;
		if ( is_array( $saved ) ) {
			$errors = (array) ( $saved['errors'] ?? array() );
			$v      = array_merge( $v, (array) ( $saved['values'] ?? array() ) );
		}
	}

	$tokens = luma_form_tokens();

	echo '<form class="form" id="kontakt-forma" method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" data-tokens="' . esc_url( admin_url( 'admin-post.php?action=luma_tokens' ) ) . '" novalidate>';

	echo '<div class="form__status" role="status" aria-live="polite">';
	if ( $sent ) {
		echo '<p class="form__ok">' . esc_html__( 'Hvala, poruka je poslata. Javićemo vam se uskoro.', 'luma-residence' ) . '</p>';
	} elseif ( ! empty( $errors['forma'] ) ) {
		echo '<p class="form__err" id="' . esc_attr( $id ) . '-err-forma">' . esc_html( $errors['forma'] ) . '</p>';
	} elseif ( $failed ) {
		echo '<p class="form__err">' . esc_html__( 'Proverite označena polja.', 'luma-residence' ) . '</p>';
	}
	echo '</div>';

	$field = static function ( string $name, string $label, string $type, string $auto ) use ( $errors, $v, $id ): void {
		$fid  = $id . '-' . $name;
		$err  = $errors[ $name ] ?? '';
		$desc = $err ? ' aria-describedby="' . esc_attr( $fid ) . '-err" aria-invalid="true"' : '';
		echo '<div class="field' . ( $err ? ' has-error' : '' ) . '"><label for="' . esc_attr( $fid ) . '">' . esc_html( $label ) . '</label>';
		if ( 'textarea' === $type ) {
			echo '<textarea id="' . esc_attr( $fid ) . '" name="' . esc_attr( $name ) . '" rows="6" maxlength="5000" required' . $desc . '>' . esc_textarea( $v[ $name ] ) . '</textarea>'; // phpcs:ignore WordPress.Security.EscapeOutput
		} else {
			echo '<input id="' . esc_attr( $fid ) . '" name="' . esc_attr( $name ) . '" type="' . esc_attr( $type ) . '" value="' . esc_attr( $v[ $name ] ) . '" autocomplete="' . esc_attr( $auto ) . '" required' . $desc . '>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		if ( $err ) {
			echo '<p class="field__err" id="' . esc_attr( $fid ) . '-err">' . esc_html( $err ) . '</p>';
		}
		echo '</div>';
	};

	$field( 'ime', __( 'Ime i prezime', 'luma-residence' ), 'text', 'name' );
	$field( 'email', __( 'Email', 'luma-residence' ), 'email', 'email' );
	$field( 'poruka', __( 'Poruka', 'luma-residence' ), 'textarea', 'off' );

	$cerr = $errors['saglasnost'] ?? '';
	echo '<div class="field field--check' . ( $cerr ? ' has-error' : '' ) . '"><label for="' . esc_attr( $id ) . '-saglasnost"><input id="' . esc_attr( $id ) . '-saglasnost" name="saglasnost" type="checkbox" value="1" required' . ( $cerr ? ' aria-describedby="' . esc_attr( $id ) . '-saglasnost-err" aria-invalid="true"' : '' ) . '> ' . esc_html__( 'Saglasan/saglasna sam da se uneti podaci obrade radi odgovora na moj upit.', 'luma-residence' ) . '</label>'; // phpcs:ignore WordPress.Security.EscapeOutput
	if ( $cerr ) {
		echo '<p class="field__err" id="' . esc_attr( $id ) . '-saglasnost-err">' . esc_html( $cerr ) . '</p>';
	}
	echo '</div>';

	echo '<div class="hp" aria-hidden="true"><label for="' . esc_attr( $id ) . '-website">' . esc_html__( 'Ne popunjavajte ovo polje', 'luma-residence' ) . '</label><input id="' . esc_attr( $id ) . '-website" name="website" type="text" tabindex="-1" autocomplete="off"></div>';
	echo '<input type="hidden" name="action" value="luma_contact">';
	echo '<input type="hidden" name="luma_back" value="' . esc_url( luma_form_return_url() ) . '">';
	wp_nonce_field( 'luma_contact', 'luma_nonce', false );
	echo '<input type="hidden" name="luma_t" value="' . esc_attr( $tokens['t'] ) . '">';
	echo '<button class="btn btn--primary" type="submit" data-busy="' . esc_attr__( 'Šaljem…', 'luma-residence' ) . '">' . esc_html__( 'Pošalji poruku', 'luma-residence' ) . '</button>';
	echo '</form>';

	// Mali skript (bez biblioteka): sveži tokeni za keširane stranice i zaštita od duplog klika.
	echo '<script>(function(){var f=document.getElementById("kontakt-forma");if(!f)return;var b=f.querySelector("button"),l=b.textContent;'
		. 'fetch(f.dataset.tokens,{credentials:"same-origin",cache:"no-store"}).then(function(r){return r.json()}).then(function(d){f.elements.luma_nonce.value=d.n;f.elements.luma_t.value=d.t}).catch(function(){});'
		. 'f.addEventListener("submit",function(){b.disabled=true;b.textContent=b.dataset.busy});'
		. 'addEventListener("pageshow",function(){b.disabled=false;b.textContent=l})})()</script>';
}
