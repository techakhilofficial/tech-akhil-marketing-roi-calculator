# Tech Akhil Marketing ROI Calculator

Source code and development documentation for the Tech Akhil Marketing ROI Calculator WordPress plugin.

## About

Tech Akhil Marketing ROI Calculator is a WordPress plugin that helps users estimate marketing return on investment (ROI).

## Source Code

This repository provides the human-readable source code and development documentation for the plugin.

- **GitHub repository:** https://github.com/techakhilofficial/tech-akhil-marketing-roi-calculator

## Development

The plugin maintains human-readable source files in `assets/src/` and runtime files in `assets/`.

Source files:

- `assets/src/dm-roi-frontend.js` — frontend JavaScript source
- `assets/src/dm-roi-frontend.css` — frontend stylesheet source
- `assets/src/dm-roi-admin.css` — admin stylesheet source

Runtime files loaded by the plugin:

- `assets/dm-roi-frontend.js` — frontend JavaScript
- `assets/dm-roi-frontend.css` — frontend styles
- `assets/dm-roi-admin.js` — admin JavaScript
- `assets/dm-roi-admin.css` — admin styles

Keep the source and corresponding runtime files synchronized whenever code or styles change. Before releasing an update, verify the files and test the plugin on a clean WordPress installation.

No automated build command is documented. Do not assume a build tool or build command without verifying the release workflow.


## Testing

The plugin should be tested on a clean WordPress installation with WordPress debugging enabled before release.

## Maintainer

techakhil
