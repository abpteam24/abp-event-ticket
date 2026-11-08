<?php
    if (!defined('ABSPATH')) {
        exit; // Exit if accessed directly
    }
    if (!class_exists('ABPET_Ajax')) {
        class ABPET_Ajax {
            public function __construct() {
                add_action('wp_ajax_abpet_global_booking', [$this, 'global_booking']);
                add_action('wp_ajax_nopriv_abpet_global_booking', [$this, 'global_booking']);
                add_action('wp_ajax_abpet_load_booking_data', [$this, 'load_booking_data']);
                add_action('wp_ajax_nopriv_abpet_load_booking_data', [$this, 'load_booking_data']);
                add_action('wp_ajax_abpet_load_date', [$this, 'load_date']);
                add_action('wp_ajax_nopriv_abpet_load_date', [$this, 'load_date']);

            }
            public function global_booking(): void {
                if (!check_ajax_referer('abpet_ajax_nonce', 'nonce', false)) {
                    wp_send_json_error(['msg' => __('Session expired. Page Reloading......', 'abp-event-ticket'), 'type' => 'warn'], 403);
                }
                $post_int = fn($key, $default = '') => isset($_POST[$key]) ? absint($_POST[$key]) : $default;
                $post_val = fn($key, $default = '') => isset($_POST[$key]) ? sanitize_text_field(wp_unslash($_POST[$key])) : $default;
                $post_id = $post_int('post_id');
                $start_date = $post_val('start_date');
                $start_time = $post_val('session_time') ?: $post_val('start_time');
                if ( strpos( $start_date, '|' ) !== false ) {
                    [ $start_date, $date_time ] = array_pad( explode( '|', $start_date, 2 ), 2, '' );
                    $start_date = trim( $start_date );
                    $start_time = $start_time ?: trim( $date_time );
                }
                $form_data['post_id'] = $post_id;
                $msg = '';
                ob_start();
                if (!empty($post_id) && $post_id > 0) {
                    if (get_post_type($post_id) == ABPET_Function::get_cpt() && (get_post_status($post_id) == 'publish' || is_admin())) {
                        $post_infos = ABPET_Function::get_all_meta($post_id);
                        $form_data['post_id'] = $post_id;
                        $sale_continue = $post_infos['sale_continue'] ?? 'on';
                        if ($sale_continue == 'on') {
                            if(empty($start_date)) {
	                            $all_dates = !empty($post_id) && $post_id > 0 ? ABPET_Function::date($post_id) : ABPET_Function::date_all();
	                            $start_date = current($all_dates);
	                            $start_date = !empty($start_date) ? gmdate('Y-m-d', strtotime($start_date)) : '';
                            }

                            $time_infos=$post_infos['time_infos']??[];
                            $time_infos = is_array($time_infos) ? $time_infos : [];
                            $all_times = ABPET_Function::available_times($post_id, $time_infos, $start_date);
                            $start_time = in_array($start_time, $all_times, true) ? $start_time : (!empty($all_times) ? current($all_times) : '');
                            $upcoming_date='';
                            if(!empty($start_date)) {
	                            $upcoming_date=!empty($start_time) ? gmdate('Y-m-d H:i', strtotime($start_date.' '.$start_time)) : $start_date;
                            }
	                        $form_data['all_dates'] = !empty($all_dates) ? $all_dates : ABPET_Function::date($post_id);
	                        $form_data['all_times'] = $all_times;
	                        $form_data['start_date'] = $start_date;
	                        $form_data['start_time'] = $start_time;
	                        $form_data['session_time'] = $start_time;
	                        $form_data['event_date'] = $upcoming_date;

                            do_action('abpet_registration', $post_infos, $form_data);
                        } else {
                            ABPET_Layout::layout_warning_info('sale_close_msg');
                        }
                        $msg = ($post_infos['post_title'] ?? '') . ' ' . __('data loaded....!', 'abp-event-ticket');
                    }
                } else {
                    $defaults = ABPET_Shortcodes::default_attribute();
                    $form_data_down = $form_data['down'] ?? [];
                    $form_data_down['global_order'] = 'yes';
                    $post_ids = ABPET_Query::get_post_id($form_data_down);
                    $form_data_down['all_post'] = $post_ids;
                    $form_data_down = array_merge($defaults, $form_data_down);
                    $form_data_down['filter_taxonomies'] = ABPET_Shortcodes::normalize_filter_taxonomies($form_data_down['filter'] ?? '');
                    $style = ($form_data_down['style'] ?? 'grid') ?: 'grid';
                    $templates = ['grid' => 'list/grid.php', 'missionary' => 'list/missionary.php',];
                    $file = ABPET_Function::template_path($templates[$style] ?? $templates['grid']);
                    //echo '<pre>';                    print_r($form_data);                    echo '</pre>';
                    ?>
                    <div class="abp_pagination _gap_fd_column">
                        <?php do_action('abpet_post_filter', $form_data_down);
                            if (is_file($file)) {
                                include_once $file;
                                do_action('abpet_' . $style . '_template', $form_data_down);
                            } else {
                                include_once ABPET_Function::template_path('list/default.php');
                                do_action('abpet_default_template', $form_data_down);
                            } ?>
                    </div>
                    <?php
                    $form_data_up = $form_data['up'] ?? [];
                    if (!empty($form_data_up)) {
                        $form_data_up['global_order'] = 'yes';
                        $post_ids = ABPET_Query::get_post_id($form_data_up);
                        $form_data_up['all_post'] = $post_ids;
                        $form_data_up = array_merge($defaults, $form_data_up);
                        $form_data_up['filter_taxonomies'] = ABPET_Shortcodes::normalize_filter_taxonomies($form_data_up['filter'] ?? '');
                        ?>
                        <div class="abp_pagination _gap_fd_column">
                            <?php do_action('abpet_post_filter', $form_data_up);
                                if (is_file($file)) {
                                    include_once $file;
                                    do_action('abpet_' . $style . '_template', $form_data_up);
                                } else {
                                    include_once ABPET_Function::template_path('list/default.php');
                                    do_action('abpet_default_template', $form_data_up);
                                } ?>
                        </div>
                        <?php
                    }
                    $msg = ABPET_Function::label() . ' ' . __('List Loaded Successfully.....! ', 'abp-event-ticket');
                }
                $html = ob_get_clean();
                wp_send_json_success(['html' => $html, 'msg' => $msg, 'type' => 'success']);
            }
            public function load_booking_data(): void {
                if (!check_ajax_referer('abpet_ajax_nonce', 'nonce', false)) {
                    wp_send_json_error(['msg' => __('Session expired. Page Reloading......', 'abp-event-ticket'), 'type' => 'warn'], 403);
                }
                $post_int = fn($key, $default = '') => isset($_POST[$key]) ? absint($_POST[$key]) : $default;
                $post_val = fn($key, $default = '') => isset($_POST[$key]) ? sanitize_text_field(wp_unslash($_POST[$key])) : $default;
                $post_id = $post_int('post_id');
                if (!empty($post_id) && $post_id > 0 && get_post_type($post_id) == ABPET_Function::get_cpt() && (get_post_status($post_id) == 'publish' || is_admin())) {
                    $post_infos = ABPET_Function::get_all_meta($post_id);
                    $form_data['post_id'] = $post_id;
                    $sale_continue = $post_infos['sale_continue'] ?? 'on';
                    $time_html = '';
                    ob_start();
                    if ($sale_continue == 'on') {
	                    $start_date = $post_val('event_date') ?: $post_val('start_date');
	                    $session_time = $post_val('session_time') ?: $post_val('start_time');
	                    if ( strpos( $start_date, '|' ) !== false ) {
		                    [ $start_date, $date_time ] = array_pad( explode( '|', $start_date, 2 ), 2, '' );
		                    $start_date   = trim( $start_date );
		                    $session_time = $session_time ?: trim( $date_time );
	                    }
	                    if(empty($start_date)) {
		                    $all_dates = ABPET_Function::date($post_id);
		                    $start_date = current($all_dates);
		                    $start_date = !empty($start_date) ? gmdate('Y-m-d', strtotime($start_date)) : '';
	                    }

	                    $time_infos=$post_infos['time_infos']??[];
	                    $time_infos = is_array($time_infos) ? $time_infos : [];
	                    $all_times = ABPET_Function::available_times($post_id, $time_infos, $start_date);
	                    if (!isset($all_dates) || !is_array($all_dates)) {
		                    $all_dates = ABPET_Function::date($post_id);
	                    }
	                    if (!in_array($session_time, $all_times, true)) {
		                    $session_time = !empty($all_times) ? current($all_times) : '';
	                    }
	                    $upcoming_date='';
	                    if(!empty($start_date)) {
		                    $upcoming_date=!empty($session_time) ? gmdate('Y-m-d H:i', strtotime($start_date.' '.$session_time)) : $start_date;
	                    }
	                    $form_data['all_dates'] = $all_dates;
	                    $form_data['all_times'] = $all_times;
	                    $form_data['start_date'] = $start_date;
	                    $form_data['start_time'] = $session_time;
	                    $form_data['event_date'] = $start_date;
	                    $form_data['session_time'] = $session_time;
	                    ob_start();
	                    ABPET_Layout::start_time( $form_data );
	                    $time_html = ob_get_clean();
	                    $selected_sp_id = $post_int( 'sp_id', 0 );
	                    if ( $selected_sp_id > 0 ) {
	                        $available_sp_ids = array_map(
	                            'absint',
	                            wp_list_pluck( (array) ( $post_infos['sp_infos'] ?? [] ), 'id' )
	                        );
	                        if ( in_array( $selected_sp_id, $available_sp_ids, true ) ) {
	                            $form_data['sp_id'] = $selected_sp_id;
	                        }
	                    }
	                    $seat_type = $post_infos['seat_type'] ?? 'sp';
                        $seat_type = ABPET_Function::on_off('sp') ? $seat_type : 'ticket';
                        if ($seat_type === 'ticket') {
                            do_action('abpet_ticket_type', $post_infos, $form_data);
                        } else {
                            do_action('abpet_sp_type', $post_infos, $form_data);
                        }
                    } else {
                        ABPET_Layout::layout_warning_info('sale_close_msg');
                    }
                    //echo '<pre>';				print_r($form_data);				echo '</pre>';
                    $html = ob_get_clean();
                    $time_options = [];
                    foreach ( (array) ( $all_times ?? [] ) as $available_time ) {
                        $time_options[] = [
                            'value' => $available_time,
                            'label' => ABPET_Function::date_format( $start_date . ' ' . $available_time ),
                        ];
                    }
                    wp_send_json_success([
                        'html' => $html,
                        'time_html' => $time_html,
                        'times' => $time_options,
                        'selected_time' => $session_time,
                        'msg' => (get_the_title($post_id) . ' ' . __('data loaded....!', 'abp-event-ticket')),
                        'type' => 'success'
                    ]);
                } else {
                    wp_send_json_success(['msg' => __('Something Wrong... Reload Page....!', 'abp-event-ticket'), 'type' => 'warn']);
                }
            }
            public function load_date(): void {
                if (!check_ajax_referer('abpet_ajax_nonce', 'nonce', false)) {
                    wp_send_json_error(['msg' => esc_html__('Session expired. Please refresh the page.', 'abp-event-ticket')], 403);
                }
                $post_int = fn($key, $default = '') => isset($_POST[$key]) ? absint($_POST[$key]) : $default;
                $post_id = $post_int('post_id');
                $all_dates = !empty($post_id) && $post_id > 0 ? ABPET_Function::date($post_id) : ABPET_Function::date_all();
                $upcoming_date = current($all_dates);
                $upcoming_date = !empty($upcoming_date) ? gmdate('Y-m-d', strtotime($upcoming_date)) : '';
                $post_infos = !empty($post_id) && $post_id > 0 ? ABPET_Function::get_all_meta($post_id) : [];
                $time_infos = is_array($post_infos['time_infos'] ?? []) ? $post_infos['time_infos'] : [];
                $all_times  = !empty($upcoming_date) ? ABPET_Function::available_times($post_id, $time_infos, $upcoming_date) : [];
                $start_time = !empty($all_times) ? current($all_times) : '';
                ob_start();
                //echo '<pre>';                    print_r($post_id);                    echo '</pre>';
                //echo '<pre>';                    print_r($all_dates);                    echo '</pre>';
                ABPET_Layout::start_date($all_dates, $upcoming_date, $all_times, $time_infos, $post_id, $start_time);
                $html_journey = ob_get_clean();
                $new_picker_config = ABPET_Layout::use_date_picker(count($all_dates)) ? ABPET_Layout::create_datepicker_array($all_dates) : '';
                wp_send_json_success([
                    'html_journey' => $html_journey, 'type' => 'success',
                    'msg' => esc_html__('Date Loaded successfully.', 'abp-event-ticket'),
                    'picker_config' => $new_picker_config,
                    'return' => '#return_date',
                    'journey' => '#start_date'
                ]);
            }

        }
        new ABPET_Ajax();
    }