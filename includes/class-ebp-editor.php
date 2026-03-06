<?php
/**
 * Classic-Editor (TinyMCE) integration for Eifelhoster Buttons Pro.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EBP_Editor {

	public function __construct() {
		// Only load on pages where the Classic Editor is present.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		add_filter( 'mce_external_plugins',   array( $this, 'mce_plugin' ) );
		add_filter( 'mce_buttons',            array( $this, 'mce_button' ) );
		add_action( 'admin_footer',           array( $this, 'render_dialog' ) );
	}

	/** Only enqueue on post / page editing screens. */
	private function is_editor_page() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		return $screen && in_array( $screen->base, array( 'post', 'page' ), true );
	}

	public function enqueue( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_media();

		wp_enqueue_style(
			'ebp-admin',
			EBP_PLUGIN_URL . 'assets/css/ebp-admin.css',
			array(),
			EBP_VERSION
		);
		wp_enqueue_script(
			'ebp-dialog',
			EBP_PLUGIN_URL . 'assets/js/ebp-dialog.js',
			array( 'jquery', 'wp-color-picker' ),
			EBP_VERSION,
			true
		);

		$defaults = ebp_get_defaults();
		wp_localize_script( 'ebp-dialog', 'ebpData', array(
			'defaults'  => $defaults,
			'dashicons' => ebp_get_dashicons(),
			'i18n'      => array(
				'title'         => __( 'Eifelhoster Button einfügen', 'eifelhoster-buttons-pro' ),
				'titleEdit'     => __( 'Eifelhoster Button bearbeiten', 'eifelhoster-buttons-pro' ),
				'insert'        => __( 'Button einfügen', 'eifelhoster-buttons-pro' ),
				'update'        => __( 'Button aktualisieren', 'eifelhoster-buttons-pro' ),
				'cancel'        => __( 'Abbrechen', 'eifelhoster-buttons-pro' ),
				'selectFile'    => __( 'Datei auswählen', 'eifelhoster-buttons-pro' ),
				'selectMedia'   => __( 'Mediendatei auswählen', 'eifelhoster-buttons-pro' ),
				'noIcon'        => __( 'Kein Symbol', 'eifelhoster-buttons-pro' ),
				'searchIcon'    => __( 'Symbol suchen…', 'eifelhoster-buttons-pro' ),
				'use'           => __( 'Verwenden', 'eifelhoster-buttons-pro' ),
				'preview'       => __( 'Vorschau', 'eifelhoster-buttons-pro' ),
				'tabText'       => __( 'Text & Schrift', 'eifelhoster-buttons-pro' ),
				'tabColors'     => __( 'Farben & Hover', 'eifelhoster-buttons-pro' ),
				'tabIcon'       => __( 'Symbol', 'eifelhoster-buttons-pro' ),
				'tabBorder'     => __( 'Rahmen & Schatten', 'eifelhoster-buttons-pro' ),
				'tabLink'       => __( 'Link & Ziel', 'eifelhoster-buttons-pro' ),
			),
		) );
	}

	/** Register the TinyMCE plugin JS. */
	public function mce_plugin( $plugins ) {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return $plugins;
		}
		$plugins['ebp_button'] = EBP_PLUGIN_URL . 'assets/js/ebp-tinymce-plugin.js';
		return $plugins;
	}

	/** Add button to TinyMCE toolbar (row 1). */
	public function mce_button( $buttons ) {
		array_push( $buttons, 'ebp_button' );
		return $buttons;
	}

	/** Output the (hidden) dialog HTML into the admin footer. */
	public function render_dialog() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || ! in_array( $screen->base, array( 'post', 'page' ), true ) ) {
			return;
		}
		?>
		<!-- Eifelhoster Buttons Pro Dialog -->
		<div id="ebp-modal-overlay" style="display:none;" aria-modal="true" role="dialog"
			aria-label="<?php esc_attr_e( 'Eifelhoster Button einfügen', 'eifelhoster-buttons-pro' ); ?>">
			<div id="ebp-modal">
				<div id="ebp-modal-header">
					<span id="ebp-modal-title">
						<span class="dashicons dashicons-button" style="margin-right:6px"></span>
						<span id="ebp-modal-title-text"><?php esc_html_e( 'Eifelhoster Button einfügen', 'eifelhoster-buttons-pro' ); ?></span>
					</span>
					<button type="button" id="ebp-modal-close" aria-label="<?php esc_attr_e( 'Schließen', 'eifelhoster-buttons-pro' ); ?>">
						<span class="dashicons dashicons-no-alt"></span>
					</button>
				</div>

				<div id="ebp-modal-tabs">
					<button type="button" class="ebp-modal-tab active" data-tab="text">
						<?php esc_html_e( 'Text & Schrift', 'eifelhoster-buttons-pro' ); ?>
					</button>
					<button type="button" class="ebp-modal-tab" data-tab="colors">
						<?php esc_html_e( 'Farben & Hover', 'eifelhoster-buttons-pro' ); ?>
					</button>
					<button type="button" class="ebp-modal-tab" data-tab="icon">
						<?php esc_html_e( 'Symbol', 'eifelhoster-buttons-pro' ); ?>
					</button>
					<button type="button" class="ebp-modal-tab" data-tab="border">
						<?php esc_html_e( 'Rahmen & Schatten', 'eifelhoster-buttons-pro' ); ?>
					</button>
					<button type="button" class="ebp-modal-tab" data-tab="link">
						<?php esc_html_e( 'Link & Ziel', 'eifelhoster-buttons-pro' ); ?>
					</button>
				</div>

				<div id="ebp-modal-body">

					<!-- ===== TAB: Text & Font ===== -->
					<div class="ebp-modal-panel active" id="ebp-panel-text">
						<table class="ebp-dialog-table">
							<tr>
								<th><?php esc_html_e( 'Button-Text', 'eifelhoster-buttons-pro' ); ?></th>
								<td><input type="text" id="ebp-f-text" class="ebp-full-width"
									placeholder="<?php esc_attr_e( 'Button-Text eingeben…', 'eifelhoster-buttons-pro' ); ?>" /></td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Schriftart', 'eifelhoster-buttons-pro' ); ?></th>
								<td><input type="text" id="ebp-f-font-family" class="ebp-full-width"
									placeholder="inherit, Arial, Georgia, …" /></td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Schriftgröße (px)', 'eifelhoster-buttons-pro' ); ?></th>
								<td><input type="number" id="ebp-f-font-size" min="8" max="120" style="width:80px" /></td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Formatierung', 'eifelhoster-buttons-pro' ); ?></th>
								<td>
									<label><input type="checkbox" id="ebp-f-font-bold" />
										<?php esc_html_e( 'Fett', 'eifelhoster-buttons-pro' ); ?></label>
									&nbsp;
									<label><input type="checkbox" id="ebp-f-font-italic" />
										<?php esc_html_e( 'Kursiv', 'eifelhoster-buttons-pro' ); ?></label>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Innenabstand (px)', 'eifelhoster-buttons-pro' ); ?></th>
								<td>
									<?php esc_html_e( 'V:', 'eifelhoster-buttons-pro' ); ?>
									<input type="number" id="ebp-f-padding-v" min="0" max="100" style="width:70px" />
									&nbsp;
									<?php esc_html_e( 'H:', 'eifelhoster-buttons-pro' ); ?>
									<input type="number" id="ebp-f-padding-h" min="0" max="200" style="width:70px" />
								</td>
							</tr>
						</table>
					</div>

					<!-- ===== TAB: Colors & Hover ===== -->
					<div class="ebp-modal-panel" id="ebp-panel-colors">
						<table class="ebp-dialog-table">
							<tr>
								<th><?php esc_html_e( 'Hintergrundfarbe', 'eifelhoster-buttons-pro' ); ?></th>
								<td><input type="text" id="ebp-f-bg-color" class="ebp-dialog-color" /></td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Hintergrundfarbe (Hover)', 'eifelhoster-buttons-pro' ); ?></th>
								<td><input type="text" id="ebp-f-bg-hover-color" class="ebp-dialog-color" /></td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Textfarbe', 'eifelhoster-buttons-pro' ); ?></th>
								<td><input type="text" id="ebp-f-text-color" class="ebp-dialog-color" /></td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Textfarbe (Hover)', 'eifelhoster-buttons-pro' ); ?></th>
								<td><input type="text" id="ebp-f-text-hover-color" class="ebp-dialog-color" /></td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Grow bei Hover', 'eifelhoster-buttons-pro' ); ?></th>
								<td>
									<input type="number" id="ebp-f-hover-grow" min="1" max="2" step="0.01" style="width:80px" />
									<span class="description">
										<?php esc_html_e( '1 = kein Grow, 1.05 = 5%', 'eifelhoster-buttons-pro' ); ?>
									</span>
									<br>
									<input type="range" id="ebp-f-hover-grow-range" min="1" max="1.5" step="0.01"
										style="width:200px;margin-top:6px" />
								</td>
							</tr>
						</table>
					</div>

					<!-- ===== TAB: Icon ===== -->
					<div class="ebp-modal-panel" id="ebp-panel-icon">
						<table class="ebp-dialog-table">
							<tr>
								<th><?php esc_html_e( 'Symboltyp', 'eifelhoster-buttons-pro' ); ?></th>
								<td>
									<label><input type="radio" name="ebp-icon-type" value="none" id="ebp-f-icon-type-none" />
										<?php esc_html_e( 'Kein Symbol', 'eifelhoster-buttons-pro' ); ?></label>
									&nbsp;
									<label><input type="radio" name="ebp-icon-type" value="dashicon" id="ebp-f-icon-type-dashicon" />
										Dashicon</label>
									&nbsp;
									<label><input type="radio" name="ebp-icon-type" value="media" id="ebp-f-icon-type-media" />
										<?php esc_html_e( 'Mediendatei', 'eifelhoster-buttons-pro' ); ?></label>
								</td>
							</tr>
							<tr id="ebp-dlg-row-dashicon">
								<th><?php esc_html_e( 'Dashicon', 'eifelhoster-buttons-pro' ); ?></th>
								<td>
									<input type="text" id="ebp-dlg-icon-search" class="ebp-full-width"
										placeholder="<?php esc_attr_e( 'Symbol suchen…', 'eifelhoster-buttons-pro' ); ?>" />
									<div id="ebp-dlg-icon-preview" class="ebp-dashicon-preview" style="margin:6px 0"></div>
									<input type="hidden" id="ebp-f-icon" value="" />
									<div id="ebp-dlg-icon-grid" class="ebp-dashicon-grid" style="max-height:160px"></div>
								</td>
							</tr>
							<tr id="ebp-dlg-row-media-icon">
								<th><?php esc_html_e( 'Bild-Datei', 'eifelhoster-buttons-pro' ); ?></th>
								<td>
									<button type="button" class="button" id="ebp-dlg-select-icon-media">
										<?php esc_html_e( 'Datei auswählen', 'eifelhoster-buttons-pro' ); ?>
									</button>
									<input type="hidden" id="ebp-f-icon-media-url" value="" />
									<div id="ebp-dlg-icon-media-preview" style="margin-top:6px"></div>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Symbolgröße (px)', 'eifelhoster-buttons-pro' ); ?></th>
								<td><input type="number" id="ebp-f-icon-size" min="8" max="120" style="width:80px" /></td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Abstand zum Text (px)', 'eifelhoster-buttons-pro' ); ?></th>
								<td><input type="number" id="ebp-f-icon-spacing" min="0" max="60" style="width:80px" /></td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Position', 'eifelhoster-buttons-pro' ); ?></th>
								<td>
									<label><input type="radio" name="ebp-icon-pos" value="before" id="ebp-f-icon-pos-before" />
										<?php esc_html_e( 'Vor dem Text', 'eifelhoster-buttons-pro' ); ?></label>
									&nbsp;
									<label><input type="radio" name="ebp-icon-pos" value="after" id="ebp-f-icon-pos-after" />
										<?php esc_html_e( 'Hinter dem Text', 'eifelhoster-buttons-pro' ); ?></label>
								</td>
							</tr>
						</table>
					</div>

					<!-- ===== TAB: Border & Shadow ===== -->
					<div class="ebp-modal-panel" id="ebp-panel-border">
						<table class="ebp-dialog-table">
							<tr>
								<th><?php esc_html_e( 'Rahmenstärke (px)', 'eifelhoster-buttons-pro' ); ?></th>
								<td><input type="number" id="ebp-f-border-width" min="0" max="20" style="width:80px" /></td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Rahmenstil', 'eifelhoster-buttons-pro' ); ?></th>
								<td>
									<select id="ebp-f-border-style">
										<option value="solid">solid</option>
										<option value="dashed">dashed</option>
										<option value="dotted">dotted</option>
										<option value="double">double</option>
										<option value="none">none</option>
									</select>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Rahmenfarbe', 'eifelhoster-buttons-pro' ); ?></th>
								<td><input type="text" id="ebp-f-border-color" class="ebp-dialog-color" /></td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Rahmenradius (px)', 'eifelhoster-buttons-pro' ); ?></th>
								<td><input type="number" id="ebp-f-border-radius" min="0" max="100" style="width:80px" /></td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Schatten', 'eifelhoster-buttons-pro' ); ?></th>
								<td>
									<label><input type="checkbox" id="ebp-f-shadow-enabled" />
										<?php esc_html_e( 'Aktivieren', 'eifelhoster-buttons-pro' ); ?></label>
								</td>
							</tr>
							<tr id="ebp-dlg-shadow-fields">
								<th><?php esc_html_e( 'Schatten-Details', 'eifelhoster-buttons-pro' ); ?></th>
								<td>
									<table class="ebp-sub-table">
										<tr>
											<td>X:</td>
											<td><input type="number" id="ebp-f-shadow-x" style="width:70px" /></td>
											<td>Y:</td>
											<td><input type="number" id="ebp-f-shadow-y" style="width:70px" /></td>
										</tr>
										<tr>
											<td><?php esc_html_e( 'Blur:', 'eifelhoster-buttons-pro' ); ?></td>
											<td><input type="number" id="ebp-f-shadow-blur" min="0" style="width:70px" /></td>
											<td><?php esc_html_e( 'Spread:', 'eifelhoster-buttons-pro' ); ?></td>
											<td><input type="number" id="ebp-f-shadow-spread" style="width:70px" /></td>
										</tr>
										<tr>
											<td><?php esc_html_e( 'Farbe:', 'eifelhoster-buttons-pro' ); ?></td>
											<td colspan="3">
												<input type="text" id="ebp-f-shadow-color" placeholder="rgba(0,0,0,0.3)"
													style="width:200px" />
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</div>

					<!-- ===== TAB: Link ===== -->
					<div class="ebp-modal-panel" id="ebp-panel-link">
						<table class="ebp-dialog-table">
							<tr>
								<th><?php esc_html_e( 'Linktyp', 'eifelhoster-buttons-pro' ); ?></th>
								<td>
									<label><input type="radio" name="ebp-link-type" value="url" id="ebp-f-link-type-url" />
										URL</label>
									&nbsp;
									<label><input type="radio" name="ebp-link-type" value="email" id="ebp-f-link-type-email" />
										E-Mail</label>
									&nbsp;
									<label><input type="radio" name="ebp-link-type" value="media" id="ebp-f-link-type-media" />
										<?php esc_html_e( 'Mediendatei', 'eifelhoster-buttons-pro' ); ?></label>
								</td>
							</tr>
							<!-- URL fields -->
							<tr id="ebp-dlg-row-url">
								<th>URL</th>
								<td><input type="url" id="ebp-f-url" class="ebp-full-width"
									placeholder="https://…" /></td>
							</tr>
							<!-- Email fields -->
							<tr id="ebp-dlg-row-email">
								<th>E-Mail</th>
								<td>
									<input type="email" id="ebp-f-email" class="ebp-full-width"
										placeholder="email@beispiel.de" style="margin-bottom:6px" />
									<input type="text" id="ebp-f-email-subject" class="ebp-full-width"
										placeholder="<?php esc_attr_e( 'Betreff', 'eifelhoster-buttons-pro' ); ?>"
										style="margin-bottom:6px" />
									<textarea id="ebp-f-email-body" class="ebp-full-width" rows="3"
										placeholder="<?php esc_attr_e( 'Nachrichtentext (optional)', 'eifelhoster-buttons-pro' ); ?>"></textarea>
								</td>
							</tr>
							<!-- Media fields -->
							<tr id="ebp-dlg-row-media">
								<th><?php esc_html_e( 'Mediendatei', 'eifelhoster-buttons-pro' ); ?></th>
								<td>
									<input type="hidden" id="ebp-f-media-url" value="" />
									<button type="button" class="button" id="ebp-dlg-select-media">
										<?php esc_html_e( 'Datei auswählen', 'eifelhoster-buttons-pro' ); ?>
									</button>
									<div id="ebp-dlg-media-preview" style="margin-top:6px;font-size:12px;color:#666"></div>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Ziel', 'eifelhoster-buttons-pro' ); ?></th>
								<td>
									<label><input type="radio" name="ebp-target" value="_self" id="ebp-f-target-self" />
										<?php esc_html_e( 'Gleiche Seite', 'eifelhoster-buttons-pro' ); ?></label>
									&nbsp;
									<label><input type="radio" name="ebp-target" value="_blank" id="ebp-f-target-blank" />
										<?php esc_html_e( 'Neues Fenster / Tab', 'eifelhoster-buttons-pro' ); ?></label>
								</td>
							</tr>
						</table>
					</div>

					<!-- Preview -->
					<div id="ebp-dialog-preview-wrap">
						<span class="ebp-preview-label">
							<?php esc_html_e( 'Vorschau:', 'eifelhoster-buttons-pro' ); ?>
						</span>
						<div id="ebp-dialog-preview">
							<a href="#" class="ebp-dialog-preview-btn" id="ebp-preview-btn">
								<span class="ebp-preview-icon-before"></span>
								<span class="ebp-preview-text">Button</span>
								<span class="ebp-preview-icon-after"></span>
							</a>
						</div>
					</div>

				</div><!-- #ebp-modal-body -->

				<div id="ebp-modal-footer">
					<button type="button" id="ebp-btn-insert" class="button button-primary">
						<span class="dashicons dashicons-insert" style="vertical-align:middle;margin-right:4px"></span>
						<span id="ebp-btn-insert-label"><?php esc_html_e( 'Button einfügen', 'eifelhoster-buttons-pro' ); ?></span>
					</button>
					<button type="button" id="ebp-btn-cancel" class="button">
						<?php esc_html_e( 'Abbrechen', 'eifelhoster-buttons-pro' ); ?>
					</button>
					<span class="ebp-footer-credit">
						Eifelhoster Buttons Pro v<?php echo esc_html( EBP_VERSION ); ?> –
						<a href="https://eifelhoster.de" target="_blank" rel="noopener">eifelhoster.de</a>
					</span>
				</div>
			</div><!-- #ebp-modal -->
		</div><!-- #ebp-modal-overlay -->
		<?php
	}
}
