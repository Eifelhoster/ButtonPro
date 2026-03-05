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
}

/** Enqueue colour-picker + admin JS/CSS */
public function enqueue_scripts( $hook ) {
if ( 'settings_page_buttonpro' !== $hook ) {
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
'font_family', 'font_size', 'icon', 'icon_media_url',
'icon_size', 'icon_spacing', 'border_width', 'border_color',
'border_radius', 'shadow_x', 'shadow_y', 'shadow_blur',
'shadow_spread', 'shadow_color', 'url', 'email',
'email_subject', 'media_url', 'bg_color', 'bg_hover_color',
'text_color', 'text_hover_color', 'hover_grow', 'padding_v', 'padding_h',
);
foreach ( $text_fields as $field ) {
$clean[ $field ] = isset( $input[ $field ] ) ? sanitize_text_field( $input[ $field ] ) : '';
}

$clean['email_body'] = isset( $input['email_body'] ) ? sanitize_textarea_field( $input['email_body'] ) : '';

$checkboxes = array( 'font_bold', 'font_italic', 'shadow_enabled' );
foreach ( $checkboxes as $field ) {
$clean[ $field ] = isset( $input[ $field ] ) && '1' === $input[ $field ] ? '1' : '0';
}

$select_fields = array(
'icon_type'     => array( 'none', 'dashicon', 'media' ),
'icon_position' => array( 'before', 'after' ),
'border_style'  => array( 'solid', 'dashed', 'dotted', 'double', 'none' ),
'link_type'     => array( 'url', 'email', 'media' ),
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
class="regular-text" placeholder="rgba(0,0,0,0.3)" />
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
</p>
</div>
</div><!-- .wrap -->
<?php
}
}
