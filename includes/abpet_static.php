<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPET_Static' ) ) {
		class ABPET_Static {
			public function __construct() {
				add_action( 'abpet_notice', [ $this, 'wc_notice' ] );
				add_action( 'wp_ajax_abpet_wc_config', array( $this, 'wc_config' ) );
				add_action( 'wp_ajax_abpet_create_page', array( $this, 'create_page' ) );
				add_action( 'wp_ajax_abpet_import_dummy', array( $this, 'import_dummy' ) );
				add_action( 'wp_ajax_abpet_import_remove_dummy', array( $this, 'remove_dummy' ) );
			}
			public function wc_notice(): void {
				if ( ABPET_WC < 2 ) {
					$type = ABPET_WC == 1 ? 'wc_active' : 'wc_install_active';
					$btn  = ABPET_WC == 1 ? __( 'Active Now', 'abp-event-ticket' ) : __( 'Install & Active Now', 'abp-event-ticket' );
					?>
                    <div class="dash_card dash_wc_setup">
                        <div class="dash_wc_setup_icon"><i class="fas fa-shopping-cart"></i></div>
                        <div class="dash_wc_setup_body">
                            <h4><?php esc_html_e( 'WooCommerce is required', 'abp-event-ticket' ); ?></h4>
                            <p><?php echo esc_html( ABPET_Static::array_info( 'must_wc' ) ); ?></p>
                        </div>
						<?php
							$icon = ABPET_WC == 1 ? 'fa-tasks' : 'fa-file-download';
						?>
                        <button type="button" class="_btn_warning_xs" onclick="abpet_wc_config('<?php echo esc_attr( $type ); ?>', this)">
                            <i class="fas <?php echo esc_attr( $icon ); ?>"></i> <?php echo esc_html( $btn ); ?>
                        </button>
                    </div>
                    <div class="_divider"></div>
					<?php
				}
			}
			public static function array_info( $key ) {
				$current_date = current_time( 'Y-m-d H:i' );
				$des          = array(
					'general_config'              => __( 'Note: Configure the general settings for this Event here. If you do not want to use any specific feature, you can enable or disable it from Main Configuration → On/Off Sections. Disabling a feature will remove it from the entire site.', 'abp-event-ticket' ),
					'sale_continue'               => __( 'Note: This switch indicate Event Ticket sale close/continue . You can  sale close/continue  by this switch. By default sale will be  continue', 'abp-event-ticket' ),
					'abpet_template'              => __( 'Note: Here You can change your details page template.', 'abp-event-ticket' ),
					'post_sku'                    => __( 'Note: Here you can add an SKU for this post. You can also show or hide it on the frontend by turning the switch On or Off.', 'abp-event-ticket' ),
					'post_icon'                   => __( 'Note: Set a custom icon or emoji for this post. The selected icon/emoji will be displayed alongside the post title wherever the title appears across the website, helping it stand out and improve visual recognition.', 'abp-event-ticket' ),
					'sub_title'                   => __( 'Note: Add a Sub-title to enable the Post sub-tile. Leave this blank if you dont want to show any Sub-title information for this Post.', 'abp-event-ticket' ),
					'post_description'            => __( 'Note: Add short description about this Event . Leave this blank if you dont want to show any  description for this Event.', 'abp-event-ticket' ),
					'display_capacity'            => __( 'Note : Enable this option to display the capacity for this Event on the frontend. This setting only works when the global Event Capacity Display option is enabled.', 'abp-event-ticket' ),
					'display_organizer'           => __( 'Note : This switch indicate Event Organizer . You can also show or hide it on the frontend by turning the switch On or Off.', 'abp-event-ticket' ),
					'display_brand'               => __( 'Note : This switch indicate Event Brand name . You can also show or hide it on the frontend by turning the switch On or Off.', 'abp-event-ticket' ),
					'display_category'            => __( 'Note : This switch indicate Event Category . You can also show or hide it on the frontend by turning the switch On or Off.', 'abp-event-ticket' ),
					'related_item'                => __( 'Note: Select related items to display on the details page. Leave this option empty or disabled if you do not want to show related items.', 'abp-event-ticket' ),
					'post_feature'                => __( 'Note: If you want to add feature for this Event, you can add Here. These feature will be show with this Event . You may leave this section empty if you do not want to show frontend. ', 'abp-event-ticket' ),
					'display_slider'              => __( 'Note: If you want to add an image gallery for this Event, you can upload images below.  You may leave this section empty if you do not want to show images. ', 'abp-event-ticket' ),
					//=============================//
					'seat_type'                   => __( 'Note: Please select your Event seat type . Default is Ticket', 'abp-event-ticket' ),
					'ticket_type'                 => __( 'Note: You have disabled the Seat Plan System from the Global On/Off Settings, so your ticket types will function as regular tickets only.If you want to use a seat plan, enable Seat Plan System from the Global On/Off Settings. Once enabled, you can turn the Seat Plan feature on or off for each Event individually, allowing you to use either a seat plan or regular tickets as needed.', 'abp-event-ticket' ),
					'single_ticket_type'          => __( 'Note: If the Global Multiple Ticket System is disabled, you cannot use multiple ticket types for any Event.To enable this feature, turn on Multiple Ticket System from the Global On/Off Settings. Once enabled, you can configure multiple ticket types and prices for each Event. You can also choose to use a single ticket for specific Events whenever needed.', 'abp-event-ticket' ),
					'display_ticket_type'         => __( 'Note : This switch indicate Event ticket type. if your all ticket/seat same type then switch will be off. if you want to multiple type please switch on', 'abp-event-ticket' ),
					'min_qty'                     => __( 'Note : Set the minimum quantity customers must select per order. This global setting applies across the entire booking system.', 'abp-event-ticket' ),
					'max_qty'                     => __( 'Note : Set the maximum quantity a customer can select per order. This global setting helps control the maximum number of tickets or seats a customer can book in a single order.', 'abp-event-ticket' ),
					//=============================//
					//=============================//
					'no_category'                 => __( 'No Category Found !', 'abp-event-ticket' ),
					'cat_name'                    => __( 'Note: Please enter a category name — the field cannot be empty. ', 'abp-event-ticket' ),
					'cat_slug'                    => __( 'Note: Category slug is optional — leave it blank to auto-generate from the name. ', 'abp-event-ticket' ),
					'cat_des'                     => __( 'Note: Category description is optional — you can add details to better explain this category. ', 'abp-event-ticket' ),
					//=============================//
					'no_organizer'                => __( 'No Organizer Found !', 'abp-event-ticket' ),
					'org_name'                    => __( 'Note: Please enter a Organizer name — the field cannot be empty. ', 'abp-event-ticket' ),
					'org_slug'                    => __( 'Note: Organizer slug is optional — leave it blank to auto-generate from the name. ', 'abp-event-ticket' ),
					'org_des'                     => __( 'Note: Organizer description is optional — you can add details to better explain this Organizer. ', 'abp-event-ticket' ),
					//=============================//
					'no_location'                 => __( 'No Location Found ! ', 'abp-event-ticket' ),
					'loc_name'                    => __( 'Note: Please enter a Location name — the field cannot be empty. ', 'abp-event-ticket' ),
					'loc_slug'                    => __( 'Note: Location slug is optional — leave it blank to auto-generate from the name. ', 'abp-event-ticket' ),
					'loc_des'                     => __( 'Note: Location Address is optional — you can add details to better explain this Location Full  Address. ', 'abp-event-ticket' ),
					//=============================//
					'no_brand'                    => __( 'No Brand Found ! ', 'abp-event-ticket' ),
					'brand_name'                  => __( 'Note: Please enter a Brand name — the field cannot be empty. ', 'abp-event-ticket' ),
					'brand_slug'                  => __( 'Note: Brand slug is optional — leave it blank to auto-generate from the name. ', 'abp-event-ticket' ),
					'brand_des'                   => __( 'Note: Brand description  is optional — you can add details to better explain this Brand. ', 'abp-event-ticket' ),
					//=============================//
					'no_feature'                  => __( 'No Feature Found ! ', 'abp-event-ticket' ),
					'feature_value'               => __( 'Note: Please enter a Feature Value  — the field optional ', 'abp-event-ticket' ),
					'feature_icon'                => __( 'Note: You can add an icon, or emoji for this Feature(optional).', 'abp-event-ticket' ),
					'feature_name'                => __( 'Note: Please enter a Feature Name  — the field cannot be empty.', 'abp-event-ticket' ),
					//=============================//
					'date_format'                 => __( 'Note:  If you want to change the Date  Format, simply choose a different format. The default date is: ', 'abp-event-ticket' ) . ' ' . date_i18n( 'D j M , Y', strtotime( $current_date ) ),
					'time_format'                 => __( 'Note : If you want to change the Time Format, simply choose a different format. The default Time Format is: ', 'abp-event-ticket' ) . ' ' . date_i18n( get_option( 'time_format' ), strtotime( $current_date ) ),
					'sale_close_before'           => __( 'Note:  Enter the time in minutes to close ticket sales before the Event starts. If not specified, it will default to 0 (e.g. 1 hour equals 60 minutes). ', 'abp-event-ticket' ),
					'advance_date_number'         => __( 'Note: Kindly provide the number of days in advance for booking. By default, the advance booking period is set to 28 days.(optional) ', 'abp-event-ticket' ),
					'date_type'                   => __( 'Note: Please Select your Event operational date type. Default operational date will be Periodic', 'abp-event-ticket' ),
					'event_type'                  => __( 'Note: Please Select your Event  type. Default Type Offline/Phisical', 'abp-event-ticket' ),
					'specific_dates'              => __( 'Note: Please add your Event operational Specific Date lists  .', 'abp-event-ticket' ),
					'operation_time'              => __( 'Note: Configure the time schedule for this event. You can add multiple times and set different times for each event date. This is useful when the event has multiple sessions or different schedules on different dates.', 'abp-event-ticket' ),
					'periodic_start_date'         => __( 'Note: Please add your Event Launching Date otherwise it will be start today ', 'abp-event-ticket' ),
					'periodic_end_date'           => __( 'Note: Please add your Event Terminate  Date otherwise it will be Continuously running periodically', 'abp-event-ticket' ),
					'periodic_after'              => __( 'Note: Please add your periodically after days. if  your Event operation day everyday this will be one(1).(optional)', 'abp-event-ticket' ),
					'date_rule'                   => __( 'Note: Enable this checkbox to configure special on/off date  settings. This option is optional. If you set a date/time in the special “On” date, that date will remain active even if it falls within an “Off” date range or on weekends.', 'abp-event-ticket' ),
					'special_on_dates'            => __( 'Note: If you add any date  in Special On Dates, it will always remain active—even if that date falls within an off date range or on weekends.', 'abp-event-ticket' ),
					'weekend'                     => __( 'Note: Please select your weekend.Default all days open(optional)', 'abp-event-ticket' ),
					'day_wise_time'               => __( 'Note:Add Day-wise Time if your Event operates on different schedules throughout the week. You can assign multiple departure times for each day, and only the configured times for the selected day will be available to attendees. ', 'abp-event-ticket' ),
					'specific_off_dates'          => __( 'Note: please add your specific Operation off dates.(optional)', 'abp-event-ticket' ),
					'date_wise_time'              => __( 'Note: Set the Event  time for specific dates. A date will only be saved if it has at least one operation time. If a date is not saved, the regular day-wise schedule or the default operation time will be applied. You can add multiple operation times for the same date.(optional)', 'abp-event-ticket' ),
					'off_date_range'              => __( 'Note: If you have off days between two dates which can add here.(optional)', 'abp-event-ticket' ),
					//=============================//
					'_tax_class'                  => __( 'Note: If you want to add any new tax class , Please go to WooCommerce ->configuration->Tax Area', 'abp-event-ticket' ),
					'enable_tax_msg'              => __( 'Note: Your Woo-commerce Tax setting already disable. If you want to enable tax please enable woo-commerce tax.', 'abp-event-ticket' ),
					//=============================//
					'display_additional_services' => __( 'Note: If you want sale additional product/equipment with this  Event then active this button and add additional service. Additional item not depends on  operation time.', 'abp-event-ticket' ),
					'active_global_additional'    => __( 'Note: Keep this switch ON to apply the global additional settings.Switch it OFF if you want to set special additional rules for this Event.additional configuration options will open when turned OFF. ', 'abp-event-ticket' ),
					//=============================//
					'attendee_off'                => __( 'Note: Globally, the Attendee Form feature is currently disabled. To add an attendee form for this Event, please enable the feature from the Global Settings, then reload this page.', 'abp-event-ticket' ),
					'client_form_option'          => __( 'Use comma( , ) to separate option.', 'abp-event-ticket' ),
					'display_client_form'         => __( 'Note: If you want to get Client information then active this button and add form/import global form or use global form as a client form', 'abp-event-ticket' ),
					'active_global_form'          => __( 'Note: Keep this switch ON to apply the global Client Form settings.Switch it OFF if you want to set special  Client Form rules for this Event. Client Form configuration options will open when turned OFF. ', 'abp-event-ticket' ),
					'display_single_form'         => __( 'If you want to get single traveller/attendee info for multiple ticket  then active this button .Default is on', 'abp-event-ticket' ),
					//=============================//
					'display_tc'                  => __( 'Use this switch to control whether the Term & Condition is displayed on the frontend. Turn the switch ON to show the Term & Condition, and OFF to hide it. By default, this option is set to ON.', 'abp-event-ticket' ),
					'active_global_tc'            => __( 'Enable this switch to apply the global Term & Condition to this post. If you want to add custom Term & Condition specifically for this post, turn the switch OFF and add your custom Term & Condition below.You can also use the Import button to bring in global Term & Condition, which you can then edit or delete based on your needs.', 'abp-event-ticket' ),
					//=============================//
					'faq_item'                    => __( 'Both the Title and Description fields are required. If either field is left empty, this FAQ item will not be displayed on the frontend.', 'abp-event-ticket' ),
					'display_faq'                 => __( 'Use this switch to control whether the FAQ is displayed on the frontend. Turn the switch ON to show the FAQ, and OFF to hide it. By default, this option is set to ON.', 'abp-event-ticket' ),
					'active_global_faq'           => __( 'Enable this switch to apply the global FAQ to this post. If you want to add custom FAQs specifically for this post, turn the switch OFF and add your custom FAQs below.You can also use the Import button to bring in global FAQs, which you can then edit or delete based on your needs.', 'abp-event-ticket' ),
					//=============================//
					'timeline_item'               => __( 'Add the event schedule steps in order: set a time (when this step happens), a short title, and a description. The item is displayed on the frontend timeline when the Title field is filled.', 'abp-event-ticket' ),
					'display_timeline'            => __( 'Use this switch to control whether the Event Timeline is displayed on the frontend. Turn the switch ON to show the timeline, and OFF to hide it. By default, this option is set to ON.', 'abp-event-ticket' ),
					//=============================//
					'search_get_wrong_data_info'  => __( 'Somethings went Wrong ! Please Try again', 'abp-event-ticket' ),
					'sale_close_msg'              => __( 'This Event sale close shortly. please try another Event.', 'abp-event-ticket' ),
					'not_date'                    => __( 'No Dates Found !', 'abp-event-ticket' ),
					'not_match'                   => __( 'No Results Found !', 'abp-event-ticket' ),
					'not_found'                   => __( 'Nothing Found !', 'abp-event-ticket' ),
					'no_sp'                       => __( 'No Seat Plan Found. Click Add New to create one.', 'abp-event-ticket' ),
					//=============================//
					'no_ticket_type'              => __( 'No Ticket Type Found ! Please add Ticket Type to use Multiple Ticket Type', 'abp-event-ticket' ),
					'no_ticket_config'            => __( 'No ticket configuration is available for this Event. Please contact the administrator.', 'abp-event-ticket' ),
					'no_sp_config'                => __( 'No Seat Plan configuration is available for this Event. Please contact the administrator.', 'abp-event-ticket' ),
					'ticket_settings'             => __( 'Configure the ticket or seat type with a name, color, prefix, and optional image, icon, or emoji. These settings will be applied automatically to all assigned seats in the Seat Plan and used throughout the booking process for consistent identification.', 'abp-event-ticket' ), //=============================//
					'no_decor_item'               => __( 'No Decor Item Found ! Please add Decor item to use Multiple Decor item', 'abp-event-ticket' ),
					'decor_setting'               => __( 'Note: Choose an image, icon, or emoji to represent this decoration item within the Seat Plan layout. Enter a name to identify the item while designing the layout. Decoration items are used only for creating and organizing the Seat Plan and are not considered bookable seats, so they do not affect pricing, availability, or the booking process. The item name will not be displayed to customers, but you can add custom text, change the font size, or modify individual items directly from the Seat Plan editor by double-clicking on them. You can also choose a background color to make different layout elements easier to identify while designing the seat arrangement. Once configured, the selected settings will automatically be applied wherever this decoration item is used.', 'abp-event-ticket' ),
					//=============================//
					'must_wc'                     => __( 'Event Ticket Plugin is entirely dependent on the WooCommerce plugin. Please install and activate the WooCommerce plugin otherwise the plugin will not work. Installing this tool may take some time', 'abp-event-ticket' ),
					//=============================//
					'abpet_ticket'                => __( 'Here you can create and manage dynamic ticket types that can be used across all Events. Examples include Economy, Business Class, VIP, Sleeper, Cabin, and more. Each ticket type can have its own price, capacity, color, icon, image, and other configurations. If a seat plan is enabled, you can create seats based on the selected ticket type, and every seat will automatically inherit the corresponding ticket settings. Every ticket type also has a unique ID . You can edit or delete ticket types at any time, but removing a ticket type that is already assigned to a Event may affect the existing configuration.', 'abp-event-ticket' ),
					'abpet_decor'                 => __( 'Note : Here you can create and manage decorative items for the seat plan layout. These items are used only for designing and organizing the seating arrangement and are not considered actual seats. Therefore, they cannot be booked, reserved, or sold. Examples include doors, exits, driver areas, engines, restrooms, food storage, baggage storage, waiting areas, lobbies, walkways, stairs, tables, partitions, and other decorative elements. If needed, you can also add custom text, icons, images, or emojis to create a more realistic seat layout. Every decoration item has its own unique ID and can be reused across multiple seat plans.', 'abp-event-ticket' ),
					'abpet_sp'                    => __( 'Note : Here you can create and manage reusable seat plans that can be assigned to any Event multiple times. Instead of designing the same layout repeatedly, you can create a seat plan once and reuse it whenever needed. The system will automatically calculate the total number of available seats and detect all assigned ticket types from the selected seat plan. If the Event is configured to use a single ticket type, all seats in the selected seat plan will automatically be converted into that ticket type, even if the original layout contains multiple ticket types. You can also use decoration items such as doors, walkways, engines, exits, luggage storage areas, tables, and other custom elements to create a more realistic layout. Every seat plan has a unique ID', 'abp-event-ticket' ),
					'abpet_sp_design'             => __( 'Note : Here you can design and customize the entire seat layout according to your requirements. You can define the overall layout structure by setting the number of rows and columns, adjusting the background image or background color, and configuring the width, height, and spacing of individual cells. Each cell can occupy one or multiple positions, allowing you to create more complex layouts such as walkways, cabins, tables, lounges, storage areas, and other custom sections.You can select a ticket type and simply click on any cell to automatically assign the selected ticket type along with the corresponding seat number. Likewise, you can select a decoration item and place it anywhere in the layout. Both seat items and decoration items support drag-and-drop functionality, allowing you to move, duplicate, or clone them easily.Every cell can have its own custom text, icon, image, emoji, color, and font size. Double-clicking on a cell allows you to modify its content and appearance individually. You can also resize cells by defining custom width and height values.Advanced selection tools are also available to speed up the design process. Use Ctrl + Click to select individual items and Shift + Click to select multiple cells within a range. Once selected, you can apply changes to all selected items simultaneously.This powerful visual editor makes it easy to create simple or highly detailed layouts for buses, trains, ferries, aircraft, theaters, stadiums, conference halls, and many other seating arrangements. ', 'abp-event-ticket' ),
					'abpet_dates'                 => __( 'Note: Set a global date configuration for your Event  that can be reused across all posts', 'abp-event-ticket' ),
					'abpet_additional'            => __( 'Note: Add extra services for products/equipment with your Event—import or set per Post (also usable globally); stock applies per Post, empty quantity = unlimited, empty max qty = no limit, empty/Zero price = free.', 'abp-event-ticket' ),
					'abpet_form'                  => __( 'Note: This is a flexibility global form system. Once you design the structure here, it serves as a global form. You can effortlessly import this form into any Event or use this setting at any Event,', 'abp-event-ticket' ),
					'abpet_faq'                   => __( 'Note: You can set all Event-related FAQs here and use them globally across all Events. You can also import these FAQs into any individual Event and customize them as needed.', 'abp-event-ticket' ),
					'abpet_tc'                    => __( 'Note: You can set all Event-related Term & Condition here and use them globally across all Event. You can also import these Term & Condition into any individual Event and customize them as needed.', 'abp-event-ticket' ),
					'abpet_location'              => __( 'Note: Here, you can add all of your Event Location, which can then be used across any Event . You can edit or delete them at any time. However, please note that deleting a location that is already assigned to a Event may affect the existing configuration. If a location is not currently assigned anywhere, deleting it will not cause any issues.', 'abp-event-ticket' ),
					'abpet_category'              => __( 'Note : Here you can create and manage all Event types or categories. You can assign  categories to each Event while creating or editing a post. Every category has a unique ID, which can also be used in shortcodes to display specific post types anywhere on your website. You can edit or delete categories at any time, but removing a category that is already assigned to a post may affect its existing configuration.', 'abp-event-ticket' ),
					'abpet_organizer'             => __( 'Note: Here you can create and manage Event organizers, operators, or companies. You can assign  organizers to each Event while creating or editing a post. Every organizer has a unique ID that can be used in shortcodes to display Event from a specific organizer anywhere on your website. You can edit or delete organizers at any time, but deleting an organizer that is already assigned to a post may affect the existing configuration.', 'abp-event-ticket' ),
					'abpet_brand'                 => __( 'Note : Here you can create and manage Event brands, manufacturers, or service providers. You can assign  brand to each Event while creating or editing a post. Every brand has a unique ID that can be used in shortcodes to display post from a specific brand anywhere on your website. You can edit or delete brands at any time, but deleting a brand that is already assigned to a post may affect the existing configuration.', 'abp-event-ticket' ),
					'abpet_feature'               => __( 'Note : Here you can create and manage Event features that help attendee understand the facilities and services available with each Event. Examples include Wi-Fi, air conditioning, charging ports, refreshments, entertainment systems, restrooms, and more. You can assign one or multiple features to each post while creating or editing a post.  You can edit or delete features at any time, but removing a feature that is already assigned to a post may affect the existing configuration.', 'abp-event-ticket' ),
					//=============================//
					'sign_up_msg'                 => __( 'Please Login your account to Download/View ticket !', 'abp-event-ticket' ),
					'no_permit_msg'               => __( 'You are not permitted to Download/View this ticket !', 'abp-event-ticket' ),
					'wrong_msg_id'                => __( 'We see, this id are not valid !', 'abp-event-ticket' ),
					'no_order_found'              => __( 'Sorry ! We can not find any Order in your criteria.', 'abp-event-ticket' ),
					//''          => __( '', 'abp-event-ticket' ),
				);
				$des          = apply_filters( 'abpet_info_array_filter', $des );
				return $des[ $key ] ?? '';
			}
			public static function icon_svg( $key ): void {
				$des              = [
					'user_group_1' => '<svg  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"  d="M17 20c0-1.657-2.239-3-5-3s-5 1.343-5 3m14-3c0-1.23-1.234-2.287-3-2.75M3 17c0-1.23 1.234-2.287 3-2.75m12-4.014a3 3 0 1 0-4-4.472m-8 4.472a3 3 0 0 1 4-4.472M12 14a3 3 0 1 1 0-6a3 3 0 0 1 0 6Z"/></svg>',
					'user_group_2' => '<svg  viewBox="0 0 24 24"> <path fill="currentColor" fill-rule="evenodd" d="M12 6a3.5 3.5 0 1 0 0 7a3.5 3.5 0 0 0 0-7m-1.5 8a4 4 0 0 0-4 4c0 1.1.9 2 2 2h7a2 2 0 0 0 2-2a4 4 0 0 0-4-4zm6.8-3.1a5.5 5.5 0 0 0-2.8-6.3c.6-.4 1.3-.6 2-.6a3.5 3.5 0 0 1 .8 6.9m2.2 7.1h.5a2 2 0 0 0 2-2a4 4 0 0 0-4-4h-1.1l-.5.8c1.9 1 3.1 3 3.1 5.2M4 7.5a3.5 3.5 0 0 1 5.5-2.9A5.5 5.5 0 0 0 6.7 11A3.5 3.5 0 0 1 4 7.5M7.1 12H6a4 4 0 0 0-4 4c0 1.1.9 2 2 2h.5a6 6 0 0 1 3-5.2z" clip-rule="evenodd"/></svg>',
					'plus'         => '<svg viewBox="0 0 16 16"><path fill="currentColor" d="M14 7H9V2H7v5H2v2h5v5h2V9h5V7z"/></svg>',
					'minus_1'      => '<svg viewBox="0 0 16 16"><path fill="currentColor" d="M2 7h12v2H2V7z"/></svg>',
					'minus_2'      => '<svg viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.707 10.295a2.41 2.41 0 0 0 0 3.41l7.588 7.588a2.41 2.41 0 0 0 3.41 0l7.588-7.588a2.41 2.41 0 0 0 0-3.41l-7.588-7.588a2.41 2.41 0 0 0-3.41 0zM8.5 12h7"/></svg>',
					'minus_3'      => '<svg viewBox="0 0 32 32"><path fill="currentColor" d="M16 3C8.832 3 3 8.832 3 16s5.832 13 13 13s13-5.832 13-13S23.168 3 16 3zm0 2c6.087 0 11 4.913 11 11s-4.913 11-11 11S5 22.087 5 16S9.913 5 16 5zm-6 10v2h12v-2H10z"/></svg>',
					'minus_4'      => '<svg viewBox="0 0 24 24"><path fill="currentColor" d="M299 213H0v-42h299v42z"/></svg>',
					'save'         => '<svg  viewBox="0 0 100 100.016"><path fill="#23475F" d="M88.555 0H83v.016a2 2 0 0 1-2 2H19a2 2 0 0 1-2-2V0H4a4 4 0 0 0-4 4v92.016a4 4 0 0 0 4 4h92a4 4 0 0 0 4-4V11.525C100.049 11.436 88.564.071 88.555 0z"/><path fill="#1C3C50" d="M81.04 53.016H18.96a2 2 0 0 0-2 2v45h66.08v-45c0-1.106-.895-2-2-2zm-61.957-10h61.834a2 2 0 0 0 2-2V.555A1.993 1.993 0 0 1 81 2.015H19c-.916 0-1.681-.62-1.917-1.46v40.46a2 2 0 0 0 2 2.001z"/><path fill="#EBF0F1" d="M22 55.985h56a2 2 0 0 1 2 2v37.031a2 2 0 0 1-2 2H22c-1.104 0-2-.396-2-1.5V57.985a2 2 0 0 1 2-2z"/><path fill="#BCC4C8" d="M25 77.016h50v1H25v-1zm0 10h50v1H25v-1z"/><path fill="#1C3C50" d="M7 84.016h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2zm83 0h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-3a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2z"/><path fill="#BCC4C8" d="M37 1.989v36.026a2 2 0 0 0 2 2h39a2 2 0 0 0 2-2V1.989c0 .007-42.982.007-43 0zm37 29.027a2 2 0 0 1-2 2h-6a2 2 0 0 1-2-2V10.989a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v20.027z"/><path fill="#FF9D00" d="M78 55.985H22a2 2 0 0 0-2 2v10.031h60V57.985a2 2 0 0 0-2-2z"/></svg>',
					'edit'         => '<svg  viewBox="0 0 16 16"><path fill="currentColor" d="M15.49 7.3h-1.16v6.35H1.67V3.28H8V2H1.67A1.21 1.21 0 0 0 .5 3.28v10.37a1.21 1.21 0 0 0 1.17 1.25h12.66a1.21 1.21 0 0 0 1.17-1.25z"/><path fill="currentColor" d="M10.56 2.87L6.22 7.22l-.44.44l-.08.08l-1.52 3.16a1.08 1.08 0 0 0 1.45 1.45l3.14-1.53l.53-.53l.43-.43l4.34-4.36l.45-.44l.25-.25a2.18 2.18 0 0 0 0-3.08a2.17 2.17 0 0 0-1.53-.63a2.19 2.19 0 0 0-1.54.63l-.7.69l-.45.44zM5.51 11l1.18-2.43l1.25 1.26zm2-3.36l3.9-3.91l1.3 1.31L8.85 9zm5.68-5.31a.91.91 0 0 1 .65.27a.93.93 0 0 1 0 1.31l-.25.24l-1.3-1.3l.25-.25a.88.88 0 0 1 .69-.25z"/></svg>',
					'drag'         => '<svg  viewBox="0 0 16 16"><path fill="currentColor" d="m15.46 7l-3.2-2.19l-.71 1l2.29 1.57H8.62V2.16l1.57 2.29l1-.71L9 .54a1.25 1.25 0 0 0-2 0l-2.22 3.2l1 .71l1.59-2.29v5.22H2.16l2.29-1.57l-.71-1L.54 7a1.25 1.25 0 0 0 0 2l3.2 2.19l.71-1l-2.29-1.57h5.21v5.22l-1.56-2.29l-1 .71L7 15.46a1.25 1.25 0 0 0 2.06 0l2.19-3.2l-1-.71l-1.63 2.29V8.62h5.22l-2.29 1.57l.71 1L15.46 9a1.25 1.25 0 0 0 0-2z"/></svg>',
					'order'        => '<svg  viewBox="0 0 16 16"> <path fill="none" stroke="currentColor" stroke-linejoin="round" d="M5 11.5h4M5 9h6M5 6.5h6m-5.5-4h-2v12h9v-12h-2m-5-1h5l-.625 2h-3.75z"/></svg>',
					'seat'         => '<svg viewBox="0 0 24 24"><path fill="currentColor" d="M4 21v-6h16v6h-2v-4H6v4H4Zm-1-7v-3h3v3H3Zm4 0V3h10v11H7Zm11 0v-3h3v3h-3Z"/></svg>',
					'globe'        => '<svg viewBox="0 0 100 100"><path id="gisGlobe0" fill="currentColor" fill-opacity="1" fill-rule="nonzero" stroke="none" stroke-dasharray="none" stroke-dashoffset="188.976" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="4" stroke-opacity="1" stroke-width="5" d="M52.5 5.682v20.187h17.676c-.988-2.823-2.13-5.429-3.408-7.75c-3.966-7.2-9-11.541-14.268-12.437zm-5 .197c-4.93 1.223-9.61 5.462-13.342 12.24c-1.278 2.321-2.42 4.927-3.408 7.75H47.5V5.88zM35.98 7.232C25.985 10.5 17.545 17.163 12.01 25.87h13.455c1.187-3.695 2.633-7.112 4.312-10.162c1.793-3.255 3.88-6.123 6.203-8.475zm29.41.463c2.145 2.263 4.082 4.967 5.758 8.012c1.68 3.05 3.123 6.467 4.307 10.162H87.99c-5.28-8.306-13.202-14.761-22.6-18.174zM9.257 30.87A44.79 44.79 0 0 0 5.072 47.5h16.79c.194-5.872.957-11.469 2.202-16.63H9.256zm19.974 0c-1.32 5.077-2.15 10.696-2.363 16.631H47.5V30.87H29.23zm23.27 0V47.5H74.06c-.212-5.935-1.043-11.554-2.364-16.63H52.5zm24.355 0c1.243 5.163 2.004 10.76 2.198 16.631h15.875a44.79 44.79 0 0 0-4.184-16.63h-13.89zM5.072 52.5a44.79 44.79 0 0 0 4.184 16.63h14.572c-1.174-5.176-1.865-10.774-1.994-16.63H5.072zm21.762 0c.14 5.915.901 11.53 2.146 16.63H47.5V52.5H26.834zm25.666 0v16.63h19.445c1.245-5.1 2.006-10.715 2.147-16.63H52.5zm26.576 0c-.129 5.855-.815 11.453-1.986 16.63h13.654a44.79 44.79 0 0 0 4.184-16.63H79.076zM12.01 74.13c5.285 8.313 13.214 14.772 22.62 18.183c-1.785-2.05-3.415-4.407-4.853-7.018c-1.83-3.325-3.389-7.08-4.63-11.164H12.01zm18.394 0c1.062 3.216 2.326 6.159 3.754 8.753c3.5 6.355 7.834 10.475 12.424 11.974c.306.023.61.054.918.07V74.132H30.404zm22.096 0v20.798a45.48 45.48 0 0 0 2.127-.162c4.485-1.575 8.713-5.658 12.14-11.883c1.429-2.594 2.693-5.537 3.754-8.752H52.5zm23.275 0c-1.239 4.085-2.796 7.84-4.627 11.165c-1.311 2.382-2.782 4.556-4.386 6.476a45.06 45.06 0 0 0 21.228-17.64H75.775z" color="currentColor" color-interpolation="sRGB" color-rendering="auto" display="inline" opacity="1" vector-effect="none" visibility="visible"/></svg>',
					'setting'      => '<svg viewBox="0 0 24 24"><g fill="none" fill-rule="evenodd"><path d="M24 0v24H0V0h24ZM12.594 23.258l-.012.002l-.071.035l-.02.004l-.014-.004l-.071-.036c-.01-.003-.019 0-.024.006l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427c-.002-.01-.009-.017-.016-.018Zm.264-.113l-.014.002l-.184.093l-.01.01l-.003.011l.018.43l.005.012l.008.008l.201.092c.012.004.023 0 .029-.008l.004-.014l-.034-.614c-.003-.012-.01-.02-.02-.022Zm-.715.002a.023.023 0 0 0-.027.006l-.006.014l-.034.614c0 .012.007.02.017.024l.015-.002l.201-.093l.01-.008l.003-.011l.018-.43l-.003-.012l-.01-.01l-.184-.092Z"/><path fill="currentColor" d="M16 15c1.306 0 2.418.835 2.83 2H20a1 1 0 1 1 0 2h-1.17a3.001 3.001 0 0 1-5.66 0H4a1 1 0 1 1 0-2h9.17A3.001 3.001 0 0 1 16 15Zm0 2a1 1 0 1 0 0 2a1 1 0 0 0 0-2ZM8 9a3 3 0 0 1 2.762 1.828l.067.172H20a1 1 0 0 1 .117 1.993L20 13h-9.17a3.001 3.001 0 0 1-5.592.172L5.17 13H4a1 1 0 0 1-.117-1.993L4 11h1.17A3.001 3.001 0 0 1 8 9Zm0 2a1 1 0 1 0 0 2a1 1 0 0 0 0-2Zm8-8c1.306 0 2.418.835 2.83 2H20a1 1 0 1 1 0 2h-1.17a3.001 3.001 0 0 1-5.66 0H4a1 1 0 0 1 0-2h9.17A3.001 3.001 0 0 1 16 3Zm0 2a1 1 0 1 0 0 2a1 1 0 0 0 0-2Z"/></g></svg>',
					'status'       => '<svg  viewBox="0 0 24 24"><path fill="currentColor" fill-rule="evenodd" d="M16.001 5.2q-.262.385-.445.82a7 7 0 0 0-.157.405c-.165.461-.27.881-.373 1.303l-.005.019c-.132.533-.264 1.067-.525 1.687a5 5 0 0 1-.48.88l-.006.006l-.002.004c2.325.684 4.925-.224 5.868-2.452c.58-1.368.257-3.08-1.052-3.672c-1.054-.476-2.088-.086-2.822 1M3.184 12.023q.147-.447.38-.854q.111-.194.224-.37c.264-.408.532-.742.8-1.076l.026-.031c.335-.42.672-.84.998-1.414q.234-.407.386-.854l.006-.019l.018-.058c1.897 1.55 2.71 4.266 1.52 6.362c-.729 1.289-2.258 2.021-3.487 1.268c-.99-.605-1.29-1.702-.871-2.954M21 17.251c-2.51 0-4.544-2.108-4.544-4.708h-2.654C13.802 16.662 17.025 20 21 20zm-8.663-4.708c0 4.119-3.223 7.457-7.198 7.457v-2.75c2.51 0 4.544-2.107 4.544-4.707zm0 0l-.002-8.324H9.68l.002 8.324z" clip-rule="evenodd"/></svg>',
					'category'     => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="12" rx="2"/>  <path d="M3 9h18"/>  <path d="M7 16v2"/>  <path d="M17 16v2"/><circle cx="7" cy="18.5" r="0.5" fill="currentColor" stroke="none"/>  <circle cx="17" cy="18.5" r="0.5" fill="currentColor" stroke="none"/></svg>',
					'category_1'   => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none">  <rect x="4" y="7" width="16" height="12" rx="2" stroke="currentColor" stroke-width="2"/>  <line x1="4" y1="11" x2="20" y2="11" stroke="currentColor" stroke-width="2"/>  <line x1="8" y1="4" x2="8" y2="9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>  <line x1="16" y1="4" x2="16" y2="9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>  <circle cx="8.5" cy="15" r="1.3" fill="currentColor"/></svg>',
					'brand_1'      => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l7 3v6c0 5-3 8.5-7 11-4-2.5-7-6-7-11V5z"/>  <path d="M9 12l2 2 4-4"/></svg>',
					'brand_2'      => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18" rx="1"/><line x1="8" y1="7" x2="8" y2="7.01"/><line x1="12" y1="7" x2="12" y2="7.01"/><line x1="16" y1="7" x2="16" y2="7.01"/><line x1="8" y1="11" x2="8" y2="11.01"/><line x1="12" y1="11" x2="12" y2="11.01"/><line x1="16" y1="11" x2="16" y2="11.01"/><path d="M9 21v-4h6v4"/></svg>',
					'organizer_1'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="3.5"/>  <path d="M5 20c0-3.5 3-6 7-6s7 2.5 7 6"/>  <path d="M17.5 4.5l1 1 2-2"/></svg>',
					'organizer_2'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h2"/><path d="M8 12h2"/><path d="M8 16h2"/><path d="M14 8l1.5 1.5L18 7"/><path d="M14 12l1.5 1.5L18 11"/><path d="M14 16l1.5 1.5L18 15"/></svg>',
					'date_1'       => '<svg viewBox="0 0 48 48">    <path fill="#CFD8DC" d="M5 38V14h38v24c0 2.2-1.8 4-4 4H9c-2.2 0-4-1.8-4-4z"/>    <path fill="#F44336" d="M43 10v6H5v-6c0-2.2 1.8-4 4-4h30c2.2 0 4 1.8 4 4z"/>    <g fill="#B71C1C">        <circle cx="33" cy="10" r="3"/>        <circle cx="15" cy="10" r="3"/>    </g>    <path fill="#B0BEC5" d="M33 3c-1.1 0-2 .9-2 2v5c0 1.1.9 2 2 2s2-.9 2-2V5c0-1.1-.9-2-2-2zM15 3c-1.1 0-2 .9-2 2v5c0 1.1.9 2 2 2s2-.9 2-2V5c0-1.1-.9-2-2-2z"/>    <path fill="#90A4AE" d="M13 20h4v4h-4zm6 0h4v4h-4zm6 0h4v4h-4zm6 0h4v4h-4zm-18 6h4v4h-4zm6 0h4v4h-4zm6 0h4v4h-4zm6 0h4v4h-4zm-18 6h4v4h-4zm6 0h4v4h-4zm6 0h4v4h-4zm6 0h4v4h-4z"/></svg>',
					'date_2'       => '<svg  viewBox="0 0 64 64">    <path fill="#ba9372" d="M58.1 19.6c-9.8-6-25-14.8-27.1-14.8c-2.1 0-16.7 8.9-26 14.8l-.5-.9C6.9 17.2 28 3.8 31 3.8s25.1 13.4 27.6 15l-.5.8"/>    <path fill="#93a2aa" d="M62 56.8c0 1.6-1.2 3-2.7 3H6.8c-1.5 0-2.7-1.3-2.7-3V21.1c0-1.6 1.2-3 2.7-3h52.5c1.5 0 2.7 1.3 2.7 3v35.7"/>    <path fill="#ed4c5c" d="M60 21.1c0-1.6-1.2-3-2.7-3H4.7c-1.5 0-2.7 1.3-2.7 3v9.5h58v-9.5z"/>    <path fill="#d9e3e8" d="M2 30.6v26.2c0 1.6 1.2 3 2.7 3h52.5c1.5 0 2.7-1.3 2.7-3V30.6H2z"/> <path fill="#93a2aa" d="M4.5 33h6v2.2h-6zm7.8 0h6v2.2h-6zm7.9 0h6v2.2h-6zm7.8 0h6v2.2h-6zm7.8 0h6v2.2h-6zm7.9 0h6v2.2h-6zm7.8 0h6v2.2h-6zM28 37.4h6v2.2h-6zm7.8 0h6v2.2h-6zm7.9 0h6v2.2h-6zm7.8 0h6v2.2h-6zm-47 4.4h6V44h-6zm7.8 0h6V44h-6zm7.9 0h6V44h-6zm7.8 0h6V44h-6zm7.8 0h6V44h-6zm7.9 0h6V44h-6zm7.8 0h6V44h-6zm-47 4.5h6v2.2h-6zm7.8 0h6v2.2h-6zm7.9 0h6v2.2h-6zm7.8 0h6v2.2h-6zm7.8 0h6v2.2h-6zm7.9 0h6v2.2h-6zm7.8 0h6v2.2h-6zm-47 4.4h6v2.2h-6zm7.8 0h6v2.2h-6zm7.9 0h6v2.2h-6zm7.8 0h6v2.2h-6zm7.8 0h6v2.2h-6zm7.9 0h6v2.2h-6zm7.8 0h6v2.2h-6zm-47 4.4h6v2.2h-6zm7.8 0h6v2.2h-6zm7.9 0h6v2.2h-6zm7.8 0h6v2.2h-6zm7.8 0h6v2.2h-6zm7.9 0h6v2.2h-6z"/><ellipse cx="31.2" cy="6.2" fill="#333" rx="1.8" ry="1.9"/><ellipse cx="31" cy="6.2" fill="#93a2aa" rx="1.8" ry="1.9"/>    <path fill="#fff" d="M19.5 25.5v.2c0 .6.1 1 .2 1.3c.1.2.3.4.7.4c.4 0 .6-.1.7-.4c.1-.2.1-.4.1-.8v-5.5h1.6v5.4c0 .7-.1 1.2-.3 1.6c-.4.7-1 1-2 1s-1.6-.3-2-.8c-.3-.5-.5-1.3-.5-2.2v-.2h1.5m5-4.8h1.6v4.8c0 .5.1.9.2 1.2c.2.4.6.7 1.3.7c.6 0 1.1-.2 1.3-.7c.1-.3.1-.7.1-1.2v-4.8h1.6v4.8c0 .8-.1 1.5-.4 1.9c-.5.8-1.4 1.3-2.7 1.3c-1.3 0-2.2-.4-2.7-1.3c-.3-.5-.4-1.1-.4-1.9c.1 0 .1-4.8.1-4.8m7.7 0h1.6v6.4h3.8v1.4h-5.4v-7.8m10 0H44l-2.6 4.9v2.9h-1.6v-2.9l-2.7-4.9H39l1.6 3.4l1.6-3.4"/></svg>',
					'clone_1'      => '<svg viewBox="0 0 1792 1792"><path fill="currentColor" d="M1664 1632V544q0-13-9.5-22.5T1632 512H544q-13 0-22.5 9.5T512 544v1088q0 13 9.5 22.5t22.5 9.5h1088q13 0 22.5-9.5t9.5-22.5zm128-1088v1088q0 66-47 113t-113 47H544q-66 0-113-47t-47-113V544q0-66 47-113t113-47h1088q66 0 113 47t47 113zm-384-384v160h-128V160q0-13-9.5-22.5T1248 128H160q-13 0-22.5 9.5T128 160v1088q0 13 9.5 22.5t22.5 9.5h160v128H160q-66 0-113-47T0 1248V160Q0 94 47 47T160 0h1088q66 0 113 47t47 113z"/></svg>',
					'clone_2'      => '<svg viewBox="0 0 36 36"><path fill="currentColor" d="M24 10V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h4V12a2 2 0 0 1 2-2Z" class="clr-i-solid clr-i-solid-path-1"/>    <path fill="currentColor" d="M30 12H14a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V14a2 2 0 0 0-2-2Zm-2 11h-5v5h-2v-5h-5v-2h5v-5h2v5h5Z" class="clr-i-solid clr-i-solid-path-2"/>    <path fill="none" d="M0 0h36v36H0z"/></svg>',
					'close_1'      => '<svg viewBox="0 0 304 384"><path fill="currentColor" d="M299 73L179 192l120 119l-30 30l-120-119L30 341L0 311l119-119L0 73l30-30l119 119L269 43z"/></svg>',
					'close_2'      => '<svg viewBox="0 0 24 24"><path fill="currentColor" d="M19 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2m-3.4 14L12 13.4L8.4 17L7 15.6l3.6-3.6L7 8.4L8.4 7l3.6 3.6L15.6 7L17 8.4L13.4 12l3.6 3.6l-1.4 1.4Z"/></svg>',
					'view_1'       => '<svg viewBox="0 0 24 24" stroke="currentColor"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M2.25.75h19.5s1.5 0 1.5 1.5v19.5s0 1.5-1.5 1.5H2.25s-1.5 0-1.5-1.5V2.25s0-1.5 1.5-1.5"/><path d="M4.267 10.722a1.825 1.825 0 0 0 0 2.544C5.818 14.821 8.591 17.25 12 17.25c3.41 0 6.183-2.428 7.735-3.983a1.825 1.825 0 0 0 0-2.544C18.182 9.168 15.406 6.739 12 6.739s-6.18 2.427-7.733 3.983"/><path d="M9.75 11.991a2.25 2.25 0 1 0 4.5 0a2.25 2.25 0 0 0-4.5 0"/></g></svg>',
					'view_2'       => '<svg viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2" d="M12 21c-5 0-11-5-11-9s6-9 11-9s11 5 11 9s-6 9-11 9Zm0-14a5 5 0 1 0 0 10a5 5 0 0 0 0-10Z"/></svg>',
					'view_3'       => '<svg viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M5 12s2.545-5 7-5c4.454 0 7 5 7 5s-2.546 5-7 5c-4.455 0-7-5-7-5z"/><path d="M12 13a1 1 0 1 0 0-2a1 1 0 0 0 0 2zm9 4v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-2M21 7V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2"/></g></svg>',
					'pdf_1'        => '<svg  viewBox="0 0 48 48"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M7.45 5.5a2 2 0 0 0-1.95 2v33.1a2 2 0 0 0 2 2h33.1a2 2 0 0 0 2-2V7.45a2 2 0 0 0-2-1.95Z"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M20.09 30V18h2a6 6 0 0 1 6 6h0a6 6 0 0 1-6 6Zm12.39-11.96h5.98m-5.98 5.98h3.9m-3.9-5.98V30M9.54 30V18h4a4 4 0 0 1 0 8h-4"/></svg>',
					'property'     => '<svg viewBox="0 0 120 125" fill="none" stroke="currentColor" stroke-width="4" stroke-linejoin="round" stroke-linecap="round"><path d="M5 55 L 60 15 L 115 55"/><rect x="18" y="50" width="84" height="70" rx="2"/><rect x="50" y="80" width="24" height="40"/> <rect x="30" y="62" width="16" height="16"/> <rect x="74" y="62" width="16" height="16"/></svg>',
					''             => '',
				];
				$allowed_svg_tags = [
					'svg'      => [ 'class' => true, 'aria-hidden' => true, 'aria-labelledby' => true, 'role' => true, 'xmlns' => true, 'width' => true, 'height' => true, 'viewbox' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, ],
					'g'        => [ 'fill' => true, ],
					'title'    => [ 'title' => true, ],
					'path'     => [ 'd' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, ],
					'rect'     => [ 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'ry' => true, 'fill' => true, 'stroke' => true, ],
					'circle'   => [ 'cx' => true, 'cy' => true, 'r' => true, 'fill' => true, 'stroke' => true, ],
					'line'     => [ 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true, 'stroke' => true, 'stroke-width' => true, ],
					'polyline' => [ 'points' => true, 'fill' => true, 'stroke' => true, ],
				];
				$icons            = $des[ $key ] ?? '';
				echo ! empty( $icons ) ? wp_kses( $icons, $allowed_svg_tags ) : '';
			}
			public static function location(): array {
				return [
					0 => [ 'name' => 'New York City' ],
					1 => [ 'name' => 'Los Angeles' ],
					2 => [ 'name' => 'Chicago' ],
					3 => [ 'name' => 'Las Vegas' ],
					4 => [ 'name' => 'San Francisco' ],
					5 => [ 'name' => 'Miami' ],
					6 => [ 'name' => 'Orlando' ],
					7 => [ 'name' => 'Boston' ],
					8 => [ 'name' => 'Nashville' ],
					9 => [ 'name' => 'Austin' ],
				];
			}
			public static function category(): array {
				return [
					0 => [ 'name' => 'Concert' ],
					1 => [ 'name' => 'Conference' ],
					2 => [ 'name' => 'Workshop' ],
					3 => [ 'name' => 'Seminar' ],
					4 => [ 'name' => 'Festival' ],
					5 => [ 'name' => 'Sports' ],
					6 => [ 'name' => 'Theater' ],
					7 => [ 'name' => 'Exhibition' ],
					8 => [ 'name' => 'Party' ],
					9 => [ 'name' => 'Other' ],
				];
			}
			public static function organizer(): array {
				return [
					0 => [ 'name' => 'Global Events Group' ],
					1 => [ 'name' => 'EventPro Productions' ],
					2 => [ 'name' => 'Premier Events Network' ],
					3 => [ 'name' => 'Elite Event Management' ],
					4 => [ 'name' => 'NextGen Events' ],
					5 => [ 'name' => 'United Event Solutions' ],
				];
			}
			public static function brand(): array {
				return [
					0 => [ 'name' => 'Live Nation' ],
					1 => [ 'name' => 'Eventbrite' ],
					2 => [ 'name' => 'AEG Presents' ],
					3 => [ 'name' => 'Ticketmaster' ],
					4 => [ 'name' => 'IMG Events' ],
					5 => [ 'name' => 'C3 Presents' ],
					6 => [ 'name' => 'Global Events' ],
					7 => [ 'name' => 'Premier Events' ],
					8 => [ 'name' => 'EventPro' ],
					9 => [ 'name' => 'Elite Entertainment' ],
				];
			}
			public static function ticket(): array {
				return [
					1 => [ 'label' => 'Ticket', 'color' => '', 'prefix' => '', 'icon' => '🎟️', 'type' => 'seat', ],
					2 => [ 'label' => 'VIP', 'color' => '#A78BFA', 'prefix' => 'VIP-', 'icon' => '👑', 'type' => 'seat', ],
					3 => [ 'label' => 'Business Class', 'color' => '#0EA5E9', 'prefix' => 'B-', 'icon' => '🛋️', 'type' => 'seat', ],
					4 => [ 'label' => 'Special', 'color' => '#6366F1', 'prefix' => 'S-', 'icon' => 'fas fa-couch', 'type' => 'seat', ],
					5 => [ 'label' => 'Couple', 'color' => '#C026D3', 'prefix' => 'C-', 'icon' => '💑', 'type' => 'seat', ],
					6 => [ 'label' => 'Female', 'color' => '#F472B6', 'prefix' => 'F-', 'icon' => '👩', 'type' => 'seat', ],
					7 => [ 'label' => 'Adult', 'color' => '#78350F', 'prefix' => 'AD-', 'icon' => 'fas fa-chair', 'type' => 'seat', ],
					8 => [ 'label' => 'Child', 'color' => '#F59E0B', 'prefix' => 'CH-', 'icon' => '🪑', 'type' => 'seat', ],
					9 => [ 'label' => 'Economy', 'color' => '#84CC16', 'prefix' => 'E-', 'icon' => '💺', 'type' => 'seat', ],
				];
			}
			public static function decoration(): array {
				return [
					1  => [ 'label' => 'Blank Space', 'color' => '', 'icon' => '', 'type' => 'other' ],
					2  => [ 'label' => 'Security', 'color' => '#1E293B', 'icon' => '👨‍✈️', 'type' => 'other' ],
					3  => [ 'label' => 'Door Entry', 'color' => '#EAB308', 'icon' => '🚪', 'type' => 'other' ],
					4  => [ 'label' => 'Stairs', 'color' => '#64748B', 'icon' => '🪜', 'type' => 'other' ],
					5  => [ 'label' => 'Aisle/Walkway', 'color' => '#94A3B8', 'icon' => '↔', 'type' => 'other' ],
					6  => [ 'label' => 'Window', 'color' => '#38BDF8', 'icon' => '🪟', 'type' => 'other' ],
					7  => [ 'label' => 'Engine Box', 'color' => '#475569', 'icon' => '⚙️', 'type' => 'other' ],
					8  => [ 'label' => 'Toilet', 'color' => '#06B6D4', 'icon' => '🚽', 'type' => 'other' ],
					9  => [ 'label' => 'Luggage Rack', 'color' => '#F97316', 'icon' => '🧳', 'type' => 'other' ],
					10 => [ 'label' => 'Food/Snacks', 'color' => '#10B981', 'icon' => '🍔', 'type' => 'other' ],
					11 => [ 'label' => 'Emergency Exit', 'color' => '#EF4444', 'icon' => '🚨', 'type' => 'other' ],
				];
			}
			public static function additional(): array {
				return [
					'as_1' => [ 'icon' => 'fas fa-parking', 'name' => 'Event Parking', 'qty' => 100, 'max_qty' => 1, 'price' => 10.00, 'description' => 'Reserved parking space for the event', 'returnable' => 'no', ],
					'as_2' => [ 'icon' => 'fas fa-utensils', 'name' => 'Meal Package', 'qty' => 100, 'max_qty' => 1, 'price' => 15.00, 'description' => 'Meal and refreshments included', 'returnable' => 'no', ],
					'as_3' => [ 'icon' => 'fas fa-couch', 'name' => 'VIP Lounge Access', 'qty' => 50, 'max_qty' => 1, 'price' => 25.00, 'description' => 'Exclusive access to the VIP lounge', 'returnable' => 'no', ],
					'as_4' => [ 'icon' => 'fas fa-shirt', 'name' => 'Event T-Shirt', 'qty' => 100, 'max_qty' => 2, 'price' => 12.00, 'description' => 'Official event branded T-shirt', 'returnable' => 'yes', ],
				];
			}
			public static function form( $key = '' ): array {
				$form['pass_name']   = [ 'type' => 'text', 'required' => 'on', 'label' => __( 'First Name', 'abp-event-ticket' ) ];
				$form['pass_name_2'] = [ 'type' => 'text', 'required' => 'on', 'label' => __( 'Last Name', 'abp-event-ticket' ) ];
				$form['pass_email']  = [ 'type' => 'email', 'required' => 'on', 'label' => __( 'E-Mail', 'abp-event-ticket' ) ];
				$form['pass_phone']  = [ 'type' => 'text', 'required' => 'on', 'label' => __( 'Phone', 'abp-event-ticket' ) ];
				$form['pass_gender'] = [ 'type' => 'select', 'required' => 'off', 'label' => __( 'Gender', 'abp-event-ticket' ), 'option' => 'male,female' ];
				$form['pass_date']   = [ 'type' => 'date', 'required' => 'off', 'label' => __( 'Date of Birth', 'abp-event-ticket' ) ];
				if ( ! is_string( $key ) && ! is_int( $key ) ) {
					return $form;
				}
				if ( $key === '' ) {
					return $form;
				}
				return is_array( $form[ $key ] ?? null ) ? $form[ $key ] : [];
			}
			public static function faq(): array {
				return [
					1  => [
						'title' => 'How do I book an event ticket?',
						'des'   => '<p>Select your preferred event, date, ticket type, and seat if seat selection is available. Complete the checkout and payment process to confirm your booking.</p>',
					],
					2  => [
						'title' => 'Can I cancel my ticket?',
						'des'   => '<p>Ticket cancellation depends on the cancellation policy set by the event organizer. Refund eligibility may vary depending on the ticket type and booking conditions.</p>',
					],
					3  => [
						'title' => 'Can I change my ticket or event date?',
						'des'   => '<p>Ticket changes or date modifications are available only when allowed by the event organizer and subject to availability.</p>',
					],
					4  => [
						'title' => 'When should I arrive at the event venue?',
						'des'   => '<p>Attendees are advised to arrive at least 15 to 30 minutes before the scheduled start time to allow enough time for entry, ticket verification, and seating.</p>',
					],
					5  => [
						'title' => 'Will I receive a ticket confirmation?',
						'des'   => '<p>Yes, a booking confirmation will be provided after successful payment. Your ticket and booking details may also be available from your account dashboard.</p>',
					],
					6  => [
						'title' => 'What payment methods are supported?',
						'des'   => '<p>Available payment methods depend on the payment gateways configured by the website administrator.</p>',
					],
					7  => [
						'title' => 'Can I select my preferred seat?',
						'des'   => '<p>Yes, if seat selection is enabled for the event, you can choose your preferred available seat during the booking process.</p>',
					],
					8  => [
						'title' => 'What happens if the event is postponed?',
						'des'   => '<p>If an event is postponed, the event organizer will provide information about the new date, ticket validity, cancellation options, or refund policy.</p>',
					],
					9  => [
						'title' => 'Are children allowed to attend the event?',
						'des'   => '<p>Children may be allowed depending on the event rules and age restrictions set by the organizer. Please check the event details before booking.</p>',
					],
					10 => [
						'title' => 'Who should I contact for assistance?',
						'des'   => '<p>For booking or event-related assistance, please contact the event organizer using the contact information provided on the website.</p>',
					],
				];
			}
			public static function tc(): false|string {
				ob_start(); ?>
                <h6>1. Acceptance of Terms</h6>
                By purchasing or using an event ticket through this website, you agree to comply with these terms and conditions. If you do not agree with any part of these terms, please do not purchase or use the ticket.
                <h6>2. Ticket Booking and Confirmation</h6>
                A ticket booking is considered confirmed only after successful payment and receipt of a booking confirmation. Customers are responsible for providing accurate information during the booking process.
                <h6>3. Attendee Information</h6>
                Attendees must provide accurate and valid information, including their name, contact details, and any other required information. Incorrect or misleading information may result in ticket cancellation without notice.
                <h6>4. Ticket and Seat Availability</h6>
                All ticket and seat bookings are subject to availability. The event organizer reserves the right to change or reassign seats when necessary due to event arrangements, venue requirements, or operational reasons.
                <h6>5. Event Entry</h6>
                Attendees are advised to arrive at the event venue at least 15 to 30 minutes before the scheduled start time. Valid tickets may be required for entry, and late arrival may be subject to the event organizer's entry policy.
                <h6>6. Cancellation and Refund Policy</h6>
                Ticket cancellation and refund eligibility may vary depending on the event organizer's policy. Any applicable processing fees, taxes, or service charges may be deducted from the refund amount.
                <h6>7. Event Changes and Postponement</h6>
                Event dates, times, venues, schedules, performers, speakers, or other event details may be changed due to unforeseen circumstances, operational requirements, weather conditions, government regulations, or other reasons beyond the organizer's reasonable control.
                <h6>8. Event Cancellation</h6>
                If an event is cancelled, the event organizer will determine the applicable refund, rescheduling, or replacement arrangements according to the event's cancellation policy.
                <h6>9. Attendee Conduct</h6>
                Attendees must behave appropriately and follow the rules of the event and venue. Unlawful, abusive, disruptive, or unsafe behavior may result in removal from the event without compensation.
                <h6>10. Personal Belongings</h6>
                Attendees are responsible for their personal belongings brought to the event. The event organizer and venue are not responsible for any loss, damage, or theft of personal items unless otherwise required by applicable law.
                <h6>11. Limitation of Liability</h6>
                The event organizer shall not be held responsible for delays, cancellations, changes, injuries, loss of personal belongings, or other circumstances beyond reasonable control, except where liability cannot legally be excluded.
                <h6>12. Privacy and Personal Information</h6>
                Personal information collected during the ticket booking process will be used to process bookings, provide event-related services, and communicate important information in accordance with the applicable privacy policy.
                <h6>13. Changes to Terms</h6>
                These terms and conditions may be updated from time to time. Continued use of the ticketing service after changes are published indicates acceptance of the revised terms and conditions.
                <h6>14. Contact Information</h6>
                If you have any questions regarding these terms and conditions, your ticket, or the event, please contact the event organizer using the contact information provided on the website.
				<?php return ob_get_clean();
			}
			public static function feature(): array {
				return [
					1  => [ 'icon' => '🎟️', 'label' => 'E-Ticket' ],
					2  => [ 'icon' => '📱', 'label' => 'Mobile Ticket' ],
					3  => [ 'icon' => '🔳', 'label' => 'QR Code Entry' ],
					4  => [ 'icon' => '💺', 'label' => 'Seat Reservation' ],
					5  => [ 'icon' => '⭐', 'label' => 'VIP Access' ],
					6  => [ 'icon' => '🎤', 'label' => 'Live Performance' ],
					7  => [ 'icon' => '🎭', 'label' => 'Live Entertainment' ],
					8  => [ 'icon' => '🍽️', 'label' => 'Food & Refreshments' ],
					9  => [ 'icon' => '🅿️', 'label' => 'Event Parking' ],
					10 => [ 'icon' => '♿', 'label' => 'Wheelchair Accessible' ],
					11 => [ 'icon' => '👨‍👩‍👧‍👦', 'label' => 'Family Friendly' ],
					12 => [ 'icon' => '👶', 'label' => 'Child Friendly' ],
					13 => [ 'icon' => '📸', 'label' => 'Photo Opportunity' ],
					14 => [ 'icon' => '🎁', 'label' => 'Event Merchandise' ],
					15 => [ 'icon' => '🌐', 'label' => 'Online Event' ],
					16 => [ 'icon' => '📡', 'label' => 'Live Streaming' ],
					17 => [ 'icon' => '🔐', 'label' => 'Secure Entry' ],
					18 => [ 'icon' => '🕐', 'label' => 'Scheduled Event' ],
					19 => [ 'icon' => '💳', 'label' => 'Online Payment' ],
					20 => [ 'icon' => '📄', 'label' => 'Digital Invoice' ],
				];
			}
			public static function sp(): void {
				$sp_data = ABPET_Query::get_sp();
				if ( empty( $sp_data ) ) {
					global $wpdb;
					$table_name = $wpdb->prefix . 'abpet_sp';
					/**
					 * Generate event seat plan.
					 *
					 * @param int $total_seats Total number of seats.
					 * @param string $seat_type Seat type ID.
					 * @param string $prefix Seat name prefix.
					 * @param int $per_side Number of seats on each side of aisle.
					 * @param string $front_label Front area label.
					 *
					 * @return array Seat plan data.
					 */
					$generate_event_plan = static function ( int $total_seats, string $seat_type, string $prefix, int $per_side, string $front_label ): array {
						$rows        = (int) ceil( $total_seats / ( $per_side * 2 ) );
						$columns     = ( $per_side * 2 ) + 1;
						$layout_data = [];
						// Front area.
						$layout_data[] = [ 'index' => '0', 'type' => 'other', 'id' => '1', 'name' => 'Entrance', 'width_ratio' => (string) $per_side, 'fs' => '15', ];
						for ( $i = 1; $i < $columns; $i ++ ) {
							if ( $i === $per_side ) {
								$layout_data[] = [ 'index' => (string) count( $layout_data ), 'type' => 'other', 'id' => '2', 'name' => $front_label, 'width_ratio' => '2', 'fs' => '15', ];
							} else {
								$layout_data[] = [ 'index' => (string) count( $layout_data ), 'type' => 'other', 'id' => '1', 'name' => '', ];
							}
						}
						$seat_number = 1;
						for ( $row = 0; $row < $rows; $row ++ ) {
							$row_letter = chr( 65 + $row );
							for ( $column = 0; $column < $columns; $column ++ ) {
								if ( $column === $per_side ) {
									$layout_data[] = [ 'index' => (string) count( $layout_data ), 'type' => 'other', 'id' => '1', 'name' => $row === 0 ? 'Center Aisle' : '', 'height_ratio' => $row === 0 ? '9' : '', 'rotate' => $row === 0 ? '90' : '', 'fs' => $row === 0 ? '18' : '', ];
									continue;
								}
								if ( $seat_number > $total_seats ) {
									$layout_data[] = [ 'index' => (string) count( $layout_data ), 'type' => 'other', 'id' => '1', 'name' => '', ];
									continue;
								}
								$side_seat_number = $column < $per_side ? $column + 1 : $column;
								$layout_data[]    = [ 'index' => (string) count( $layout_data ), 'type' => 'seat', 'id' => $seat_type, 'name' => $prefix . '-' . $row_letter . $side_seat_number, ];
								$seat_number ++;
							}
						}
						return [
							'name'        => uniqid( 'sp_' ),
							'total_seats' => $total_seats,
							'others'      => wp_json_encode( [
								'bg_image' => '',
								'bg_color' => '#fff',
								'row'      => $rows + 1,
								'column'   => $columns,
								'width'    => 60,
								'height'   => 60,
								'gap'      => 5,
								'radius'   => 5,
							] ),
							'layout_data' => wp_json_encode( $layout_data ),
							'seat_info'   => wp_json_encode( [
								$seat_type => (string) $total_seats,
							] ),
						];
					};
					/*
					 * Event Seat Plan 1: Small Event
					 * 50 Seats
					 */
					$event_plan_data_1 = $generate_event_plan( 50, '1', 'SEAT', 5, 'Stage' );
					/*
					 * Event Seat Plan 2: Conference / Seminar
					 * 100 Seats
					 */
					$event_plan_data_2 = $generate_event_plan( 100, '2', 'SEAT', 5, 'Stage' );
					/*
					 * Event Seat Plan 3: Theater / Auditorium
					 * 150 Seats
					 */
					$event_plan_data_3 = $generate_event_plan( 150, '3', 'SEAT', 5, 'Stage' );
					/*
					 * Event Seat Plan 4: Large Conference / Concert
					 * 200 Seats
					 */
					$event_plan_data_4 = $generate_event_plan( 200, '4', 'SEAT', 5, 'Stage' );
					/*
					 * Event Seat Plan 5: Large Event
					 * 250 Seats
					 */
					$event_plan_data_5 = $generate_event_plan( 250, '5', 'SEAT', 5, 'Stage' );
					$event_plans       = [
						$event_plan_data_1,
						$event_plan_data_2,
						$event_plan_data_3,
						$event_plan_data_4,
						$event_plan_data_5,
					];
					$ticket_infos      = ABPET_Function::get_option( 'abpet_ticket_sp' );
					foreach ( $event_plans as $event_plan ) {
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
						$wpdb->insert( $table_name, $event_plan );
						$id                           = $wpdb->insert_id;
						$ticket_infos[ $id ]['type']  = json_decode( $event_plan['seat_info'], true );
						$ticket_infos[ $id ]['total'] = $event_plan['total_seats'];
					}
					update_option( 'abpet_ticket_sp', $ticket_infos );
				}
			}
			public function wc_config(): void {
				if ( ! check_ajax_referer( 'abpet_admin_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid security token or Insufficient permissions.', 'abp-event-ticket' ), 'type' => 'warn' ], 403 );
				}
				$post_val  = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : $default;
				$page_type = $post_val( 'type' );
				if ( $page_type == 'wc_install_active' ) {
					include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
					include_once( ABSPATH . 'wp-admin/includes/file.php' );
					include_once( ABSPATH . 'wp-admin/includes/misc.php' );
					include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
					$plugin = 'woocommerce';
					$api    = plugins_api( 'plugin_information', array(
						'slug'   => $plugin,
						'fields' => array(
							'short_description' => false,
							'sections'          => false,
							'requires'          => false,
							'rating'            => false,
							'ratings'           => false,
							'downloaded'        => false,
							'last_updated'      => false,
							'added'             => false,
							'tags'              => false,
							'compatibility'     => false,
							'homepage'          => false,
							'donate_link'       => false,
						),
					) );
					if ( is_wp_error( $api ) ) {
						wp_send_json_error( [ 'html' => '', 'msg' => $api->get_error_message() ] );
					}
					$title              = 'title';
					$url                = 'url';
					$nonce              = 'nonce';
					$woocommerce_plugin = new Plugin_Upgrader( new Plugin_Installer_Skin( compact( 'title', 'url', 'nonce', 'plugin', 'api' ) ) );
					$installed          = $woocommerce_plugin->install( $api->download_link );
					if ( is_wp_error( $installed ) ) {
						wp_send_json_error( [ 'msg' => $installed->get_error_message(), 'type' => 'warn' ] );
					}
					$activated = activate_plugin( 'woocommerce/woocommerce.php' );
					if ( is_wp_error( $activated ) ) {
						wp_send_json_error( [ 'msg' => $activated->get_error_message(), 'type' => 'warn' ] );
					}
					wp_send_json_success( [ 'msg' => esc_html__( 'WooCommerce installed and activated successfully!', 'abp-event-ticket' ), 'type' => 'success' ], 200 );
				}
				if ( $page_type == 'wc_active' ) {
					if ( defined( 'ABPET_WC' ) && ABPET_WC == 1 ) {
						$activated = activate_plugin( 'woocommerce/woocommerce.php' );
						if ( is_wp_error( $activated ) ) {
							wp_send_json_error( [ 'msg' => $activated->get_error_message(), 'type' => 'warn' ] );
						}
						wp_send_json_success( [ 'msg' => esc_html__( 'WooCommerce activated successfully!', 'abp-event-ticket' ), 'type' => 'success' ], 200 );
					}
				}
				wp_send_json_error( [ 'msg' => esc_html__( 'WooCommerce is either not installed or already active.', 'abp-event-ticket' ), 'type' => 'warn' ], 403 );
			}
			public function create_page(): void {
				if ( ! check_ajax_referer( 'abpet_admin_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid security token or Insufficient permissions.', 'abp-event-ticket' ), 'type' => 'warn' ], 403 );
				}
				$post_val  = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : $default;
				$page_type = $post_val( 'type' );
				if ( ! empty( $page_type ) ) {
					if ( ! ABPET_Function::get_page_by_slug( $page_type ) ) {
						$label      = ABPET_Function::label();
						$short_code = '';
						if ( $page_type == 'tf_booking' ) {
							$label      = __( 'Booking', 'abp-event-ticket' );
							$short_code = '[abpet-booking]';
						}
						if ( $page_type == 'tf_post' ) {
							$short_code = '[abpet-post]';
						}
						if ( $page_type == 'tf_gallery' ) {
							$label      = __( 'Gallery', 'abp-event-ticket' );
							$short_code = '[abpet-gallery]';
						}
						$page    = array(
							'post_type'    => 'page',
							'post_name'    => $page_type,
							'post_title'   => $label,
							'post_content' => $short_code,
							'post_status'  => 'publish',
						);
						$post_id = wp_insert_post( $page );
						if ( is_wp_error( $post_id ) || 0 === $post_id ) {
							wp_send_json_error( [ 'type' => 'warn', 'msg' => esc_html__( 'Failed to create page.', 'abp-event-ticket' ) ] );
						}
						flush_rewrite_rules();
						/* translators: %s: Trnasport Label */
						$translated_format = esc_html__( '%s Page Created successfully.....', 'abp-event-ticket' );
						$msg               = sprintf( $translated_format, $label );
						wp_send_json_success( [ 'type' => 'success', 'msg' => $msg ] );
					}
					wp_send_json_error( [ 'type' => 'warn', 'msg' => esc_html__( 'Page already exists.', 'abp-event-ticket' ) ] );
				} else {
					wp_send_json_error( [ 'type' => 'warn', 'msg' => esc_html__( 'Something Wrong...!', 'abp-event-ticket' ) ] );
				}
			}
			public function import_dummy(): void {
				if ( ! check_ajax_referer( 'abpet_admin_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid security token or Insufficient permissions.', 'abp-event-ticket' ), 'type' => 'warn' ], 403 );
				}
				$dummy_infos = $this->dummy_data();
				$previous_registry = ABPET_Function::get_option( 'abpet_dummy_registry', [] );
				$registry = [
					'posts' => array_map( 'absint', $previous_registry['posts'] ?? [] ),
					'terms' => is_array( $previous_registry['terms'] ?? null ) ? $previous_registry['terms'] : [],
				];
				if ( isset( $dummy_infos['taxonomy'] ) ) {
					foreach ( $dummy_infos['taxonomy'] as $tax => $taxonomy_option ) {
						if ( taxonomy_exists( $tax ) ) {
							foreach ( $taxonomy_option as $taxonomy_data ) {
								$name = sanitize_text_field( $taxonomy_data['name'] ?? '' );
								if ( empty( $name ) ) {
									continue;
								}
								$existing = get_term_by( 'name', $name, $tax );
								$term_id  = $existing ? (int) $existing->term_id : 0;
								if ( ! $term_id ) {
									$term = wp_insert_term( $name, $tax );
									if ( ! is_wp_error( $term ) ) {
										$term_id = (int) $term['term_id'];
										$registry['terms'][] = [ 'taxonomy' => $tax, 'term_id' => $term_id ];
									}
								}
							}
						}
					}
					do_action( 'abpet_location_update' );
					do_action( 'abpet_category_update' );
					do_action( 'abpet_organizer_update' );
					do_action( 'abpet_brand_update' );
				}
				if ( isset( $dummy_infos['options'] ) ) {
					foreach ( $dummy_infos['options'] as $option => $dummy_option ) {
						$option_data = get_option( $option );
						if ( empty( $option_data ) ) {
							update_option( $option, $dummy_option );
						}
					}
				}
				if ( isset( $dummy_infos['custom_post'] ) ) {
					$dummy_posts = $this->dummy();
					foreach ( $dummy_posts as $dummy_data ) {
						$args = array();
						if ( isset( $dummy_data['name'] ) ) {
							$args['post_title'] = $dummy_data['name'];
						}
						$args['post_status'] = 'publish';
						$args['post_type']   = ABPET_Function::get_cpt();
						$post_id             = wp_insert_post( $args );
						if ( is_wp_error( $post_id ) || ! $post_id ) {
							continue;
						}
						$post_data           = $dummy_data['post_data'] ?? [];
						if ( ! empty( $post_data ) ) {
							foreach ( $post_data as $meta_key => $data ) {
								update_post_meta( $post_id, $meta_key, $data );
							}
						}
						$this->sync_event_taxonomies( $post_id, $post_data );
						$registry['posts'][] = (int) $post_id;
					}
				}
				update_option( 'abpet_dummy_registry', $registry, false );
				flush_rewrite_rules();
				wp_send_json_success( [
					'msg' => esc_html__( 'Dummy data imported successfully!', 'abp-event-ticket' )
				] );
			}
			public function remove_dummy(): void {
				if ( ! check_ajax_referer( 'abpet_admin_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid security token or Insufficient permissions.', 'abp-event-ticket' ), 'type' => 'warn' ], 403 );
				}
				$post_ids = get_posts( [
					'post_type'      => ABPET_Function::get_cpt(),
					'post_status'    => 'any',
					'posts_per_page' => -1,
					'fields'         => 'ids',
					// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Fixed plugin dummy-data flag lookup.
					'meta_key'       => 'dummy',
					// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Fixed plugin dummy-data flag lookup.
					'meta_value'     => 'on',
				] );
				foreach ( $post_ids as $post_id ) {
					wp_delete_post( (int) $post_id, true );
				}
				$registry = ABPET_Function::get_option( 'abpet_dummy_registry', [] );
				foreach ( ( $registry['terms'] ?? [] ) as $term_data ) {
					$taxonomy = sanitize_key( $term_data['taxonomy'] ?? '' );
					$term_id  = absint( $term_data['term_id'] ?? 0 );
					if ( $taxonomy && $term_id && taxonomy_exists( $taxonomy ) ) {
						$term = get_term( $term_id, $taxonomy );
						if ( $term && ! is_wp_error( $term ) && 0 === (int) $term->count ) {
							wp_delete_term( $term_id, $taxonomy );
						}
					}
				}
				delete_option( 'abpet_dummy_registry' );
				flush_rewrite_rules();
				wp_send_json_success( [ 'msg' => esc_html__( 'Dummy data removed successfully.', 'abp-event-ticket' ), 'type' => 'success' ] );
			}
			public function dummy_data(): array {
				return [
					'taxonomy'    => [
						'abpet_location'  => ABPET_Static::location(),
						'abpet_category'  => ABPET_Static::category(),
						'abpet_organizer' => ABPET_Static::organizer(),
						'abpet_brand'     => ABPET_Static::brand(),
					],
					'options'     => [
						'abpet_ticket'     => ABPET_Static::ticket(),
						'abpet_decor'      => ABPET_Static::decoration(),
						'abpet_additional' => ABPET_Static::additional(),
						'abpet_form'       => ABPET_Static::form(),
						'abpet_faq'        => ABPET_Static::faq(),
						'abpet_tc'         => ABPET_Static::tc(),
						'abpet_feature'    => ABPET_Static::feature(),
					],
					'custom_post' => []
				];
			}
			public function dummy( $count = 5 ): array {
				$on_off         = [ 'on', 'off' ];
				$event_type         = [  'online', 'offline' ];
				$template       = [ "default", "light" ];
				$icon           = [ "🎟️", "🎫", "🎪", "🎭", "🎤", "🎬", "🏟️", "🎉", "📅", "💺", "fas fa-ticket", "fas fa-ticket-simple", "fas fa-calendar-days", "fas fa-masks-theater", "fas fa-microphone", "fas fa-music", "fas fa-trophy", "fas fa-champagne-glasses", "fas fa-users", "fas fa-star", ];
				$all_organizer  = ABPET_Function::get_option( 'abpet_organizer' );
				$organizer      = [ 'Global Events Group', 'EventPro Productions', 'Premier Events Network', 'Elite Event Management', 'NextGen Events', 'United Event Solutions', ];
				$all_brands     = ABPET_Function::get_option( 'abpet_brand' );
				$brand          = [ 'Live Nation', 'Eventbrite', 'AEG Presents', 'Ticketmaster', 'IMG Events', 'C3 Presents', 'Global Events', 'Premier Events', 'EventPro', 'Elite Entertainment', ];
				$all_categories = ABPET_Function::get_option( 'abpet_category' );
				$categories     = [ 'Concert', 'Conference', 'Workshop', 'Seminar', 'Festival', 'Sports', 'Theater', 'Exhibition', 'Party', 'Other', ];
				$features       = ABPET_Function::get_option( 'abpet_feature' );
				$feature_total  = is_array( $features ) ? sizeof( $features ) : 0;
				$feature_pick   = $feature_total > 0 ? array_rand( $features, min( 5, $feature_total ) ) : [];
				$feature_pick   = is_array( $feature_pick ) ? $feature_pick : [ $feature_pick ];
				$names             = [ 'Summer Music Festival 2026', 'Global Business & Technology Conference', 'International Food & Culture Festival', 'Future Innovation & Startup Summit', 'Live Concert Night 2026', 'Creative Arts & Design Exhibition', 'World Sports & Fitness Expo', 'Professional Leadership Conference', 'Digital Marketing & Growth Summit', 'International Film & Entertainment Festival', ];
				$subtitles         = [
					'Experience an unforgettable celebration of music, entertainment, and live performances.',
					'Connect with industry leaders and explore the future of business and technology.',
					'Discover delicious flavors, traditions, and cultures from around the world.',
					'Meet innovators, entrepreneurs, and visionaries shaping the future.',
					'Enjoy an exciting evening of live music, entertainment, and unforgettable moments.',
					'Explore inspiring artwork, creative ideas, and modern design from talented artists.',
					'Discover the latest trends in sports, fitness, health, and active living.',
					'Learn from experienced professionals and develop the skills to lead with confidence.',
					'Explore powerful digital strategies, emerging trends, and proven growth techniques.',
					'Celebrate the best of cinema, entertainment, storytelling, and creative filmmaking.',
				];
				$post_descriptions = [
					'Join us for an exciting summer celebration featuring live music, talented performers, interactive activities, and a vibrant atmosphere. Gather your friends and family for a memorable day filled with entertainment and fun.',
					'Bring together professionals, entrepreneurs, and technology enthusiasts for an inspiring conference focused on business growth, innovation, emerging technologies, and industry trends. Connect, learn, and discover new opportunities.',
					'Experience a colorful celebration of global cuisine and culture featuring authentic food, live performances, cultural showcases, and family-friendly activities. Discover new traditions and enjoy flavors from around the world.',
					'Explore the ideas and technologies shaping tomorrow at this exciting innovation and startup summit. Meet ambitious founders, investors, industry experts, and creative thinkers while discovering new opportunities for growth and collaboration.',
					'Get ready for an unforgettable night of live music and entertainment featuring exciting performances, talented artists, and an energetic atmosphere. Book your tickets and enjoy a night to remember.',
					'Discover inspiring artwork, creative concepts, and innovative designs from emerging and established artists. This exhibition brings together creativity, imagination, and modern design in one inspiring experience.',
					'Explore the latest developments in sports, fitness, wellness, and active living. Meet industry professionals, discover new products, join exciting activities, and get inspired to live a healthier lifestyle.',
					'Gain valuable insights from experienced leaders and professionals at this leadership-focused conference. Learn practical strategies, exchange ideas, build meaningful connections, and develop the skills needed for professional success.',
					'Discover the latest digital marketing strategies, technologies, and growth opportunities. Learn from industry experts, explore emerging trends, and gain practical insights to help businesses grow in the digital world.',
					'Celebrate the art of filmmaking and entertainment with a diverse selection of films, creative showcases, industry discussions, and special presentations. Experience inspiring stories and discover new voices from the world of cinema.',
				];
				$all_data          = [];
				$ticket_infos   = $this->ticket_info( $count );
				$date_infos    = $this->date_info( $count );

				for ( $i = 0; $i < $count; $i ++ ) {
					$rand_key = isset( $names[$i] ) ? $i : array_rand( $names );
					$all_data[ $i ]['name']      = $names[ $rand_key ];
					$all_data[ $i ]['post_data'] = [
						'sale_continue'               => 'on',
						'abpet_template'              => $template[ wp_rand( 0, 1 ) ],
						'display_sku'                 => 'on',
						'post_sku'                    => wp_rand( 100, 999 ),
						'post_icon'                   => $icon[ $rand_key ],
						'event_type'                  => $event_type[ wp_rand( 0, 1 ) ],
						'sub_title'                   => $subtitles[ $rand_key ],
						'post_description'            => $post_descriptions[ $rand_key ],
						'display_organizer'           => $on_off[ wp_rand( 0, 1 ) ],
						'abpet_organizer'             => $this->get_id( $all_organizer, $organizer[ array_rand( $organizer ) ] ),
						'display_brand'               => $on_off[ wp_rand( 0, 1 ) ],
						'abpet_brand'                 => $this->get_id( $all_brands, $brand[ array_rand( $brand ) ] ),
						'display_capacity'            => $on_off[ wp_rand( 0, 1 ) ],
						'display_category'            => $on_off[ wp_rand( 0, 1 ) ],
						'abpet_category'              => $this->get_id( $all_categories, $categories[ array_rand( $categories ) ] ),
						'post_feature'                => implode( ',', $feature_pick ),
						'abpet_slider'                => '10,20,30,40,50,100,60,70,80,90',
						'abpet_dates'                 => $date_infos[ $i ]??[],
						'display_additional_services' => 'on',
						'active_global_additional'    => 'on',
						'display_client_form'         => 'on',
						'active_global_form'          => 'on',
						'display_single_form'         => $on_off[ wp_rand( 0, 1 ) ],
						'display_faq'                 => 'on',
						'active_global_faq'           => 'on',
						'display_tc'                  => 'on',
						'active_global_tc'            => 'on',
						'dummy'                       => 'on',
						'seat_type'                   => $ticket_infos[ $i ]['seat_type']??'ticket',
						'display_ticket_type'         => 'on',
						'min_qty'                     => wp_rand( 1, 2 ),
						'max_qty'                     => wp_rand( 3, 10 ),
						'ticket_infos'                => $ticket_infos[ $i ]['ticket_infos']??[],
						'sp_infos'                    => $ticket_infos[ $i ]['sp_infos']??[],
						'all_ticket_type'             => $ticket_infos[ $i ]['all_ticket_type']??[],
					];
				}
				return $all_data;
			}
			public function ticket_info( $count ): array {
				ABPET_Static::sp();
				$ticket_options  = ABPET_Function::get_option( 'abpet_ticket' );
				$ticket_options  = is_array( $ticket_options ) ? $ticket_options : [];
				$random_num      = sizeof( $ticket_options ) > 4 ? 3 : sizeof( $ticket_options );
				$all_ticket_type = $random_num > 0 ? array_rand( $ticket_options, $random_num ) : [];
				$all_ticket_type = is_array( $all_ticket_type ) ? $all_ticket_type : [ $all_ticket_type ];
				$all_sp_ticket   = ABPET_Function::get_option( 'abpet_ticket_sp' );
				$sp_id           = [];
				if ( ! empty( $all_sp_ticket ) ) {
					$sp_id = array_keys( $all_sp_ticket );
				}
				$all_data          = [];
				$all_data['sp_id'] = ! empty( $sp_id ) ? $sp_id[ array_rand( $sp_id ) ] : '';
				if ( ! empty( $count ) && $count > 0 ) {
					for ( $key = 0; $key < $count; $key ++ ) {
						$seat_type = ! empty( $sp_id ) ? array( 'ticket', 'sp' )[ wp_rand( 0, 1 ) ] : 'ticket';
						if ( $seat_type == 'sp' ) {
							$sp_select  = $sp_id[ array_rand( $sp_id ) ];
							$tickets    = [];
							$seat_infos = $all_sp_ticket[ $sp_select ] ?? [];
							if ( ! empty( $seat_infos ) ) {
								$seat_info = $seat_infos['type'] ?? [];
								if ( ! empty( $seat_info ) ) {
									$tickets = array_merge( $tickets, array_keys( $seat_info ) );
								}
							}
							$all_ticket_type                       = array_values( array_unique( $tickets ) );
							$all_data[ $key ]['sp_infos'][0]['id'] = $sp_select;
						}
						foreach ( $all_ticket_type as $type_id ) {
							$all_data[ $key ]['ticket_infos'][ $type_id ]['price']   = wp_rand( 30, 80 );
							$all_data[ $key ]['ticket_infos'][ $type_id ]['qty']     = wp_rand( 30, 60 );
							$all_data[ $key ]['ticket_infos'][ $type_id ]['reserve'] = wp_rand( 5, 10 );
							$all_data[ $key ]['ticket_infos'][ $type_id ]['min_qty'] = wp_rand( 1, 2 );
							$all_data[ $key ]['ticket_infos'][ $type_id ]['max_qty'] = wp_rand( 2, 5 );
						}
						$all_data[ $key ]['all_ticket_type'] = $all_ticket_type;
						$all_data[ $key ]['seat_type'] = $seat_type;
					}
				}
				return $all_data;
			}
			public function date_info( $count ): array {
				$date_infos = [];
				if ( ! empty( $count ) && $count > 0 ) {
					$times = [
						0 => [ 'label' => 'Morning', 'value' => '09:15' ],
						1 => [ 'label' => 'Late Morning', 'value' => '11:30' ],
						2 => [ 'label' => 'Afternoon', 'value' => '14:00' ],
						3 => [ 'label' => 'Evening', 'value' => '18:45' ],
						4 => [ 'label' => 'Night', 'value' => '21:10' ],
						5 => [ 'label' => 'Morning', 'value' => '08:15' ],
						6 => [ 'label' => 'Late Morning', 'value' => '10:30' ],
						7 => [ 'label' => 'Noon', 'value' => '12:00' ],
						8 => [ 'label' => 'Afternoon', 'value' => '15:45' ],
						9 => [ 'label' => 'Night', 'value' => '20:10' ],
					];
					for ( $key = 0; $key < $count; $key ++ ) {
						$rand_num = wp_rand( 1, 10 );
						$date_types                      = [ 'periodic_date', 'specific_date' ];
						$date_type                       = $date_types[ wp_rand( 0, 1 ) ];
						$date_infos[ $key ]['date_type'] = $date_type;
						if ( $date_type == 'periodic_date' ) {
							$date_infos[ $key ]['periodic_start_date'] = gmdate( 'Y-m-d', strtotime( '+' . $rand_num . ' days', time() ) );
							$date_infos[ $key ]['periodic_after']      = wp_rand( 1, 4 );
						} else {
							for ( $i = 0; $i < $rand_num; $i ++ ) {
								$rand_num_                                  = wp_rand( 1, 60 );
								$date_infos[ $key ]['specific_dates'][ $i ] = gmdate( 'Y-m-d', strtotime( '+' . ( $rand_num + $rand_num_ ) . ' days', time() ) );
							}
						}
						$date_infos[ $key ]['time_infos']['time'] = array_intersect_key( $times, array_flip( array_rand( $times, 3 ) ) );
					}
				}
				return $date_infos;
			}
			public function get_id( $options = [], $name = '' ): int|string|null {
				if ( ! empty( $options ) ) {
					foreach ( $options as $key => $option ) {
						if ( isset( $option['name'] ) && $option['name'] === $name ) {
							return $key;
						}
					}
				}
				return null;
			}
			private function dummy_post_count(): int {
				return count( get_posts( [
					'post_type'      => ABPET_Function::get_cpt(),
					'post_status'    => 'any',
					'posts_per_page' => -1,
					'fields'         => 'ids',
					// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Fixed plugin dummy-data flag lookup.
					'meta_key'       => 'dummy',
					// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Fixed plugin dummy-data flag lookup.
					'meta_value'     => 'on',
				] ) );
			}
			private function sync_event_taxonomies( int $post_id, array $post_data ): void {
				foreach ( [
					'abpet_category'  => 'abpet_category',
					'abpet_location'  => 'abpet_location',
					'abpet_organizer' => 'abpet_organizer',
					'abpet_brand'     => 'abpet_brand',
				] as $meta_key => $taxonomy ) {
					if ( ! taxonomy_exists( $taxonomy ) ) {
						continue;
					}
					$value = $post_data[ $meta_key ] ?? '';
					$ids   = is_array( $value ) ? $value : explode( ',', (string) $value );
					$ids   = array_values( array_filter( array_map( 'absint', $ids ) ) );
					wp_set_object_terms( $post_id, $ids, $taxonomy, false );
				}
			}
		}
		new ABPET_Static();
	}