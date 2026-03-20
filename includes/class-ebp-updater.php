<?php
/**
 * GitHub-based auto-updater for Eifelhoster Buttons Pro.
 *
 * Hooks into the WordPress plugin-update mechanism and checks the GitHub
 * Releases API for new versions.  When a newer release is found WordPress
 * will show the standard "update available" notice and will download + install
 * the ZIP from GitHub – just like a wordpress.org update, but fully replacing
 * the existing installation (no parallel copy).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EBP_Updater {

	/** GitHub repository owner/name. */
	const GITHUB_REPO = 'Eifelhoster/ButtonPro';

	/** WordPress plugin slug (folder/file). */
	const PLUGIN_SLUG = 'eifelhoster-buttons-pro/eifelhoster-buttons-pro.php';

	/** Transient key for caching the remote version info. */
	const TRANSIENT_KEY = 'ebp_github_update_info';

	/** How long to cache the remote check (12 hours). */
	const CACHE_SECONDS = 43200;

	public function __construct() {
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_update' ) );
		add_filter( 'plugins_api',                           array( $this, 'plugins_api_info' ), 10, 3 );
		add_filter( 'upgrader_source_selection',             array( $this, 'fix_source_dir' ),   10, 4 );
	}

	// -------------------------------------------------------------------------
	// Inject update info into the WordPress update transient.
	// -------------------------------------------------------------------------
	public function check_for_update( $transient ) {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		$release = $this->get_latest_release();
		if ( ! $release ) {
			return $transient;
		}

		$remote_version = ltrim( $release['tag_name'], 'v' );
		$current        = EBP_VERSION;

		if ( version_compare( $remote_version, $current, '>' ) ) {
			$transient->response[ self::PLUGIN_SLUG ] = (object) array(
				'slug'        => dirname( self::PLUGIN_SLUG ),
				'plugin'      => self::PLUGIN_SLUG,
				'new_version' => $remote_version,
				'url'         => $release['html_url'],
				'package'     => $release['zipball_url'],
			);
		} else {
			// No update needed – tell WordPress the plugin is up-to-date.
			$transient->no_update[ self::PLUGIN_SLUG ] = (object) array(
				'slug'        => dirname( self::PLUGIN_SLUG ),
				'plugin'      => self::PLUGIN_SLUG,
				'new_version' => $current,
				'url'         => 'https://eifelhoster.de',
				'package'     => '',
			);
		}

		return $transient;
	}

	// -------------------------------------------------------------------------
	// Provide plugin info for the "View details" popup.
	// -------------------------------------------------------------------------
	public function plugins_api_info( $result, $action, $args ) {
		if ( $action !== 'plugin_information' ) {
			return $result;
		}
		if ( ! isset( $args->slug ) || $args->slug !== dirname( self::PLUGIN_SLUG ) ) {
			return $result;
		}

		$release = $this->get_latest_release();
		if ( ! $release ) {
			return $result;
		}

		$remote_version = ltrim( $release['tag_name'], 'v' );

		return (object) array(
			'name'          => 'Eifelhoster Buttons Pro',
			'slug'          => dirname( self::PLUGIN_SLUG ),
			'version'       => $remote_version,
			'author'        => '<a href="https://eifelhoster.de">Michael Krämer</a>',
			'homepage'      => 'https://eifelhoster.de',
			'download_link' => $release['zipball_url'],
			'sections'      => array(
				'description' => 'Eifelhoster Buttons Pro – grafisch gestaltete Buttons für WordPress.',
				'changelog'   => nl2br( esc_html( $release['body'] ?? '' ) ),
			),
		);
	}

	// -------------------------------------------------------------------------
	// Rename the extracted directory to match the expected plugin slug.
	//
	// GitHub ZIPs unpack to a directory like "Eifelhoster-ButtonPro-<sha>".
	// WordPress expects "eifelhoster-buttons-pro".  This filter renames it.
	// -------------------------------------------------------------------------
	public function fix_source_dir( $source, $remote_source, $upgrader, $hook_extra ) {
		if ( ! isset( $hook_extra['plugin'] ) || $hook_extra['plugin'] !== self::PLUGIN_SLUG ) {
			return $source;
		}

		$correct_dir = trailingslashit( $remote_source ) . dirname( self::PLUGIN_SLUG ) . '/';

		if ( $source !== $correct_dir ) {
			global $wp_filesystem;
			if ( $wp_filesystem->move( $source, $correct_dir ) ) {
				return $correct_dir;
			}
		}

		return $source;
	}

	// -------------------------------------------------------------------------
	// Fetch the latest GitHub release (cached).
	// -------------------------------------------------------------------------
	private function get_latest_release() {
		$cached = get_transient( self::TRANSIENT_KEY );
		if ( $cached !== false ) {
			return $cached ?: null; // false-y empty string = failed last time.
		}

		$url      = 'https://api.github.com/repos/' . self::GITHUB_REPO . '/releases/latest';
		$response = wp_remote_get( $url, array(
			'timeout'    => 10,
			'user-agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' ),
			'headers'    => array( 'Accept' => 'application/vnd.github+json' ),
		) );

		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
			// Cache failure briefly so we don't hammer the API.
			set_transient( self::TRANSIENT_KEY, '', 300 );
			return null;
		}

		$body    = wp_remote_retrieve_body( $response );
		$release = json_decode( $body, true );

		if ( empty( $release['tag_name'] ) ) {
			set_transient( self::TRANSIENT_KEY, '', 300 );
			return null;
		}

		set_transient( self::TRANSIENT_KEY, $release, self::CACHE_SECONDS );
		return $release;
	}
}
