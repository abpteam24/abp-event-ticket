<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPET_Location' ) ) {
		class ABPET_Location {
			public function __construct() {
				add_action( 'abpet_global_location', [ $this, 'global_location' ] );
				add_action( 'wp_ajax_abpet_add_tax_location', [ $this, 'add_tax_location' ] );
				add_action( 'wp_ajax_abpet_save_tax_location', [ $this, 'save_tax_location' ] );
				add_action( 'wp_ajax_abpet_delete_tax_location', [ $this, 'delete_tax_location' ] );
				add_action( 'abpet_location_update', [ $this, 'update_location' ] );
			}
			public function global_location(): void {
				$label = ABPET_Function::location_label(); ?>
                <div class="_fj_between">
                    <h5 class="abp_gap_xs"><span class="fas fa-map-marker-alt"></span><?php echo esc_html( $label ); ?></h5>
					<?php ABPET_Layout::button_global_popup( 'tax_location', __( 'Add New', 'abp-event-ticket' ) . ' ' . $label ); ?>
                </div>
				<?php ABPET_Layout::info_text( 'abpet_location' ); ?>
                <div class="tax_location _ov_auto_mar_t_xs">
					<?php $this->location_list(); ?>
                </div>
				<?php
			}
			public function add_tax_location(): void {
				if ( ! check_ajax_referer( 'abpet_admin_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid security token or Insufficient permissions.', 'abp-event-ticket' ), 'type' => 'warn' ], 403 );
				}
				$term_id = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : '';
				ob_start();
				$name      = $slug = $des = '';
				$label     = ABPET_Function::location_label();
				$btn_label = __( 'Save', 'abp-event-ticket' ) . ' ' . $label;
				$title     = __( 'Add new ', 'abp-event-ticket' ) . ' ' . $label;
				if ( ! empty( $term_id ) && $term_id > 0 ) {
					$term = get_term( $term_id );
					if ( ! empty( $term ) ) {
						$name      = $term->name;
						$slug      = $term->slug;
						$des       = $term->description;
						$btn_label = __( 'Update', 'abp-event-ticket' ) . ' ' . $label . ' ' . $name;
						$title     = __( 'Edit ', 'abp-event-ticket' ) . ' ' . $label . ' ' . $name;
					}
				}
				?>
                <div class="abp_form">
                    <h5 class="abp_gap_xs">📍<?php echo esc_html( $title ); ?></h5>
                    <div class="_divider_xs"></div>
                    <input type="hidden" name="id" value="<?php echo esc_attr( $term_id ); ?>"/>
                    <div class="group_setting">
                        <div class="setting_item">
                            <label>
                                <span><?php echo esc_html( $label ); ?><sup class="_color_required">*</sup></span>
                                <input class="_form_control" name="name" value="<?php echo esc_attr( $name ); ?>" placeholder="<?php esc_attr_e( 'Name', 'abp-event-ticket' ); ?>" required/>
                            </label>
                            <div class="_divider_xs"></div>
							<?php ABPET_Layout::info_text( 'loc_name' ); ?>
                        </div>
                        <div class="setting_item">
                            <label class="_f_equal_f_wrap">
                                <span class="abp_label"><?php echo esc_html( $label ) . ' ' . esc_html_e( 'Slug (Optional)', 'abp-event-ticket' ); ?></span>
                                <input class="_form_control" name="slug" value="<?php echo esc_attr( $slug ); ?>" placeholder="<?php esc_attr_e( 'Slug', 'abp-event-ticket' ); ?>"/>
                            </label>
                            <div class="_divider_xs"></div>
							<?php ABPET_Layout::info_text( 'loc_slug' ); ?>
                        </div>
                        <div class="setting_item full_width">
                            <label class="_f_equal_f_wrap">
                                <span class="abp_label"><?php esc_html_e( 'Full Address(optional)', 'abp-event-ticket' ); ?></span>
                                <textarea class="_form_control" name="description" placeholder="<?php esc_attr_e( 'Address', 'abp-event-ticket' ); ?>"><?php echo esc_html( $des ); ?></textarea>
                            </label>
                            <div class="_divider_xs"></div>
							<?php ABPET_Layout::info_text( 'loc_des' ); ?>
                        </div>
                    </div>
                    <div class="_divider_xs"></div>
					<?php ABPET_Layout::button_global_save( 'tax_location', $btn_label ); ?>
                </div>
				<?php
				$html = ob_get_clean();
				wp_send_json_success( [ 'html' => $html, 'type' => 'success', 'msg' => $label . ' ' . __( 'Form Loaded Successfully .....! ', 'abp-event-ticket' ) ] );
			}
			public function save_tax_location(): void {
				if ( ! check_ajax_referer( 'abpet_admin_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid security token or Insufficient permissions.', 'abp-event-ticket' ), 'type' => 'warn' ], 403 );
				}
				$post_int    = fn( $key, $default = 0 ) => isset( $_POST[ $key ] ) ? absint( $_POST[ $key ] ) : $default;
				$post_val    = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : $default;
				$post_slug   = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? sanitize_title( wp_unslash( $_POST[ $key ] ) ) : $default;
				$tax_id      = $post_int( 'id' );
				$name        = $post_val( 'name' );
				$slug        = $post_slug( 'slug' );
				$description = $post_val( 'description' );
				$post_id     = $post_int( 'post_id' );
				if ( empty( $name ) ) {
					ob_start();
					$html = '';
					if ( empty( $post_id ) || $post_id <= 0 ) {
						ob_start();
						$this->location_list();
						$html = ob_get_clean();
					}
					wp_send_json_error( [ 'html' => $html, 'msg' => ABPET_Function::location_label() . ' ' . __( 'Name cannot be blank!', 'abp-event-ticket' ), 'type' => 'warn' ] );
				}
				if ( $tax_id > 0 ) {
					$result = wp_update_term( $tax_id, 'abpet_location', [
						'name'        => $name,
						'slug'        => $slug,
						'description' => $description,
					] );
				} else {
					$result = wp_insert_term( $name, 'abpet_location', [
						'slug'        => $slug,
						'description' => $description,
					] );
				}
				if ( is_wp_error( $result ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => $result->get_error_message() ] );
				}
				$term_id = absint( $result['term_id'] ?? 0 );
				if ( $term_id <= 0 ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Failed to resolve location context.', 'abp-event-ticket' ), 'type' => 'warn' ] );
				}
				$this->update_location();
				ob_start();
				$html = '';
				if ( empty( $post_id ) || $post_id <= 0 ) {
					ob_start();
					$this->location_list();
					$html = ob_get_clean();
				}
				wp_send_json_success( [
					'html' => $html,
					'type' => 'success',
					'msg'  => ABPET_Function::location_label() . ' ' . __( 'Saved Successfully !', 'abp-event-ticket' ),
					'js'   => ABPET_Function::option_js($post_id,'abpet_location'),
				] );
			}
			public function delete_tax_location(): void {
				if ( ! check_ajax_referer( 'abpet_admin_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid security token or Insufficient permissions.', 'abp-event-ticket' ), 'type' => 'warn' ], 403 );
				}
				$tax_id = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : '';
				if ( empty( $tax_id ) || ! is_numeric( $tax_id ) ) {
					ob_start();
					$this->location_list();
					$html = ob_get_clean();
					wp_send_json_error( [ 'html' => $html, 'msg' => ABPET_Function::location_label() . ' ' . __( 'id Invalid...!', 'abp-event-ticket' ), 'type' => 'warn' ] );
				}
				$result = wp_delete_term( $tax_id, 'abpet_location' );
				$this->update_location();
				ob_start();
				$this->location_list();
				$html = ob_get_clean();
				if ( is_wp_error( $result ) ) {
					wp_send_json_error( [ 'html' => $html, 'msg' => $result->get_error_message(), 'type' => 'warn' ] );
				}
				wp_send_json_success( [ 'html' => $html, 'type' => 'success', 'msg' => ABPET_Function::location_label() . ' ' . __( 'Deleted Successfully !', 'abp-event-ticket' ) ] );
			}
			public function update_location(): void {
				$taxonomies = ABPET_Function::get_taxonomy( 'abpet_location' );
				$taxonomies = is_array( $taxonomies ) ? $taxonomies : [];
				$location   = [];
				if ( count( $taxonomies ) > 0 ) {
					foreach ( $taxonomies as $taxonomy ) {
						$term_id                             = $taxonomy->term_id;
						$location[ $term_id ]['label']        = $taxonomy->name;
						$location[ $term_id ]['description'] = $taxonomy->description;
						$location[ $term_id ]['slug']        = $taxonomy->slug;
					}
				}
				ksort( $location );
				update_option( 'abpet_location', $location );
			}
			public function location_list(): void {
				$options = ABPET_Function::get_option( 'abpet_location' );
				$options = is_array( $options ) ? $options : [];
				$count   = 1;
				if ( count( $options ) > 0 ) { ?>
                    <table class="abp">
                        <thead>
                        <tr>
                            <th><?php esc_html_e( 'SI', 'abp-event-ticket' ) ?></th>
                            <th><?php esc_html_e( 'ID', 'abp-event-ticket' ) ?></th>
                            <th class="_min_150"><?php echo esc_html( ABPET_Function::location_label() ); ?></th>
                            <th><?php esc_html_e( 'Full Address', 'abp-event-ticket' ) ?></th>
                            <th><?php esc_html_e( 'Shortcode Post', 'abp-event-ticket' ) ?></th>
                            <th><?php esc_html_e( 'Action', 'abp-event-ticket' ) ?></th>
                        </tr>
                        </thead>
                        <tbody>
						<?php foreach ( $options as $term_id => $option ) {
							$name = $option['name'] ?? ''; ?>
                            <tr>
                                <th><?php echo esc_html( $count ); ?>.</th>
                                <th><?php echo esc_html( $term_id ); ?></th>
                                <th class="_text_left"><a href="<?php echo esc_url( get_term_link( (int) $term_id ) ); ?>" target="_blank" class="abp_fs_h5_color_theme"><?php echo esc_html( $name ); ?></a></th>
                                <td><?php echo esc_html( $option['description'] ?? '' ); ?></td>
                                <th class="_text_nowrap"><code> [abpet-post loc_id="<?php echo esc_attr( $term_id ); ?>"]</code></th>
                                <td>
                                    <div class="_fj_center">
                                        <div class="_group_content">
                                            <button type="button" class="_btn_light_yellow_xxs" onclick="abpet_popup_open_global('tax_location','<?php echo esc_attr( $term_id ); ?>')" title="<?php echo esc_attr__( 'Edit : ', 'abp-event-ticket' ) . ' ' . esc_attr( $name ); ?>"><?php ABPET_Static::icon_svg( 'edit' ); ?></button>
                                            <button type="button" class="_btn_light_danger_xxs" onclick="abpet_delete_global('tax_location','<?php echo esc_attr( $term_id ); ?>')" title="<?php echo esc_attr__( 'Trash : ', 'abp-event-ticket' ) . ' ' . esc_attr( $name ); ?>"><?php ABPET_Static::icon_svg( 'close_2' ); ?></button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
							<?php $count ++;
						} ?>
                        </tbody>
                    </table>
				<?php } else {
					ABPET_Layout::layout_warning_info( 'no_location' );
				}
			}
		}
		new ABPET_Location();
	}