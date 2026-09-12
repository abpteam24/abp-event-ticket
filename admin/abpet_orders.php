<?php
    if (!defined('ABSPATH')) {
        exit; // Exit if accessed directly
    }
    if (!class_exists('ABPET_Orders')) {
        class ABPET_Orders {
            public function __construct() {
                add_action('abpet_load_orders', [$this, 'load_orders']);
                add_action('wp_ajax_abpet_load_order_list', [$this, 'load_order_list']);
                add_action('wp_ajax_abpet_item_cancel', [$this, 'item_cancel']);
            }
            public function load_orders(): void {
                ?>
                <div class="abpet_orders _section_card">
                    <h4 class="abp_title_gap_xs"><span>📋</span> <?php esc_html_e('Order Filter', 'abp-event-ticket'); ?></h4>
                    <div class="_ov_initial_mar_t_xs">
                        <form class="abp_search_form" method="post" action="">
                            <div class="_form_inline">
                                <?php
                                    ABPET_Layout::filter_post_list();
                                    ABPET_Layout::filter_booking_date();
                                    ABPET_Layout::filter_booking_date_between();
                                    ABPET_Layout::filter_order_date();
                                    ABPET_Layout::filter_order_date_between();
                                    ABPET_Layout::filter_user_id();
                                    ABPET_Layout::filter_order_id();
                                    ABPET_Layout::filter_bill_name();
                                    ABPET_Layout::filter_bill_email();
                                    ABPET_Layout::filter_bill_phone();
                                ?>
                            </div>
                            <div class="_form_inline_mar_t_xs">
                                <div class="_input_item">
                                    <button type="submit" class="_btn_theme_xs_w_full">
                                        <span class="_mar_r_xs">🔎</span><?php esc_html_e('Search', 'abp-event-ticket'); ?>
                                    </button>
                                </div>
                                <div class="_input_item">
                                    <button class="_btn_theme_xs _w_full" title="<?php esc_attr_e('More Options', 'abp-event-ticket'); ?>" type="button" data-collapse-target="#view_more_filter_option"
                                            data-close-text="👁️ <?php esc_attr_e('More Options', 'abp-event-ticket'); ?>" data-open-text="🙈  <?php esc_attr_e('Close Options', 'abp-event-ticket'); ?>"
                                    >
                                        <span data-text>👁️ <?php esc_html_e('More Options', 'abp-event-ticket'); ?></span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="_divider_xs"></div>
                    <div class="order_list">
                        <?php $this->order_lists(); ?>
                    </div>
                </div>
                <?php
            }
            public function order_lists($filter_args = []): void {
                $page_number = isset($filter_args['page_number']) && is_numeric($filter_args['page_number']) ? (int)$filter_args['page_number'] : 1;
                $limit = isset($filter_args['page_item']) && is_numeric($filter_args['page_item']) ? (int)$filter_args['page_item'] : ABPET_Function::get_option('abpet_per_page_item', 20);
                $data_status = !empty($filter_args['status']) ? sanitize_text_field($filter_args['status']) : '';
                $si = ($page_number - 1) * $limit + 1;
                $offset = $si - 1;
                $booking_lists = ABPET_Query::get_booking_query($filter_args, $limit, $offset);
                $total_order = ABPET_Query::get_booking_query($filter_args, 0, 0, true);
                $filter_args['status'] = 'all';
                $label = ABPET_Function::label();
                $brand_icon = ABPET_Function::icon();
                $booked_status = ABPET_Function::booking_status();
                $booked_status = $booked_status ? explode(',', $booked_status) : [];
                $_filter_args = $filter_args;
                $total_additional = 0;
                $total_sale =$total_price= 0;
                // echo '<pre>';                print_r($booking_lists);                echo '</pre>';
                $count_foot_left_col = 0;
                $count_foot_right_col = 0;
                ?>
                <div class="_ov_auto_fj_between _mar_b_xs">
                    <div class="_group_content order_status_menu">
                        <button class="_btn_light_green_pale_xs_text_nowrap <?php echo esc_attr($data_status === 'all' ? 'abp_active' : ''); ?>" type="button" data-status="all" title="<?php esc_attr_e('All Booking', 'abp-event-ticket'); ?>">
                            <?php echo esc_html(__('All Booking', 'abp-event-ticket') . ' (' . ABPET_Query::get_booking_query($filter_args, 0, 0, true) . ' )') ?>
                        </button>
                        <button class="_btn_light_green_pale_xs_text_nowrap <?php echo esc_attr(!$data_status ? 'abp_active' : ''); ?>" type="button" data-status="" title="<?php esc_attr_e('Booking Completed', 'abp-event-ticket'); ?>">
                            <?php
                                $filter_args['status'] = '';
                                echo esc_html(__('Booking Completed', 'abp-event-ticket') . ' (' . ABPET_Query::get_booking_query($filter_args, 0, 0, true) . ' )');
                            ?>
                        </button>
                        <?php
                            $all_status = wc_get_order_statuses();
                            if (!empty($all_status) && is_array($all_status)) {
                                foreach ($all_status as $key => $status) {
                                    ?>
                                    <button class="_btn_light_green_pale_xs_text_nowrap <?php echo esc_attr($data_status === $key ? 'abp_active' : ''); ?>" type="button" data-status="<?php echo esc_attr($key); ?>">
                                        <?php
                                            $filter_args['status'] = sanitize_key($key);
                                            echo esc_html($status . ' (' . ABPET_Query::get_booking_query($filter_args, 0, 0, true) . ')');
                                        ?>
                                    </button>
                                    <?php
                                }
                            }
                        ?>
                    </div>
                    <?php do_action('abpet_order_tab_action', $_filter_args); ?>
                </div>
                <?php if (!empty($booking_lists) && is_array($booking_lists)) { ?>
                    <table class=" abp">
                        <thead>
                        <tr>
                            <th><?php esc_html_e('Action', 'abp-event-ticket'); ?><?php $count_foot_left_col++; ?></th>
                            <th><?php esc_html_e('Order ID/ Date', 'abp-event-ticket'); ?><?php $count_foot_left_col++; ?></th>
                            <th><?php ABPET_Layout::image_icon($brand_icon); ?><?php echo esc_html($label); ?><?php $count_foot_left_col++; ?></th>
                            <th><?php esc_html_e('Event Date / Session', 'abp-event-ticket'); ?><?php $count_foot_left_col++; ?></th>
                            <th><?php esc_html_e('Ticket Info', 'abp-event-ticket'); ?><?php $count_foot_left_col++; ?></th>
                            <?php if (ABPET_Function::on_off('additional_info')) { ?>
                                <th><?php esc_html_e('Additional Info', 'abp-event-ticket'); ?><?php $count_foot_left_col++; ?></th>
                            <?php } ?>
                            <th><?php esc_html_e('Price ', 'abp-event-ticket'); ?></th>
                            <?php if (ABPET_Function::on_off('additional_info')) { ?>
                                <th><?php esc_html_e('Additional ', 'abp-event-ticket'); ?></th>
                            <?php } ?>
                            <th><?php esc_html_e('Total ', 'abp-event-ticket'); ?></th>
                            <th><?php esc_html_e('Status', 'abp-event-ticket'); ?><?php $count_foot_right_col++; ?></th>
                            <th><?php esc_html_e('Payment Method', 'abp-event-ticket'); ?><?php $count_foot_right_col++; ?></th>
                            <th><?php esc_html_e('Billing Info', 'abp-event-ticket'); ?><?php $count_foot_right_col++; ?></th>
                            <?php if (ABPET_Function::on_off('client_info')) { ?>
                                <th><?php esc_html_e('Passenger Info', 'abp-event-ticket'); ?><?php $count_foot_right_col++; ?></th>
                            <?php } ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($booking_lists as $booking_list) {
                            $post_id=$booking_list['post_id'] ?? '';
                            $post_infos['post_id'] =$post_id;
                            $order_status = $booking_list['order_status'] ?? '';
                            $item_total=$booking_list['total'] ?? 0;
                            $total_price = $total_price+($booking_list['price'] ?? 0);
                            $total_sale = $total_sale + $item_total;
                            $price = $booking_list['price'] ?? 0;
                            $ex_price = $booking_list['ex_price'] ?? 0;
                            $total_additional = $total_additional + $ex_price;
                            $ticket_infos = json_decode($booking_list['ticket_info'] ?? '', true) ?: [];
                            $passenger_infos = json_decode($booking_list['pass_info'] ?? '', true) ?: [];
                            $additional_infos = json_decode($booking_list['ex_info'] ?? '', true) ?: [];
                            $others = json_decode($booking_list['others'] ?? '', true) ?: [];
                            ?>
                            <tr>
                                <th>
                                    <div class="_group_content">
                                        <?php do_action('abpet_order_action', ($booking_list['id'] ?? ''));
                                            if (in_array($order_status, $booked_status, true)) { ?>
                                                <button class="_btn_light_danger_xxs item_cancel" data-item_id="<?php echo esc_attr($booking_list['id'] ?? ''); ?>" title="<?php esc_attr_e('Ticket Cancel', 'abp-event-ticket'); ?>" type="button"><?php ABPET_Static::icon_svg('close_2'); ?></button>
                                            <?php } ?>
                                    </div>
                                </th>
                                <th class="_text_left">
                                    <p class="abp"><?php echo esc_html($si . '. #' . ($booking_list['order_id'] ?? '')); ?></p>
                                    <p class="abp_color_theme"><?php echo esc_html(ABPET_Function::date_format($booking_list['created_at'] ?? '')); ?></p>
                                </th>
                                <th class="_text_left">
                                    <div class="_gap_xxs"><?php ABPET_Layout::title($post_infos); ?></div>
                                </th>
                                <td>
                                    <p class="abp_color_theme"><?php echo esc_html(ABPET_Function::date_format(($booking_list['event_date'] ?? '') . ' ' . ($booking_list['session_time'] ?? ''))); ?></p>
                                </td>
                                <th><?php ABPET_Layout::ticket_info($ticket_infos, $post_id, $booking_list['seat_type'] ?? '', $booking_list['sp_id'] ?? 0); ?></th>
                                <?php if (ABPET_Function::on_off('additional_info')) { ?>
                                    <td><?php ABPET_Layout::additional_info($additional_infos); ?></td>
                                <?php } ?>
                                <th><?php echo $price > 0 ? wp_kses_post(wc_price($price)) : esc_html__('FREE', 'abp-event-ticket'); ?></th>
                                <?php if (ABPET_Function::on_off('additional_info')) { ?>
                                    <th><?php echo $ex_price > 0 ? wp_kses_post(wc_price($ex_price)) : esc_html__('FREE', 'abp-event-ticket'); ?></th>
                                <?php } ?>
                                <th><?php echo $item_total > 0 ? wp_kses_post(wc_price($item_total)) : esc_html__('FREE', 'abp-event-ticket'); ?></th>
                                <th>
                                    <span class="abp_tag _text_capitalize <?php echo esc_attr($order_status); ?>"> <?php echo esc_html(ABPET_Layout::status_text($order_status)); ?></span>
                                </th>
                                <th class="_text_capitalize"><?php echo esc_html($booking_list['payment_method'] ?? ''); ?></th>
                                <td>
                                    <div class="load_more">
                                        <?php ABPET_Layout::billing_info($booking_list); ?>
                                        <span class="load_more_action" data-less="<?php esc_attr_e('....Less ', 'abp-event-ticket'); ?>" data-more="<?php esc_attr_e('....More', 'abp-event-ticket'); ?>"><?php esc_html_e('.... More', 'abp-event-ticket'); ?></span>
                                    </div>
                                </td>
                                <?php if (ABPET_Function::on_off('client_info')) { ?>
                                    <td>
                                        <?php if (!empty($passenger_infos)) { ?>
                                            <div class="load_more">
                                                <?php ABPET_Layout::client_info($passenger_infos); ?>
                                                <span class="load_more_action" data-less="<?php esc_html_e('....Less ', 'abp-event-ticket'); ?>" data-more="<?php esc_html_e('.... More', 'abp-event-ticket'); ?>"><?php esc_html_e('.... More', 'abp-event-ticket'); ?></span>
                                            </div>
                                        <?php } ?>
                                    </td>
                                <?php } ?>
                            </tr>
                            <?php $si++;
                        } ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="<?php echo esc_attr($count_foot_left_col); ?>"><?php esc_html_e('Total Summary', 'abp-event-ticket'); ?></th>
                            <th><?php echo (!empty($total_price) && $total_price > 0) ? wp_kses_post(wc_price($total_price)) : esc_html__('FREE', 'abp-event-ticket'); ?></th>
                            <?php if (ABPET_Function::on_off('additional_info')) { ?>
                                <th><?php echo (!empty($total_additional) && $total_additional > 0) ? wp_kses_post(wc_price($total_additional)) : esc_html__('FREE', 'abp-event-ticket'); ?></th>
                            <?php } ?>
                            <th><?php echo (!empty($total_sale) && $total_sale > 0) ? wp_kses_post(wc_price($total_sale)) : esc_html__('FREE', 'abp-event-ticket'); ?></th>
                            <th colspan="<?php echo esc_attr($count_foot_right_col); ?>"></th>
                        </tr>
                        </tfoot>
                    </table>
                <?php } else {
                    ABPET_Layout::layout_warning_info('no_order_found');
                }
                do_action('abpet_pagination', ['page_item' => $limit, 'page_number' => $page_number, 'total' => $total_order, 'style' => 'ajax']); ?>
                <?php
            }
            public function load_order_list(): void {
                if (!check_ajax_referer('abpet_admin_ajax_nonce', 'nonce', false) || !current_user_can('manage_options')) {
                    wp_send_json_error(['msg' => __('Invalid security token or Insufficient permissions.', 'abp-event-ticket'), 'type' => 'warn'], 403);
                }
                ob_start();
                $filter_args = isset($_POST) ? array_map('sanitize_text_field', wp_unslash($_POST)) : [];
                $limit = isset($filter_args['page_item']) ? (int)$filter_args['page_item'] : 20;
                $data_limit = (int)ABPET_Function::get_option('abpet_per_page_item', 20);
                $filter_args['page_item'] = $limit > 0 ? $limit : $data_limit;
                if ($limit > 0 && $data_limit !== $limit) {
                    update_option('abpet_per_page_item', $limit);
                }
                $this->order_lists($filter_args);
                $html = ob_get_clean();
                wp_send_json_success(['html' => $html, 'msg' => esc_html__('Order Loaded Successfully !', 'abp-event-ticket'), 'type' => 'success']);
            }
            public function item_cancel(): void {
                if (!check_ajax_referer('abpet_admin_ajax_nonce', 'nonce', false) || !current_user_can('manage_options')) {
                    wp_send_json_error(['msg' => __('Invalid security token or Insufficient permissions.', 'abp-event-ticket'), 'type' => 'warn'], 403);
                }
                $item_id = isset($_POST['item_id']) ? sanitize_text_field(wp_unslash($_POST['item_id'])) : '';
                if (!empty($item_id)) {
                    global $wpdb;
                    $table_name = $wpdb->prefix . 'abpet_orders';
                    $booking_lists = ABPET_Query::get_booking_query(['id' => $item_id]);
                    if (!empty($booking_lists) && is_array($booking_lists)) {
                        $value = current($booking_lists);
                        $others = $value['others'] ?? '';
                        if (!empty($others)) {
                            $others = json_decode($others, true) ?: [];
                            $user_id = get_current_user_id();
                            $others['cancel_by'] = $user_id;
                            $data = [
                                'others' => wp_json_encode($others),
                                'order_status' => 'wc-cancelled',
                                'updated_at' => current_time('Y-m-d H:i:s')
                            ];
                            $where = ['id' => (int)$item_id];
                            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                            $wpdb->update($table_name, $data, $where, ['%s', '%s', '%s'], ['%d']);
                        }
                    }
                    wp_send_json_success(['html' => '', 'msg' => esc_html__('Deleted Successfully !', 'abp-event-ticket'), 'type' => 'success']);
                }
                wp_send_json_error(['html' => '', 'msg' => esc_html__('Something Error Occurred !', 'abp-event-ticket'), 'type' => 'warn']);
            }
        }
        new ABPET_Orders();
    }