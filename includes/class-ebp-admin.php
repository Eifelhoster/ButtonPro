<?php
/**
 * Admin settings page for Eifelhoster Buttons Pro.
 * Settings > ButtonPro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EBP_Admin {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/** Add sub-menu under Settings */
	public function add_menu() {
		add_options_page(
			__( 'ButtonPro Einstellungen', 'eifelhoster-buttons-pro' ),
			'ButtonPro',
			'manage_options',
			'buttonpro',
			array( $this, 'render_page' )
		);

		// Documentation sub-page (hidden from menu, accessible via the Dokumentation button).
		add_submenu_page(
			null,
			__( 'ButtonPro Dokumentation', 'eifelhoster-buttons-pro' ),
			__( 'Dokumentation', 'eifelhoster-buttons-pro' ),
			'manage_options',
			'buttonpro-docs',
			array( $this, 'render_docs_page' )
		);
	}

	/** Enqueue colour-picker + admin JS/CSS */
	public function enqueue_scripts( $hook ) {
		// Load admin CSS on both the settings page and the documentation page.
		$is_settings = ( 'settings_page_buttonpro' === $hook );
		$is_docs     = ( 'admin_page_buttonpro-docs' === $hook );

		if ( $is_docs ) {
			wp_enqueue_style(
				'ebp-admin',
				EBP_PLUGIN_URL . 'assets/css/ebp-admin.css',
				array(),
				EBP_VERSION
			);
			return;
		}

		if ( ! $is_settings ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_media();
		wp_enqueue_style(
			'ebp-admin',
			EBP_PLUGIN_URL . 'assets/css/ebp-admin.css',
			array(),
			EBP_VERSION
		);
		wp_enqueue_script(
			'ebp-admin',
			EBP_PLUGIN_URL . 'assets/js/ebp-admin.js',
			array( 'jquery', 'wp-color-picker' ),
			EBP_VERSION,
			true
		);
		wp_localize_script( 'ebp-admin', 'ebpAdminData', array(
			'dashicons' => ebp_get_dashicons(),
			'defaults'  => ebp_get_defaults(),
			'ajaxurl'   => admin_url( 'admin-ajax.php' ),
			'nonce'     => wp_create_nonce( 'ebp_search_content' ),
		) );
	}

	/** Register the single option array */
	public function register_settings() {
		register_setting(
			'ebp_settings_group',
			EBP_OPTION_KEY,
			array( $this, 'sanitize_settings' )
		);
	}

	/** Sanitise before saving */
	public function sanitize_settings( $input ) {
		$clean = array();

		$text_fields = array(
			'font_family', 'font_size', 'button_width', 'icon', 'icon_media_url',
			'icon_size', 'icon_spacing', 'border_width', 'border_color',
			'border_radius', 'shadow_x', 'shadow_y', 'shadow_blur',
			'shadow_spread', 'shadow_color', 'url', 'email',
			'email_subject', 'media_url', 'bg_color', 'bg_hover_color',
			'text_color', 'text_hover_color', 'hover_grow', 'padding_v', 'padding_h',
			'content_id',
		);
		foreach ( $text_fields as $field ) {
			$clean[ $field ] = isset( $input[ $field ] ) ? sanitize_text_field( $input[ $field ] ) : '';
		}

		$clean['email_body'] = isset( $input['email_body'] ) ? sanitize_textarea_field( $input['email_body'] ) : '';

		// Logo URL – stored as a plain URL.
		$clean['logo_url'] = isset( $input['logo_url'] ) ? esc_url_raw( $input['logo_url'] ) : '';

		$checkboxes = array( 'font_bold', 'font_italic', 'shadow_enabled' );
		foreach ( $checkboxes as $field ) {
			$clean[ $field ] = isset( $input[ $field ] ) && '1' === $input[ $field ] ? '1' : '0';
		}

		$select_fields = array(
			'icon_type'     => array( 'none', 'dashicon', 'media' ),
			'icon_position' => array( 'before', 'after' ),
			'border_style'  => array( 'solid', 'dashed', 'dotted', 'double', 'none' ),
			'link_type'     => array( 'url', 'email', 'media', 'content' ),
			'target'        => array( '_self', '_blank' ),
		);
		foreach ( $select_fields as $field => $allowed ) {
			$clean[ $field ] = ( isset( $input[ $field ] ) && in_array( $input[ $field ], $allowed, true ) )
				? $input[ $field ]
				: $allowed[0];
		}

		return $clean;
	}

	/** Render the settings page */
	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$d = ebp_get_defaults();
		?>
		<div class="wrap ebp-admin-wrap">
			<h1><span class="dashicons dashicons-button ebp-title-icon"></span>
				<?php esc_html_e( 'ButtonPro – Standardwerte', 'eifelhoster-buttons-pro' ); ?>
			</h1>
			<p class="ebp-subtitle">
				<?php esc_html_e( 'Diese Werte werden als Standard für neue Buttons im Classic Editor verwendet.', 'eifelhoster-buttons-pro' ); ?>
			</p>

			<form method="post" action="options.php" id="ebp-settings-form">
				<?php settings_fields( 'ebp_settings_group' ); ?>

				<!-- Tabs -->
				<div class="ebp-tabs">
					<button type="button" class="ebp-tab-btn active" data-tab="text">
						<?php esc_html_e( 'Text & Schrift', 'eifelhoster-buttons-pro' ); ?>
					</button>
					<button type="button" class="ebp-tab-btn" data-tab="colors">
						<?php esc_html_e( 'Farben & Hover', 'eifelhoster-buttons-pro' ); ?>
					</button>
					<button type="button" class="ebp-tab-btn" data-tab="icon">
						<?php esc_html_e( 'Symbol (Icon)', 'eifelhoster-buttons-pro' ); ?>
					</button>
					<button type="button" class="ebp-tab-btn" data-tab="border">
						<?php esc_html_e( 'Rahmen & Schatten', 'eifelhoster-buttons-pro' ); ?>
					</button>
					<button type="button" class="ebp-tab-btn" data-tab="link">
						<?php esc_html_e( 'Link & Ziel', 'eifelhoster-buttons-pro' ); ?>
					</button>
				</div>

				<!-- ===== TAB: Text & Font ===== -->
				<div class="ebp-tab-panel active" id="ebp-tab-text">
					<table class="form-table ebp-form-table">
						<tr>
							<th><?php esc_html_e( 'Schriftart', 'eifelhoster-buttons-pro' ); ?></th>
							<td>
								<input type="text" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[font_family]"
									value="<?php echo esc_attr( $d['font_family'] ); ?>" class="regular-text"
									placeholder="inherit, Arial, Georgia, …" />
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Schriftgröße (px)', 'eifelhoster-buttons-pro' ); ?></th>
							<td>
								<input type="number" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[font_size]"
									value="<?php echo esc_attr( $d['font_size'] ); ?>" min="8" max="120" class="small-text" />
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Formatierung', 'eifelhoster-buttons-pro' ); ?></th>
							<td>
								<label>
									<input type="checkbox" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[font_bold]"
										value="1" <?php checked( '1', $d['font_bold'] ); ?> />
									<?php esc_html_e( 'Fett', 'eifelhoster-buttons-pro' ); ?>
								</label>
								&nbsp;&nbsp;
								<label>
									<input type="checkbox" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[font_italic]"
										value="1" <?php checked( '1', $d['font_italic'] ); ?> />
									<?php esc_html_e( 'Kursiv', 'eifelhoster-buttons-pro' ); ?>
								</label>
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Innenabstand (px)', 'eifelhoster-buttons-pro' ); ?></th>
							<td>
								<?php esc_html_e( 'Oben/Unten:', 'eifelhoster-buttons-pro' ); ?>
								<input type="number" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[padding_v]"
									value="<?php echo esc_attr( $d['padding_v'] ); ?>" min="0" max="100" class="small-text" />
								&nbsp;
								<?php esc_html_e( 'Links/Rechts:', 'eifelhoster-buttons-pro' ); ?>
								<input type="number" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[padding_h]"
									value="<?php echo esc_attr( $d['padding_h'] ); ?>" min="0" max="200" class="small-text" />
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Button-Breite gesamt (px)', 'eifelhoster-buttons-pro' ); ?></th>
							<td>
								<input type="number" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[button_width]"
									value="<?php echo esc_attr( $d['button_width'] ); ?>" min="0" max="2000" class="small-text" />
								<span class="description">
									<?php esc_html_e( '0 = automatische Breite', 'eifelhoster-buttons-pro' ); ?>
								</span>
							</td>
						</tr>
					</table>
				</div>

				<!-- ===== TAB: Colors & Hover ===== -->
				<div class="ebp-tab-panel" id="ebp-tab-colors">
					<table class="form-table ebp-form-table">
						<tr>
							<th><?php esc_html_e( 'Hintergrundfarbe', 'eifelhoster-buttons-pro' ); ?></th>
							<td>
								<input type="text" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[bg_color]"
									value="<?php echo esc_attr( $d['bg_color'] ); ?>" class="ebp-color-picker" />
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Hintergrundfarbe (Hover)', 'eifelhoster-buttons-pro' ); ?></th>
							<td>
								<input type="text" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[bg_hover_color]"
									value="<?php echo esc_attr( $d['bg_hover_color'] ); ?>" class="ebp-color-picker" />
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Textfarbe', 'eifelhoster-buttons-pro' ); ?></th>
							<td>
								<input type="text" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[text_color]"
									value="<?php echo esc_attr( $d['text_color'] ); ?>" class="ebp-color-picker" />
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Textfarbe (Hover)', 'eifelhoster-buttons-pro' ); ?></th>
							<td>
								<input type="text" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[text_hover_color]"
									value="<?php echo esc_attr( $d['text_hover_color'] ); ?>" class="ebp-color-picker" />
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Grow bei Hover', 'eifelhoster-buttons-pro' ); ?></th>
							<td>
								<input type="number" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[hover_grow]"
									value="<?php echo esc_attr( $d['hover_grow'] ); ?>"
									min="1" max="2" step="0.01" class="small-text" id="ebp-hover-grow" />
								<span class="description">
									<?php esc_html_e( '1 = kein Grow, 1.05 = 5% größer', 'eifelhoster-buttons-pro' ); ?>
								</span>
								<br>
								<input type="range" min="1" max="1.5" step="0.01"
									value="<?php echo esc_attr( $d['hover_grow'] ); ?>"
									id="ebp-hover-grow-range" style="width:200px;margin-top:6px" />
							</td>
						</tr>
					</table>
				</div>

				<!-- ===== TAB: Icon ===== -->
				<div class="ebp-tab-panel" id="ebp-tab-icon">
					<table class="form-table ebp-form-table">
						<tr>
							<th><?php esc_html_e( 'Symboltyp', 'eifelhoster-buttons-pro' ); ?></th>
							<td>
								<label>
									<input type="radio" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[icon_type]"
										value="none" <?php checked( 'none', $d['icon_type'] ); ?> class="ebp-icon-type-radio" />
									<?php esc_html_e( 'Kein Symbol', 'eifelhoster-buttons-pro' ); ?>
								</label>
								&nbsp;
								<label>
									<input type="radio" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[icon_type]"
										value="dashicon" <?php checked( 'dashicon', $d['icon_type'] ); ?> class="ebp-icon-type-radio" />
									Dashicon
								</label>
								&nbsp;
								<label>
									<input type="radio" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[icon_type]"
										value="media" <?php checked( 'media', $d['icon_type'] ); ?> class="ebp-icon-type-radio" />
									<?php esc_html_e( 'Mediendatei', 'eifelhoster-buttons-pro' ); ?>
								</label>
							</td>
						</tr>
						<tr id="ebp-row-dashicon" style="<?php echo 'dashicon' !== $d['icon_type'] ? 'display:none' : ''; ?>">
							<th><?php esc_html_e( 'Dashicon auswählen', 'eifelhoster-buttons-pro' ); ?></th>
							<td>
								<div class="ebp-dashicon-picker-wrap">
									<input type="text" id="ebp-dashicon-search" placeholder="<?php esc_attr_e( 'Suchen…', 'eifelhoster-buttons-pro' ); ?>" class="regular-text" />
									<input type="hidden" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[icon]"
										id="ebp-selected-icon" value="<?php echo esc_attr( $d['icon'] ); ?>" />
									<div id="ebp-dashicon-preview" class="ebp-dashicon-preview">
										<?php if ( $d['icon'] ) : ?>
											<span class="dashicons dashicons-<?php echo esc_attr( $d['icon'] ); ?>"></span>
											<span class="ebp-icon-name"><?php echo esc_html( $d['icon'] ); ?></span>
										<?php else : ?>
											<?php esc_html_e( 'Kein Symbol ausgewählt', 'eifelhoster-buttons-pro' ); ?>
										<?php endif; ?>
									</div>
									<div id="ebp-dashicon-grid" class="ebp-dashicon-grid"></div>
								</div>
							</td>
						</tr>
						<tr id="ebp-row-media-icon" style="<?php echo 'media' !== $d['icon_type'] ? 'display:none' : ''; ?>">
							<th><?php esc_html_e( 'Symbol-Mediendatei', 'eifelhoster-buttons-pro' ); ?></th>
							<td>
								<input type="hidden" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[icon_media_url]"
									id="ebp-icon-media-url" value="<?php echo esc_url( $d['icon_media_url'] ); ?>" />
								<button type="button" class="button" id="ebp-select-icon-media">
									<?php esc_html_e( 'Datei auswählen', 'eifelhoster-buttons-pro' ); ?>
								</button>
								<span id="ebp-icon-media-preview">
									<?php if ( $d['icon_media_url'] ) : ?>
										<img src="<?php echo esc_url( $d['icon_media_url'] ); ?>"
											style="max-height:40px;margin-left:8px;vertical-align:middle" />
									<?php endif; ?>
								</span>
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Symbolgröße (px)', 'eifelhoster-buttons-pro' ); ?></th>
							<td>
								<input type="number" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[icon_size]"
									value="<?php echo esc_attr( $d['icon_size'] ); ?>" min="8" max="120" class="small-text" />
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Abstand zum Text (px)', 'eifelhoster-buttons-pro' ); ?></th>
							<td>
								<input type="number" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[icon_spacing]"
									value="<?php echo esc_attr( $d['icon_spacing'] ); ?>" min="0" max="60" class="small-text" />
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Symbolposition', 'eifelhoster-buttons-pro' ); ?></th>
							<td>
								<label>
									<input type="radio" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[icon_position]"
										value="before" <?php checked( 'before', $d['icon_position'] ); ?> />
									<?php esc_html_e( 'Vor dem Text', 'eifelhoster-buttons-pro' ); ?>
								</label>
								&nbsp;
								<label>
									<input type="radio" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[icon_position]"
										value="after" <?php checked( 'after', $d['icon_position'] ); ?> />
									<?php esc_html_e( 'Hinter dem Text', 'eifelhoster-buttons-pro' ); ?>
								</label>
							</td>
						</tr>
					</table>
				</div>

				<!-- ===== TAB: Border & Shadow ===== -->
				<div class="ebp-tab-panel" id="ebp-tab-border">
					<table class="form-table ebp-form-table">
						<tr>
							<th><?php esc_html_e( 'Rahmenstärke (px)', 'eifelhoster-buttons-pro' ); ?></th>
							<td>
								<input type="number" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[border_width]"
									value="<?php echo esc_attr( $d['border_width'] ); ?>" min="0" max="20" class="small-text" />
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Rahmenstil', 'eifelhoster-buttons-pro' ); ?></th>
							<td>
								<select name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[border_style]">
									<?php foreach ( array( 'solid', 'dashed', 'dotted', 'double', 'none' ) as $s ) : ?>
										<option value="<?php echo esc_attr( $s ); ?>" <?php selected( $s, $d['border_style'] ); ?>>
											<?php echo esc_html( $s ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Rahmenfarbe', 'eifelhoster-buttons-pro' ); ?></th>
							<td>
								<input type="text" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[border_color]"
									value="<?php echo esc_attr( $d['border_color'] ); ?>" class="ebp-color-picker" />
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Rahmenradius (px)', 'eifelhoster-buttons-pro' ); ?></th>
							<td>
								<input type="number" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[border_radius]"
									value="<?php echo esc_attr( $d['border_radius'] ); ?>" min="0" max="100" class="small-text" />
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Schatten aktivieren', 'eifelhoster-buttons-pro' ); ?></th>
							<td>
								<label>
									<input type="checkbox" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[shadow_enabled]"
										value="1" <?php checked( '1', $d['shadow_enabled'] ); ?> id="ebp-shadow-enabled" />
									<?php esc_html_e( 'Ja', 'eifelhoster-buttons-pro' ); ?>
								</label>
							</td>
						</tr>
						<tr id="ebp-shadow-fields" style="<?php echo '1' !== $d['shadow_enabled'] ? 'display:none' : ''; ?>">
							<th><?php esc_html_e( 'Schatten-Einstellungen', 'eifelhoster-buttons-pro' ); ?></th>
							<td>
								<table class="ebp-sub-table">
									<tr>
										<td><?php esc_html_e( 'X-Offset:', 'eifelhoster-buttons-pro' ); ?></td>
										<td>
											<input type="number" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[shadow_x]"
												value="<?php echo esc_attr( $d['shadow_x'] ); ?>" class="small-text" />
										</td>
										<td><?php esc_html_e( 'Y-Offset:', 'eifelhoster-buttons-pro' ); ?></td>
										<td>
											<input type="number" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[shadow_y]"
												value="<?php echo esc_attr( $d['shadow_y'] ); ?>" class="small-text" />
										</td>
									</tr>
									<tr>
										<td><?php esc_html_e( 'Unschärfe:', 'eifelhoster-buttons-pro' ); ?></td>
										<td>
											<input type="number" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[shadow_blur]"
												value="<?php echo esc_attr( $d['shadow_blur'] ); ?>" min="0" class="small-text" />
										</td>
										<td><?php esc_html_e( 'Ausbreitung:', 'eifelhoster-buttons-pro' ); ?></td>
										<td>
											<input type="number" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[shadow_spread]"
												value="<?php echo esc_attr( $d['shadow_spread'] ); ?>" class="small-text" />
										</td>
									</tr>
									<tr>
										<td><?php esc_html_e( 'Schattenfarbe:', 'eifelhoster-buttons-pro' ); ?></td>
										<td colspan="3">
											<input type="text" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[shadow_color]"
												value="<?php echo esc_attr( $d['shadow_color'] ); ?>"
												class="ebp-color-picker" />
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</div>

				<!-- ===== TAB: Link ===== -->
				<div class="ebp-tab-panel" id="ebp-tab-link">
					<table class="form-table ebp-form-table">
						<tr>
							<th><?php esc_html_e( 'Standard-Linktyp', 'eifelhoster-buttons-pro' ); ?></th>
							<td>
								<label>
									<input type="radio" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[link_type]"
										value="url" <?php checked( 'url', $d['link_type'] ); ?> class="ebp-link-type-radio" />
									URL
								</label>
								&nbsp;
								<label>
									<input type="radio" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[link_type]"
										value="email" <?php checked( 'email', $d['link_type'] ); ?> class="ebp-link-type-radio" />
									E-Mail
								</label>
								&nbsp;
								<label>
									<input type="radio" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[link_type]"
										value="media" <?php checked( 'media', $d['link_type'] ); ?> class="ebp-link-type-radio" />
									<?php esc_html_e( 'Mediendatei', 'eifelhoster-buttons-pro' ); ?>
								</label>
								&nbsp;
								<label>
									<input type="radio" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[link_type]"
										value="content" <?php checked( 'content', $d['link_type'] ); ?> class="ebp-link-type-radio" />
									<?php esc_html_e( 'Inhalt', 'eifelhoster-buttons-pro' ); ?>
								</label>
							</td>
						</tr>
						<tr id="ebp-admin-row-content" style="<?php echo 'content' !== $d['link_type'] ? 'display:none' : ''; ?>">
							<th><?php esc_html_e( 'Inhalt auswählen', 'eifelhoster-buttons-pro' ); ?></th>
							<td>
								<input type="text" id="ebp-admin-content-search" class="regular-text"
									placeholder="<?php esc_attr_e( 'Suche (min. 2 Zeichen)…', 'eifelhoster-buttons-pro' ); ?>" />
								<div id="ebp-admin-content-results" style="margin-top:6px"></div>
								<input type="hidden" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[content_id]"
									id="ebp-admin-content-id" value="<?php echo esc_attr( $d['content_id'] ); ?>" />
								<div id="ebp-admin-content-selected" style="margin-top:4px;font-size:12px;color:#555">
									<?php
									if ( ! empty( $d['content_id'] ) ) {
										$post_title = get_the_title( (int) $d['content_id'] );
										if ( $post_title ) {
											echo '<span class="dashicons dashicons-yes" style="color:green"></span> '
												. esc_html( $post_title );
										}
									}
									?>
								</div>
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Standard-Ziel', 'eifelhoster-buttons-pro' ); ?></th>
							<td>
								<label>
									<input type="radio" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[target]"
										value="_self" <?php checked( '_self', $d['target'] ); ?> />
									<?php esc_html_e( 'Gleiche Seite', 'eifelhoster-buttons-pro' ); ?>
								</label>
								&nbsp;
								<label>
									<input type="radio" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[target]"
										value="_blank" <?php checked( '_blank', $d['target'] ); ?> />
									<?php esc_html_e( 'Neues Fenster / Tab', 'eifelhoster-buttons-pro' ); ?>
								</label>
							</td>
						</tr>
					</table>
				</div>

				<!-- Logo Upload (always visible, below all tabs) -->
				<div class="ebp-logo-upload-section">
					<h2><?php esc_html_e( 'Logo / Plugin-Bild', 'eifelhoster-buttons-pro' ); ?></h2>
					<p class="description">
						<?php esc_html_e( 'Laden Sie hier Ihr Logo hoch. Es wird in der Dokumentationsseite angezeigt.', 'eifelhoster-buttons-pro' ); ?>
					</p>
					<table class="form-table ebp-form-table">
						<tr>
							<th><?php esc_html_e( 'Logo', 'eifelhoster-buttons-pro' ); ?></th>
							<td>
								<input type="hidden" name="<?php echo esc_attr( EBP_OPTION_KEY ); ?>[logo_url]"
									id="ebp-logo-url" value="<?php echo esc_url( $d['logo_url'] ); ?>" />
								<button type="button" class="button" id="ebp-select-logo">
									<?php esc_html_e( 'Logo auswählen', 'eifelhoster-buttons-pro' ); ?>
								</button>
								<?php
								$remove_style = empty( $d['logo_url'] )
									? 'margin-left:8px;display:none'
									: 'margin-left:8px';
								?>
								<button type="button" class="button" id="ebp-remove-logo"
									style="<?php echo esc_attr( $remove_style ); ?>">
									<?php esc_html_e( 'Entfernen', 'eifelhoster-buttons-pro' ); ?>
								</button>
								<div id="ebp-logo-preview" style="margin-top:10px">
									<?php if ( ! empty( $d['logo_url'] ) ) : ?>
										<img src="<?php echo esc_url( $d['logo_url'] ); ?>"
											style="max-height:80px;max-width:300px;display:block" />
									<?php endif; ?>
								</div>
							</td>
						</tr>
					</table>
				</div>

				<?php submit_button( __( 'Einstellungen speichern', 'eifelhoster-buttons-pro' ) ); ?>
			</form>

			<!-- Live Preview -->
			<div class="ebp-admin-preview-box">
				<h2><?php esc_html_e( 'Vorschau', 'eifelhoster-buttons-pro' ); ?></h2>
				<div id="ebp-admin-preview">
					<a href="#" class="ebp-preview-btn" id="ebp-preview-link">
						<span class="ebp-preview-icon-before"></span>
						<span class="ebp-preview-text">Button Text</span>
						<span class="ebp-preview-icon-after"></span>
					</a>
				</div>
			</div>

			<div class="ebp-admin-footer">
				<p>
					<?php printf(
						/* translators: 1: plugin name 2: version 3: author link */
						esc_html__( '%1$s v%2$s – %3$s', 'eifelhoster-buttons-pro' ),
						'<strong>Eifelhoster Buttons Pro</strong>',
						esc_html( EBP_VERSION ),
						'<a href="https://eifelhoster.de" target="_blank" rel="noopener">eifelhoster.de · Michael Krämer</a>'
					); ?>
					&nbsp;&nbsp;|&nbsp;&nbsp;&copy; 2026 Michael Krämer
				</p>
				<p>
					<a href="<?php echo esc_url( admin_url( 'options-general.php?page=buttonpro-docs' ) ); ?>"
						class="button button-secondary ebp-docs-btn">
						<span class="dashicons dashicons-book-alt" style="vertical-align:middle;margin-top:-2px"></span>
						<?php esc_html_e( 'Dokumentation', 'eifelhoster-buttons-pro' ); ?>
					</a>
				</p>
			</div>
		</div><!-- .wrap -->
		<?php
	}

	/** Render the documentation page */
	public function render_docs_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$d        = ebp_get_defaults();
		$logo_url = ! empty( $d['logo_url'] ) ? $d['logo_url'] : '';
		$back_url = admin_url( 'options-general.php?page=buttonpro' );
		?>
		<div class="wrap ebp-admin-wrap ebp-docs-wrap">

			<!-- Header with logo -->
			<div class="ebp-docs-header">
				<?php if ( $logo_url ) : ?>
					<img src="<?php echo esc_url( $logo_url ); ?>"
						alt="<?php esc_attr_e( 'Logo', 'eifelhoster-buttons-pro' ); ?>"
						class="ebp-docs-logo" />
				<?php else : ?>
					<p class="ebp-docs-no-logo">
						<?php esc_html_e( 'Noch kein Logo hochgeladen. Laden Sie auf der Einstellungsseite ein Logo hoch.', 'eifelhoster-buttons-pro' ); ?>
					</p>
				<?php endif; ?>

				<h1>
					<span class="dashicons dashicons-book-alt ebp-title-icon"></span>
					<?php esc_html_e( 'ButtonPro – Dokumentation', 'eifelhoster-buttons-pro' ); ?>
				</h1>
				<p>
					<a href="<?php echo esc_url( $back_url ); ?>" class="button button-secondary">
						&larr; <?php esc_html_e( 'Zurück zu den Einstellungen', 'eifelhoster-buttons-pro' ); ?>
					</a>
				</p>
			</div>

			<div class="ebp-docs-content">

				<!-- ============================================================ -->
				<!-- 1. Einführung                                                  -->
				<!-- ============================================================ -->
				<div class="ebp-docs-section">
					<h2>1. <?php esc_html_e( 'Einführung', 'eifelhoster-buttons-pro' ); ?></h2>
					<p>
						<?php esc_html_e( 'Willkommen bei Eifelhoster Buttons Pro! Dieses Plugin ermöglicht Ihnen, ansprechend gestaltete Buttons in Ihre WordPress-Seiten und -Beiträge einzufügen – sowohl im Classic Editor als auch im Elementor Page Builder.', 'eifelhoster-buttons-pro' ); ?>
					</p>
					<p>
						<?php esc_html_e( 'Mit wenigen Klicks können Sie Farben, Schriftarten, Symbole, Rahmen, Schatten und Links für jeden Button individuell anpassen. Auf dieser Dokumentationsseite erfahren Sie Schritt für Schritt, wie das Plugin funktioniert.', 'eifelhoster-buttons-pro' ); ?>
					</p>
				</div>

				<!-- ============================================================ -->
				<!-- 2. Grundeinstellungen                                          -->
				<!-- ============================================================ -->
				<div class="ebp-docs-section">
					<h2>2. <?php esc_html_e( 'Grundeinstellungen', 'eifelhoster-buttons-pro' ); ?></h2>
					<p>
						<?php esc_html_e( 'Unter Einstellungen → ButtonPro finden Sie die Seite für die Standardwerte. Diese Werte gelten als Vorgabe für jeden neuen Button, den Sie erstellen.', 'eifelhoster-buttons-pro' ); ?>
					</p>
					<ol>
						<li>
							<strong><?php esc_html_e( 'Text &amp; Schrift', 'eifelhoster-buttons-pro' ); ?></strong> –
							<?php esc_html_e( 'Definieren Sie Schriftart, Schriftgröße, Fett-/Kursivstellung, Innenabstände und die Gesamtbreite des Buttons.', 'eifelhoster-buttons-pro' ); ?>
						</li>
						<li>
							<strong><?php esc_html_e( 'Farben &amp; Hover', 'eifelhoster-buttons-pro' ); ?></strong> –
							<?php esc_html_e( 'Legen Sie Hintergrundfarbe, Textfarbe sowie die Farben im Hover-Zustand (wenn die Maus über den Button fährt) fest.', 'eifelhoster-buttons-pro' ); ?>
						</li>
						<li>
							<strong><?php esc_html_e( 'Symbol (Icon)', 'eifelhoster-buttons-pro' ); ?></strong> –
							<?php esc_html_e( 'Wählen Sie optional ein Dashicon-Symbol oder eine Mediendatei als Icon für den Button.', 'eifelhoster-buttons-pro' ); ?>
						</li>
						<li>
							<strong><?php esc_html_e( 'Rahmen &amp; Schatten', 'eifelhoster-buttons-pro' ); ?></strong> –
							<?php esc_html_e( 'Geben Sie Ihren Buttons einen Rahmen und/oder einen Schlagschatten.', 'eifelhoster-buttons-pro' ); ?>
						</li>
						<li>
							<strong><?php esc_html_e( 'Link &amp; Ziel', 'eifelhoster-buttons-pro' ); ?></strong> –
							<?php esc_html_e( 'Bestimmen Sie den Standard-Linktyp (URL, E-Mail, Mediendatei oder Seiteninhalt) und ob der Link im selben oder einem neuen Tab geöffnet werden soll.', 'eifelhoster-buttons-pro' ); ?>
						</li>
					</ol>
					<p>
						<?php esc_html_e( 'Nachdem Sie Ihre Einstellungen vorgenommen haben, klicken Sie auf „Einstellungen speichern". Die Werte werden als Standard für alle neuen Buttons verwendet.', 'eifelhoster-buttons-pro' ); ?>
					</p>
				</div>

				<!-- ============================================================ -->
				<!-- 3. Verwendung im Elementor-Widget                             -->
				<!-- ============================================================ -->
				<div class="ebp-docs-section">
					<h2>3. <?php esc_html_e( 'Verwendung im Elementor-Widget', 'eifelhoster-buttons-pro' ); ?></h2>
					<p>
						<?php esc_html_e( 'Falls Elementor auf Ihrer Website installiert ist, steht Ihnen das Widget „Eifelhoster Button Pro" in der Elementor-Kategorie „Eifelhoster" zur Verfügung.', 'eifelhoster-buttons-pro' ); ?>
					</p>
					<ol>
						<li><?php esc_html_e( 'Öffnen Sie eine Seite oder einen Beitrag im Elementor-Editor.', 'eifelhoster-buttons-pro' ); ?></li>
						<li><?php esc_html_e( 'Suchen Sie im Widget-Panel nach „Eifelhoster Button Pro" oder klappen Sie die Kategorie „Eifelhoster" auf.', 'eifelhoster-buttons-pro' ); ?></li>
						<li><?php esc_html_e( 'Ziehen Sie das Widget per Drag &amp; Drop in Ihr Layout.', 'eifelhoster-buttons-pro' ); ?></li>
						<li><?php esc_html_e( 'Im linken Bereich erscheinen die Einstellungen des Widgets – alle Felder sind mit den gespeicherten Standardwerten vorbelegt.', 'eifelhoster-buttons-pro' ); ?></li>
						<li><?php esc_html_e( 'Passen Sie die Einstellungen nach Bedarf an. Die Vorschau im Editor aktualisiert sich automatisch.', 'eifelhoster-buttons-pro' ); ?></li>
						<li><?php esc_html_e( 'Speichern und veröffentlichen Sie die Seite.', 'eifelhoster-buttons-pro' ); ?></li>
					</ol>
					<p>
						<strong><?php esc_html_e( 'Hinweis:', 'eifelhoster-buttons-pro' ); ?></strong>
						<?php esc_html_e( 'Die im Elementor-Widget eingestellten Werte überschreiben die Standardwerte nur für diesen einen Button.', 'eifelhoster-buttons-pro' ); ?>
					</p>
				</div>

				<!-- ============================================================ -->
				<!-- 4. Upload von Logo/Bildern                                    -->
				<!-- ============================================================ -->
				<div class="ebp-docs-section">
					<h2>4. <?php esc_html_e( 'Upload von Logo/Bildern', 'eifelhoster-buttons-pro' ); ?></h2>
					<p>
						<?php esc_html_e( 'Sie können auf der Einstellungsseite ein Logo hochladen. Dieses Logo erscheint dann oben auf dieser Dokumentationsseite.', 'eifelhoster-buttons-pro' ); ?>
					</p>
					<ol>
						<li>
							<?php esc_html_e( 'Gehen Sie zu Einstellungen → ButtonPro.', 'eifelhoster-buttons-pro' ); ?>
						</li>
						<li>
							<?php esc_html_e( 'Scrollen Sie nach unten zum Abschnitt „Logo / Plugin-Bild".', 'eifelhoster-buttons-pro' ); ?>
						</li>
						<li>
							<?php esc_html_e( 'Klicken Sie auf „Logo auswählen" und wählen Sie ein Bild aus der WordPress-Mediathek aus (oder laden Sie ein neues Bild hoch).', 'eifelhoster-buttons-pro' ); ?>
						</li>
						<li>
							<?php esc_html_e( 'Klicken Sie auf „Einstellungen speichern". Das Logo wird sofort in der Dokumentation angezeigt.', 'eifelhoster-buttons-pro' ); ?>
						</li>
					</ol>
					<p>
						<?php esc_html_e( 'Für Symbolbilder in Buttons gehen Sie ebenso vor: Wählen Sie unter „Symbol (Icon)" den Typ „Mediendatei" und klicken Sie auf „Datei auswählen".', 'eifelhoster-buttons-pro' ); ?>
					</p>
				</div>

				<!-- ============================================================ -->
				<!-- 5. Häufige Anwendungsfälle                                    -->
				<!-- ============================================================ -->
				<div class="ebp-docs-section">
					<h2>5. <?php esc_html_e( 'Häufige Anwendungsfälle', 'eifelhoster-buttons-pro' ); ?></h2>
					<ul>
						<li>
							<strong><?php esc_html_e( 'Call-to-Action-Button:', 'eifelhoster-buttons-pro' ); ?></strong>
							<?php esc_html_e( 'Erstellen Sie einen auffälligen Button mit Ihrer Markenfarbe, der auf eine wichtige Seite verlinkt.', 'eifelhoster-buttons-pro' ); ?>
						</li>
						<li>
							<strong><?php esc_html_e( 'Download-Button:', 'eifelhoster-buttons-pro' ); ?></strong>
							<?php esc_html_e( 'Setzen Sie den Linktyp auf „Mediendatei" und wählen Sie eine PDF-Datei aus der Mediathek.', 'eifelhoster-buttons-pro' ); ?>
						</li>
						<li>
							<strong><?php esc_html_e( 'E-Mail-Button:', 'eifelhoster-buttons-pro' ); ?></strong>
							<?php esc_html_e( 'Wählen Sie den Linktyp „E-Mail" und tragen Sie Ihre E-Mail-Adresse sowie einen optionalen Betreff ein. Der Button öffnet beim Klick das E-Mail-Programm des Besuchers.', 'eifelhoster-buttons-pro' ); ?>
						</li>
						<li>
							<strong><?php esc_html_e( 'Navigations-Button:', 'eifelhoster-buttons-pro' ); ?></strong>
							<?php esc_html_e( 'Setzen Sie den Linktyp auf „Inhalt" und suchen Sie nach einer Seite oder einem Beitrag. Der Button verlinkt automatisch auf diese Seite.', 'eifelhoster-buttons-pro' ); ?>
						</li>
					</ul>
				</div>

				<!-- ============================================================ -->
				<!-- 6. Hinweise für Einsteiger                                    -->
				<!-- ============================================================ -->
				<div class="ebp-docs-section">
					<h2>6. <?php esc_html_e( 'Hinweise für Einsteiger', 'eifelhoster-buttons-pro' ); ?></h2>
					<ul>
						<li>
							<strong><?php esc_html_e( 'Farben:', 'eifelhoster-buttons-pro' ); ?></strong>
							<?php esc_html_e( 'Klicken Sie auf das farbige Feld neben einem Farbfeld, um den Farbwähler zu öffnen. Sie können Farben entweder visuell auswählen oder einen Hex-Code (z. B. #007bff) direkt eingeben.', 'eifelhoster-buttons-pro' ); ?>
						</li>
						<li>
							<strong><?php esc_html_e( 'Hover-Effekt:', 'eifelhoster-buttons-pro' ); ?></strong>
							<?php esc_html_e( 'Der Hover-Zustand wird aktiv, wenn ein Besucher die Maus über den Button bewegt. Mit „Grow bei Hover" können Sie den Button leicht vergrößern (z. B. 1.05 = 5 % größer).', 'eifelhoster-buttons-pro' ); ?>
						</li>
						<li>
							<strong><?php esc_html_e( 'Vorschau:', 'eifelhoster-buttons-pro' ); ?></strong>
							<?php esc_html_e( 'Auf der Einstellungsseite sehen Sie unterhalb des Formulars eine Live-Vorschau Ihres Buttons. Diese aktualisiert sich automatisch, wenn Sie Einstellungen ändern.', 'eifelhoster-buttons-pro' ); ?>
						</li>
						<li>
							<strong><?php esc_html_e( 'Shortcode:', 'eifelhoster-buttons-pro' ); ?></strong>
							<?php esc_html_e( 'Im Classic Editor fügen Sie Buttons über die Toolbar-Schaltfläche ein. Der Shortcode lautet [eifelhoster_button …].', 'eifelhoster-buttons-pro' ); ?>
						</li>
						<li>
							<strong><?php esc_html_e( 'Standardwerte ändern:', 'eifelhoster-buttons-pro' ); ?></strong>
							<?php esc_html_e( 'Bestehende Buttons werden durch eine Änderung der Standardwerte nicht verändert. Nur neu angelegte Buttons erhalten die neuen Vorgabewerte.', 'eifelhoster-buttons-pro' ); ?>
						</li>
					</ul>
				</div>

				<!-- ============================================================ -->
				<!-- 7. Entwickler / Kontakt                                       -->
				<!-- ============================================================ -->
				<div class="ebp-docs-section ebp-docs-contact">
					<h2>7. <?php esc_html_e( 'Entwickler / Kontakt', 'eifelhoster-buttons-pro' ); ?></h2>
					<p><?php esc_html_e( 'Bei Fragen, Anregungen oder Supportbedarf wenden Sie sich bitte direkt an den Entwickler:', 'eifelhoster-buttons-pro' ); ?></p>
					<address class="ebp-docs-address">
						<strong>Michael Krämer</strong><br />
						Founder &amp; CEO eifelhoster.de<br />
						Webhosting, Webdesign und Service<br />
						Dorfstr. 24<br />
						54597 Roth bei Prüm<br /><br />
						<?php esc_html_e( 'Fon:', 'eifelhoster-buttons-pro' ); ?> <a href="tel:+4965526009995">+49 6552 6009995</a><br />
						<?php esc_html_e( 'Fax:', 'eifelhoster-buttons-pro' ); ?> +49 6552 6009996<br />
						<?php esc_html_e( 'Mobil:', 'eifelhoster-buttons-pro' ); ?> <a href="tel:+491794773134">+49 179 4773134</a><br />
						<?php esc_html_e( 'Web:', 'eifelhoster-buttons-pro' ); ?> <a href="https://www.eifelhoster.de" target="_blank" rel="noopener">www.eifelhoster.de</a><br />
						<?php esc_html_e( 'Mail:', 'eifelhoster-buttons-pro' ); ?> <a href="mailto:mk@michael-kraemer.eu">mk@michael-kraemer.eu</a>
					</address>
				</div>

			</div><!-- .ebp-docs-content -->

			<div class="ebp-admin-footer">
				<p>
					<?php printf(
						/* translators: 1: plugin name 2: version 3: author link */
						esc_html__( '%1$s v%2$s – %3$s', 'eifelhoster-buttons-pro' ),
						'<strong>Eifelhoster Buttons Pro</strong>',
						esc_html( EBP_VERSION ),
						'<a href="https://eifelhoster.de" target="_blank" rel="noopener">eifelhoster.de · Michael Krämer</a>'
					); ?>
					&nbsp;&nbsp;|&nbsp;&nbsp;&copy; 2026 Michael Krämer
				</p>
			</div>

		</div><!-- .wrap -->
		<?php
	}
}
