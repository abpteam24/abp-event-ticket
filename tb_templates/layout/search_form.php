<?php
    if (!defined('ABSPATH')) {
        exit; // Exit if accessed directly
    }
    add_action('abpet_search_form_template', function ($post_infos = [], $form_data = []) {
        $admin_order = $post_infos['admin_order'] ?? '';
        $global_order = $post_infos['global_order'] ?? '';
        $post_id = absint($post_infos['post_id'] ?? 0);
        $all_post_ids = $post_infos['all_post'] ?? [$post_id];
        $form_type = $form_data['form'] ?? 'inline';
        $brand_icon = ABPET_Function::icon();
        $abpet_cart_success = function_exists('WC') && WC()->session ? WC()->session->get('abpet_cart_success') : '';
        if (!empty($abpet_cart_success) && empty($admin_order)) {
            ?>
            <div class="toast_notice" data-type="success">
                <?php echo esc_html(sanitize_text_field($abpet_cart_success)); ?>
            </div>
            <?php
            WC()->session->set('abpet_cart_success', null);
        }
        $all_dates = ABPET_Function::date_all($all_post_ids);
        $upcoming_date = current($all_dates);
        $upcoming_date = !empty($upcoming_date) ? gmdate('Y-m-d', strtotime($upcoming_date)) : '';
        //echo '<pre>';        print_r($all_dates);        echo '</pre>';
        ?>
        <div id="abpet_search_area">
            <h5 class="_abp_gap_xs">
                <span class="fas fa-ticket"></span><?php esc_html_e('BUY TICKET', 'abp-event-ticket'); ?>
            </h5>
            <form class="abp_search_form <?php echo esc_attr($form_type === 'column' ? '_form_column' : '_form_inline'); ?>" method="post" action="">
                <?php if ($post_id > 0 && empty($global_order)) { ?>
                    <input type="hidden" name="post_id" value="<?php echo esc_attr($post_id); ?>"/>
                <?php } else {
                    ABPET_Layout::filter_post_list();
                }
                ?>
                <div class="start_date _input_item">
                    <?php ABPET_Layout::start_date($all_dates, $upcoming_date); ?>
                </div>
                <div class="_input_item_fj_between_fd_column">
                    <span></span>
                    <button type="submit" class="_btn_theme">
                        <?php ABPET_Layout::image_icon($brand_icon); ?>
                        <?php esc_html_e('Check Availability', 'abp-event-ticket'); ?>
                    </button>
                </div>
            </form>
        </div>
        <?php
    }, 10, 2);