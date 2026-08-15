<?php
    if (!defined('ABSPATH')) {
        exit; // Exit if accessed directly
    }
    if (!class_exists('ABPET_Feature')) {
        class ABPET_Feature {
            public function __construct() {
                add_action('abpet_global_feature', array($this, 'global_feature'));
                add_action('wp_ajax_abpet_add_option_feature', array($this, 'add_option_feature'));
                add_action('wp_ajax_abpet_save_option_feature', array($this, 'save_option_feature'));
                add_action('wp_ajax_abpet_delete_option_feature', array($this, 'delete_option_feature'));
            }
            public function global_feature(): void {
                if (ABPET_Function::on_off('feature')) {
                    $label = ABPET_Function::feature_label(); ?>
                    <div class="_fj_between">
                        <h5 class="_abp"><span class="_mar_r_xs">🔗</span><?php echo esc_html($label); ?></h5>
                        <?php ABPET_Layout::button_global_popup('option_feature', __('Add New', 'abp-event-ticket') . ' ' . $label); ?>
                    </div>
                    <?php ABPET_Layout::info_text('abpet_feature'); ?>
                    <div class="option_feature _ov_auto_mar_t_xs">
                        <?php $this->feature_list(); ?>
                    </div>
                    <?php
                }
            }
            public function add_option_feature(): void {
                if (!check_ajax_referer('abpet_admin_ajax_nonce', 'nonce', false) || !current_user_can('manage_options')) {
                    wp_send_json_error(['msg' => __('Invalid security token or Insufficient permissions.', 'abp-event-ticket'), 'type' => 'warn'], 403);
                }
                ob_start();
                $id = isset($_POST['id']) ? absint(wp_unslash($_POST['id'])) : '';
                $options = ABPET_Function::get_option('abpet_feature');
                $options = is_array($options) ? $options : [];
                $feature = $options[$id] ?? [];
                $label = ABPET_Function::feature_label();
                $btn_label = __('Save', 'abp-event-ticket') . ' ' . $label;
                $title = __('Add new ', 'abp-event-ticket') . ' ' . $label;
                ?>
                <div class="abp_form">
                    <h5 class="_abp"><span class="_mar_r_xs">🔗</span><?php echo esc_html($title); ?></h5>
                    <?php ABPET_Layout::info_text('feature_icon');
                        ABPET_Layout::info_text('feature_name');
                        ABPET_Layout::info_text('feature_value'); ?>
                    <div class="_divider_xxs"></div>
                    <div class="configuration_content">
                        <table class="_abp ">
                            <thead>
                            <tr>
                                <th><?php esc_html_e('Icon', 'abp-event-ticket'); ?></th>
                                <th><?php esc_html_e('Label', 'abp-event-ticket'); ?><sup class="_color_required">*</sup></th>
                                <th><?php esc_html_e('Value', 'abp-event-ticket'); ?></th>
                                <th class="_w_10"><?php esc_html_e('Action', 'abp-event-ticket'); ?></th>
                            </tr>
                            </thead>
                            <tbody class="insertable_area sortable_area">
                            <?php self::form_feature($feature, $id); ?>
                            </tbody>
                        </table>
                        <div class="_divider_xs"></div>
                        <div class="_fj_between">
                            <?php ABPET_Layout::button_add(__('Add New Feature Item', 'abp-event-ticket')); ?>
                            <?php ABPET_Layout::button_global_save('option_feature', $btn_label); ?>
                        </div>
                        <div class="abp_hidden">
                            <table class="_abp">
                                <tbody class="hidden_content">
                                <?php self::form_feature(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php
                $html = ob_get_clean();
                wp_send_json_success(['html' => $html, 'type' => 'success', 'msg' => $label . ' ' . __('Form Loaded Successfully .....! ', 'abp-event-ticket')]);
            }
            public function save_option_feature(): void {
                if (!check_ajax_referer('abpet_admin_ajax_nonce', 'nonce', false) || !current_user_can('manage_options')) {
                    wp_send_json_error(['msg' => __('Invalid security token or Insufficient permissions.', 'abp-event-ticket'), 'type' => 'warn'], 403);
                }
                $post_int = fn($key, $default = 0) => isset($_POST[$key]) ? absint($_POST[$key]) : $default;
                $post_array = fn($key) => (isset($_POST[$key]) && is_array($_POST[$key])) ? array_map('sanitize_text_field', wp_unslash($_POST[$key])) : [];
                $old_features = ABPET_Function::get_option('abpet_feature');
                $old_features = is_array($old_features) ? $old_features : [];
                $feature_ids = $post_array('feature_id');
                $feature_names = $post_array('feature_name');
                $feature_values = $post_array('feature_value');
                $feature_icon = $post_array('feature_icon');
                $post_id = $post_int('post_id');
                if (!empty($feature_names)) {
                    foreach ($feature_names as $key => $feature_name) {
                        $feature_val = $feature_values[$key] ?? '';
                        if ($feature_name !== '') {
                            $old_id = isset($feature_ids[$key]) ? (int)$feature_ids[$key] : '';
                            if (!empty($old_id) && isset($old_features[$old_id])) {
                                $id = $old_id;
                            } else {
                                $id = 1;
                                while (isset($old_features[$id])) {
                                    $id++;
                                }
                            }
                            $old_features[$id] = [
                                'label' => $feature_name,
                                'value' => $feature_val,
                                'icon' => $feature_icon[$key] ?? '',
                            ];
                        }
                    }
                }
                update_option('abpet_feature', $old_features);
                $html = '';
                if (empty($post_id) || $post_id <= 0) {
                    ob_start();
                    $this->feature_list();
                    $html = ob_get_clean();
                }
                wp_send_json_success([
                    'html' => $html,
                    'msg' => __('Feature Saved Successfully..........!!', 'abp-event-ticket'),
                    'js' => ABPET_Function::option_js($post_id,'abpet_feature'),
                ]);
            }
            public function delete_option_feature(): void {
                if (!check_ajax_referer('abpet_admin_ajax_nonce', 'nonce', false) || !current_user_can('manage_options')) {
                    wp_send_json_error(['msg' => __('Invalid security token or Insufficient permissions.', 'abp-event-ticket'), 'type' => 'warn'], 403);
                }
                $id = isset($_POST['id']) ? absint(wp_unslash($_POST['id'])) : 0;
                $options = ABPET_Function::get_option('abpet_feature');
                $options = is_array($options) ? $options : [];
                if (!empty($id) && isset($options[$id])) {
                    unset($options[$id]);
                    update_option('abpet_feature', $options);
                }
                ob_start();
                $this->feature_list();
                $html = ob_get_clean();
                wp_send_json_success([
                    'html' => $html,
                    'msg' => ABPET_Function::feature_label() . ' ' . __('Deleted Successfully!', 'abp-event-ticket'),
                    'type' => 'success'
                ]);
            }
            public function feature_list(): void {
                $features = ABPET_Function::get_option('abpet_feature');
                //echo '<pre>';				print_r( $features );				echo '</pre>';
                if (sizeof($features) > 0) { ?>
                    <div class="_group_list">
                        <?php foreach ($features as $key => $feature) {
                            $label = $feature['label'] ?? '';
                            $value = $feature['value'] ?? '';
                            if (!empty($label)) { ?>
                                <div class="_list_item">
                                    <h6 class="_abp_gap_xs">
                                        <?php ABPET_Layout::image_icon($feature['icon'] ?? '');
                                            echo esc_html($label . ' ' . (!empty($value) ? '-' . $value : '')); ?>
                                    </h6>
                                    <div class="_group_content">
                                        <button type="button" class="_btn_light_yellow_xxs" onclick="abpet_popup_open_global('option_feature','<?php echo esc_attr($key); ?>')" title="<?php echo esc_attr__('Edit : ', 'abp-event-ticket') . ' ' . esc_attr($label); ?>"><?php ABPET_Static::icon_svg('edit'); ?></button>
                                        <button type="button" class="_btn_light_danger_xxs" onclick="abpet_delete_global('option_feature','<?php echo esc_attr($key); ?>')" title="<?php echo esc_attr__('Trash : ', 'abp-event-ticket') . ' ' . esc_attr($label); ?>"><?php ABPET_Static::icon_svg('close_1'); ?></button>
                                    </div>
                                </div>
                                <?php
                            }
                        } ?>
                    </div>
                <?php } else {
                    ABPET_Layout::layout_warning_info('no_feature');
                }
            }
            public static function form_feature($feature = [], $id = ''): void {
                $label = $feature['label'] ?? '';
                $value = $feature['value'] ?? '';
                $icon = $feature['icon'] ?? '';
                ?>
                <tr class="delete_area">
                    <th><?php do_action('abpet_add_icon', 'feature_icon[]', $icon); ?></th>
                    <th>
                        <label>
                            <input type="hidden" name="feature_id[]" value="<?php echo esc_attr($id); ?>"/>
                            <input type="text" class="_form_control validation_name" name="feature_name[]" placeholder="<?php esc_attr_e('EX: Feature Title', 'abp-event-ticket'); ?>" value="<?php echo esc_attr($label); ?>" required/>
                        </label>
                    </th>
                    <th>
                        <label>
                            <input type="text" class="_form_control validation_name" name="feature_value[]" placeholder="<?php esc_attr_e('EX: Feature Value', 'abp-event-ticket'); ?>" value="<?php echo esc_attr($value); ?>"/>
                        </label>
                    </th>
                    <td><?php ABPET_Layout::button_delete_sort(); ?></td>
                </tr>
                <?php
            }
        }
        new ABPET_Feature();
    }