=== ABP Event Ticket ===
Contributors: abpteam
Tags: event tickets, ticket booking, seat reservation, event registration, seat plan
Requires at least: 6.2
Tested up to: 7.1
Requires PHP: 7.4
WC requires at least: 8.0
WC tested up to: 9.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Sell event tickets online with WooCommerce using general admission, reserved seats, and attendee forms.

== Description ==

ABP Event Ticket turns WooCommerce into a complete event ticketing and registration system. Create events, configure ticket types and prices, publish event details, collect attendee information, and sell tickets through the standard WooCommerce checkout. It is the simplest way to sell event tickets, concert tickets, conference tickets, festival tickets, and workshop registration online.

The plugin supports both general admission and reserved seating. Administrators can build reusable seat plans with drag-and-drop controls, automatic numbering, custom labels, ticket-type assignment, and flexible layouts. Seat availability is updated in real time as tickets are booked, so you never oversell an event.

Create reusable global configuration for ticket types, seat plans, dates, locations, categories, organizers, brands, features, additional services, attendee forms, FAQs, and Terms & Conditions. Global configuration can be imported into an individual event and customized when needed. Customers can book from any page with a lightweight shortcode, and a Bookings tab appears automatically in their WooCommerce account.

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
* Google Location Map with search-and-select admin picker
* Frontend location map with three display styles (default, light, modern)
* Event features and related event display
* Configurable attendee information forms
* Optional additional services with pricing
* Event FAQs and Terms & Conditions
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

Show an interactive Google Map for the locations assigned to an event. The feature is powered by the Google Maps JavaScript API and the Places library.

Setup:

1. Enable Google Location Map under the plugin's global ON/OFF settings.
2. Paste your Google Maps JavaScript API key into the Google Maps API Key field.
3. Open a Location in the global data, search for the address or place, and select it on the map to save its latitude, longitude, address, and place data.
4. Assign that location to an event.

When the global switch is on, a valid API key is saved, the event has a location, and that location has saved map data, the map is displayed on the event details page. If any of these conditions is missing, no map is rendered.

The map appears in the event details templates and includes:

* Search-and-select map picker in the Location admin screen
* Draggable marker to fine-tune the saved coordinates
* Per-location map data (latitude, longitude, address, and place ID)
* Three frontend map styles: default, light, and modern
* Clickable marker with location name and address info window
* "Get Directions" links to Google Maps

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
9.  Frontend event detail - ticket/seat selection and booking form
10. Location map picker in the admin and the frontend location map


== Need help or have suggestions? ==
If you need any further assistance or support, please contact us through the [🎫 support form](https://abp-team.com/support-desk/). We welcome your suggestions, so feel free to tell us anything we can improve in the plugin.

🌐 [Live Demo](https://https://event-ticket.abp-team.com/)
📖 [Documentation](https://https://event-ticket.abp-team.com/documentation/)
💬 [Support Forum](https://wordpress.org/support/plugin/abp-event-ticket/)
🐛 [Bug Reports](https://github.com/abpteam24/abp-event-ticket/issues)
📧 Email: support@abp-team.com


== Changelog ==

= 1.0.0 =

* Initial release.

== Upgrade Notice ==

= 1.0.0 =

Initial release.
