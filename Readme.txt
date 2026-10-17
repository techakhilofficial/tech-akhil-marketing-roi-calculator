=== Tech Akhil Marketing ROI Calculator ===
Contributors: techakhil
Tags: roi calculator, lead capture, whatsapp, marketing tools, calculator
Requires at least: 6.2
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 2.9.8
License: GPLv2 or later

Calculate marketing ROI, revenue, profit and ROAS, capture leads, and export campaign reports.

== Description ==
Calculate marketing ROI, revenue, profit and ROAS, capture leads, and export filtered campaign reports with configurable templates.

Tech Akhil Marketing ROI Calculator helps estimate campaign performance, calculate profitability, and capture leads with configurable marketing templates.

Tech Akhil Marketing ROI Calculator helps site owners present a marketing performance estimate on a WordPress page and optionally capture a visitor's contact details. Calculations are estimates based on visitor-entered assumptions and are not a guarantee of campaign results.

== Features ==
* Responsive frontend calculator.
* ROI, ROAS, estimated revenue, profit, clicks, conversions, cost per conversion, and break-even ROAS estimates.
* Lead capture with server-side validation, nonce verification, and temporary rate limiting.
* Admin lead list with search/date filters and CSV export.
* UTM source, medium, campaign, and landing page capture.
* Admin setup page with shortcode copy and Gutenberg / Elementor instructions.
* Appearance settings for title, button label, light/dark theme, button shape, and currency.
* Template Manager with per-template enable/disable switches and individual accent colors. Disabled templates are hidden from the dropdown and blocked when used directly by shortcode.
* Optional “Powered by Tech Akhil” footer link, disabled by default.
* Clean input labels without intrusive help bubbles or repetitive explanatory boxes.
* Separate agency/management fees, creative/production cost, and other cost fields.
* Campaign comparison with a selector for previously calculated campaign snapshots saved in this browser. Campaign snapshots are stored locally in the current browser, not synchronized across devices.
* Print dialog with the browser's “Save as PDF” option.
* Optional WhatsApp link after a successful lead submission.
* Starter templates for general marketing, paid ad channels, SEO, email, affiliate, e-commerce, and lead-generation campaigns. Template names are descriptive presets and do not indicate a live advertising-platform connection.

== Installation ==
1. Upload the plugin ZIP via Plugins → Add New Plugin → Upload Plugin.
2. Install and activate it.
3. Open ROI Leads → Calculator Setup.
4. Copy `[dm_roi_calculator]`.
5. Edit a page and add a Shortcode block, paste the shortcode, and publish. In Elementor, use the Shortcode widget.
6. Open ROI Leads → Settings to configure design, currency, and optional WhatsApp behavior.
7. Open the public page to test the calculator.

== Shortcode examples ==

Default calculator:
`[dm_roi_calculator]`

Google Ads starter preset:
`[dm_roi_calculator template="google-ads"]`

Meta Ads starter preset:
`[dm_roi_calculator template="meta-ads"]`

All available template shortcodes (only enabled templates will render):

* `[dm_roi_calculator template="general"]`
* `[dm_roi_calculator template="google-ads"]`
* `[dm_roi_calculator template="meta-ads"]`
* `[dm_roi_calculator template="linkedin-ads"]`
* `[dm_roi_calculator template="tiktok-ads"]`
* `[dm_roi_calculator template="microsoft-ads"]`
* `[dm_roi_calculator template="pinterest-ads"]`
* `[dm_roi_calculator template="x-ads"]`
* `[dm_roi_calculator template="seo"]`
* `[dm_roi_calculator template="email-marketing"]`
* `[dm_roi_calculator template="affiliate-marketing"]`
* `[dm_roi_calculator template="ecommerce"]`
* `[dm_roi_calculator template="lead-generation"]`

An optional title can be supplied, for example `[dm_roi_calculator title="Campaign ROI Estimate"]`. The default shortcode presents the dropdown of enabled templates. Direct shortcodes for disabled templates display an administrator-controlled disabled message.

== Calculation notes ==

* Estimated clicks = advertising budget ÷ CPC.
* Estimated conversions = estimated clicks × conversion rate.
* Estimated revenue = estimated conversions × average order/customer value.
* Total campaign cost = ad/marketing spend + agency/management fees + creative/production cost + other costs. Ecommerce actual mode additionally includes COGS, refunds/discounts, and shipping/payment fees in total cost. Do not enter the same cost in more than one field.
* Estimated profit = estimated revenue − total campaign cost.
* ROI = (estimated revenue − total campaign cost) ÷ total campaign cost × 100.
* ROAS = attributed revenue ÷ ad/marketing spend (excluding agency and other fees from the denominator).
* Cost per conversion = total campaign cost ÷ estimated conversions.
* Break-even ROAS = total campaign cost ÷ advertising budget.

Actual performance may differ from these estimates. For lead-generation campaigns, conversion assumptions should reflect the stage being measured (lead or paying customer).

== Frequently Asked Questions ==

= How do I add the calculator to a page? =
Open ROI Leads → Calculator Setup, copy `[dm_roi_calculator]`, and paste it into a WordPress Shortcode block or a page builder's Shortcode widget.

= Will visitors see the shortcode? =
No. When the shortcode is processed correctly, visitors see the calculator, not the shortcode text.

= Can I customize the design? =
Open ROI Leads → Settings. You can adjust title, button label, theme, button shape, currency, template enable/disable switches, and each template accent color.

= How do I compare campaigns? =
Calculate a campaign, click “Add to Comparison”, change the inputs and campaign name, calculate the next campaign, and add it to the comparison list. The comparison is temporary to the current page session and is not saved to the server.

= How do I save a PDF report? =
After calculating, choose “Print / Save PDF”. In your browser's print dialog, select “Save as PDF”. This uses the browser print function and is not a server-generated PDF file.

= Where are leads stored? =
Leads appear under ROI Leads in the WordPress admin. Authorized administrators can search/filter and export CSV.

= Does it connect directly to Google Ads or Meta Ads? =
No. The current presets use manually entered assumptions; they do not connect to advertising platforms or import live ad data.

= How does WhatsApp work? =
Open ROI Leads → Settings, enable the WhatsApp link, enter the full international phone number using digits only, and set the message. The link appears after a successful lead save.

== Privacy ==

When a visitor submits the form, the plugin stores name, email address, phone number, budget, estimated metrics, campaign name, UTM source/medium/campaign, landing page URL, and submission date in the site's WordPress database. It sends a notification email to the WordPress administrator. The site owner should publish an appropriate privacy policy, disclose lead capture and email notification, and manage data retention according to applicable law. Temporary rate-limit transients use a salted hash of the request IP and expire after ten minutes; the raw IP is not stored by this feature.

== Source Code ==

Public maintained source repository: https://github.com/techakhilofficial/tech-akhil-marketing-roi-calculator

Readable asset sources are maintained in `assets/src/`:
- `dm-roi-frontend.js` — frontend JavaScript source.
- `dm-roi-frontend.css` — readable frontend stylesheet source.
- `dm-roi-admin.css` — readable admin stylesheet source.

The files in `assets/` are the versions loaded by the plugin. Keep each readable source file synchronized with its corresponding served asset when making changes. CSS files are formatted for human readability; no automated CSS build command is required. Dynamic per-calculator colors are added through WordPress's `wp_add_inline_style()` API. The admin live preview updates the preview elements through the enqueued admin JavaScript without depending on stylesheet CSSOM access.

The readable frontend JavaScript source is included at `assets/src/dm-roi-frontend.js`; the served frontend script is `assets/dm-roi-frontend.js`. These files are identical in this release. No minification/build tool is required; edit the source file and copy it over the served file after changes. The frontend stylesheet is `assets/dm-roi-frontend.css`. Admin assets are readable files in `assets/`. This plugin does not bundle third-party JavaScript libraries.

== Changelog ==

= 2.9.8 =
* Made admin live preview updates independent of CSS stylesheet rule access.
* Kept admin preview styling updates in the enqueued JavaScript.

= 2.9.7 =
* Removed hardcoded inline style attributes from plugin markup.
* Moved per-instance calculator colors to WordPress-enqueued inline CSS.
* Updated the admin live preview to modify rules in the enqueued stylesheet.
* Added readable CSS source files under assets/src/.

= 2.9.6 =
* Synchronized the readable frontend JavaScript source with the served frontend script.
* Updated the plugin version for this corrective release.

= 2.9.5 =
* Added the standard plain-text short description in the readme header before the Description section.
