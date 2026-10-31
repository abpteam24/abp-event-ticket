<?php
    if (!defined('ABSPATH')) {
        exit; // Exit if accessed directly
    }
    if (!class_exists('ABPET_FAQ')) {
        class ABPET_FAQ {
            public function __construct() {
                add_action('abpet_global_faq', array($this, 'global_faq'));
                add_action('wp_ajax_abpet_save_faq_config', array($this, 'save_faq_config'));
                add_filter('abpet_get_faq_array', array($this, 'get_faq_array'));
                add_action('abpet_post_content', array($this, 'post_faq'));
                add_action('wp_ajax_abpet_import_faq_content', array($this, 'import_faq_content'));
            }
            public function global_faq(): void {
                if (ABPET_Function::on_off('faq')) {
                    ?>
                    <div class="faq_configuration">
                        <h4 class="abp_gap_xs">❓<?php esc_html_e('Global FAQ Configuration', 'abp-event-ticket'); ?></h4>
                        <?php ABPET_Layout::info_text('abpet_faq'); ?>
                        <div class="_divider_xxs"></div>
                        <div class="faq_config _mar_t_xs">
                            <?php $this->faq_config(); ?>
                        </div>
                    </div>
                    <?php
                }
            }
            public function faq_config(): void {
                $faqs = ABPET_Function::get_option('abpet_faq');
                ?>
                <div class="abp_form">
                    <?php $this->faq($faqs, true); ?>
                </div>
                <?php
            }
            public function faq($faqs = [], $global = false): void {
                ?>
                <div class="configuration_content">
                    <div class="insertable_area sortable_area">
                        <?php
                            if (!empty($faqs) && sizeof($faqs) > 0) {
                                foreach ($faqs as $faq) {
                                    $this->faq_item($faq);
                                }
                            }
                        ?>
                    </div>
                    <div class="_fj_between">
                        <?php ABPET_Layout::button_add(__('Add New FAQ Item', 'abp-event-ticket'));
                            if ($global) {
                                ABPET_Layout::button_global_save('faq_config', __('Save FAQs Configuration', 'abp-event-ticket'));
                            } ?>
                    </div>
                    <div class="abp_hidden">
                        <div class="hidden_content">
                            <?php $this->faq_item(); ?>
                        </div>
                    </div>
                </div>
                <?php
            }
            public function faq_item($faq = []): void {
                $title = $faq['title'] ?? __('NEW', 'abp-event-ticket');
                $description = $faq['des'] ?? '';
                $description = $description ? html_entity_decode($description) : '';
                $editor_id = uniqid('abpet_editor_');
                ?>
                <div class="delete_area faq_item _mar_b_xs <?php echo esc_attr(empty($faq) ? 'active' : ''); ?>">
                    <div class="faq_question">
                        <h6 class="abp edit_hook" data-paste="#faq_title"><?php echo esc_html($title); ?></h6>
                        <?php ABPET_Layout::button_delete_sort_edit(); ?>
                    </div>
                    <div class="edit_area">
                        <div class="faq_answer_content">
                            <?php ABPET_Layout::info_text('faq_item'); ?>
                            <div class="_divider_xs"></div>
                            <label class="_f_equal_f_wrap">
                                <span class="abp_label"><?php esc_html_e('FAQ Title', 'abp-event-ticket'); ?><sup class="_color_required">*</sup></span>
                                <input type="text" class="_form_control" name="faq_title[]" data-pass="#faq_title" placeholder="<?php esc_attr_e('EX: What is the check-in time?', 'abp-event-ticket'); ?>" value="<?php echo esc_attr($title); ?>"/>
                            </label>
                            <div class="_fd_column_mar_t_xs">
                                <span class="abp_label"><?php esc_html_e('Description', 'abp-event-ticket'); ?></span>
                                <?php
                                    wp_editor(
                                        $description,
                                        $editor_id,
                                        array(
                                            'textarea_name' => 'faq_description[]',
                                            'textarea_rows' => 6,
                                            'media_buttons' => true,
                                            'teeny' => false,
                                            'quicktags' => true
                                        )
                                    );
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            public function post_faq($post_infos = []): void {
                if (ABPET_Function::on_off('faq')) {
                    $post_id = absint($post_infos['post_id'] ?? 0);
                    $display = $post_infos['display_faq'] ?? 'on';
                    $active_global_faq = $post_infos['active_global_faq'] ?? 'on';
                    $faqs = get_post_meta($post_id, 'abpet_faq', true);
                    $faqs = is_array($faqs) ? $faqs : [];
                    ?>
                    <div class="tab_item faq_configuration" data-tabs="#abpet_faq">
                        <h4 class="abp_color_theme_gap_xxs">❓<?php esc_html_e('FAQs Configuration', 'abp-event-ticket'); ?></h4>
                        <div class="_divider_xs"></div>
                        <div class="group_setting">
                            <div class="setting_item">
                                <div class="_f_wrap_fj_between_fa_center">
                                    <label>
                                        <?php ABPET_Layout::switch_checkbox('display_faq', $display); ?>
                                        <span><?php esc_html_e('Active FAQs ?', 'abp-event-ticket'); ?></span>
                                    </label>
                                </div>
                                <div class="_divider_xs"></div>
                                <?php ABPET_Layout::info_text('display_faq'); ?>
                            </div>
                            <div data-collapse="#display_faq" class="setting_item <?php echo esc_attr($display == 'on' ? 'abp_active' : ''); ?>">
                                <div class="_fj_between">
                                    <label>
                                        <?php ABPET_Layout::switch_checkbox('active_global_faq', $active_global_faq); ?>
                                        <span ><?php esc_html_e('Use Global FAQ ?', 'abp-event-ticket'); ?></span>
                                    </label>
                                    <div data-collapse="#active_global_faq" class=" <?php echo esc_attr($active_global_faq == 'on' ? '' : 'abp_active'); ?>">
                                        <button type="button" class="_btn_theme" onclick="abpet_import_global('faq_content')"><span class="fas fa-file-upload _mar_r_xs"></span><?php esc_html_e('Import Global FAQ', 'abp-event-ticket'); ?></button>
                                    </div>
                                </div>
                                <div class="_divider_xs"></div>
                                <?php ABPET_Layout::info_text('active_global_faq'); ?>
                            </div>
                        </div>
                        <div data-collapse="#display_faq" class="<?php echo esc_attr($display == 'on' ? 'abp_active' : ''); ?>">
                            <div class="_mar_t_xs <?php echo esc_attr($active_global_faq == 'on' ? '' : 'abp_active'); ?>" data-collapse="#active_global_faq">
                                <div class="faq_content">
                                    <?php $this->faq($faqs); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }
            }
            public function save_faq_config(): void {
                if (!check_ajax_referer('abpet_admin_ajax_nonce', 'nonce', false) || !current_user_can('manage_options')) {
                    wp_send_json_error(['msg' => __('Invalid security token or Insufficient permissions.', 'abp-event-ticket'), 'type' => 'warn'], 403);
                }
                $abpet_faq = $this->get_faq_array();
                update_option('abpet_faq', $abpet_faq);
                ob_start();
                $this->faq_config();
                $html = ob_get_clean();
                wp_send_json_success([
                    'html' => $html,
                    'msg' => __('FAQs Configuration Saved Successfully..... !! ', 'abp-event-ticket'),
                    'type' => 'success'
                ]);
            }
            public function get_faq_array(array $abpet_faq = []): array {
                $has_post_nonce = isset($_POST['abpet_post_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['abpet_post_nonce'])), 'abpet_post_nonce');
                $has_ajax_nonce = check_ajax_referer('abpet_admin_ajax_nonce', 'nonce', false);
                if (($has_post_nonce || $has_ajax_nonce) && current_user_can('manage_options')) {
                    $post_array = fn($key) => (isset($_POST[$key]) && is_array($_POST[$key])) ? array_map('sanitize_text_field', wp_unslash($_POST[$key])) : [];
                    $post_html_array = fn($key) => (isset($_POST[$key]) && is_array($_POST[$key])) ? array_map('wp_kses_post', wp_unslash($_POST[$key])) : [];
                    $titles = $post_array('faq_title');
                    $descriptions = $post_html_array('faq_description');
                    if (!empty($titles)) {
                        foreach ($titles as $key => $title) {
                            if ($title && !empty($descriptions[$key])) {
                                $abpet_faq[$key] = [
                                    'title' => $title,
                                    'des' => $descriptions[$key],
                                ];
                            }
                        }
                    }
                }
                return $abpet_faq;
            }
            public function import_faq_content(): void {
                if (!check_ajax_referer('abpet_admin_ajax_nonce', 'nonce', false) || !current_user_can('manage_options')) {
                    wp_send_json_error(['msg' => __('Invalid security token or Insufficient permissions.', 'abp-event-ticket'), 'type' => 'warn'], 403);
                }
                $faqs = ABPET_Function::get_option('abpet_faq');
                $faqs = is_array($faqs) ? $faqs : [];
                ob_start();
                $this->faq($faqs);
                $html_content = ob_get_clean();
                wp_send_json_success(['html' => $html_content, 'msg' => __('FAQ Imported Successfully ..... !! ', 'abp-event-ticket'), 'type' => 'success']);
            }
        }
        new ABPET_FAQ();
    }
