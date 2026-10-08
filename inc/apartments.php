<?php
/**
 * Brojke o stanovima iz aplikacije za prodaju (server čita API, rezultat se kešira).
 *
 * @package luma-residence
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Broj slobodnih, rezervisanih i prodatih stanova.
 *
 * @return array{available:int,reserved:int,sold:int,total:int}|null Null ako podaci nisu dostupni.
 */
function luma_apartment_stats(): ?array {
	$cached = get_transient( 'luma_apt_stats' );
	if ( is_array( $cached ) && isset( $cached['total'] ) ) {
		return $cached;
	}

	$last = get_option( 'luma_apt_stats_last' );
	$last = ( is_array( $last ) && isset( $last['total'] ) ) ? $last : null;

	// Posle greške ne pokušavamo ponovo narednih 60 sekundi; prikazuje se poslednja poznata vrednost.
	if ( get_transient( 'luma_apt_fail' ) ) {
		return $last;
	}

	$stats = null;
	$url   = luma_opt( 'app_api' );
	if ( '' !== $url ) {
		$res = wp_safe_remote_get(
			$url,
			array(
				'timeout' => 5,
				'headers' => array( 'Accept' => 'application/json' ),
			)
		);
		if ( ! is_wp_error( $res ) && 200 === (int) wp_remote_retrieve_response_code( $res ) ) {
			$data = json_decode( wp_remote_retrieve_body( $res ), true );
			if ( is_array( $data ) ) {
				$count = array(
					'available' => 0,
					'reserved'  => 0,
					'sold'      => 0,
				);
				foreach ( $data as $row ) {
					$status = ( is_array( $row ) && isset( $row['status'] ) ) ? (string) $row['status'] : '';
					if ( isset( $count[ $status ] ) ) {
						++$count[ $status ];
					}
				}
				$total = array_sum( $count );
				if ( $total > 0 ) {
					$stats = $count + array( 'total' => $total );
				}
			}
		}
	}

	if ( $stats ) {
		set_transient( 'luma_apt_stats', $stats, 5 * MINUTE_IN_SECONDS );
		update_option( 'luma_apt_stats_last', $stats, false );
		return $stats;
	}

	set_transient( 'luma_apt_fail', 1, MINUTE_IN_SECONDS );
	return $last;
}
