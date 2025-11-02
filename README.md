Filmhanterare
===============

A small WordPress plugin to manage films as a custom post type.

Development notes

- Classes are namespaced under `\Filmhanterare` and are autoloadable via Composer (PSR-4).
- If you don't use Composer, the plugin falls back to requiring the files from `includes/`.
- Run PHPCS with the WordPress ruleset: `phpcs --standard=phpcs.xml.dist`.

Suggested next steps

- Add PHPUnit tests using the WP test scaffold.
- Add CI to run PHPCS and tests on push.
- Consider bundling third-party JS/CSS locally rather than relying on CDNs.
