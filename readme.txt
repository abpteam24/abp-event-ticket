=== ABP Event Ticket ===
Contributors: abpteam
Tags: event tickets, ticket booking, seat reservation, event registration, seat plan
Requires at least: 6.2
Tested up to: 7.1
Requires PHP: 7.4
WC requires at least: 8.0
WC tested up to: 9.4
Stable tag: 1.0.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Sell event tickets online with WooCommerce using general admission, reserved seats, and attendee forms.

== Description ==

ABP Event Ticket turns WooCommerce into a complete event ticketing and registration system. Create events, configure ticket types and prices, publish event details, collect attendee information, and sell tickets through the standard WooCommerce checkout. It is the simplest way to sell event tickets, concert tickets, conference tickets, festival tickets, and workshop registration online.

The plugin supports both general admission and reserved seating. Administrators can build reusable seat plans with drag-and-drop controls, automatic numbering, custom labels, ticket-type assignment, and flexible layouts. Seat availability is updated in real time as tickets are booked, so you never oversell an event.

Create reusable global configuration for ticket types, seat plans, dates, locations, categories, organizers, brands, features, additional services, attendee forms, FAQs, Terms & Conditions, event timelines, and speakers. Global configuration can be imported into an individual event and customized when needed. Customers can book from any page with a lightweight shortcode, and a Bookings tab appears automatically in their WooCommerce account.

== Key Features ==

* Event creation and management
* Event list with pagination and status filters
* Event edit, clone, view, trash, restore, and permanent delete actions
* General admission ticket sales
* Reserved seat ticket sales
* Reusable global ticket types
* Multiple ticket types and pricing options
* Minimum, maximum, and reserved quantity controls
* Reusable seat plans
* Drag-and-drop seat plan designer
* Automatic seat numbering and custom seat prefixes
* Custom seat labels, cells, dimensions, spacing, and layout
* Ticket-type assignment to seats
* Multiple seat plan instances and seat layers
* Real-time seat availability
* Specific-date and periodic-date event schedules
* Date-wise and day-wise time configuration
* Advance booking and sale closing buffer controls
* Location, category, organizer, and brand management
* Google Location Map with search-and-select admin picker and key-free iframe fallback
* Frontend location map with three display styles (default, light, modern)
* Event features and related event display
* Configurable attendee information forms
* Optional additional services with pricing
* Event FAQs and Terms & Conditions
* Event Timeline with time, title, and description entries
* Event Speakers with full profiles (bio, photo, designation, company, website, social links)
* Global and event-specific configuration
* Feature enable and disable controls
* Event search, filtering, and pagination
* Event details templates and display themes
* Event gallery and image slider
* WooCommerce cart, checkout, payment, tax, coupon, and order integration
* Booking and order details in the WordPress dashboard
* Translation-ready
* Gutenberg-compatible event post type and taxonomies
* Polylang-compatible frontend language filtering when Polylang is active

== Ticket Types and Pricing ==

Create ticket types such as Adult, Child, VIP, or Early Bird and configure their prices and quantities. Ticket types can be created globally and reused across events. For reserved seating events, ticket types can also be assigned to specific seats or seat areas.

== Seat Plan Designer ==

The seat plan designer lets you create reusable seating layouts for theaters, conferences, classrooms, venues, and other events.

Seat plans support:

* Drag-and-drop positioning
* Automatic seat numbering
* Custom seat names and prefixes
* Custom cells and text
* Flexible rows and columns
* Adjustable cell size and gaps
* Ticket-type assignment
* Multiple layout configurations
* Visual and background customization
* Clone, edit, view, and delete actions

== Event Scheduling ==

Configure when tickets are available for each event:

* Specific event dates
* Periodic dates
* Date-wise times
* Day-wise times
* Advance booking date limits
* Sale closing buffer time
* Availability and quantity controls

== Google Location Map ==

Show a Google Map for the locations assigned to an event. With a Google Maps JavaScript API key the interactive map is rendered via the JavaScript API and Places library. When no API key is saved, the map is rendered as an embedded iframe (no API key required), so the location still displays as long as the global Google Location Map switch is on.

Setup:

1. Enable Google Location Map under the plugin's global ON/OFF settings.
2. Paste your Google Maps JavaScript API key into the Google Maps API Key field.
3. Open a Location in the global data, search for the address or place, and select it on the map to save its latitude, longitude, address, and place data.
4. Assign that location to an event.

When the global switch is on, the event has a location, and that location has saved map data, the map is displayed on the event details page. With an API key saved, the interactive JavaScript map renders; without a key, an embedded iframe map renders instead. If the switch is off or no map data exists for the location, no map is rendered.

The map appears in the event details templates and includes:

* Search-and-select map picker in the Location admin screen
* Draggable marker to fine-tune the saved coordinates
* Per-location map data (latitude, longitude, address, and place ID)
* Three frontend map styles: default, light, and modern
* Clickable marker with location name and address info window
* "Get Directions" links to Google Maps

== Event Timeline ==

Display a visual event timeline on the event details page showing the schedule of activities in chronological order. Each timeline entry consists of a time, a title, and a rich-text description.

Setup:

1. Enable Event Timeline under the plugin's global ON/OFF settings.
2. Open an event and go to the Timeline tab.
3. Turn on the "Active Timeline" switch for that event.
4. Click "Add New Timeline Item" and fill in the time, title, and description for each schedule entry.
5. Save the event.

The timeline appears on the event details page when the event has at least one timeline entry with a title. It supports three display styles that match the event details template:

* **Default** - Vertical line with dot markers and a bordered card layout
* **Light** - Vertical line with circle markers and shadowed card layout
* **Modern** - Header banner with badge markers and dashed card layout within a rounded container

The timeline section is gated by the global Event Timeline switch. When the switch is OFF, no timeline is displayed anywhere on the site even if events have timeline entries saved. The per-event "Active Timeline" switch controls whether the timeline is shown for that specific event.

== Event Speakers ==

Create and manage reusable speaker profiles with rich information including bio, photo, designation, company, website, and social media links (Twitter/X, LinkedIn, Facebook, Instagram). A speaker can be linked to any number of events.

Setup:

1. Enable Event Speaker under the plugin's global ON/OFF settings.
2. Open the plugin's Global Data section and go to the Speaker tab.
3. Click "Add New Speaker" and fill in the name, bio, photo, designation, company, website, and social links.
4. Save the speaker.
5. Open an event and go to the event edit screen.
6. In the Speaker section, search and select one or more speakers to associate with the event.
7. Save the event.

Speaker management includes:

* Add, edit, and delete speakers from the Global Data > Speaker tab
* Rich-text speaker bio
* Speaker photo upload with image selection and removal
* Designation and company fields
* Website URL
* Social profile URLs for Twitter/X, LinkedIn, Facebook, and Instagram
* Optional per-speaker slug, used for the speaker profile page URL
* A speaker picker in the event editor with multi-select, so a single speaker can be reused across many events
* A per-event "Active Speaker" switch to show or hide the speaker section for that event
* A global Event Speaker switch that hides all speaker features when turned OFF

Speakers are stored as terms in the `abpet_speaker` taxonomy, which is registered against the Event post type. Per-speaker data such as photo, designation, company, website, and social links is stored in speaker meta.

= Frontend Display =

Linked speakers are displayed automatically on the event details page, directly below the event timeline. No shortcode is required.

The section follows the active event details template, so it matches your site automatically:

* Default - vertical list with a round photo, name, designation, company, bio, and social links
* Light - responsive card grid with a large photo, centred text, and a social footer
* Modern - centred avatar row inside a bordered panel

A speaker with no photo shows their initials in a coloured circle instead. Speakers with no bio, website, or social links render cleanly without empty gaps.

The section is skipped entirely when the event has no speakers linked, when the global Event Speaker switch is OFF, or when the per-event "Active Speaker" switch is OFF. The per-event switch is ON by default, so speakers appear as soon as you link them.

= Speaker Page =

Every speaker has its own page at `/speaker/{speaker-slug}/`, for example `/speaker/dr-sarah-chen/`.

Unlike the other taxonomy pages, the speaker page does not run an event list. It is a dedicated profile page that shows only the speaker information entered in the admin:

* Photo, or the speaker's initials in a coloured circle when no photo is set
* Name
* Designation and company
* Website
* Full bio, with rich text formatting preserved
* Every saved social profile, shown with its network name and icon

Sections with no data are omitted entirely, so a speaker with only a name shows only their name.

The page template can be overridden the same way as the other templates: copy `page/speaker.php` from the plugin's `tb_templates` folder into `wp-content/tb_templates/page/`.

To list a speaker's events somewhere, for example on a normal WordPress page or inside your own template, use the shortcode:

`[abpet-post speaker_id="TERM_ID"]`

The term ID is shown in the Global Data > Speaker list.

= Display Hook =

If you want to place the speaker section somewhere else, or restyle it, use the `abpet_speaker` action:

`<?php do_action( 'abpet_speaker', $post_infos, $post_id, 'default' ); ?>`

Accepted arguments: the event meta array, the event post ID, and the style (`default`, `light`, or `modern`).

Each style also fires its own template action after rendering, for example `abpet_speaker_default_template`.

= Template Overrides =

All three designs live in separate template files and can be overridden without touching the plugin:

1. Create a `tb_templates/speaker` folder inside your `wp-content` directory.
2. Copy `default.php`, `light.php`, or `modern.php` from the plugin's `tb_templates/speaker` folder into it.
3. Edit your copy. The plugin uses your file instead of its own.

== Attendee Information and Services ==

Collect the information required for registration through configurable attendee forms. Add optional services, such as meals, merchandise, or other event extras, with separate prices.

Global attendee forms and additional services can be imported into an event and customized for that event.

== WooCommerce Integration ==

WooCommerce is required for ticket cart and checkout functionality. Customers can select an event, ticket type, date, seats (when enabled), attendee details, and optional services before completing payment through any payment gateway supported by WooCommerce.

WooCommerce manages the cart, checkout, payment, tax, coupon, customer account, and order workflow.

== Shortcodes ==

Add these shortcodes to any WordPress page:

`[abpet-booking]`

Displays the event listing and booking interface, including search, filters, ticket selection, attendee forms, and checkout flow.

`[abpet-post]`

Displays an event listing without the booking wrapper.

`[abpet-gallery]`

Displays event images in a gallery or slider.

Common attributes include:

* `post_id` - Display a specific event.
* `cat_id` - Filter by category.
* `loc_id` - Filter by location.
* `organizer_id` - Filter by organizer.
* `brand_id` - Filter by brand.
* `style` - Listing style: `grid`, `list`, `missionary`, or `minimal`.
* `slider_style` - Gallery style: `gallery` or `slider`.
* `pagination` - Enable or disable pagination.
* `pagination-style` - Pagination mode: `live` or `number`.
* `column` - Number of listing columns.
* `sort` - Event order: `ASC` or `DESC`.

Example:

`[abpet-booking style="grid" column="3" pagination="yes"]`

Minimal event list example:

`[abpet-post style="minimal" show="8" pagination-style="number"]`

== My Account Bookings ==

When used with WooCommerce, a "Bookings" tab is added automatically to the WooCommerce My Account area. Logged-in customers can view their event bookings, event dates, ticket details, additional services, attendee information, and order totals in one place, with pagination for large histories.

== Requirements ==

* WordPress 6.2 or later
* PHP 7.4 or later
* MySQL 5.7 or later
* WooCommerce 8.0 or later

== Gutenberg and Multilingual Support ==

The event post type and all event taxonomies are registered with REST API support, so events and taxonomies can be edited with the Gutenberg block editor. The plugin's event listing and booking shortcodes can also be inserted into Gutenberg Shortcode blocks.

The plugin is translation-ready and supports Polylang's frontend language filtering for event listings. Create a translated event and translated taxonomy terms in Polylang for each language you publish. WooCommerce handles translated checkout and customer account pages according to the multilingual plugin configuration.

Plugin-specific global configuration labels, ticket names, and option values are stored as reusable settings and are not automatically duplicated or translated by Polylang. Translate those values through your multilingual workflow or use event-specific values when each language needs different content.

== Installation ==

= Automatic Installation =

1. Install and activate WooCommerce.
2. In WordPress, go to Plugins > Add New.
3. Search for "ABP Event Ticket".
4. Install and activate the plugin.
5. Open Event Ticket from the WordPress admin menu.
6. Configure the global settings.
7. Create ticket types, seat plans, dates, locations, and other reusable data.
8. Create an event and configure its tickets, prices, schedule, and availability.
9. Add one of the ABP Event Ticket shortcodes to a page.

= Manual Installation =

1. Download the plugin ZIP file.
2. Go to Plugins > Add New > Upload Plugin.
3. Upload and install the ZIP file.
4. Activate ABP Event Ticket.
5. Install and activate WooCommerce if it is not already installed.
6. Configure the plugin and create your events.

== Frequently Asked Questions ==

= Is WooCommerce required? =

Yes. WooCommerce is required for the cart, checkout, payment, and order functionality.

= Can I sell both general admission and reserved seat tickets? =

Yes. Each event can use general admission tickets or a configurable seat plan for reserved seating.

= Can I create reusable ticket types? =

Yes. Global ticket types can be reused across multiple events.

= Can I reuse a seat plan? =

Yes. A global seat plan can be assigned to multiple events and can be used multiple times within an event.

= Can I create custom seat layouts? =

Yes. The seat plan designer supports drag-and-drop positioning, automatic numbering, custom labels, ticket-type assignment, custom cells, and layout controls.

= Can I collect attendee information? =

Yes. Create global attendee forms or configure event-specific attendee fields.

= Can I add optional services to a ticket booking? =

Yes. Additional services can be created globally or for a specific event and can include their own prices.

= Can I configure recurring or specific event dates? =

Yes. Events support both specific dates and periodic dates, with day-wise and date-wise time configuration.

= Can I customize the event listing and gallery? =

Yes. Use the listing and gallery shortcodes with supported attributes to control the displayed event content and layout.

= Can I show a Google Map for event locations? =

Yes. Enable Google Location Map in the global ON/OFF settings, add a Google Maps API key, and save map coordinates for each location. The map is displayed on the event details page when the event location has saved map data. You can choose between default, light, and modern map styles.

= Can I display an event timeline? =

Yes. Enable Event Timeline in the global ON/OFF settings, then open an event and add timeline entries with a time, title, and description for each schedule item. The timeline is displayed on the event details page in three styles: default, light, and modern.

= Can I add speakers to events? =

Yes. Enable Event Speaker in the global ON/OFF settings, then create speaker profiles in the Global Data > Speaker tab. Assign one or more speakers to an event from the event edit screen. A speaker can be reused across any number of events.

Assigned speakers appear automatically on the event details page below the event timeline, and each speaker gets a public profile page at `/speaker/{speaker-slug}/`. Both are included in 1.0.3.

= Is the plugin translation-ready? =

Yes. The plugin uses the WordPress localization system and is translation-ready.

== Screenshots ==

1.  Admin Dashboard - overview charts and System Status health check
2. Event list page - admin events with create, edit, clone, trash, restore actions
3. Event create / edit page - tickets, prices, quantities, and schedule
4. Ticket/Seat Plan designer - drag-and-drop seat layout editor
5.  Global Data - dates, location, category, organizer, brand lists
6. Configuration - general settings and ON/OFF feature switches
7. Orders - booking and order management with status and check-in
8. Frontend event listing - event grid with search and filters
9.  Frontend event detail
10. Frontend event detail
11. Frontend event detail

== Need help or have suggestions? ==
If you need any further assistance or support, please contact us through the [🎫 support form](https://abp-team.com/support-desk/). We welcome your suggestions, so feel free to tell us anything we can improve in the plugin.

🌐 [Live Demo](https://demo-et.abp-team.com/)
📖 [Documentation](https://demo-et.abp-team.com/documentation/)
💬 [Support Forum](https://wordpress.org/support/plugin/abp-event-ticket/)
🐛 [Bug Reports](https://github.com/abpteam24/abp-event-ticket/issues)
📧 Email: support@abp-team.com


== Changelog ==

= 1.0.4 =

* New: `abpet_speaker_page_data` filter that passes the complete speaker profile data to the speaker templates.
* New: `abpet_speaker_profile` filter for customizing a single speaker profile.
* New: Additional filters for speaker initials, role, fact rows, social links, and photo markup.
* Fix: Speaker photo, bio, designation, company, website, and social URLs are now sanitized before saving.
* Fix: Speaker templates now use prefixed variables, so a shortcode or theme output elsewhere on the same page can no longer collide with them.
* Fix: Admin settings and configuration grids no longer force a 400px minimum width, removing horizontal scrolling on phones and small tablets.

Released: October 2, 2026

= 1.0.3 =

* New: Event Speaker feature with a global ON/OFF switch.
* New: Speaker management in the Global Data admin area, with a dedicated Speaker tab and an AJAX add/edit/delete interface.
* New: Speaker profiles with name, optional slug, rich-text bio, photo upload, designation, company, website, and social profile URLs for Twitter/X, LinkedIn, Facebook, and Instagram.
* New: `abpet_speaker` taxonomy registered against the Event post type, so a speaker can be linked to any number of events.
* New: Speaker picker in the event editor with multi-select assignment.
* New: Every speaker gets a public profile page at `/speaker/{slug}/`. Unlike the other taxonomy pages it runs no event list and shows only the speaker's own admin-entered details: photo or initials, name, designation, company, website, rich-text bio, and every saved social profile.
* New: `[abpet-post speaker_id="TERM_ID"]` shortcode attribute, for listing a speaker's events on a normal page or in your own template.
* New: Speakers are displayed on the event details page below the event timeline, in a separate section that matches the active details template.
* New: Three built-in speaker designs (Default, Light, Modern), each in its own template file that can be overridden from `wp-content/tb_templates/speaker`.
* New: `abpet_speaker` action to place or restyle the speaker section, plus per-style template actions.
* New: Per-event "Active Speaker" switch to hide the section for a single event.
* New: Speakers without a photo show their initials instead.
* New: Per-speaker data is stored in speaker meta, and the global speaker list is cached for fast admin rendering.
* Improved: Rich-text fields inside global admin popups are now initialized and submitted correctly, which also benefits existing global data forms.

Released: September 28, 2026

= 1.0.2 =

* New: Event Timeline feature with global ON/OFF switch and per-event toggle.
* New: Timeline tab in event post settings with repeatable time, title, and rich-text description entries via WP Editor.
* New: Frontend event timeline on all three event details templates (default, light, and modern) with clean vertical classic design.
* New: `abpet_timeline` hook for displaying the timeline on the event details page.
* New: CSS styles for timeline display across all three template variants.

Released: September 20, 2026

= 1.0.1 =

* New: Google Location Map feature with ON/OFF switch and Google Maps API key setting.
* New: Search-and-select map picker in the Location admin screen with draggable marker.
* New: Per-location map data storage (latitude, longitude, address, and place ID).
* New: Frontend location map on event details templates with three display styles (default, light, and modern). When no API key is saved, the map loads as an embedded iframe so locations still display.
* New: `abpet_map` hook for displaying the map on the event details page.
* Fix: Location list now correctly displays the stored location label.
* Fix: Event details and category/location/brand/organizer templates no longer call `the_post()` on an empty main query, preventing a PHP "Undefined array key 0" notice when no posts are found.

Released: September 13, 2026

= 1.0.0 =

* Initial release.

== Upgrade Notice ==

= 1.0.4 =

Fixed: Admin settings and configuration screens now fit small phone and tablet screens without horizontal scrolling.

= 1.0.3 =

Added: Event Speaker feature for creating speaker profiles, linking them to events, displaying them on the event details page, and giving each speaker a public profile page.

= 1.0.2 =

Updated to add the Event Timeline feature for displaying event schedules on the details page.

= 1.0.1 =

Updated to add the Google Location Map feature for event locations.
