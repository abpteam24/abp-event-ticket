<?php
    if (!defined('ABSPATH')) {
        exit; // Exit if accessed directly
    }
    if (!class_exists('ABPET_Ajax')) {
        class ABPET_Ajax {
            public function __construct() {
                add_action('wp_ajax_abpet_global_booking', [$this, 'global_booking']);
                add_action('wp_ajax_nopriv_abpet_global_booking', [$this, 'global_booking']);
                add_action('wp_ajax_abpet_load_transport_data', [$this, 'load_transport_data']);
                add_action('wp_ajax_nopriv_abpet_load_transport_data', [$this, 'load_transport_data']);
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
                $form_data['post_id'] = $post_id;
                $msg = '';
                ob_start();
                if (!empty($post_id) && $post_id > 0) {
                    if (get_post_type($post_id) == ABPET_Function::get_cpt() && (get_post_status($post_id) == 'publish' || is_admin())) {
                        $post_infos = ABPET_Function::get_all_meta($post_id);
                        $sale_continue = $post_infos['sale_continue'] ?? 'on';
                        if ($sale_continue == 'on') {
                            if(empty($start_date)) {
	                            $all_dates = !empty($post_id) && $post_id > 0 ? ABPET_Function::date($post_id) : ABPET_Function::date_all();
	                            $start_date = current($all_dates);
	                            $start_date = !empty($start_date) ? gmdate('Y-m-d', strtotime($start_date)) : '';
                            }

                            $time_infos=$post_infos['time_infos']??[];
                            $all_times = ABPET_Function::time($time_infos, $start_date);
	                        $start_time = !empty($all_times) ? current($all_times) : '';
                            $upcoming_date='';
                            if(!empty($start_date)) {
	                            $upcoming_date=!empty($start_time) ? gmdate('Y-m-d H:i', strtotime($start_date.' '.$start_time)) : $start_date;
                            }
	                        $form_data['start_date'] = $start_date;
	                        $form_data['start_time'] = $start_time;
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
            public function load_transport_data(): void {
                if (!check_ajax_referer('abpet_ajax_nonce', 'nonce', false)) {
                    wp_send_json_error(['msg' => __('Session expired. Page Reloading......', 'abp-event-ticket'), 'type' => 'warn'], 403);
                }
                $post_int = fn($key, $default = '') => isset($_POST[$key]) ? absint($_POST[$key]) : $default;
                $post_val = fn($key, $default = '') => isset($_POST[$key]) ? sanitize_text_field(wp_unslash($_POST[$key])) : $default;
                $post_id = $post_int('post_id');
                if (!empty($post_id) && $post_id > 0 && get_post_type($post_id) == ABPET_Function::get_cpt() && (get_post_status($post_id) == 'publish' || is_admin())) {
                    $post_infos = ABPET_Function::get_all_meta($post_id);
                    $sale_continue = $post_infos['sale_continue'] ?? 'on';
                    ob_start();
                    if ($sale_continue == 'on') {
	                    if(empty($start_date)) {
		                    $all_dates = ABPET_Function::date($post_id);
		                    $start_date = current($all_dates);
		                    $start_date = !empty($start_date) ? gmdate('Y-m-d', strtotime($start_date)) : '';
	                    }

	                    $time_infos=$post_infos['time_infos']??[];
	                    $all_times = ABPET_Function::time($time_infos, $start_date);
	                    $start_time = !empty($all_times) ? current($all_times) : '';
	                    $upcoming_date='';
	                    if(!empty($start_date)) {
		                    $upcoming_date=!empty($start_time) ? gmdate('Y-m-d H:i', strtotime($start_date.' '.$start_time)) : $start_date;
	                    }
	                    $form_data['start_date'] = $start_date;
	                    $form_data['start_time'] = $start_time;
	                    $form_data['event_date'] = $upcoming_date;
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
                    wp_send_json_success(['html' => $html, 'msg' => (get_the_title($post_id) . ' ' . __('data loaded....!', 'abp-event-ticket')), 'type' => 'success']);
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
                ob_start();
                //echo '<pre>';                    print_r($post_id);                    echo '</pre>';
                //echo '<pre>';                    print_r($all_dates);                    echo '</pre>';
                ABPET_Layout::start_date($all_dates, $upcoming_date);
                $html_journey = ob_get_clean();
                $new_picker_config = !empty($all_dates) ? ABPET_Layout::create_datepicker_array($all_dates) : '';
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