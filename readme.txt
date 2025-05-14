=== Filmhanterare ===
Contributors: Jamal, developed with help from DeepSeek AI
Tags: movies, films, cinema, management, custom post type
Requires at least: WordPress 5.6
Tested up to: WordPress 6.5
Stable tag: 2.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

An advanced system for managing movies as a custom post type with complete metadata management.

== Description ==

Filmhanterare is a professional WordPress plugin for managing movie databases with:

* Custom post type for films
* Comprehensive metadata fields (runtime, age rating, showtimes)
* Modern responsive frontend display
* REST API integration
* Custom admin columns
* Beautiful admin interface

Perfect for:
- Cinema websites
- Film review blogs
- Movie database sites
- Film festival organizers

== Features ==

✅ Custom "Film" post type with proper permalinks  
✅ Advanced metaboxes for film information:
  - Synopsis/plot
  - Runtime (in minutes)
  - Age rating (B, 7, 11, 15)
  - Showtimes with dates/times/languages  
✅ Responsive frontend templates  
✅ REST API support  
✅ Custom admin columns for quick overview  
✅ Optimized for performance  
✅ Modern, accessible UI  
✅ Multilingual ready  

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/filmhanterare` directory
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Start adding films through the new 'Films' menu item

== Screenshots ==

1. Film edit screen with all metadata fields
2. Frontend display of a single film
3. Admin list view of films with custom columns

== Frequently Asked Questions ==

= How do I change the film permalinks? =
Go to Settings > Permalinks and click "Save Changes" to refresh permalinks.

= Can I add custom fields? =
Yes! Use the `filmhanterare_metadata_fields` filter to add custom fields.

= Is this plugin translation ready? =
Yes, all strings are properly localized. Swedish is included by default.

== Changelog ==

= 2.0.0 =
* Complete rewrite with modern architecture
* Added REST API support
* New responsive frontend templates
* Improved admin interface
* Added showtimes functionality

= 1.0.0 =
* Initial release with basic functionality

== Upgrade Notice ==

2.0.0 includes breaking changes to the data structure. Please backup your database before upgrading.

== Developer Documentation ==

### Filters:
- `filmhanterare_metadata_fields` - Modify registered metadata fields
- `filmhanterare_admin_columns` - Customize admin columns
- `filmhanterare_rest_fields` - Add custom REST API fields

### Actions:
- `filmhanterare_before_save` - Fires before saving film data
- `filmhanterare_after_save` - Fires after saving film data

### Template Overrides:
Create these files in your theme to override:
- `single-film.php` - Single film template
- `archive-film.php` - Film archive template

== Credits ==

Developed by Jamal with ❤️ for the WordPress community.

Special thanks to:
- DeepSeek AI
- WordPress core contributors
- Swedish Film Institute for inspiration
- All beta testers