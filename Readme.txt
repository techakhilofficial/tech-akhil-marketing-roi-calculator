=== Tech Akhil Marketing ROI Calculator ===
Contributors: techakhil
Tags: roi calculator, lead capture, whatsapp, marketing tools, calculator
Requires at least: 6.2
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 2.9.6
License: GPLv2 or later

Calculate marketing ROI, revenue, profit and ROAS, capture leads, and export campaign reports.

== Description ==
Calculate marketing ROI, revenue, profit and ROAS, capture leads, and export filtered campaign reports with configurable templates.

Tech Akhil Marketing ROI Calculator helps estimate campaign performance, calculate profitability, and capture leads with configurable marketing templates.

== Changelog ==

= 2.9.6 =
* Synchronized the readable frontend JavaScript source with the served frontend script.
* Updated the plugin version for this corrective release.

= 2.9.5 =
* Added the standard plain-text short description in the readme header before the Description section.

= 2.9.4 =
* Passed explicit SQL placeholder arguments to prepared lead queries for static-analysis compatibility.
* Documented why private admin lead queries are not cached.

= 2.9.3 =
* Replaced dynamically assembled WHERE clauses with fixed SQL conditions and prepared values for lead filtering and CSV export.
* Added an explicit concise short-description section to the readme.

= 2.9.1 =
* Run prepared lead queries directly at execution to avoid unprepared-query variable findings.
* Added an explicit Short Description section for readme parser compatibility.

= 2.9.0 =
* Escaped CSV output findings with targeted PHPCS annotations while preserving valid CSV formatting.
* Refactored lead list and export SQL to build prepared queries directly at execution with identifier and value placeholders.
* Moved the short description into the standard readme description position for parser compatibility.

= 2.8.9 =
* Reworked leads queries to prepare SQL at execution and parameterize the row limit.
* Added nonce verification to lead-filter requests and validated calendar dates.
* Replaced direct file writes for CSV output with escaped CSV line generation and response output.
* Added a plugin directory short description.

= 2.8.8 =
* Position the selected template icon and name above the calculator/results panels on desktop.
* Correct icon box alignment and prevent clipping.


= 2.8.6 =
* Reformatted exported lead timestamps as readable text so Excel does not display date serials as hash marks in narrow columns.
* Renamed the setup page heading and settings page title to Tech Akhil Marketing ROI Calculator.
* Rebuilt Setup Documents as a professional in-admin guide for WordPress, Elementor, settings and lead reports; removed the duplicate marketing template list.
* Added copy buttons beside individual template shortcodes.
* Added consistent Tech Akhil branding and a website link at the bottom of Settings, Leads and Setup Documents pages.


= 2.8.5 =
* Fixed CSV export to stream a real CSV download through admin-post instead of returning the WordPress admin page HTML.
* Fixed date filtering with explicit start-of-day and end-of-day bounds.
* Added a template filter to the Leads page and applied the same filters to CSV export.
* Stored the selected template with new lead records and added a best-effort template inference for older records.
* Renamed the plugin admin menu to TechAkhil ROI with Settings, Leads and Setup Documents submenus.


= 2.8.4 =
* Redesigned admin settings as a responsive, tabbed workspace with template cards and bulk enable/disable controls.
* Improved settings grouping, live preview and responsive styling.



= 2.8.3 =
* Removed the estimate/actual formula explanation text from the public results page.
* Removed the Compare Campaigns button and comparison panel from the public calculator.
* Individual template shortcodes now lock the calculator to that template and do not show the platform dropdown. The common shortcode keeps the enabled-template dropdown.

Tech Akhil Marketing ROI Calculator helps site owners present a marketing performance estimate on a WordPress page and optionally capture a visitor's contact details. Calculations are estimates based on visitor-entered assumptions and are not a guarantee of campaign results.

= Features =
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

== Changelog ==

= 2.8.8 =
* Position the selected template icon and name above the calculator/results panels on desktop.
* Correct icon box alignment and prevent clipping.


= 2.8.6 =
* Reformatted exported lead timestamps as readable text so Excel does not display date serials as hash marks in narrow columns.
* Renamed the setup page heading and settings page title to Tech Akhil Marketing ROI Calculator.
* Rebuilt Setup Documents as a professional in-admin guide for WordPress, Elementor, settings and lead reports; removed the duplicate marketing template list.
* Added copy buttons beside individual template shortcodes.
* Added consistent Tech Akhil branding and a website link at the bottom of Settings, Leads and Setup Documents pages.


= 2.8.5 =
* Fixed CSV export to stream a real CSV download through admin-post instead of returning the WordPress admin page HTML.
* Fixed date filtering with explicit start-of-day and end-of-day bounds.
* Added a template filter to the Leads page and applied the same filters to CSV export.
* Stored the selected template with new lead records and added a best-effort template inference for older records.
* Renamed the plugin admin menu to TechAkhil ROI with Settings, Leads and Setup Documents submenus.


= 2.8.4 =
* Redesigned admin settings as a responsive, tabbed workspace with template cards and bulk enable/disable controls.
* Improved settings grouping, live preview and responsive styling.



= 2.8.1 =
* Refined the calculator and admin visual styling to more closely match the supplied magenta dashboard reference, with cleaner spacing, metric cards, form controls, and responsive layout.


= 2.7.0 =
* Added template enable/disable controls and individual template accent colors; disabled templates are hidden and server-side blocked.
* Added separate agency/management fees, creative/production costs, accessible input help controls, and optional Tech Akhil attribution footer.
* Added template icons to the dropdown and complete template shortcode documentation.
* Updated template colors dynamically when a visitor changes the selected template.
* Improved campaign comparison handling to prevent adding an unchanged duplicate result.

= 2.6.0 =
* Replaced the one-size-fits-all form with platform- and mode-specific inputs for all 13 templates.
* Added separate calculation paths for paid search, Meta/TikTok CPM-based forecasting, LinkedIn lead quality, SEO organic metrics, email funnel rates, affiliate commissions, ecommerce contribution profit, and lead-generation funnel metrics.
* Added contextual outputs for organic position, email delivery/open/click rates, video views/completion, reach, engagement, qualified leads, customers, orders, commission, and gross profit.
* Actual metrics remain manual inputs; no advertising platform API connection is included.

= 2.5.0 =
* Added estimate and actual-results calculation modes.
* Actual mode calculates CPC, conversion rate, average value, ROI, ROAS, CPA/CPL, CTR and CPM from manually entered campaign results.
* Clarified that platform templates label metrics and do not connect to advertising platforms or import benchmarks.



= 2.0.0 =
* Added advanced ROI, ROAS, profit, cost-per-conversion, and break-even ROAS metrics.
* Added configurable title, button label, currency, theme, colors, and button shape.
* Added admin setup guide with shortcode copy, Gutenberg, Elementor, and template instructions.
* Added campaign comparison, browser print / Save as PDF, and starter marketing presets.
* Added UTM attribution fields, lead search/date filters, and expanded CSV export.
* Preserved the existing `[dm_roi_calculator]` shortcode and legacy lead columns.

== Source Code ==

The readable frontend JavaScript source is included at `assets/src/dm-roi-frontend.js`; the served frontend JavaScript is `assets/dm-roi-frontend.js`. The readable source and served frontend script are identical in this release. No minification/build tool is required; edit `assets/src/dm-roi-frontend.js` and copy it over `assets/dm-roi-frontend.js` after changes. A public source repository URL still needs to be added here before resubmission, if requested by the reviewer. The frontend stylesheet is `assets/dm-roi-frontend.css`. Admin assets are readable files in `assets/`. This plugin does not bundle third-party JavaScript libraries.
