<?php
    if (!defined('ABSPATH')) {
        exit; // Exit if accessed directly
    }
    add_action('abpet_details_light_template', function ($post_id,$form_data = []) {
        if (!empty($post_id) && $post_id > 0 && get_post_type( $post_id ) == ABPET_Function::get_cpt() && (get_post_status($post_id)=='publish' || is_admin()) ) {
            $post_infos = ABPET_Function::get_all_meta($post_id);
            $form_data['form'] = 'inline';
            $display_return = $post_infos['display_return'] ?? 'off';
            $bp_dp = $form_data['bp_dp'] ?? '';
            $display_return=ABPET_Function::on_off('return')?$display_return:'off';
            $content = get_post_field('post_content', $post_id);
            //echo '<pre>';print_r(ABPET_Function::get_route_info());echo '</pre>';
            ?>
            <div id="abpet_area" class="abpet_area default_details_page">
                <div class="abp_container">
                    <div class="_abp_row">
                        <div class="_f_equal_f_wrap_gap_section_15">
                            <div class="_min_500">
                                <h1 class="_abp_color_theme_fs_h3"><?php ABPET_Layout::title($post_infos); ?></h1>
                                <?php ABPET_Layout::sub_title($post_infos); ?>
                                <div class="_gap_xs_mar_t_xs">
                                    <?php ABPET_Layout::capacity($post_infos);
                                        ABPET_Layout::category($post_infos);
                                        ABPET_Layout::brand($post_infos);
                                        ABPET_Layout::organizer($post_infos, 'publish');
                                    ?>
                                </div>
                                <?php ABPET_Layout::item_feature($post_infos['post_feature'] ?? '');
                                    ABPET_Layout::description($post_infos);                          ?>
                            </div>
                            <div class="_min_500">
                                <?php do_action('abpet_search_form', $post_infos, $form_data); ?>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($content)) { ?>
                        <div class="_abp_row">
                            <div class="_col_12">
                                <div class="the_post_content">
                                    <?php the_content(); ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="_abp_row">
                        <div class="_col_12 abpet_booking">
                            <?php do_action('abpet_registration', $post_infos, $form_data); ?>
                        </div>
                    </div>
                    <div class="_abp_row">
                        <div class="_f_equal_f_wrap_gap_w_full">
                            <div class="_min_500 _fd_column_gap_xs">
                                <?php do_action('abpet_faq', $post_infos); ?>
                                <?php do_action('abpet_slider', ($post_infos['abpet_slider'] ?? [])); ?>
                            </div>
                            <div class="_min_500">
                                <?php do_action('abpet_term_condition', $post_infos); ?>
                            </div>
                        </div>
                    </div>
                    <div class="_abp_row">
                        <div class="_col_12"> <?php do_action('abpet_related_item', ($post_infos['related_item'] ?? '')); ?></div>
                    </div>
                </div>
            </div>
            <?php
        }
    });
