<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPET_Speaker' ) ) {
		class ABPET_Speaker {
			public function __construct() {
				add_action( 'abpet_global_speaker', array( $this, 'global_speaker' ) );
				add_action( 'wp_ajax_abpet_add_tax_speaker', array( $this, 'add_tax_speaker' ) );
				add_action( 'wp_ajax_abpet_save_tax_speaker', array( $this, 'save_tax_speaker' ) );
				add_action( 'wp_ajax_abpet_delete_tax_speaker', array( $this, 'delete_tax_speaker' ) );
				add_action( 'abpet_speaker_update', array( $this, 'update_speaker' ) );
			}
			public function global_speaker(): void {
				if ( ABPET_Function::on_off( 'speaker' ) ) {
					$label = ABPET_Function::speaker_label(); ?>
					<div class="_fj_between">
						<h5 class="abp"><span class="_mar_r_xs">🎤</span><?php echo esc_html( $label ); ?></h5>
						<?php ABPET_Layout::button_global_popup( 'tax_speaker', __( 'Add New', 'abp-event-ticket' ) . ' ' . $label ); ?>
					</div>
					<?php ABPET_Layout::info_text( 'abpet_speaker' ); ?>
					<div class="tax_speaker _ov_auto_mar_t_xs">
						<?php $this->speaker_list(); ?>
					</div>
					<?php
				}
			}
			public function add_tax_speaker(): void {
				if ( ! check_ajax_referer( 'abpet_admin_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid security token or Insufficient permissions.', 'abp-event-ticket' ), 'type' => 'warn' ], 403 );
				}
				$term_id = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
				ob_start();
				$name         = $slug = $des = '';
				$meta         = [];
				$label        = ABPET_Function::speaker_label();
				$btn_label    = __( 'Save', 'abp-event-ticket' ) . ' ' . $label;
				$title        = __( 'Add new ', 'abp-event-ticket' ) . ' ' . $label;
				if ( ! empty( $term_id ) ) {
					$term = get_term( $term_id, 'abpet_speaker' );
					if ( ! empty( $term ) && ! is_wp_error( $term ) ) {
						$name      = $term->name;
						$slug      = $term->slug;
						$des       = $term->description;
						$btn_label = __( 'Update', 'abp-event-ticket' ) . ' ' . $label . ' ' . $name;
						$title     = __( 'Edit ', 'abp-event-ticket' ) . ' ' . $label . ' ' . $name;
						$meta      = get_term_meta( $term_id, '_abpet_speaker_meta', true );
						$meta      = is_array( $meta ) ? $meta : [];
					}
				}
				?>
				<div class="abp_form">
					<h5 class="abp"><span class="_mar_r_xs">🎤</span><?php echo esc_html( $title ); ?></h5>
					<div class="_divider_xs"></div>
					<input type="hidden" name="id" value="<?php echo esc_attr( $term_id ); ?>"/>
					<div class="group_setting">
						<div class="setting_item full_width">
							<label class="_f_equal_f_wrap">
								<span class="abp_label"><?php echo esc_html( $label ) . ' ' . esc_html__( 'Name', 'abp-event-ticket' ); ?><sup class="_color_required">*</sup></span>
								<input class="_form_control" name="name" value="<?php echo esc_attr( $name ); ?>" placeholder="<?php esc_attr_e( 'Name', 'abp-event-ticket' ); ?>" required/>
							</label>
							<div class="_divider_xs"></div>
							<?php ABPET_Layout::info_text( 'speaker_name' ); ?>
						</div>
						<div class="setting_item full_width">
							<label class="_f_equal_f_wrap">
								<span class="abp_label"><?php echo esc_html( $label ) . ' ' . esc_html__( 'Slug (Optional)', 'abp-event-ticket' ); ?></span>
								<input class="_form_control" name="slug" value="<?php echo esc_attr( $slug ); ?>" placeholder="<?php esc_attr_e( 'Slug', 'abp-event-ticket' ); ?>"/>
							</label>
							<div class="_divider_xs"></div>
							<?php ABPET_Layout::info_text( 'speaker_slug' ); ?>
						</div>
						<div class="setting_item full_width">
							<label class="_f_equal_f_wrap">
								<span class="abp_label"><?php echo esc_html( $label ) . ' ' . esc_html__( 'Bio', 'abp-event-ticket' ); ?></span>
								<?php
									$editor_id = 'abpet_speaker_bio_' . $term_id;
									wp_editor(
										$des,
										$editor_id,
										array(
											'textarea_name' => 'description',
											'textarea_rows' => 6,
											'media_buttons' => true,
											'teeny'         => false,
											'quicktags'     => true,
										)
									);
								?>
							</label>
							<div class="_divider_xs"></div>
							<?php ABPET_Layout::info_text( 'speaker_bio' ); ?>
						</div>
						<div class="setting_item full_width">
							<span class="abp_label"><?php echo esc_html( $label ) . ' ' . esc_html__( 'Photo', 'abp-event-ticket' ); ?></span>
							<div class="speaker_photo_selection">
								<?php do_action( 'abpet_image_selection', 'meta[photo]', absint( $meta['photo'] ?? 0 ) ); ?>
							</div>
							<div class="_divider_xs"></div>
							<?php ABPET_Layout::info_text( 'speaker_photo' ); ?>
						</div>
						<div class="setting_item full_width">
							<label class="_f_equal_f_wrap">
								<span class="abp_label"><?php echo esc_html( $label ) . ' ' . esc_html__( 'Designation', 'abp-event-ticket' ); ?></span>
								<input class="_form_control" name="meta[designation]" value="<?php echo esc_attr( $meta['designation'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'e.g., CEO, CTO, Professor', 'abp-event-ticket' ); ?>"/>
							</label>
							<div class="_divider_xs"></div>
							<?php ABPET_Layout::info_text( 'speaker_designation' ); ?>
						</div>
						<div class="setting_item full_width">
							<label class="_f_equal_f_wrap">
								<span class="abp_label"><?php echo esc_html( $label ) . ' ' . esc_html__( 'Company', 'abp-event-ticket' ); ?></span>
								<input class="_form_control" name="meta[company]" value="<?php echo esc_attr( $meta['company'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Company/Organization', 'abp-event-ticket' ); ?>"/>
							</label>
							<div class="_divider_xs"></div>
							<?php ABPET_Layout::info_text( 'speaker_company' ); ?>
						</div>
						<div class="setting_item full_width">
							<label class="_f_equal_f_wrap">
								<span class="abp_label"><?php echo esc_html( $label ) . ' ' . esc_html__( 'Website', 'abp-event-ticket' ); ?></span>
								<input type="url" class="_form_control" name="meta[website]" value="<?php echo esc_attr( $meta['website'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'https://example.com', 'abp-event-ticket' ); ?>"/>
							</label>
							<div class="_divider_xs"></div>
							<?php ABPET_Layout::info_text( 'speaker_website' ); ?>
						</div>
						<div class="setting_item full_width">
							<strong><?php esc_html_e( 'Social Links', 'abp-event-ticket' ); ?></strong>
							<div class="_divider_xxs"></div>
							<?php
							$social = $meta['social'] ?? [];
							$social_fields = [
								'twitter'   => [ 'icon' => 'fab fa-twitter', 'label' => 'Twitter/X', 'info' => 'speaker_social_twitter' ],
								'linkedin'  => [ 'icon' => 'fab fa-linkedin', 'label' => 'LinkedIn', 'info' => 'speaker_social_linkedin' ],
								'facebook'  => [ 'icon' => 'fab fa-facebook', 'label' => 'Facebook', 'info' => 'speaker_social_facebook' ],
								'instagram' => [ 'icon' => 'fab fa-instagram', 'label' => 'Instagram', 'info' => 'speaker_social_instagram' ],
							];
							foreach ( $social_fields as $key => $info ) { ?>
								<div class="setting_item full_width _mar_t_xxs">
									<label class="_f_equal_f_wrap">
										<span class="abp_label"><i class="<?php echo esc_attr( $info['icon'] ); ?>"></i> <?php echo esc_html( $info['label'] ); ?></span>
										<input type="url" class="_form_control" name="meta[social][<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $social[ $key ] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Profile URL', 'abp-event-ticket' ); ?>"/>
									</label>
									<div class="_divider_xxs"></div>
									<?php ABPET_Layout::info_text( $info['info'] ); ?>
								</div>
							<?php } ?>
						</div>
					</div>
					<div class="_divider_xs"></div>
					<?php ABPET_Layout::button_global_save( 'tax_speaker', $btn_label ); ?>
				</div>
				<?php
				$html = ob_get_clean();
				wp_send_json_success( [ 'html' => $html, 'type' => 'success', 'msg' => $label . ' ' . __( 'Form Loaded Successfully .....! ', 'abp-event-ticket' ) ] );
			}
			public function save_tax_speaker(): void {
				if ( ! check_ajax_referer( 'abpet_admin_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid security token or Insufficient permissions.', 'abp-event-ticket' ), 'type' => 'warn' ], 403 );
				}
				$post_int       = fn( $key, $default = 0 ) => isset( $_POST[ $key ] ) ? absint( $_POST[ $key ] ) : $default;
				$post_val       = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : $default;
				$post_textarea  = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? wp_kses_post( wp_unslash( $_POST[ $key ] ) ) : $default;
				$post_slug      = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? sanitize_title( wp_unslash( $_POST[ $key ] ) ) : $default;
				$post_meta      = fn( $key, $default = '' ) => isset( $_POST['meta'] ) && is_array( $_POST['meta'] ) && isset( $_POST['meta'][ $key ] )
					? sanitize_text_field( wp_unslash( $_POST['meta'][ $key ] ) ) : $default;
				$post_meta_url  = fn( $key, $default = '' ) => isset( $_POST['meta'] ) && is_array( $_POST['meta'] ) && isset( $_POST['meta'][ $key ] )
					? esc_url_raw( wp_unslash( $_POST['meta'][ $key ] ) ) : $default;
				$post_meta_int  = fn( $key, $default = 0 ) => isset( $_POST['meta'] ) && is_array( $_POST['meta'] ) && isset( $_POST['meta'][ $key ] )
					? absint( $_POST['meta'][ $key ] ) : $default;
				$post_social    = fn( $key, $default = '' ) => isset( $_POST['meta'] ) && is_array( $_POST['meta'] )
					&& isset( $_POST['meta']['social'] ) && is_array( $_POST['meta']['social'] )
					&& isset( $_POST['meta']['social'][ $key ] )
					? esc_url_raw( wp_unslash( $_POST['meta']['social'][ $key ] ) ) : $default;
				$tax_id         = $post_int( 'id' );
				$name           = $post_val( 'name' );
				$slug           = $post_slug( 'slug' );
				$description    = $post_textarea( 'description' );
				$post_id        = $post_int( 'post_id' );
				$meta           = [
					'photo'       => $post_meta_int( 'photo' ),
					'designation' => $post_meta( 'designation' ),
					'company'     => $post_meta( 'company' ),
					'website'     => $post_meta_url( 'website' ),
					'social'      => [],
				];
				$social_keys    = [ 'twitter', 'linkedin', 'facebook', 'instagram' ];
				foreach ( $social_keys as $key ) {
					$meta['social'][ $key ] = $post_social( $key );
				}
				if ( empty( $name ) ) {
					ob_start();
					if ( empty( $post_id ) || $post_id <= 0 ) {
						$this->speaker_list();
					}
					$html = ob_get_clean();
					wp_send_json_error( [ 'html' => $html, 'type' => 'warn', 'msg' => ABPET_Function::speaker_label() . ' ' . __( 'Name cannot be blank!', 'abp-event-ticket' ) ], 400 );
				}
				if ( $tax_id > 0 ) {
					$result = wp_update_term( $tax_id, 'abpet_speaker', [
						'name'        => $name,
						'slug'        => $slug,
						'description' => $description,
					] );
				} else {
					$result = wp_insert_term( $name, 'abpet_speaker', [
						'slug'        => $slug,
						'description' => $description,
					] );
				}
				if ( is_wp_error( $result ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => $result->get_error_message() ] );
				}
				$term_id = absint( $result['term_id'] ?? 0 );
				if ( $term_id <= 0 ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Failed to resolve speaker context.', 'abp-event-ticket' ), 'type' => 'warn' ] );
				}
				update_term_meta( $term_id, '_abpet_speaker_meta', $meta );
				$this->update_speaker();
				ob_start();
				if ( empty( $post_id ) || $post_id <= 0 ) {
					$this->speaker_list();
				}
				$html = ob_get_clean();
				wp_send_json_success( [ 'html' => $html, 'type' => 'success', 'js' => ABPET_Function::option_js( $post_id, 'abpet_speaker' ), 'msg' => ABPET_Function::speaker_label() . ' ' . __( 'Saved Successfully !', 'abp-event-ticket' ), ] );
			}
			public function delete_tax_speaker(): void {
				if ( ! check_ajax_referer( 'abpet_admin_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid security token or Insufficient permissions.', 'abp-event-ticket' ), 'type' => 'warn' ], 403 );
				}
				$tax_id = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : '';
				if ( empty( $tax_id ) || ! is_numeric( $tax_id ) ) {
					ob_start();
					$this->speaker_list();
					$html = ob_get_clean();
					wp_send_json_error( [ 'html' => $html, 'msg' => ABPET_Function::speaker_label() . ' ' . __( 'id Invalid...!', 'abp-event-ticket' ), 'type' => 'warn' ] );
				}
				delete_term_meta( $tax_id, '_abpet_speaker_meta' );
				$result = wp_delete_term( $tax_id, 'abpet_speaker' );
				$this->update_speaker();
				ob_start();
				$this->speaker_list();
				$html = ob_get_clean();
				if ( is_wp_error( $result ) ) {
					wp_send_json_error( [ 'html' => $html, 'msg' => $result->get_error_message(), 'type' => 'warn' ] );
				}
				wp_send_json_success( [ 'html' => $html, 'type' => 'success', 'msg' => ABPET_Function::speaker_label() . ' ' . __( 'Deleted Successfully !', 'abp-event-ticket' ) ] );
			}
			public function update_speaker(): void {
				$taxonomies = ABPET_Function::get_taxonomy( 'abpet_speaker' );
				$speaker    = [];
				if ( ! empty( $taxonomies ) && is_array( $taxonomies ) && sizeof( $taxonomies ) > 0 ) {
					foreach ( $taxonomies as $taxonomy ) {
						$meta = get_term_meta( $taxonomy->term_id, '_abpet_speaker_meta', true );
						$meta = is_array( $meta ) ? $meta : [];
						$speaker[ $taxonomy->term_id ]['label']       = $taxonomy->name;
						$speaker[ $taxonomy->term_id ]['description'] = $taxonomy->description;
						$speaker[ $taxonomy->term_id ]['meta']        = $meta;
					}
				}
				ksort( $speaker );
				update_option( 'abpet_speaker', $speaker );
			}
			public function speaker_list(): void {
				$all_speaker = ABPET_Function::get_option( 'abpet_speaker' );
				if ( ! empty( $all_speaker ) && is_array( $all_speaker ) && sizeof( $all_speaker ) > 0 ) {
					$count = 1; ?>
					<table class="abp">
						<thead>
						<tr>
							<th><?php esc_html_e( 'SI', 'abp-event-ticket' ) ?></th>
							<th class="_min_150"><?php echo esc_html( ABPET_Function::speaker_label() ); ?></th>
							<th><?php esc_html_e( 'ID', 'abp-event-ticket' ) ?></th>
							<th class="_min_150"><?php esc_html_e( 'Designation', 'abp-event-ticket' ); ?></th>
							<th class="_min_150"><?php esc_html_e( 'Company', 'abp-event-ticket' ); ?></th>
							<th class="_w_100"><?php esc_html_e( 'Action', 'abp-event-ticket' ) ?></th>
						</tr>
						</thead>
						<tbody>
						<?php foreach ( $all_speaker as $id => $speaker ) {
							$meta = $speaker['meta'] ?? [];
							$photo_url = '';
							if ( ! empty( $meta['photo'] ) ) {
								$photo_url = wp_get_attachment_url( $meta['photo'] );
							} ?>
							<tr>
								<td><?php echo esc_html( $count++ ); ?></td>
								<td>
									<div class="_gap_xs">
										<?php if ( $photo_url ) { ?>
											<img src="<?php echo esc_url( $photo_url ); ?>" alt="" style="width:32px;height:32px;border-radius:50%;object-fit:cover;"/>
										<?php } ?>
										<strong><?php echo esc_html( $speaker['label'] ); ?></strong>
									</div>
								</td>
								<td><?php echo esc_html( $id ); ?></td>
								<td><?php echo esc_html( $meta['designation'] ?? '' ); ?></td>
								<td><?php echo esc_html( $meta['company'] ?? '' ); ?></td>
								<td>
									<div class="_group_content">
									<button type="button" class="_btn_light_yellow_xxs" onclick="abpet_popup_open_global('tax_speaker','<?php echo esc_attr( $id ); ?>')" title="<?php echo esc_attr__( 'Edit : ', 'abp-event-ticket' ) . ' ' . esc_attr( $speaker['label'] ); ?>"><?php ABPET_Static::icon_svg( 'edit' ); ?></button>
									<button type="button" class="_btn_light_danger_xxs" onclick="abpet_delete_global('tax_speaker','<?php echo esc_attr( $id ); ?>')" title="<?php echo esc_attr__( 'Trash : ', 'abp-event-ticket' ) . ' ' . esc_attr( $speaker['label'] ); ?>"><?php ABPET_Static::icon_svg( 'close_1' ); ?></button>
									</div>
								</td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
					<?php
				} else {
					ABPET_Layout::layout_warning_info( 'no_speaker' );
				}
			}
		}
		new ABPET_Speaker();
	}