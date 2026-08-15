<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPET_Brand' ) ) {
		class ABPET_Brand {
			public function __construct() {
				add_action( 'abpet_global_brand', array( $this, 'global_brand' ) );
				add_action( 'wp_ajax_abpet_add_tax_brand', array( $this, 'add_tax_brand' ) );
				add_action( 'wp_ajax_abpet_save_tax_brand', array( $this, 'save_tax_brand' ) );
				add_action( 'wp_ajax_abpet_delete_tax_brand', array( $this, 'delete_tax_brand' ) );
				add_action( 'abpet_brand_update', array( $this, 'update_brand' ) );
			}
			public function global_brand(): void {
				if ( ABPET_Function::on_off( 'brand' ) ) {
					$label = ABPET_Function::brand_label(); ?>
                    <div class="_fj_between">
                        <h5 class="_abp"><span class="_mar_r_xs">🏷️</span><?php echo esc_html( $label ); ?></h5>
						<?php ABPET_Layout::button_global_popup( 'tax_brand', __( 'Add New', 'abp-event-ticket' ) . ' ' . $label ); ?>
                    </div>
					<?php ABPET_Layout::info_text( 'abpet_brand' ); ?>
                    <div class="tax_brand _ov_auto_mar_t_xs">
						<?php $this->brand_list(); ?>
                    </div>
					<?php
				}
			}
			public function add_tax_brand(): void {
				if ( ! check_ajax_referer( 'abpet_admin_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid security token or Insufficient permissions.', 'abp-event-ticket' ), 'type' => 'warn' ], 403 );
				}
				$term_id = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
				ob_start();
				$name      = $slug = $des = '';
				$label     = ABPET_Function::brand_label();
				$btn_label = __( 'Save', 'abp-event-ticket' ) . ' ' . $label;
				$title     = __( 'Add new ', 'abp-event-ticket' ) . ' ' . $label;
				if ( ! empty( $term_id ) ) {
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
                    <h5 class="_abp"><span class="_mar_r_xs">🏷️</span><?php echo esc_html( $title ); ?></h5>
                    <div class="_divider_xs"></div>
                    <input type="hidden" name="id" value="<?php echo esc_attr( $term_id ); ?>"/>
                    <div class="group_setting">
                        <div class="setting_item full_width">
                            <label class="_f_equal_f_wrap">
                                <span class="_abp_label"><?php echo esc_html( $label ) . ' ' . esc_html__( 'Name', 'abp-event-ticket' ); ?><sup class="_color_required">*</sup></span>
                                <input class="_form_control" name="name" value="<?php echo esc_attr( $name ); ?>" placeholder="<?php esc_attr_e( 'Name', 'abp-event-ticket' ); ?>" required/>
                            </label>
                            <div class="_divider_xs"></div>
							<?php ABPET_Layout::info_text( 'brand_name' ); ?>
                        </div>
                        <div class="setting_item full_width">
                            <label class="_f_equal_f_wrap">
                                <span class="_abp_label"><?php echo esc_html( $label ) . ' ' . esc_html__( 'Slug (Optional)', 'abp-event-ticket' ); ?></span>
                                <input class="_form_control" name="slug" value="<?php echo esc_attr( $slug ); ?>" placeholder="<?php esc_attr_e( 'Slug', 'abp-event-ticket' ); ?>"/>
                            </label>
                            <div class="_divider_xs"></div>
							<?php ABPET_Layout::info_text( 'brand_slug' ); ?>
                        </div>
                        <div class="setting_item full_width">
                            <label class="_f_equal_f_wrap">
                                <span class="_abp_label"><?php echo esc_html( $label ) . ' ' . esc_html__( 'Description(Optional)', 'abp-event-ticket' ); ?></span>
                                <textarea class="_form_control" name="description" placeholder="<?php esc_attr_e( 'Description', 'abp-event-ticket' ); ?>"><?php echo esc_html( $des ); ?></textarea>
                            </label>
                            <div class="_divider_xs"></div>
							<?php ABPET_Layout::info_text( 'brand_des' ); ?>
                        </div>
                    </div>
                    <div class="_divider_xs"></div>
					<?php ABPET_Layout::button_global_save( 'tax_brand', $btn_label ); ?>
                </div>
				<?php
				$html = ob_get_clean();
				wp_send_json_success( [ 'html' => $html, 'type' => 'success', 'msg' => $label . ' ' . __( 'Form Loaded Successfully .....! ', 'abp-event-ticket' ) ] );
			}
			public function save_tax_brand(): void {
				if ( ! check_ajax_referer( 'abpet_admin_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid security token or Insufficient permissions.', 'abp-event-ticket' ), 'type' => 'warn' ], 403 );
				}
				$post_int      = fn( $key, $default = 0 ) => isset( $_POST[ $key ] ) ? absint( $_POST[ $key ] ) : $default;
				$post_val      = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : $default;
				$post_textarea = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) ) : $default;
				$post_slug     = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? sanitize_title( wp_unslash( $_POST[ $key ] ) ) : $default;
				$tax_id        = $post_int( 'id' );
				$name          = $post_val( 'name' );
				$slug          = $post_slug( 'slug' );
				$description   = $post_textarea( 'description' );
				$post_id       = $post_int( 'post_id' );
				$label         = ABPET_Function::brand_label();
				if ( empty( $name ) ) {
					ob_start();
					if ( empty( $post_id ) || $post_id <= 0 ) {
						$this->brand_list();
					}
					$html = ob_get_clean();
					wp_send_json_error( [ 'html' => $html, 'type' => 'warn', 'msg' => $label . ' ' . __( 'Name cannot be blank!', 'abp-event-ticket' ) ], 400 );
				}
				if ( $tax_id > 0 ) {
					$result = wp_update_term( $tax_id, 'abpet_brand', [
						'name'        => $name,
						'slug'        => $slug,
						'description' => $description,
					] );
				} else {
					$result = wp_insert_term( $name, 'abpet_brand', [
						'slug'        => $slug,
						'description' => $description,
					] );
				}
				$this->update_brand();
				ob_start();
				if ( empty( $post_id ) || $post_id <= 0 ) {
					$this->brand_list();
				}
				$html = ob_get_clean();
				if ( is_wp_error( $result ) ) {
					wp_send_json_error( [ 'html' => $html, 'type' => 'warn', 'msg' => $result->get_error_message() ], 400 );
				}
				wp_send_json_success( [ 'html' => $html, 'type' => 'success', 'js' => ABPET_Function::option_js( $post_id, 'abpet_brand' ), 'msg' => $label . ' ' . __( 'Saved Successfully !', 'abp-event-ticket' ), ] );
			}
			public function delete_tax_brand(): void {
				if ( ! check_ajax_referer( 'abpet_admin_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid security token or Insufficient permissions.', 'abp-event-ticket' ), 'type' => 'warn' ], 403 );
				}
				$tax_id = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : '';
				$label  = ABPET_Function::brand_label();
				if ( empty( $tax_id ) || ! is_numeric( $tax_id ) ) {
					ob_start();
					$this->brand_list();
					$html = ob_get_clean();
					wp_send_json_error( [ 'html' => $html, 'msg' => $label . ' ' . __( 'id Invalid...!', 'abp-event-ticket' ), 'type' => 'warn' ] );
				}
				$result = wp_delete_term( $tax_id, 'abpet_brand' );
				$this->update_brand();
				ob_start();
				$this->brand_list();
				$html = ob_get_clean();
				if ( is_wp_error( $result ) ) {
					wp_send_json_error( [ 'html' => $html, 'msg' => $result->get_error_message(), 'type' => 'warn' ] );
				}
				wp_send_json_success( [ 'html' => $html, 'type' => 'success', 'msg' => $label . ' ' . __( 'Deleted Successfully !', 'abp-event-ticket' ) ] );
			}
			public function update_brand(): void {
				$taxonomies = ABPET_Function::get_taxonomy( 'abpet_brand' );
				$category   = [];
				if ( ! empty( $taxonomies ) && is_array( $taxonomies ) && sizeof( $taxonomies ) > 0 ) {
					foreach ( $taxonomies as $taxonomy ) {
						$category[ $taxonomy->term_id ]['label']       = $taxonomy->name;
						$category[ $taxonomy->term_id ]['description'] = $taxonomy->description;
					}
				}
				ksort( $category );
				update_option( 'abpet_brand', $category );
			}
			public function brand_list(): void {
				$brands = ABPET_Function::get_option( 'abpet_brand' );
				$count  = 1;
				if ( ! empty( $brands ) && is_array( $brands ) && sizeof( $brands ) > 0 ) { ?>
                    <table class="_abp">
                        <thead>
                        <tr>
                            <th><?php esc_html_e( 'SI', 'abp-event-ticket' ) ?></th>
                            <th class="_min_200"><?php echo esc_html( ABPET_Function::brand_label() ); ?></th>
                            <th><?php esc_html_e( 'ID', 'abp-event-ticket' ) ?></th>
                            <th class="_min_150"><?php esc_html_e( 'Description', 'abp-event-ticket' ) ?></th>
                            <th class="_w_250"><?php esc_html_e( 'Shortcode', 'abp-event-ticket' ) ?></th>
                            <th class="_w_100"><?php esc_html_e( 'Action', 'abp-event-ticket' ) ?></th>
                        </tr>
                        </thead>
                        <tbody>
						<?php foreach ( $brands as $term_id => $brand ) {
							$name = $brand['name'] ?? ''; ?>
                            <tr>
                                <th><?php echo esc_html( $count ); ?>.</th>
                                <th class="_text_left"><a href="<?php echo esc_url( get_term_link( $term_id ) ); ?>" target="_blank" class="_abp_fs_h5 _color_theme"><?php echo esc_html( $name ); ?></a></th>
                                <th><?php echo esc_html( $term_id ); ?></th>
                                <td><?php echo esc_html( $brand['description'] ?? '' ); ?></td>
                                <th><code> [abpet-post brand_id="<?php echo esc_attr( $term_id ); ?>"]</code></th>
                                <th>
                                    <div class="_fj_center">
                                        <div class="_group_content">
                                            <button type="button" class="_btn_light_yellow_xxs" onclick="abpet_popup_open_global('tax_brand','<?php echo esc_attr( $term_id ); ?>')" title="<?php echo esc_attr__( 'Edit : ', 'abp-event-ticket' ) . ' ' . esc_attr( $name ); ?>"><?php ABPET_Static::icon_svg( 'edit' ); ?></button>
                                            <button type="button" class="_btn_light_danger_xxs" onclick="abpet_delete_global('tax_brand','<?php echo esc_attr( $term_id ); ?>')" title="<?php echo esc_attr__( 'Trash : ', 'abp-event-ticket' ) . ' ' . esc_attr( $name ); ?>"><?php ABPET_Static::icon_svg( 'close_1' ); ?></button>
                                        </div>
                                    </div>
                                </th>
                            </tr>
							<?php $count ++;
						} ?>
                        </tbody>
                    </table>
				<?php } else {
					ABPET_Layout::layout_warning_info( 'no_brand' );
				}
			}
		}
		new ABPET_Brand();
	}