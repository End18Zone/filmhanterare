Filmhanterare
===============

A small WordPress plugin to manage films as a custom post type.

Development notes

- Classes are namespaced under `\Filmhanterare` and are autoloadable via Composer (PSR-4).
- If you don't use Composer, the plugin falls back to requiring the files from `includes/`.
- Run PHPCS with the WordPress ruleset: `phpcs --standard=phpcs.xml.dist`.

Bundled vendor assets

This plugin bundles minimal copies of third-party assets used in the admin UI under `assets/vendor/`:

- `assets/vendor/jquery-ui-smoothness.css` — a trimmed snapshot of the jQuery UI Smoothness theme used for the datepicker.
- `assets/vendor/jquery-timepicker.min.css` — minimal styling for the timepicker.
- `assets/vendor/jquery-timepicker.min.js` — a small compatibility shim for the timepicker (replace with upstream for full functionality).

If you prefer to use CDN-hosted versions or the full upstream libraries, replace the files in `assets/vendor/` with official copies.

Suggested next steps

- Add PHPUnit tests using the WP test scaffold.
- Add CI to run PHPCS and tests on push.
- Consider bundling third-party JS/CSS locally rather than relying on CDNs.
