<?php
/**
 * Settings page for SectionCore Pin & Freeze.
 *
 * @package WP_Pin_Freeze
 */

if (!defined('ABSPATH')) {
	exit;
}

if (class_exists('WPPF_Settings_Page')) {
	return;
}

/**
 * Settings management class.
 */
class WPPF_Settings_Page
{
	const SECTIONCORE_PARENT_MENU_SLUG = 'sectioncore-settings';
	const OPTION_CAPTURE_SELECTOR = 'wppf_capture_selector'; // Legacy.
	const OPTION_CAPTURE_SELECTOR_TYPE = 'wppf_capture_selector_type';
	const OPTION_CAPTURE_SELECTOR_VALUE = 'wppf_capture_selector_value';

	const DEFAULT_SELECTOR_TYPE = 'id';
	const DEFAULT_SELECTOR_VALUE = 'content';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public static function init()
	{
		add_action('admin_menu', array(__CLASS__, 'register_settings_page'), 95);
		add_action('admin_init', array(__CLASS__, 'register_settings'));
	}

	/**
	 * Register options page under Settings.
	 *
	 * @return void
	 */
	public static function register_settings_page()
	{
			if (self::has_sectioncore_parent_menu()) {
				add_submenu_page(
					self::SECTIONCORE_PARENT_MENU_SLUG,
					__('Pin & Freeze', 'sectioncore-pin-freeze'),
					__('Pin & Freeze', 'sectioncore-pin-freeze'),
					'architect_options',
					'sectioncore-pin-freeze',
					array(__CLASS__, 'render_page')
			);
			return;
		}

		add_options_page(
			__('Pin & Freeze', 'sectioncore-pin-freeze'),
			__('Pin & Freeze', 'sectioncore-pin-freeze'),
			'architect_options',
			'sectioncore-pin-freeze',
			array(__CLASS__, 'render_page')
		);
	}

	private static function has_sectioncore_parent_menu(): bool
	{
		global $admin_page_hooks;

		return is_array($admin_page_hooks) && isset($admin_page_hooks[self::SECTIONCORE_PARENT_MENU_SLUG]);
	}

	/**
	 * Register plugin settings and fields.
	 *
	 * @return void
	 */
	public static function register_settings()
	{
		register_setting(
			'wppf_settings',
			self::OPTION_CAPTURE_SELECTOR_TYPE,
			array(
				'type' => 'string',
				'default' => self::DEFAULT_SELECTOR_TYPE,
				'sanitize_callback' => array(__CLASS__, 'sanitize_capture_selector_type'),
			)
		);

		register_setting(
			'wppf_settings',
			self::OPTION_CAPTURE_SELECTOR_VALUE,
			array(
				'type' => 'string',
				'default' => self::DEFAULT_SELECTOR_VALUE,
				'sanitize_callback' => array(__CLASS__, 'sanitize_capture_selector_value'),
			)
		);

		add_settings_section(
			'wppf_capture_section',
			__('Frontend Capture', 'sectioncore-pin-freeze'),
			array(__CLASS__, 'render_section_description'),
			'sectioncore-pin-freeze'
		);

		add_settings_field(
			self::OPTION_CAPTURE_SELECTOR_TYPE,
			__('Capture Selector Type', 'sectioncore-pin-freeze'),
			array(__CLASS__, 'render_capture_selector_type_field'),
			'sectioncore-pin-freeze',
			'wppf_capture_section'
		);

		add_settings_field(
			self::OPTION_CAPTURE_SELECTOR_VALUE,
			__('Capture Selector Value', 'sectioncore-pin-freeze'),
			array(__CLASS__, 'render_capture_selector_value_field'),
			'sectioncore-pin-freeze',
			'wppf_capture_section'
		);
	}

	/**
	 * @param mixed $value Raw type value.
	 */
	public static function sanitize_capture_selector_type($value): string
	{
		$type = sanitize_key((string) $value);
		if (!in_array($type, array('id', 'class', 'tag', 'xpath'), true)) {
			$type = self::DEFAULT_SELECTOR_TYPE;
		}

		return $type;
	}

	/**
	 * @param mixed $value Raw value.
	 */
	public static function sanitize_capture_selector_value($value): string
	{
		$selector_value = trim((string) $value);
		$selector_type = isset($_POST[self::OPTION_CAPTURE_SELECTOR_TYPE])
			? self::sanitize_capture_selector_type(wp_unslash($_POST[self::OPTION_CAPTURE_SELECTOR_TYPE]))
			: self::get_capture_selector_contract()['type'];

		if ($selector_value === '') {
			return self::DEFAULT_SELECTOR_VALUE;
		}

		if (!self::is_selector_value_valid_for_type($selector_type, $selector_value)) {
			add_settings_error(
				self::OPTION_CAPTURE_SELECTOR_VALUE,
				'invalid_selector',
				__('Selector inválido para el tipo elegido. Revisa id/class/tag/xpath.', 'sectioncore-pin-freeze')
			);
			return self::DEFAULT_SELECTOR_VALUE;
		}

		return $selector_value;
	}

	/**
	 * @return void
	 */
	public static function render_section_description()
	{
		echo '<p>' . esc_html__('Define un contrato tipado para capturar el contenedor frontend sin header/footer.', 'sectioncore-pin-freeze') . '</p>';
	}

	/**
	 * @return void
	 */
	public static function render_capture_selector_type_field()
	{
		$contract = self::get_capture_selector_contract();
		$type = $contract['type'];
		?>
		<select name="<?php echo esc_attr(self::OPTION_CAPTURE_SELECTOR_TYPE); ?>" id="<?php echo esc_attr(self::OPTION_CAPTURE_SELECTOR_TYPE); ?>">
			<option value="id" <?php selected($type, 'id'); ?>><?php esc_html_e('ID', 'sectioncore-pin-freeze'); ?></option>
			<option value="class" <?php selected($type, 'class'); ?>><?php esc_html_e('Class', 'sectioncore-pin-freeze'); ?></option>
			<option value="tag" <?php selected($type, 'tag'); ?>><?php esc_html_e('Tag', 'sectioncore-pin-freeze'); ?></option>
			<option value="xpath" <?php selected($type, 'xpath'); ?>><?php esc_html_e('XPath', 'sectioncore-pin-freeze'); ?></option>
		</select>
		<p class="description"><?php esc_html_e('Contrato explícito: id | class | tag | xpath.', 'sectioncore-pin-freeze'); ?></p>
		<?php
	}

	/**
	 * @return void
	 */
	public static function render_capture_selector_value_field()
	{
		$contract = self::get_capture_selector_contract();
		$value = $contract['value'];
		?>
		<input type="text" name="<?php echo esc_attr(self::OPTION_CAPTURE_SELECTOR_VALUE); ?>"
			id="<?php echo esc_attr(self::OPTION_CAPTURE_SELECTOR_VALUE); ?>" class="regular-text"
			value="<?php echo esc_attr($value); ?>" />
		<p class="description">
			<?php esc_html_e('Ejemplos: id=content, class=site-main, tag=main, xpath=//main[@id="content"]', 'sectioncore-pin-freeze'); ?>
		</p>
		<?php
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 */
	public static function render_page()
	{
		if (!current_user_can('architect_options')) {
			return;
		}
		$help_markdown = self::get_architect_help_markdown();
		add_thickbox();
		?>
		<div class="wrap">
			<div class="sectioncore-help-header">
				<h1><?php esc_html_e('SectionCore Pin & Freeze', 'sectioncore-pin-freeze'); ?></h1>
				<a href="#TB_inline?width=920&height=680&inlineId=wppf-architect-help-modal" class="button button-secondary thickbox" title="Ayuda SectionCore"><?php esc_html_e('Ayuda', 'sectioncore-pin-freeze'); ?></a>
			</div>
			<style>
				.sectioncore-help-header {
					display: flex;
					align-items: center;
					justify-content: space-between;
					gap: 16px;
					margin-bottom: 12px;
				}
                .sectioncore-help-header h1 {
					margin: 0;
				}
            </style>
			<div id="wppf-architect-help-modal" style="display:none;">
				<?php if (function_exists("sectioncore_render_markdown")) : ?>
                <div class="sectioncore-help-markdown sectioncore-help-markdown--rendered"><?php echo sectioncore_render_markdown($help_markdown); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
                <?php else : ?>
                <pre class="sectioncore-help-markdown sectioncore-help-markdown--plain"><?php echo esc_html($help_markdown); ?></pre>
                <?php endif; ?>
			</div>
			<form method="post" action="options.php">
				<?php
				settings_fields('wppf_settings');
				do_settings_sections('sectioncore-pin-freeze');
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * @return array{type:string,value:string}
	 */
	public static function get_capture_selector_contract(): array
	{
		$type = get_option(self::OPTION_CAPTURE_SELECTOR_TYPE, '');
		$value = get_option(self::OPTION_CAPTURE_SELECTOR_VALUE, '');

		$type = self::sanitize_capture_selector_type($type);
		$value = trim((string) $value);

		if ($value === '') {
			$legacy = trim((string) get_option(self::OPTION_CAPTURE_SELECTOR, ''));
			$legacy_contract = self::convert_legacy_selector($legacy);
			if ($legacy_contract !== null) {
				$type = $legacy_contract['type'];
				$value = $legacy_contract['value'];
			}
		}

		if ($value === '' || !self::is_selector_value_valid_for_type($type, $value)) {
			$type = self::DEFAULT_SELECTOR_TYPE;
			$value = self::DEFAULT_SELECTOR_VALUE;
		}

		return array(
			'type' => $type,
			'value' => $value,
		);
	}

	/**
	 * Legacy string helper for backward compatibility in UI copy.
	 */
	public static function get_capture_selector(): string
	{
		$contract = self::get_capture_selector_contract();
		if ($contract['type'] === 'id') {
			return '#' . $contract['value'];
		}
		if ($contract['type'] === 'class') {
			return '.' . $contract['value'];
		}

		return $contract['value'];
	}

	/**
	 * @return array{type:string,value:string}|null
	 */
	private static function convert_legacy_selector(string $selector): ?array
	{
		if ($selector === '') {
			return null;
		}

		if (strpos($selector, '#') === 0) {
			$value = substr($selector, 1);
			if (self::is_selector_value_valid_for_type('id', $value)) {
				return array('type' => 'id', 'value' => $value);
			}
		}

		if (strpos($selector, '.') === 0) {
			$value = substr($selector, 1);
			if (self::is_selector_value_valid_for_type('class', $value)) {
				return array('type' => 'class', 'value' => $value);
			}
		}

		if (self::is_selector_value_valid_for_type('tag', $selector)) {
			return array('type' => 'tag', 'value' => $selector);
		}

		return null;
	}

	private static function is_selector_value_valid_for_type(string $type, string $value): bool
	{
		if ($type === 'id' || $type === 'class') {
			return preg_match('/^[A-Za-z0-9_-]+$/', $value) === 1;
		}

		if ($type === 'tag') {
			return preg_match('/^[A-Za-z][A-Za-z0-9:-]*$/', $value) === 1;
		}

		if ($type === 'xpath') {
			return strpos($value, '/') === 0;
		}

		return false;
	}

	private static function get_architect_help_markdown(): string
	{
		$path = dirname(__DIR__) . '/docs/architect-help.md';
		if (!is_readable($path)) {
			return "# Pin & Freeze\n\nNo hay documentación de ayuda disponible todavía.";
		}

		$contents = file_get_contents($path);

		return is_string($contents) && $contents !== '' ? $contents : "# Pin & Freeze\n\nNo hay documentación de ayuda disponible todavía.";
	}
}
