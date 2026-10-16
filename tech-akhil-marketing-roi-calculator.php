<?php
/**
 * Plugin Name: Tech Akhil Marketing ROI Calculator
 * Plugin URI: https://techakhil.com/digital-marketing-roi-calculator/
 * Description: A configurable marketing ROI calculator with lead capture, template controls, UTM attribution and CSV export.
 * Version: 2.9.6
 * Author: Akhilesh Kumar Meena
 * Author URI: https://techakhil.com/akhilesh-kumar-meena/
 * License: GPLv2 or later
 * Text Domain: tech-akhil-marketing-roi-calculator
 * Requires at least: 6.2
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'DM_ROI_VERSION', '2.9.6' );
define( 'DM_ROI_TABLE_SUFFIX', 'roi_calculator_leads' );

function dm_roi_table_name() {
    global $wpdb;
    return $wpdb->prefix . DM_ROI_TABLE_SUFFIX;
}

function dm_roi_create_lead_table() {
    global $wpdb;
    $table = dm_roi_table_name();
    $charset_collate = $wpdb->get_charset_collate();
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    $sql = "CREATE TABLE {$table} (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        name varchar(100) NOT NULL,
        email varchar(100) NOT NULL,
        phone varchar(30) NOT NULL,
        budget varchar(50) NOT NULL,
        estimated_roi varchar(50) NOT NULL,
        estimated_roas decimal(14,2) NOT NULL DEFAULT 0,
        estimated_revenue decimal(14,2) NOT NULL DEFAULT 0,
        estimated_profit decimal(14,2) NOT NULL DEFAULT 0,
        estimated_leads decimal(14,2) NOT NULL DEFAULT 0,
        campaign varchar(100) NOT NULL DEFAULT '',
        template varchar(60) NOT NULL DEFAULT '',
        source varchar(100) NOT NULL DEFAULT '',
        medium varchar(100) NOT NULL DEFAULT '',
        utm_campaign varchar(150) NOT NULL DEFAULT '',
        landing_page text NULL,
        PRIMARY KEY  (id),
        KEY created_at (created_at),
        KEY campaign (campaign)
    ) {$charset_collate};";
    dbDelta( $sql );
    update_option( 'dm_roi_db_version', DM_ROI_VERSION );
}
register_activation_hook( __FILE__, 'dm_roi_create_lead_table' );

function dm_roi_maybe_upgrade_table() {
    if ( get_option( 'dm_roi_db_version' ) !== DM_ROI_VERSION ) { dm_roi_create_lead_table(); }
    // Preserve settings created by the 1.x releases when upgrading.
    if ( false === get_option( 'dm_roi_settings', false ) ) {
        $legacy = dm_roi_defaults();
        $legacy['primary_color'] = sanitize_hex_color( get_option( 'dm_roi_primary_color', $legacy['primary_color'] ) ) ?: $legacy['primary_color'];
        $legacy['card_color'] = sanitize_hex_color( get_option( 'dm_roi_card_color', $legacy['card_color'] ) ) ?: $legacy['card_color'];
        $legacy['wa_enable'] = get_option( 'dm_roi_wa_enable', $legacy['wa_enable'] ) === '1' ? '1' : '0';
        $legacy['wa_number'] = preg_replace( '/[^0-9]/', '', (string) get_option( 'dm_roi_wa_number', $legacy['wa_number'] ) );
        $legacy['wa_message'] = sanitize_textarea_field( get_option( 'dm_roi_wa_message', $legacy['wa_message'] ) );
        update_option( 'dm_roi_settings', $legacy );
    }
}
add_action( 'plugins_loaded', 'dm_roi_maybe_upgrade_table' );

function dm_roi_templates() {
    return array(
        'general' => array( 'label' => 'General Marketing', 'icon' => '📈', 'color' => '#9d174d' ),
        'google-ads' => array( 'label' => 'Google Ads', 'icon' => '🔎', 'color' => '#4285f4' ),
        'meta-ads' => array( 'label' => 'Meta Ads', 'icon' => '∞', 'color' => '#0866ff' ),
        'linkedin-ads' => array( 'label' => 'LinkedIn Ads', 'icon' => '💼', 'color' => '#0a66c2' ),
        'tiktok-ads' => array( 'label' => 'TikTok Ads', 'icon' => '♪', 'color' => '#111111' ),
        'microsoft-ads' => array( 'label' => 'Microsoft / Bing Ads', 'icon' => '🪟', 'color' => '#008272' ),
        'pinterest-ads' => array( 'label' => 'Pinterest Ads', 'icon' => '📌', 'color' => '#bd081c' ),
        'x-ads' => array( 'label' => 'X Ads', 'icon' => '𝕏', 'color' => '#111111' ),
        'seo' => array( 'label' => 'SEO', 'icon' => '🔍', 'color' => '#16a34a' ),
        'email-marketing' => array( 'label' => 'Email Marketing', 'icon' => '✉️', 'color' => '#7c3aed' ),
        'affiliate-marketing' => array( 'label' => 'Affiliate Marketing', 'icon' => '🔗', 'color' => '#ea580c' ),
        'ecommerce' => array( 'label' => 'E-commerce', 'icon' => '🛍️', 'color' => '#0891b2' ),
        'lead-generation' => array( 'label' => 'Lead Generation', 'icon' => '🎯', 'color' => '#0f766e' ),
    );
}

function dm_roi_defaults() {
    return array(
        'primary_color' => '#9d174d', 'card_color' => '#ffffff', 'button_text' => 'Calculate My ROI',
        'title' => 'Digital Marketing ROI Calculator', 'currency' => 'INR', 'theme' => 'light',
        'wa_enable' => '0', 'wa_number' => '', 'wa_message' => 'Hello, I calculated my marketing ROI.',
        'show_comparison' => '1', 'show_pdf' => '1', 'lead_position' => 'before', 'button_radius' => 'rounded',
        'enabled_templates' => array_keys( dm_roi_templates() ), 'template_colors' => array_combine( array_keys( dm_roi_templates() ), array_column( dm_roi_templates(), 'color' ) ), 'powered_by' => '0',
    );
}
function dm_roi_get_settings() {
    $settings = wp_parse_args( get_option( 'dm_roi_settings', array() ), dm_roi_defaults() );
    $templates = dm_roi_templates();
    if ( ! is_array( $settings['enabled_templates'] ) ) { $settings['enabled_templates'] = array_keys( $templates ); }
    if ( ! is_array( $settings['template_colors'] ) ) { $settings['template_colors'] = array(); }
    foreach ( $templates as $slug => $template ) { $settings['template_colors'][ $slug ] = sanitize_hex_color( $settings['template_colors'][ $slug ] ?? '' ) ?: $template['color']; }
    return $settings;
}

function dm_roi_safe_cell( $value ) {
    $value = (string) $value;
    if ( preg_match( '/^[\s]*[=+\-@]/u', $value ) || preg_match( '/^[\t\r]/', $value ) ) { return "'" . $value; }
    return $value;
}
function dm_roi_allow_submission() {
    $ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
    if ( '' === $ip ) { return true; }
    $key = 'dm_roi_rate_' . hash_hmac( 'sha256', $ip, wp_salt( 'auth' ) );
    $count = (int) get_transient( $key );
    if ( $count >= 8 ) { return false; }
    set_transient( $key, $count + 1, 10 * MINUTE_IN_SECONDS );
    return true;
}

function dm_roi_save_lead() {
    check_ajax_referer( 'dm_roi_lead_nonce', 'security' );
    if ( ! dm_roi_allow_submission() ) { wp_send_json_error( array( 'message' => __( 'Too many submissions. Please try again later.', 'tech-akhil-marketing-roi-calculator' ) ), 429 ); }

    $name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
    $email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
    $phone = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
    $budget_raw = isset( $_POST['budget'] ) ? sanitize_text_field( wp_unslash( $_POST['budget'] ) ) : '';
    $roi_raw = isset( $_POST['roi'] ) ? sanitize_text_field( wp_unslash( $_POST['roi'] ) ) : '0';
    $roas_raw = isset( $_POST['roas'] ) ? sanitize_text_field( wp_unslash( $_POST['roas'] ) ) : '0';
    $revenue_raw = isset( $_POST['revenue'] ) ? sanitize_text_field( wp_unslash( $_POST['revenue'] ) ) : '0';
    $profit_raw = isset( $_POST['profit'] ) ? sanitize_text_field( wp_unslash( $_POST['profit'] ) ) : '0';
    $leads_raw = isset( $_POST['leads'] ) ? sanitize_text_field( wp_unslash( $_POST['leads'] ) ) : '0';
    $budget = is_numeric( $budget_raw ) ? (float) $budget_raw : -1;
    $roi = is_numeric( $roi_raw ) ? (float) $roi_raw : 0;
    $roas = is_numeric( $roas_raw ) ? (float) $roas_raw : 0;
    $revenue = is_numeric( $revenue_raw ) ? (float) $revenue_raw : 0;
    $profit = is_numeric( $profit_raw ) ? (float) $profit_raw : 0;
    $leads = is_numeric( $leads_raw ) ? (float) $leads_raw : 0;
    $campaign = isset( $_POST['campaign'] ) ? sanitize_text_field( wp_unslash( $_POST['campaign'] ) ) : '';
    $template = isset( $_POST['platform'] ) ? sanitize_key( wp_unslash( $_POST['platform'] ) ) : '';
    if ( ! array_key_exists( $template, dm_roi_templates() ) ) { $template = 'general'; }
    $source = isset( $_POST['utm_source'] ) ? sanitize_text_field( wp_unslash( $_POST['utm_source'] ) ) : '';
    $medium = isset( $_POST['utm_medium'] ) ? sanitize_text_field( wp_unslash( $_POST['utm_medium'] ) ) : '';
    $utm_campaign = isset( $_POST['utm_campaign'] ) ? sanitize_text_field( wp_unslash( $_POST['utm_campaign'] ) ) : '';
    $landing = isset( $_POST['landing_page'] ) ? esc_url_raw( wp_unslash( $_POST['landing_page'] ) ) : '';

    if ( '' === $name || strlen( $name ) > 100 || ! is_email( $email ) || strlen( $email ) > 100 || '' === $phone || strlen( $phone ) > 30 || $budget < 0 || ! is_finite( $budget ) ) {
        wp_send_json_error( array( 'message' => __( 'Please enter valid contact details and budget.', 'tech-akhil-marketing-roi-calculator' ) ), 400 );
    }
    global $wpdb;
    $table = dm_roi_table_name();
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- WordPress has no CRUD API for a plugin-owned custom table.
    $inserted = $wpdb->insert( $table, array(
        'name' => $name, 'email' => $email, 'phone' => $phone, 'budget' => $budget,
        'estimated_roi' => $roi, 'estimated_roas' => $roas, 'estimated_revenue' => $revenue,
        'estimated_profit' => $profit, 'estimated_leads' => $leads, 'campaign' => substr( $campaign, 0, 100 ), 'template' => $template,
        'source' => substr( $source, 0, 100 ), 'medium' => substr( $medium, 0, 100 ),
        'utm_campaign' => substr( $utm_campaign, 0, 150 ), 'landing_page' => $landing,
    ), array( '%s','%s','%s','%f','%f','%f','%f','%f','%f','%s','%s','%s','%s','%s','%s' ) );
    if ( false === $inserted ) { wp_send_json_error( array( 'message' => __( 'Could not save your request. Please try again.', 'tech-akhil-marketing-roi-calculator' ) ), 500 ); }

    $admin_email = sanitize_email( get_option( 'admin_email' ) );
    if ( $admin_email ) {
        $subject = __( 'New ROI Calculator lead', 'tech-akhil-marketing-roi-calculator' );
        $message = "New ROI Calculator lead received.\n\nName: {$name}\nEmail: {$email}\nPhone: {$phone}\nBudget: {$budget}\nEstimated ROI: {$roi}%\nEstimated ROAS: {$roas}x\nCampaign: {$campaign}\nSource: {$source}\nUTM Campaign: {$utm_campaign}";
        wp_mail( $admin_email, $subject, $message );
    }
    wp_send_json_success( array( 'message' => __( 'Your results are ready.', 'tech-akhil-marketing-roi-calculator' ) ) );
}
add_action( 'wp_ajax_dm_roi_save_lead', 'dm_roi_save_lead' );
add_action( 'wp_ajax_nopriv_dm_roi_save_lead', 'dm_roi_save_lead' );

function dm_roi_shortcode( $atts = array() ) {
    $s = dm_roi_get_settings();
    $atts = shortcode_atts( array( 'template' => '', 'title' => '' ), $atts, 'dm_roi_calculator' );
    $title = $atts['title'] ? sanitize_text_field( $atts['title'] ) : $s['title'];
    $id = 'dm-roi-' . wp_rand( 10000, 999999 );
    $currency_symbols = array( 'INR' => '₹', 'USD' => '$', 'GBP' => '£', 'EUR' => '€' );
    $symbol = isset( $currency_symbols[ $s['currency'] ] ) ? $currency_symbols[ $s['currency'] ] : '₹';
    $primary = sanitize_hex_color( $s['primary_color'] ) ?: '#9d174d';
    $card = sanitize_hex_color( $s['card_color'] ) ?: '#ffffff';
    $templates = dm_roi_templates();
    $is_fixed_template = (string) $atts['template'] !== '';
    $template = $is_fixed_template && isset( $templates[ $atts['template'] ] ) ? $atts['template'] : 'general';
    $enabled_templates = array_values( array_intersect( array_keys( $templates ), (array) $s['enabled_templates'] ) );
    if ( ! $enabled_templates ) { return '<div class="dm-roi-notice">' . esc_html__( 'The calculator is temporarily unavailable. Please enable at least one template in ROI Leads → Settings.', 'tech-akhil-marketing-roi-calculator' ) . '</div>'; }
    if ( ! in_array( $template, $enabled_templates, true ) ) {
        if ( $is_fixed_template ) { return '<div class="dm-roi-notice">' . esc_html__( 'This calculator template is currently disabled by the site administrator.', 'tech-akhil-marketing-roi-calculator' ) . '</div>'; }
        $template = $enabled_templates[0];
    }
    $primary = $s['template_colors'][ $template ] ?? $primary;
    $wa = $s['wa_enable'] === '1' && preg_match( '/^[0-9]{8,15}$/', $s['wa_number'] );
    ob_start(); ?>
    <div id="<?php echo esc_attr( $id ); ?>" class="dm-roi-widget <?php echo $s['theme'] === 'dark' ? 'dm-roi-dark' : ''; ?>" style="--dm-primary:<?php echo esc_attr( $primary ); ?>;--dm-card:<?php echo esc_attr( $card ); ?>;--dm-button-radius:<?php echo $s['button_radius'] === 'square' ? '3px' : ( $s['button_radius'] === 'pill' ? '999px' : '8px' ); ?>" data-template="<?php echo esc_attr( $template ); ?>" data-currency="<?php echo esc_attr( $s['currency'] ); ?>" data-symbol="<?php echo esc_attr( $symbol ); ?>" data-primary="<?php echo esc_attr( $primary ); ?>" data-enabled-templates="<?php echo esc_attr( wp_json_encode( $enabled_templates ) ); ?>" data-template-colors="<?php echo esc_attr( wp_json_encode( $s['template_colors'] ) ); ?>" data-template-definitions="<?php echo esc_attr( wp_json_encode( $templates ) ); ?>" data-powered-by="<?php echo esc_attr( $s['powered_by'] ); ?>" data-ajax="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'dm_roi_lead_nonce' ) ); ?>" data-wa="<?php echo $wa ? '1' : '0'; ?>" data-wa-number="<?php echo esc_attr( $s['wa_number'] ); ?>" data-wa-message="<?php echo esc_attr( $s['wa_message'] ); ?>">

      <h2><?php echo esc_html( $title ); ?></h2>
      <div class="dm-roi-template-identity" data-template-identity aria-live="polite"><span class="dm-roi-template-identity-icon" data-template-icon aria-hidden="true"><?php echo esc_html( $templates[ $template ]['icon'] ); ?></span><div><strong data-template-label><?php echo esc_html( $templates[ $template ]['label'] ); ?></strong><span>Campaign ROI Calculator</span></div></div>
      <p class="dm-roi-intro"><?php echo esc_html__( 'Enter your campaign assumptions to estimate performance. Results are estimates, not guarantees.', 'tech-akhil-marketing-roi-calculator' ); ?></p>
      <form class="dm-roi-form" method="post" action="#dm-roi-calculator" novalidate>
        <?php if ( ! $is_fixed_template ) : ?>
        <div class="dm-roi-template-picker" style="margin:0 0 16px">
          <label for="<?php echo esc_attr( $id ); ?>-platform"><?php esc_html_e( 'Marketing Platform / Campaign Type', 'tech-akhil-marketing-roi-calculator' ); ?></label>
          <select id="<?php echo esc_attr( $id ); ?>-platform" class="dm-roi-platform" name="platform">
            <?php foreach ( $enabled_templates as $template_slug ) : ?><option value="<?php echo esc_attr( $template_slug ); ?>" data-color="<?php echo esc_attr( $s['template_colors'][ $template_slug ] ); ?>" <?php selected( $template_slug, $template ); ?>><?php echo esc_html( $templates[ $template_slug ]['icon'] . ' ' . $templates[ $template_slug ]['label'] ); ?></option><?php endforeach; ?>
          </select>
        </div>
        <?php else : ?><input type="hidden" name="platform" value="<?php echo esc_attr( $template ); ?>" class="dm-roi-platform" /><?php endif; ?>
        <div class="dm-roi-grid">
          <div class="dm-roi-full"><label for="<?php echo esc_attr( $id ); ?>-mode">Calculation Mode</label><select id="<?php echo esc_attr( $id ); ?>-mode" class="dm-roi-mode" name="calculation_mode"><option value="estimate">Estimate future performance</option><option value="actual">Calculate from actual campaign results</option></select></div>
          <div class="dm-roi-dynamic-field" data-field="spend" hidden><label for="<?php echo esc_attr( $id ); ?>-spend">Marketing / Campaign Spend</label><input id="<?php echo esc_attr( $id ); ?>-spend" name="spend" type="number" min="0" step="0.01" value="10000"></div>
          <div class="dm-roi-dynamic-field" data-field="agency_fees" hidden><label for="<?php echo esc_attr( $id ); ?>-agency_fees">Agency / Management Fees</label><input id="<?php echo esc_attr( $id ); ?>-agency_fees" name="agency_fees" type="number" min="0" step="0.01" value="0"></div>
          <div class="dm-roi-dynamic-field" data-field="creative_cost" hidden><label for="<?php echo esc_attr( $id ); ?>-creative_cost">Creative / Production Cost</label><input id="<?php echo esc_attr( $id ); ?>-creative_cost" name="creative_cost" type="number" min="0" step="0.01" value="0"></div>
          <div class="dm-roi-dynamic-field" data-field="other_costs" hidden><label for="<?php echo esc_attr( $id ); ?>-other_costs">Other Costs</label><input id="<?php echo esc_attr( $id ); ?>-other_costs" name="other_costs" type="number" min="0" step="0.01" value="0"></div>
          <div class="dm-roi-dynamic-field" data-field="impressions" hidden><label for="<?php echo esc_attr( $id ); ?>-impressions">Impressions</label><input id="<?php echo esc_attr( $id ); ?>-impressions" name="impressions" type="number" min="0" step="1" value="0"></div>
          <div class="dm-roi-dynamic-field" data-field="cpc" hidden><label for="<?php echo esc_attr( $id ); ?>-cpc">Average CPC / Cost Per Click</label><input id="<?php echo esc_attr( $id ); ?>-cpc" name="cpc" type="number" min="0" step="0.01" value="20"></div>
          <div class="dm-roi-dynamic-field" data-field="ctr_rate" hidden><label for="<?php echo esc_attr( $id ); ?>-ctr_rate">Expected CTR / Click Rate (%)</label><input id="<?php echo esc_attr( $id ); ?>-ctr_rate" name="ctr_rate" type="number" min="0" step="0.01" value="2"></div>
          <div class="dm-roi-dynamic-field" data-field="cpm_input" hidden><label for="<?php echo esc_attr( $id ); ?>-cpm_input">Expected CPM</label><input id="<?php echo esc_attr( $id ); ?>-cpm_input" name="cpm_input" type="number" min="0" step="0.01" value="100"></div>
          <div class="dm-roi-dynamic-field" data-field="conversion_rate" hidden><label for="<?php echo esc_attr( $id ); ?>-conversion_rate">Conversion Rate (%)</label><input id="<?php echo esc_attr( $id ); ?>-conversion_rate" name="conversion_rate" type="number" min="0" step="0.01" value="3"></div>
          <div class="dm-roi-dynamic-field" data-field="value" hidden><label for="<?php echo esc_attr( $id ); ?>-value">Revenue Per Conversion / Order Value</label><input id="<?php echo esc_attr( $id ); ?>-value" name="value" type="number" min="0" step="0.01" value="5000"></div>
          <div class="dm-roi-dynamic-field" data-field="clicks" hidden><label for="<?php echo esc_attr( $id ); ?>-clicks">Clicks</label><input id="<?php echo esc_attr( $id ); ?>-clicks" name="clicks" type="number" min="0" step="1" value="0"></div>
          <div class="dm-roi-dynamic-field" data-field="visits" hidden><label for="<?php echo esc_attr( $id ); ?>-visits">Visits / Sessions</label><input id="<?php echo esc_attr( $id ); ?>-visits" name="visits" type="number" min="0" step="1" value="0"></div>
          <div class="dm-roi-dynamic-field" data-field="conversions" hidden><label for="<?php echo esc_attr( $id ); ?>-conversions">Conversions / Orders</label><input id="<?php echo esc_attr( $id ); ?>-conversions" name="conversions" type="number" min="0" step="1" value="0"></div>
          <div class="dm-roi-dynamic-field" data-field="revenue" hidden><label for="<?php echo esc_attr( $id ); ?>-revenue">Attributed Revenue / Sales</label><input id="<?php echo esc_attr( $id ); ?>-revenue" name="revenue" type="number" min="0" step="0.01" value="0"></div>
          <div class="dm-roi-dynamic-field" data-field="seo_clicks" hidden><label for="<?php echo esc_attr( $id ); ?>-seo_clicks">Organic Search Clicks</label><input id="<?php echo esc_attr( $id ); ?>-seo_clicks" name="seo_clicks" type="number" min="0" step="1" value="0"></div>
          <div class="dm-roi-dynamic-field" data-field="organic_sessions" hidden><label for="<?php echo esc_attr( $id ); ?>-organic_sessions">Organic Sessions / Visits</label><input id="<?php echo esc_attr( $id ); ?>-organic_sessions" name="organic_sessions" type="number" min="0" step="1" value="0"></div>
          <div class="dm-roi-dynamic-field" data-field="avg_position" hidden><label for="<?php echo esc_attr( $id ); ?>-avg_position">Average Organic Position (optional)</label><input id="<?php echo esc_attr( $id ); ?>-avg_position" name="avg_position" type="number" min="0" step="0.01" value="0"></div>
          <div class="dm-roi-dynamic-field" data-field="emails_sent" hidden><label for="<?php echo esc_attr( $id ); ?>-emails_sent">Emails Sent</label><input id="<?php echo esc_attr( $id ); ?>-emails_sent" name="emails_sent" type="number" min="0" step="1" value="10000"></div>
          <div class="dm-roi-dynamic-field" data-field="emails_delivered" hidden><label for="<?php echo esc_attr( $id ); ?>-emails_delivered">Emails Delivered</label><input id="<?php echo esc_attr( $id ); ?>-emails_delivered" name="emails_delivered" type="number" min="0" step="1" value="9500"></div>
          <div class="dm-roi-dynamic-field" data-field="email_opens" hidden><label for="<?php echo esc_attr( $id ); ?>-email_opens">Unique Opens</label><input id="<?php echo esc_attr( $id ); ?>-email_opens" name="email_opens" type="number" min="0" step="1" value="3000"></div>
          <div class="dm-roi-dynamic-field" data-field="email_clicks" hidden><label for="<?php echo esc_attr( $id ); ?>-email_clicks">Unique Email Clicks</label><input id="<?php echo esc_attr( $id ); ?>-email_clicks" name="email_clicks" type="number" min="0" step="1" value="500"></div>
          <div class="dm-roi-dynamic-field" data-field="orders" hidden><label for="<?php echo esc_attr( $id ); ?>-orders">Orders</label><input id="<?php echo esc_attr( $id ); ?>-orders" name="orders" type="number" min="0" step="1" value="0"></div>
          <div class="dm-roi-dynamic-field" data-field="qualified_leads" hidden><label for="<?php echo esc_attr( $id ); ?>-qualified_leads">Qualified Leads</label><input id="<?php echo esc_attr( $id ); ?>-qualified_leads" name="qualified_leads" type="number" min="0" step="1" value="0"></div>
          <div class="dm-roi-dynamic-field" data-field="customers" hidden><label for="<?php echo esc_attr( $id ); ?>-customers">Customers Acquired</label><input id="<?php echo esc_attr( $id ); ?>-customers" name="customers" type="number" min="0" step="1" value="0"></div>
          <div class="dm-roi-dynamic-field" data-field="leads" hidden><label for="<?php echo esc_attr( $id ); ?>-leads">Leads Generated</label><input id="<?php echo esc_attr( $id ); ?>-leads" name="leads" type="number" min="0" step="1" value="0"></div>
          <div class="dm-roi-dynamic-field" data-field="video_views" hidden><label for="<?php echo esc_attr( $id ); ?>-video_views">Video Views</label><input id="<?php echo esc_attr( $id ); ?>-video_views" name="video_views" type="number" min="0" step="1" value="0"></div>
          <div class="dm-roi-dynamic-field" data-field="video_completions" hidden><label for="<?php echo esc_attr( $id ); ?>-video_completions">Video Completions</label><input id="<?php echo esc_attr( $id ); ?>-video_completions" name="video_completions" type="number" min="0" step="1" value="0"></div>
          <div class="dm-roi-dynamic-field" data-field="reach" hidden><label for="<?php echo esc_attr( $id ); ?>-reach">Reach</label><input id="<?php echo esc_attr( $id ); ?>-reach" name="reach" type="number" min="0" step="1" value="0"></div>
          <div class="dm-roi-dynamic-field" data-field="engagements" hidden><label for="<?php echo esc_attr( $id ); ?>-engagements">Engagements / Saves</label><input id="<?php echo esc_attr( $id ); ?>-engagements" name="engagements" type="number" min="0" step="1" value="0"></div>
          <div class="dm-roi-dynamic-field" data-field="commission_rate" hidden><label for="<?php echo esc_attr( $id ); ?>-commission_rate">Commission Rate (%)</label><input id="<?php echo esc_attr( $id ); ?>-commission_rate" name="commission_rate" type="number" min="0" step="0.01" value="10"></div>
          <div class="dm-roi-dynamic-field" data-field="cogs" hidden><label for="<?php echo esc_attr( $id ); ?>-cogs">Cost of Goods Sold</label><input id="<?php echo esc_attr( $id ); ?>-cogs" name="cogs" type="number" min="0" step="0.01" value="0"></div>
          <div class="dm-roi-dynamic-field" data-field="refunds" hidden><label for="<?php echo esc_attr( $id ); ?>-refunds">Refunds / Discounts</label><input id="<?php echo esc_attr( $id ); ?>-refunds" name="refunds" type="number" min="0" step="0.01" value="0"></div>
          <div class="dm-roi-dynamic-field" data-field="fees" hidden><label for="<?php echo esc_attr( $id ); ?>-fees">Shipping / Payment / Network Fees</label><input id="<?php echo esc_attr( $id ); ?>-fees" name="fees" type="number" min="0" step="0.01" value="0"></div>
          <div class="dm-roi-dynamic-field" data-field="gross_margin" hidden><label for="<?php echo esc_attr( $id ); ?>-gross_margin">Gross Margin (%)</label><input id="<?php echo esc_attr( $id ); ?>-gross_margin" name="gross_margin" type="number" min="0" step="0.01" value="40"></div>
          <div class="dm-roi-dynamic-field" data-field="lead_to_customer" hidden><label for="<?php echo esc_attr( $id ); ?>-lead_to_customer">Lead-to-Customer Rate (%)</label><input id="<?php echo esc_attr( $id ); ?>-lead_to_customer" name="lead_to_customer" type="number" min="0" step="0.01" value="10"></div>
          <div class="dm-roi-dynamic-field" data-field="qualified_rate" hidden><label for="<?php echo esc_attr( $id ); ?>-qualified_rate">Lead Qualification Rate (%)</label><input id="<?php echo esc_attr( $id ); ?>-qualified_rate" name="qualified_rate" type="number" min="0" step="0.01" value="30"></div>
          <div class="dm-roi-dynamic-field" data-field="delivery_rate" hidden><label for="<?php echo esc_attr( $id ); ?>-delivery_rate">Expected Delivery Rate (%)</label><input id="<?php echo esc_attr( $id ); ?>-delivery_rate" name="delivery_rate" type="number" min="0" step="0.01" value="95"></div>
          <div class="dm-roi-dynamic-field" data-field="open_rate" hidden><label for="<?php echo esc_attr( $id ); ?>-open_rate">Expected Open Rate (%)</label><input id="<?php echo esc_attr( $id ); ?>-open_rate" name="open_rate" type="number" min="0" step="0.01" value="30"></div>
          <div><label for="<?php echo esc_attr( $id ); ?>-campaign"><?php esc_html_e( 'Campaign Name', 'tech-akhil-marketing-roi-calculator' ); ?></label><input id="<?php echo esc_attr( $id ); ?>-campaign" name="campaign" type="text" maxlength="100" placeholder="e.g. Summer Campaign"></div>
          <div class="dm-roi-full"><label for="<?php echo esc_attr( $id ); ?>-name"><?php esc_html_e( 'Your Name', 'tech-akhil-marketing-roi-calculator' ); ?></label><input id="<?php echo esc_attr( $id ); ?>-name" name="name" autocomplete="name" maxlength="100" required></div>
          <div><label for="<?php echo esc_attr( $id ); ?>-email"><?php esc_html_e( 'Email Address', 'tech-akhil-marketing-roi-calculator' ); ?></label><input id="<?php echo esc_attr( $id ); ?>-email" name="email" type="email" autocomplete="email" maxlength="100" required></div>
          <div><label for="<?php echo esc_attr( $id ); ?>-phone"><?php esc_html_e( 'Phone Number', 'tech-akhil-marketing-roi-calculator' ); ?></label><input id="<?php echo esc_attr( $id ); ?>-phone" name="phone" type="tel" autocomplete="tel" maxlength="30" required></div>
          <div class="dm-roi-full"><button type="button" class="dm-roi-btn dm-roi-calculate"><?php echo esc_html( $s['button_text'] ); ?></button><p class="dm-roi-status" aria-live="polite"></p></div>
        </div>
      </form>
      <section class="dm-roi-results" hidden aria-live="polite"><h3 class="dm-roi-results-title"><?php esc_html_e( 'Campaign Performance Results', 'tech-akhil-marketing-roi-calculator' ); ?></h3><p class="dm-roi-results-platform" aria-live="polite"></p><div class="dm-roi-metrics">
        <div class="dm-roi-metric"><span><?php esc_html_e( 'Estimated Clicks', 'tech-akhil-marketing-roi-calculator' ); ?></span><strong data-metric="clicks">—</strong></div>
        <div class="dm-roi-metric"><span><?php esc_html_e( 'Estimated Conversions', 'tech-akhil-marketing-roi-calculator' ); ?></span><strong data-metric="conversions">—</strong></div>
        <div class="dm-roi-metric"><span><?php esc_html_e( 'Estimated Revenue', 'tech-akhil-marketing-roi-calculator' ); ?></span><strong data-metric="revenue">—</strong></div>
        <div class="dm-roi-metric"><span><?php esc_html_e( 'Estimated Profit', 'tech-akhil-marketing-roi-calculator' ); ?></span><strong data-metric="profit">—</strong></div>
        <div class="dm-roi-metric"><span><?php esc_html_e( 'ROI', 'tech-akhil-marketing-roi-calculator' ); ?></span><strong data-metric="roi">—</strong></div>
        <div class="dm-roi-metric"><span><?php esc_html_e( 'ROAS', 'tech-akhil-marketing-roi-calculator' ); ?></span><strong data-metric="roas">—</strong></div>
        <div class="dm-roi-metric"><span><?php esc_html_e( 'Cost Per Conversion', 'tech-akhil-marketing-roi-calculator' ); ?></span><strong data-metric="cpl">—</strong></div>
        <div class="dm-roi-metric"><span><?php esc_html_e( 'Break-even ROAS', 'tech-akhil-marketing-roi-calculator' ); ?></span><strong data-metric="break_even">—</strong></div>
        <div class="dm-roi-metric"><span><?php esc_html_e( 'Total Cost', 'tech-akhil-marketing-roi-calculator' ); ?></span><strong data-metric="total_cost">—</strong></div>
        <div class="dm-roi-metric"><span><?php esc_html_e( 'Cost Per Acquisition (CPA)', 'tech-akhil-marketing-roi-calculator' ); ?></span><strong data-metric="cpa">—</strong></div>
        <div class="dm-roi-metric"><span><?php esc_html_e( 'Click-through Rate (CTR)', 'tech-akhil-marketing-roi-calculator' ); ?></span><strong data-metric="ctr">—</strong></div>
        <div class="dm-roi-metric"><span><?php esc_html_e( 'Cost Per 1,000 Impressions (CPM)', 'tech-akhil-marketing-roi-calculator' ); ?></span><strong data-metric="cpm">—</strong></div>
        <div class="dm-roi-metric"><span><?php esc_html_e( 'Profit Margin', 'tech-akhil-marketing-roi-calculator' ); ?></span><strong data-metric="margin">—</strong></div>
        <div class="dm-roi-metric" data-output="impressions" hidden><span>Impressions</span><strong data-metric="impressions">—</strong></div>
        <div class="dm-roi-metric" data-output="visits" hidden><span>Visits / Sessions</span><strong data-metric="visits">—</strong></div>
        <div class="dm-roi-metric" data-output="qualified_leads" hidden><span>Qualified Leads</span><strong data-metric="qualified_leads">—</strong></div>
        <div class="dm-roi-metric" data-output="customers" hidden><span>Customers Acquired</span><strong data-metric="customers">—</strong></div>
        <div class="dm-roi-metric" data-output="orders" hidden><span>Orders</span><strong data-metric="orders">—</strong></div>
        <div class="dm-roi-metric" data-output="delivery_rate" hidden><span>Email Delivery Rate</span><strong data-metric="delivery_rate">—</strong></div>
        <div class="dm-roi-metric" data-output="open_rate" hidden><span>Email Open Rate</span><strong data-metric="open_rate">—</strong></div>
        <div class="dm-roi-metric" data-output="click_rate" hidden><span>Click Rate</span><strong data-metric="click_rate">—</strong></div>
        <div class="dm-roi-metric" data-output="average_position" hidden><span>Average Organic Position</span><strong data-metric="average_position">—</strong></div>
        <div class="dm-roi-metric" data-output="commission" hidden><span>Affiliate Commission</span><strong data-metric="commission">—</strong></div>
        <div class="dm-roi-metric" data-output="gross_profit" hidden><span>Gross Profit</span><strong data-metric="gross_profit">—</strong></div>
        <div class="dm-roi-metric" data-output="reach" hidden><span>Reach</span><strong data-metric="reach">—</strong></div>
        <div class="dm-roi-metric" data-output="video_views" hidden><span>Video Views</span><strong data-metric="video_views">—</strong></div>
        <div class="dm-roi-metric" data-output="video_completion_rate" hidden><span>Video Completion Rate</span><strong data-metric="video_completion_rate">—</strong></div>
        <div class="dm-roi-metric" data-output="engagements" hidden><span>Engagements / Saves</span><strong data-metric="engagements">—</strong></div>
        <div class="dm-roi-metric" data-output="emails_delivered" hidden><span>Emails Delivered</span><strong data-metric="emails_delivered">—</strong></div>
        <div class="dm-roi-metric" data-output="email_opens" hidden><span>Unique Email Opens</span><strong data-metric="email_opens">—</strong></div>
      </div><div class="dm-roi-actions"><?php if ( $s['show_pdf'] === '1' ) : ?><button type="button" class="dm-roi-btn dm-roi-print"><?php esc_html_e( 'Print / Save PDF', 'tech-akhil-marketing-roi-calculator' ); ?></button><?php endif; ?><a class="dm-roi-btn dm-roi-whatsapp" href="#" target="_blank" rel="noopener noreferrer" hidden><?php esc_html_e( 'Discuss on WhatsApp', 'tech-akhil-marketing-roi-calculator' ); ?></a></div></section>
    </div>
    <?php wp_enqueue_style( 'dm-roi-frontend', plugins_url( 'assets/dm-roi-frontend.css', __FILE__ ), array(), DM_ROI_VERSION ); wp_enqueue_script( 'dm-roi-frontend', plugins_url( 'assets/dm-roi-frontend.js', __FILE__ ), array(), DM_ROI_VERSION, true ); ?>

    <?php
    return ob_get_clean();
}
add_shortcode( 'dm_roi_calculator', 'dm_roi_shortcode' );


function dm_roi_plugin_action_links( $links ) {
    $settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=dm-roi-settings' ) ) . '">' . esc_html__( 'Settings', 'tech-akhil-marketing-roi-calculator' ) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'dm_roi_plugin_action_links' );

function dm_roi_admin_menu() {
    add_menu_page( 'Tech Akhil ROI Settings', 'TechAkhil ROI', 'manage_options', 'dm-roi-settings', 'dm_roi_settings_page', 'dashicons-chart-line', 30 );
    add_submenu_page( 'dm-roi-settings', 'Settings', 'Settings', 'manage_options', 'dm-roi-settings', 'dm_roi_settings_page' );
    add_submenu_page( 'dm-roi-settings', 'Leads', 'Leads', 'manage_options', 'dm-roi-leads', 'dm_roi_leads_page' );
    add_submenu_page( 'dm-roi-settings', 'Setup Documents', 'Setup Documents', 'manage_options', 'dm-roi-setup', 'dm_roi_setup_page' );
}
function dm_roi_admin_assets( $hook ) {
    // Use WordPress's admin page hook instead of reading the page from $_GET.
    // This avoids treating a navigation parameter as submitted form data.
    $allowed_hooks = array(
        'toplevel_page_dm-roi-settings',
        'dm-roi-settings_page_dm-roi-leads',
        'dm-roi-settings_page_dm-roi-setup',
        'dm-roi-settings_page_dm-roi-settings',
        'techakhil-roi_page_dm-roi-leads',
        'techakhil-roi_page_dm-roi-setup',
        'techakhil-roi_page_dm-roi-settings',
        'toplevel_page_dm-roi-leads',
        'roi-leads_page_dm-roi-setup',
        'roi-leads_page_dm-roi-settings',
    );
    if ( ! in_array( $hook, $allowed_hooks, true ) ) {
        return;
    }
    wp_enqueue_style( 'dm-roi-admin', plugins_url( 'assets/dm-roi-admin.css', __FILE__ ), array(), DM_ROI_VERSION );
    wp_enqueue_script( 'dm-roi-admin', plugins_url( 'assets/dm-roi-admin.js', __FILE__ ), array(), DM_ROI_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'dm_roi_admin_assets' );

add_action( 'admin_menu', 'dm_roi_admin_menu' );

function dm_roi_admin_brand_footer() {
    echo '<div class="dm-roi-brand-footer"><span class="dm-roi-brand-mark">TA</span><span><strong>Tech Akhil</strong><small>Marketing tools for smarter decisions</small></span><a href="https://techakhil.com/" target="_blank" rel="noopener noreferrer">techakhil.com <span aria-hidden="true">↗</span></a></div>';
}

function dm_roi_setup_page() {
    if ( ! current_user_can( 'manage_options' ) ) { return; }
    ?>
    <div class="wrap dm-roi-docs-wrap">
      <header class="dm-roi-docs-hero"><div class="dm-roi-docs-brand"><span class="dm-roi-docs-logo">TA</span><span>TECH AKHIL <small>PLUGIN GUIDE</small></span></div><h1><?php esc_html_e( 'Tech Akhil Marketing ROI Calculator', 'tech-akhil-marketing-roi-calculator' ); ?></h1><p>Setup, publish, customize and troubleshoot your calculator from one place.</p><a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=dm-roi-settings' ) ); ?>">Open Calculator Settings</a></header>
      <section class="dm-roi-docs-card"><div class="dm-roi-docs-card-title"><span class="dashicons dashicons-shortcode"></span><div><h2>Quick setup</h2><p>Copy this shortcode to embed the calculator with all enabled templates.</p></div></div><div class="dm-roi-docs-copy"><code>[dm_roi_calculator]</code><button type="button" class="button button-primary" data-copy-shortcode="[dm_roi_calculator]">Copy shortcode</button><span class="dm-roi-copy-status" aria-live="polite"></span></div></section>
      <div class="dm-roi-docs-grid">
        <section class="dm-roi-docs-card"><div class="dm-roi-docs-card-title"><span class="dashicons dashicons-wordpress"></span><div><h2>Add to WordPress</h2><p>Block Editor / Gutenberg</p></div></div><ol><li>Go to <strong>Pages → Add New</strong>, or edit an existing page.</li><li>Click the <strong>+</strong> button and add a <strong>Shortcode</strong> block.</li><li>Paste <code>[dm_roi_calculator]</code>.</li><li>Publish or update the page, then open the public page to test it.</li></ol></section>
        <section class="dm-roi-docs-card"><div class="dm-roi-docs-card-title"><span class="dashicons dashicons-layout"></span><div><h2>Add to Elementor</h2><p>Elementor page builder</p></div></div><ol><li>Open the page and choose <strong>Edit with Elementor</strong>.</li><li>Search for the <strong>Shortcode</strong> widget.</li><li>Drag it to the required section and paste <code>[dm_roi_calculator]</code>.</li><li>Click <strong>Update</strong>, then preview the page on desktop and mobile.</li></ol></section>
        <section class="dm-roi-docs-card"><div class="dm-roi-docs-card-title"><span class="dashicons dashicons-admin-settings"></span><div><h2>Manage settings</h2><p>Templates, colours and sharing</p></div></div><ol><li>Open <strong>TechAkhil ROI → Settings</strong>.</li><li>In <strong>Templates</strong>, enable/disable templates and set each template colour.</li><li>In <strong>Style</strong>, adjust the main colour, card background, theme and button shape.</li><li>In <strong>WhatsApp &amp; Sharing</strong> and <strong>Extras</strong>, configure optional features, then click <strong>Save Settings</strong>.</li></ol><a href="<?php echo esc_url( admin_url( 'admin.php?page=dm-roi-settings' ) ); ?>">Go to Settings ↗</a></section>
        <section class="dm-roi-docs-card"><div class="dm-roi-docs-card-title"><span class="dashicons dashicons-chart-bar"></span><div><h2>View leads and reports</h2><p>Filter and export submissions</p></div></div><ol><li>Open <strong>TechAkhil ROI → Leads</strong>.</li><li>Choose start/end dates or a template, or search by name, email, phone or campaign.</li><li>Click <strong>Apply filters</strong> to update the table.</li><li>Click <strong>Export CSV</strong> to download the currently filtered records and open them in Excel.</li></ol><a href="<?php echo esc_url( admin_url( 'admin.php?page=dm-roi-leads' ) ); ?>">Open Leads ↗</a></section>
        <section class="dm-roi-docs-card dm-roi-docs-card-wide"><div class="dm-roi-docs-card-title"><span class="dashicons dashicons-editor-code"></span><div><h2>Individual template shortcodes</h2><p>These are managed in Settings → Templates. Each individual shortcode shows only that template; it does not show the template dropdown.</p></div></div><p>Use the copy buttons beside a shortcode in Template Management. The common shortcode above displays all currently enabled templates.</p><a href="<?php echo esc_url( admin_url( 'admin.php?page=dm-roi-settings' ) ); ?>">Manage templates ↗</a></section>
        <section class="dm-roi-docs-card dm-roi-docs-card-wide"><div class="dm-roi-docs-card-title"><span class="dashicons dashicons-sos"></span><div><h2>Quick troubleshooting</h2><p>If the calculator does not look updated or a report appears incorrect</p></div></div><ul><li>After changing settings, save them and refresh the public page.</li><li>If an old design remains, clear your WordPress/cache plugin/CDN cache and hard-refresh the browser.</li><li>Test a shortcode on a staging page before replacing a live page.</li><li>CSV exports should contain column headers and lead rows, not WordPress HTML. If not, note the selected filters and share the downloaded file for diagnosis.</li></ul></section>
      </div>
      <?php dm_roi_admin_brand_footer(); ?>
    </div>
    <?php
}

function dm_roi_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) { return; }
    $s = dm_roi_get_settings();
    if ( isset( $_POST['dm_roi_save_settings'] ) ) {
        check_admin_referer( 'dm_roi_settings_save', 'dm_roi_settings_nonce' );
        $primary = isset( $_POST['primary_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['primary_color'] ) ) : '#9d174d';
        $card = isset( $_POST['card_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['card_color'] ) ) : '#ffffff';
        $currency = isset( $_POST['currency'] ) ? sanitize_text_field( wp_unslash( $_POST['currency'] ) ) : 'INR';
        if ( ! in_array( $currency, array( 'INR','USD','GBP','EUR' ), true ) ) { $currency = 'INR'; }
        $theme_raw = isset( $_POST['theme'] ) ? sanitize_key( wp_unslash( $_POST['theme'] ) ) : 'light';
        $theme = 'dark' === $theme_raw ? 'dark' : 'light';
        $radius = isset( $_POST['button_radius'] ) ? sanitize_key( wp_unslash( $_POST['button_radius'] ) ) : 'rounded';
        if ( ! in_array( $radius, array( 'rounded','square','pill' ), true ) ) { $radius = 'rounded'; }
        $s = array(
            'primary_color' => $primary ?: '#9d174d', 'card_color' => $card ?: '#ffffff',
            'button_text' => isset( $_POST['button_text'] ) ? sanitize_text_field( wp_unslash( $_POST['button_text'] ) ) : 'Calculate My ROI',
            'title' => isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : 'Digital Marketing ROI Calculator',
            'currency' => $currency, 'theme' => $theme, 'button_radius' => $radius,
            'wa_enable' => isset( $_POST['wa_enable'] ) ? '1' : '0',
            'wa_number' => isset( $_POST['wa_number'] ) ? preg_replace( '/[^0-9]/', '', sanitize_text_field( wp_unslash( $_POST['wa_number'] ) ) ) : '',
            'wa_message' => isset( $_POST['wa_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['wa_message'] ) ) : '',
            'show_comparison' => isset( $_POST['show_comparison'] ) ? '1' : '0', 'show_pdf' => isset( $_POST['show_pdf'] ) ? '1' : '0',
            'lead_position' => 'before',
            'enabled_templates' => array_values( array_intersect( array_keys( dm_roi_templates() ), isset( $_POST['enabled_templates'] ) && is_array( $_POST['enabled_templates'] ) ? array_map( 'sanitize_key', wp_unslash( $_POST['enabled_templates'] ) ) : array() ) ),
            'template_colors' => array(),
            'powered_by' => isset( $_POST['powered_by'] ) ? '1' : '0',
        );
        foreach ( dm_roi_templates() as $slug => $template_def ) {
            $raw_color = isset( $_POST['template_colors'][ $slug ] ) ? sanitize_hex_color( wp_unslash( $_POST['template_colors'][ $slug ] ) ) : '';
            $s['template_colors'][ $slug ] = $raw_color ?: $template_def['color'];
        }
        update_option( 'dm_roi_settings', $s );
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Settings saved.', 'tech-akhil-marketing-roi-calculator' ) . '</p></div>';
    }
    ?>
    <div class="wrap dm-roi-settings-wrap">
      <div class="dm-roi-settings-top"><div><h1><?php esc_html_e( 'Tech Akhil Marketing ROI Calculator Settings', 'tech-akhil-marketing-roi-calculator' ); ?></h1><p>Manage calculator appearance, templates, lead sharing and display options from one place.</p></div><button type="submit" form="dm-roi-settings-form" name="dm_roi_save_settings" value="1" class="button button-primary dm-roi-top-save">Save Settings</button></div>
      <nav class="dm-roi-settings-tabs" aria-label="Settings sections" role="tablist">
        <button type="button" class="dm-roi-tab is-active" role="tab" aria-selected="true" data-panel="general">⚙ General</button>
        <button type="button" class="dm-roi-tab" role="tab" aria-selected="false" data-panel="templates">✦ Templates</button>
        <button type="button" class="dm-roi-tab" role="tab" aria-selected="false" data-panel="style">◈ Style</button>
        <button type="button" class="dm-roi-tab" role="tab" aria-selected="false" data-panel="whatsapp">↗ WhatsApp & Sharing</button>
        <button type="button" class="dm-roi-tab" role="tab" aria-selected="false" data-panel="extras">＋ Extras</button>
      </nav>
      <form id="dm-roi-settings-form" method="post"><?php wp_nonce_field( 'dm_roi_settings_save', 'dm_roi_settings_nonce' ); ?>
        <section class="dm-roi-settings-panel is-active" data-panel-content="general" role="tabpanel"><div class="dm-roi-panel-heading"><div><h2>General settings</h2><p>Set the calculator title, main button label and currency.</p></div></div><div class="dm-roi-settings-grid">
          <label class="dm-roi-setting-card"><span>Calculator title</span><input id="dm-title" name="title" value="<?php echo esc_attr( $s['title'] ); ?>" maxlength="120"><small>Displayed at the top of the calculator.</small></label>
          <label class="dm-roi-setting-card"><span>Calculate button label</span><input id="dm-button-text" name="button_text" value="<?php echo esc_attr( $s['button_text'] ); ?>" maxlength="60"><small>Text shown on the main calculation button.</small></label>
          <label class="dm-roi-setting-card"><span>Currency</span><select id="dm-currency" name="currency"><?php foreach ( array( 'INR'=>'Indian Rupee (₹)','USD'=>'US Dollar ($)','GBP'=>'British Pound (£)','EUR'=>'Euro (€)' ) as $k=>$v ) : ?><option value="<?php echo esc_attr( $k ); ?>" <?php selected( $s['currency'], $k ); ?>><?php echo esc_html( $v ); ?></option><?php endforeach; ?></select><small>Used for monetary values in the calculator.</small></label>
        </div></section>
        <section class="dm-roi-settings-panel" data-panel-content="templates" role="tabpanel" hidden><div class="dm-roi-panel-heading"><div><h2>Template management</h2><p>Enable or disable calculator templates and customize each template's accent colour.</p></div><div class="dm-roi-bulk-actions"><button type="button" class="button" data-template-bulk="enable">Enable all</button><button type="button" class="button" data-template-bulk="disable">Disable all</button></div></div><div class="dm-roi-template-grid">
          <?php foreach ( dm_roi_templates() as $slug => $template_def ) : $template_on = in_array( $slug, (array) $s['enabled_templates'], true ); ?>
          <article class="dm-roi-template-card"><div class="dm-roi-template-card-top"><span class="dm-roi-template-icon" aria-hidden="true"><?php echo esc_html( $template_def['icon'] ); ?></span><label class="dm-roi-template-toggle"><input type="checkbox" name="enabled_templates[]" value="<?php echo esc_attr( $slug ); ?>" <?php checked( $template_on ); ?>><span class="dm-roi-toggle-track"><span></span></span><span class="screen-reader-text">Enable <?php echo esc_html( $template_def['label'] ); ?></span></label></div><h3><?php echo esc_html( $template_def['label'] ); ?></h3><p><?php echo esc_html( $template_def['label'] . ' calculator template' ); ?></p><div class="dm-roi-template-color"><label for="dm-color-<?php echo esc_attr( $slug ); ?>">Accent colour</label><input id="dm-color-<?php echo esc_attr( $slug ); ?>" type="color" name="template_colors[<?php echo esc_attr( $slug ); ?>]" value="<?php echo esc_attr( $s['template_colors'][ $slug ] ); ?>"></div><div class="dm-roi-shortcode-copy-row"><code>[dm_roi_calculator template="<?php echo esc_html( $slug ); ?>"]</code><button type="button" class="button dm-roi-copy-template" data-copy-shortcode="[dm_roi_calculator template=&quot;<?php echo esc_attr( $slug ); ?>&quot;]">Copy</button><span class="dm-roi-copy-status" aria-live="polite"></span></div></article>
          <?php endforeach; ?>
        </div><p class="description">The common shortcode <code>[dm_roi_calculator]</code> shows enabled templates. Individual shortcodes display only their selected template.</p></section>
        <section class="dm-roi-settings-panel" data-panel-content="style" role="tabpanel" hidden><div class="dm-roi-panel-heading"><div><h2>Style & appearance</h2><p>Choose the overall calculator colour, card background, theme and button shape.</p></div></div><div class="dm-roi-settings-grid">
          <label class="dm-roi-setting-card"><span>Primary / button colour</span><input id="dm-primary" type="color" name="primary_color" value="<?php echo esc_attr( $s['primary_color'] ); ?>"><small>Used for primary actions and highlights.</small></label>
          <label class="dm-roi-setting-card"><span>Card background</span><input id="dm-card" type="color" name="card_color" value="<?php echo esc_attr( $s['card_color'] ); ?>"><small>Background colour for calculator cards.</small></label>
          <label class="dm-roi-setting-card"><span>Theme</span><select id="dm-theme" name="theme"><option value="light" <?php selected( $s['theme'], 'light' ); ?>>Light</option><option value="dark" <?php selected( $s['theme'], 'dark' ); ?>>Dark</option></select><small>Choose a light or dark calculator theme.</small></label>
          <label class="dm-roi-setting-card"><span>Button shape</span><select id="dm-radius" name="button_radius"><option value="rounded" <?php selected( isset( $s['button_radius'] ) ? $s['button_radius'] : 'rounded', 'rounded' ); ?>>Rounded</option><option value="square" <?php selected( isset( $s['button_radius'] ) ? $s['button_radius'] : 'rounded', 'square' ); ?>>Square</option><option value="pill" <?php selected( isset( $s['button_radius'] ) ? $s['button_radius'] : 'rounded', 'pill' ); ?>>Pill</option></select><small>Controls the shape of calculator buttons.</small></label>
        </div><div class="dm-roi-live-preview"><h3>Live preview</h3><div id="dm-roi-preview" style="background:<?php echo esc_attr( $s['card_color'] ); ?>"><h3 id="dm-roi-preview-title" style="color:<?php echo esc_attr( $s['primary_color'] ); ?>"><?php echo esc_html( $s['title'] ); ?></h3><button type="button" id="dm-roi-preview-button" class="button button-primary" style="background:<?php echo esc_attr( $s['primary_color'] ); ?>;border-color:<?php echo esc_attr( $s['primary_color'] ); ?>;border-radius:<?php echo isset( $s['button_radius'] ) && $s['button_radius'] === 'pill' ? '999px' : ( isset( $s['button_radius'] ) && $s['button_radius'] === 'square' ? '3px' : '8px' ); ?>"><?php echo esc_html( $s['button_text'] ); ?></button></div></div></section>
        <section class="dm-roi-settings-panel" data-panel-content="whatsapp" role="tabpanel" hidden><div class="dm-roi-panel-heading"><div><h2>WhatsApp & sharing</h2><p>Configure the WhatsApp follow-up shown after a successful lead submission.</p></div></div><div class="dm-roi-settings-grid">
          <div class="dm-roi-setting-card"><label><input type="checkbox" name="wa_enable" value="1" <?php checked( $s['wa_enable'], '1' ); ?>> <strong>Enable WhatsApp link</strong></label><small>Show the WhatsApp action after a successful lead save.</small></div>
          <label class="dm-roi-setting-card"><span>WhatsApp phone number</span><input id="dm-wa-number" name="wa_number" value="<?php echo esc_attr( $s['wa_number'] ); ?>" placeholder="919876543210"><small>Country code plus number, digits only.</small></label>
          <label class="dm-roi-setting-card dm-roi-card-wide"><span>WhatsApp message</span><textarea id="dm-wa-message" rows="3" name="wa_message"><?php echo esc_textarea( $s['wa_message'] ); ?></textarea><small>Prefilled message opened in WhatsApp.</small></label>
        </div></section>
        <section class="dm-roi-settings-panel" data-panel-content="extras" role="tabpanel" hidden><div class="dm-roi-panel-heading"><div><h2>Extra options</h2><p>Control optional calculator actions and branding.</p></div></div><div class="dm-roi-settings-grid">
          <div class="dm-roi-setting-card"><label><input type="checkbox" name="show_pdf" value="1" <?php checked( $s['show_pdf'], '1' ); ?>> <strong>Print / Save PDF</strong></label><small>Show the browser print and Save as PDF button.</small></div>
          <div class="dm-roi-setting-card"><label><input type="checkbox" name="powered_by" value="1" <?php checked( $s['powered_by'], '1' ); ?>> <strong>Powered by Tech Akhil</strong></label><small>Show an optional Tech Akhil link in the calculator footer.</small></div>
        </div></section>
        <div class="dm-roi-settings-footer"><button type="submit" name="dm_roi_save_settings" value="1" class="button button-primary">Save Settings</button><span>Changes apply to the calculator after saving and refreshing its page.</span></div>
      </form>
      <?php dm_roi_admin_brand_footer(); ?>
    </div>
    <?php
}

function dm_roi_leads_filters() {
    global $wpdb;
    $table = dm_roi_table_name();
    $has_filters = isset( $_GET['s'] ) || isset( $_GET['date_from'] ) || isset( $_GET['date_to'] ) || isset( $_GET['template'] );
    if ( $has_filters && ( ! isset( $_GET['dm_roi_filter_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['dm_roi_filter_nonce'] ) ), 'dm_roi_filter_leads' ) ) ) {
        $search = $from = $to = $template = '';
    } else {
        $search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
        $from = isset( $_GET['date_from'] ) ? sanitize_text_field( wp_unslash( $_GET['date_from'] ) ) : '';
        $to = isset( $_GET['date_to'] ) ? sanitize_text_field( wp_unslash( $_GET['date_to'] ) ) : '';
        $template = isset( $_GET['template'] ) ? sanitize_key( wp_unslash( $_GET['template'] ) ) : '';
    }
    if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $from ) || ! checkdate( (int) substr( $from, 5, 2 ), (int) substr( $from, 8, 2 ), (int) substr( $from, 0, 4 ) ) ) { $from = ''; }
    if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $to ) || ! checkdate( (int) substr( $to, 5, 2 ), (int) substr( $to, 8, 2 ), (int) substr( $to, 0, 4 ) ) ) { $to = ''; }
    $allowed = array_keys( dm_roi_templates() );
    if ( $template && ! in_array( $template, $allowed, true ) ) { $template = ''; }

    // Keep SQL structure fixed; all user-controlled values are passed as prepared replacements.
    $search_like = $search !== '' ? '%' . $wpdb->esc_like( $search ) . '%' : '%%';
    $from_value = $from !== '' ? $from . ' 00:00:00' : '';
    $to_value = $to !== '' ? $to . ' 23:59:59' : '';
    $template_like = $template !== '' ? $wpdb->esc_like( $template . ' - ' ) . '%' : '%';
    $args = array(
        $table,
        $search !== '' ? 1 : 0,
        $search_like, $search_like, $search_like, $search_like, $search_like,
        $from !== '' ? 1 : 0, $from_value,
        $to !== '' ? 1 : 0, $to_value,
        $template !== '' ? 1 : 0, $template, $template_like,
    );
    return array( $args, $search, $from, $to, $template );
}

function dm_roi_csv_line( $values ) {
    $escaped = array();
    foreach ( $values as $value ) {
        $value = (string) dm_roi_safe_cell( $value );
        $escaped[] = '"' . str_replace( '"', '""', $value ) . '"';
    }
    return implode( ',', $escaped ) . "\r\n";
}

function dm_roi_export_csv() {
    if ( ! current_user_can( 'manage_options' ) ) { wp_die( esc_html__( 'You are not allowed to export leads.', 'tech-akhil-marketing-roi-calculator' ), 403 ); }
    check_admin_referer( 'dm_roi_export_csv' );
    global $wpdb;
    list( $query_args ) = dm_roi_leads_filters();
    list( $table, $search_enabled, $search_name, $search_email, $search_phone, $search_campaign, $search_utm_campaign, $from_enabled, $from_value, $to_enabled, $to_value, $template_enabled, $template_value, $template_like ) = $query_args;
    // Custom-table admin export: every dynamic value and identifier is bound to an explicit placeholder.
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- This is an on-demand admin CSV export from a plugin-owned custom table; caching private lead data is inappropriate.
    $rows = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM %i WHERE (%d = 0 OR (name LIKE %s OR email LIKE %s OR phone LIKE %s OR campaign LIKE %s OR utm_campaign LIKE %s)) AND (%d = 0 OR created_at >= %s) AND (%d = 0 OR created_at <= %s) AND (%d = 0 OR (template = %s OR (template = '' AND campaign LIKE %s))) ORDER BY id DESC",
            $table,
            $search_enabled,
            $search_name,
            $search_email,
            $search_phone,
            $search_campaign,
            $search_utm_campaign,
            $from_enabled,
            $from_value,
            $to_enabled,
            $to_value,
            $template_enabled,
            $template_value,
            $template_like
        ),
        ARRAY_A
    );
    nocache_headers();
    header( 'Content-Type: text/csv; charset=utf-8' );
    header( 'Content-Disposition: attachment; filename=tech-akhil-roi-leads-' . gmdate( 'Y-m-d' ) . '.csv' );
    header( 'X-Content-Type-Options: nosniff' );
    echo "\xEF\xBB\xBF"; // UTF-8 BOM for Excel.
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSV fields are quoted and escaped by dm_roi_csv_line(); HTML escaping would corrupt the downloadable CSV.
    echo dm_roi_csv_line( array( 'ID', 'Date', 'Name', 'Email', 'Phone', 'Budget', 'ROI %', 'ROAS', 'Revenue', 'Profit', 'Conversions', 'Campaign', 'Template', 'UTM Source', 'UTM Medium', 'UTM Campaign', 'Landing Page' ) );
    foreach ( (array) $rows as $row ) {
        $template = isset( $row['template'] ) && $row['template'] !== '' ? $row['template'] : ( preg_match( '/^([a-z0-9-]+)\s+-\s+/i', (string) $row['campaign'], $m ) ? $m[1] : '' );
        $data = array( $row['id'] ?? '', ( ! empty( $row['created_at'] ) ? (string) $row['created_at'] . ' (WordPress time)' : '' ), $row['name'] ?? '', $row['email'] ?? '', $row['phone'] ?? '', $row['budget'] ?? '', $row['estimated_roi'] ?? '', $row['estimated_roas'] ?? '', $row['estimated_revenue'] ?? '', $row['estimated_profit'] ?? '', $row['estimated_leads'] ?? '', $row['campaign'] ?? '', $template, $row['source'] ?? '', $row['medium'] ?? '', $row['utm_campaign'] ?? '', $row['landing_page'] ?? '' );
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSV fields are quoted and escaped by dm_roi_csv_line(); HTML escaping would corrupt the downloadable CSV.
        echo dm_roi_csv_line( $data );
    }
    exit;
}
add_action( 'admin_post_dm_roi_export_csv', 'dm_roi_export_csv' );

function dm_roi_leads_page() {
    if ( ! current_user_can( 'manage_options' ) ) { return; }
    global $wpdb;
    list( $query_args, $search, $from, $to, $template ) = dm_roi_leads_filters();
    list( $table, $search_enabled, $search_name, $search_email, $search_phone, $search_campaign, $search_utm_campaign, $from_enabled, $from_value, $to_enabled, $to_value, $template_enabled, $template_value, $template_like ) = $query_args;
    // Custom-table admin list: explicit placeholders bind all dynamic values and the row limit.
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- This is an on-demand admin view of private lead data; caching filtered results is inappropriate.
    $leads = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM %i WHERE (%d = 0 OR (name LIKE %s OR email LIKE %s OR phone LIKE %s OR campaign LIKE %s OR utm_campaign LIKE %s)) AND (%d = 0 OR created_at >= %s) AND (%d = 0 OR created_at <= %s) AND (%d = 0 OR (template = %s OR (template = '' AND campaign LIKE %s))) ORDER BY id DESC LIMIT %d",
            $table,
            $search_enabled,
            $search_name,
            $search_email,
            $search_phone,
            $search_campaign,
            $search_utm_campaign,
            $from_enabled,
            $from_value,
            $to_enabled,
            $to_value,
            $template_enabled,
            $template_value,
            $template_like,
            500
        )
    );
    $export_args = array( 'action' => 'dm_roi_export_csv', 's' => $search, 'date_from' => $from, 'date_to' => $to, 'template' => $template, 'dm_roi_filter_nonce' => wp_create_nonce( 'dm_roi_filter_leads' ) );
    $export_url = wp_nonce_url( add_query_arg( $export_args, admin_url( 'admin-post.php' ) ), 'dm_roi_export_csv' );
    $templates = dm_roi_templates();
    ?>
    <div class="wrap dm-roi-leads-page"><h1>Tech Akhil ROI — Leads <a class="page-title-action" href="<?php echo esc_url( $export_url ); ?>">Export CSV</a></h1><p>Filter saved leads by date range, template or search. The CSV export uses the same filters.</p>
    <form method="get" class="dm-roi-admin-filters"><?php wp_nonce_field( 'dm_roi_filter_leads', 'dm_roi_filter_nonce' ); ?><input type="hidden" name="page" value="dm-roi-leads"><div class="dm-roi-date-row"><div class="dm-roi-filter-field"><label for="dm-roi-date-from"><strong>Start date</strong></label><input id="dm-roi-date-from" type="date" name="date_from" value="<?php echo esc_attr( $from ); ?>"></div><div class="dm-roi-filter-field"><label for="dm-roi-date-to"><strong>End date</strong></label><input id="dm-roi-date-to" type="date" name="date_to" value="<?php echo esc_attr( $to ); ?>"></div><div class="dm-roi-filter-field"><label for="dm-roi-template"><strong>Template</strong></label><select id="dm-roi-template" name="template"><option value="">All templates</option><?php foreach ( $templates as $slug => $def ) : ?><option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $template, $slug ); ?>><?php echo esc_html( $def['label'] ); ?></option><?php endforeach; ?></select></div><button class="button button-primary" type="submit">Apply filters</button><a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=dm-roi-leads' ) ); ?>">Reset filters</a></div><div class="dm-roi-search-row"><div class="dm-roi-filter-field" style="flex:1"><label for="dm-roi-search"><strong>Search leads</strong></label><input id="dm-roi-search" type="search" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="Name, email, phone or campaign"></div><button class="button" type="submit">Search</button></div></form>
    <div class="dm-roi-leads-table-wrap"><table class="wp-list-table widefat striped"><thead><tr><th>Date</th><th>Name</th><th>Email</th><th>Phone</th><th>Template</th><th>Budget</th><th>ROI</th><th>ROAS</th><th>Revenue</th><th>Profit</th><th>Campaign / UTM</th></tr></thead><tbody>
    <?php if ( $leads ) : foreach ( $leads as $lead ) : $lead_template = ! empty( $lead->template ) ? $lead->template : ( preg_match( '/^([a-z0-9-]+)\s+-\s+/i', (string) $lead->campaign, $m ) ? $m[1] : '' ); ?><tr><td><?php echo esc_html( $lead->created_at ); ?></td><td><?php echo esc_html( $lead->name ); ?></td><td><?php echo esc_html( $lead->email ); ?></td><td><?php echo esc_html( $lead->phone ); ?></td><td><?php echo esc_html( isset( $templates[ $lead_template ] ) ? $templates[ $lead_template ]['label'] : ( $lead_template ?: '—' ) ); ?></td><td><?php echo esc_html( $lead->budget ); ?></td><td><?php echo esc_html( strpos( (string) $lead->estimated_roi, '%' ) !== false ? $lead->estimated_roi : $lead->estimated_roi . '%' ); ?></td><td><?php echo esc_html( $lead->estimated_roas ); ?>x</td><td><?php echo esc_html( $lead->estimated_revenue ); ?></td><td><?php echo esc_html( $lead->estimated_profit ); ?></td><td><?php echo esc_html( $lead->campaign . ' / ' . $lead->source . ' / ' . $lead->utm_campaign ); ?></td></tr><?php endforeach; else : ?><tr><td colspan="11">No leads match these filters.</td></tr><?php endif; ?>
    </tbody></table></div><?php dm_roi_admin_brand_footer(); ?></div>
    <?php
}
