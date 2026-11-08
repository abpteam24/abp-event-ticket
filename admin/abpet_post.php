<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPET_Post' ) ) {
		class ABPET_Post {
			public function __construct() {
				add_action( 'abpet_load_posts', array( $this, 'load_posts' ) );
				add_action( 'admin_notices', array( $this, 'configuration_notice' ) );
				add_action( 'wp_ajax_abpet_post_permanent_remove', array( $this, 'post_permanent_remove' ) );
				add_action( 'wp_ajax_abpet_post_move_trash', array( $this, 'post_move_trash' ) );
				add_action( 'wp_ajax_abpet_post_restore', array( $this, 'post_restore' ) );
				add_action( 'wp_ajax_abpet_reload_post_list', array( $this, 'reload_post_list' ) );
			}
			public function load_posts( $abpet_info = [] ): void {
				$total_posts   = $abpet_info['total_post'] ?? 0;
				$total_publish = $abpet_info['total_publish'] ?? 0;
				$total_draft   = $abpet_info['total_draft'] ?? 0;
				$total_private = $abpet_info['total_private'] ?? 0;
				$total_trash   = $abpet_info['total_trash'] ?? 0;
				$status        = 'publish';
				if ( isset( $_GET['_abpet_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_abpet_nonce'] ) ), 'abpet_url_action' ) ) {
					$status = isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : 'publish';
				}
				$status                = $status ?? 'publish';
				$filter_args['status'] = $status;
				?>
                <div class="abpet_posts _section_card">
                    <div class="_fj_between_f_wrap">
                        <div class="_group_content">
                            <input type="hidden" name="select_hidden_post_status" value="<?php echo esc_attr( $status ); ?>"/>
                            <button type="button" class="_btn_light_active_xs <?php echo esc_attr( $status == 'all' ? 'abp_active' : '' ); ?>" data-href="<?php echo esc_url( ABPET_Function::build_url( 'posts', [ 'status' => 'all' ] ) ); ?>"><?php esc_html_e( 'All', 'abp-event-ticket' ); ?> ( <?php echo esc_html( $total_posts ); ?> )</button>
                            <button type="button" class="_btn_light_active_xs <?php echo esc_attr( $status == 'publish' ? 'abp_active' : '' ); ?>" data-href="<?php echo esc_url( ABPET_Function::build_url( 'posts', [ 'status' => 'publish' ] ) ); ?>"><?php esc_html_e( 'Published', 'abp-event-ticket' ); ?> ( <?php echo esc_html( $total_publish ); ?> )</button>
                            <button type="button" class="_btn_light_active_xs <?php echo esc_attr( $status == 'private' ? 'abp_active' : '' ); ?>" data-href="<?php echo esc_url( ABPET_Function::build_url( 'posts', [ 'status' => 'private' ] ) ); ?>"><?php esc_html_e( 'Private', 'abp-event-ticket' ); ?> ( <?php echo esc_html( $total_private ); ?> )</button>
                            <button type="button" class="_btn_light_active_xs <?php echo esc_attr( $status == 'draft' ? 'abp_active' : '' ); ?>" data-href="<?php echo esc_url( ABPET_Function::build_url( 'posts', [ 'status' => 'draft' ] ) ); ?>"><?php esc_html_e( 'Draft', 'abp-event-ticket' ); ?> ( <?php echo esc_html( $total_draft ); ?> )</button>
                            <button type="button" class="_btn_light_active_xs <?php echo esc_attr( $status == 'trash' ? 'abp_active' : '' ); ?>" data-href="<?php echo esc_url( ABPET_Function::build_url( 'posts', [ 'status' => 'trash' ] ) ); ?>"><?php esc_html_e( 'Trash', 'abp-event-ticket' ); ?> ( <?php echo esc_html( $total_trash ); ?> )</button>
                        </div>
                        <a class="_btn_navy_blue_xs" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=' . ABPET_Function::get_cpt() ) ); ?>">
							<?php ABPET_Static::svg( 'plus' );
								esc_html_e( 'Add New Event', 'abp-event-ticket' ); ?>
                        </a>
                    </div>
                    <div class="_divider_xs"></div>
                    <div class=" post_list">
						<?php $this->post_table( $filter_args ); ?>
                    </div>
                </div>
				<?php
			}
			public function post_table( $filter_args ): void {
				//echo '<pre>';print_r($filter_args);echo '</pre>';
				$status = $filter_args['status'] ?? '';
				if ( empty( $status ) || $status == 'all' ) {
					$status = [ 'publish', 'draft', 'private', 'trash' ];
				}
				$page_number               = absint( $filter_args['page_number'] ?? 1 ) ?: 1;
				$limit                     = absint( ( $filter_args['page_item'] ?? 0 ) ?: ABPET_Function::get_option( 'abpet_per_page_item', 20 ) );
				$count                     = ( $page_number - 1 ) * $limit + 1;
				$cpt                       = ABPET_Function::get_cpt();
				$filters['status']         = $status;
				$filters['posts_per_page'] = $limit;
				$filters['paged']          = $page_number;
				//echo '<pre>';print_r($filters);echo '</pre>';
				$post_ids = ABPET_Query::get_post_id( $filters );
				if ( ! empty( $post_ids ) && sizeof( $post_ids ) > 0 ) {
					$total_post   = sizeof( ABPET_Query::get_post_id( [ 'status' => $status ] ) );
					$new_post_url = admin_url( 'post-new.php?post_type=' . $cpt );
					?>
                    <table class="abp">
                        <thead>
                        <tr>
                            <th class="_w_50"><?php esc_html_e( 'SI', 'abp-event-ticket' ); ?></th>
                            <th class="_w_100"><?php esc_html_e( 'Image', 'abp-event-ticket' ); ?></th>
                            <th><?php echo esc_html( ABPET_Function::label() ); ?></th>
                            <th><?php esc_html_e( 'Ticket Type & Qty', 'abp-event-ticket' ); ?></th>
                            <th><?php esc_html_e( 'Shortcode', 'abp-event-ticket' ); ?></th>
                            <th><?php esc_html_e( 'Actions', 'abp-event-ticket' ); ?></th>
                        </tr>
                        </thead>
                        <tbody>
						<?php
							foreach ( $post_ids as $post_id ) {
								$post_infos    = ABPET_Function::get_all_meta( $post_id );
								$title         = $post_infos['post_title'] ?? '';
								$seat_type     = $post_infos['seat_type'] ?? 'ticket';
								$seat_type     = ABPET_Function::on_off( 'sp' ) ? $seat_type : 'ticket';
								$edit_link     = get_edit_post_link( $post_id );
								$sale_continue = $post_infos['sale_continue'] ?? 'on';
								$post_status   = get_post_status( $post_id );
								$new_post_url  = add_query_arg( array( 'copy_post' => $post_id, '_abpet_nonce' => wp_create_nonce( 'abpet_copy_post_action' ), ), $new_post_url );
								?>
                                <tr>
                                    <th><?php echo esc_html( $count ); ?>.</th>
                                    <td><?php ABPET_Layout::image( $post_id ); ?></td>
                                    <td>
                                        <div class="_mar_b_xxs">
											<?php if ( $post_status == 'trash' ) { ?>
                                                <h6 class="abp_color_warning"><?php ABPET_Layout::title( $post_infos ); ?></h6>
											<?php } else { ?>
                                                <a href="<?php echo esc_url( $edit_link ); ?>" class="abp_fs_h6_color_theme"><?php ABPET_Layout::title( $post_infos ); ?></a>
											<?php } ?>
                                        </div>
                                        <div class="_gap_xxs">
                                            <span class="abp_tag"><?php echo esc_html( __( 'ID : ', 'abp-event-ticket' ) . ' ' . $post_id ); ?></span>
                                            <span class=" abp_tag <?php echo esc_attr( $sale_continue == 'on' ? 'publish' : 'trash' ); ?>">
                                                <?php echo esc_html( $sale_continue == 'on' ? __( 'Sale On', 'abp-event-ticket' ) : __( 'Sale Off', 'abp-event-ticket' ) ); ?>
                                            </span>
                                            <span class="abp_tag <?php echo esc_attr( $post_status ); ?>"><?php echo esc_html( $post_status ); ?></span>
                                        </div>
                                    </td>
                                    <th><?php echo esc_html( ABPET_Layout::ticket_type( $seat_type ) . ' - ' . ABPET_Function::get_total_qty( $post_id, $post_infos ) ); ?></th>
                                    <th><code> [abpet-post post_id="<?php echo esc_attr( $post_id ); ?>"]</code></th>
                                    <th>
                                        <div class="_group_content">
                                            <button type="button" class="_btn_light_navy_blue_xxs" data-href="<?php echo esc_url( $new_post_url ); ?>" data-blank="_blank" title="<?php echo esc_html__( 'Copy/Clone : ', 'abp-event-ticket' ) . ' ' . esc_html( $title ); ?>"><?php ABPET_Static::svg( 'clone_1' ); ?></button>
											<?php if ( $post_status == 'trash' ) { ?>
                                                <button type="button" class="_btn_light_success_xxs " onclick="abpet_post_action('restore','<?php echo esc_attr( $post_id ); ?>')" title="<?php echo esc_html__( 'Restore : ', 'abp-event-ticket' ) . ' ' . esc_html( $title ); ?>">♻️</button>
                                                <button type="button" class="_btn_light_danger_xxs" onclick="abpet_post_action('permanent_remove','<?php echo esc_attr( $post_id ); ?>')" title="<?php echo esc_html__( 'Permanent Remove : ', 'abp-event-ticket' ) . ' ' . esc_html( $title ); ?>"><?php ABPET_Static::svg( 'close_2' ); ?></button>
											<?php } else { ?>
                                                <button type="button" class="_btn_light_yellow_xxs" data-href="<?php echo esc_url( $edit_link ); ?>" data-blank="_blank" title="<?php echo esc_html__( 'Edit : ', 'abp-event-ticket' ) . ' ' . esc_html( $title ); ?>"><?php ABPET_Static::svg( 'edit' ); ?></button>
                                                <button type="button" class="_btn_light_theme_xxs" data-href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" data-blank="_blank" title="<?php echo esc_html__( 'View : ', 'abp-event-ticket' ) . ' ' . esc_html( $title ); ?>"><?php ABPET_Static::svg( 'view_1' ); ?></button>
                                                <button type="button" class="_btn_light_danger_xxs" onclick="abpet_post_action('move_trash','<?php echo esc_attr( $post_id ); ?>')" title="<?php echo esc_html__( 'Move to Trash : ', 'abp-event-ticket' ) . ' ' . esc_html( $title ); ?>"><?php ABPET_Static::svg( 'close_1' ); ?></button>
											<?php } ?>
                                        </div>
                                    </th>
                                </tr>
								<?php
								$count ++;
							}
						?>
                        </tbody>
                    </table>
                    <div class="_divider_xs"></div>
					<?php
					do_action( 'abpet_pagination', [ 'page_item' => $limit, 'page_number' => $page_number, 'total' => $total_post, 'style' => 'ajax' ] );
				} else {
					ABPET_Layout::layout_warning_info( 'not_found' );
				}
			}
			public function configuration_notice(): void {
				$errors = get_transient( 'abpet_event_configuration_errors_' . get_current_user_id() );
				if ( empty( $errors ) || ! is_array( $errors ) ) {
					return;
				}
				delete_transient( 'abpet_event_configuration_errors_' . get_current_user_id() );
				echo '<div class="notice notice-error"><p><strong>' . esc_html__( 'Event was saved as draft because its configuration is incomplete:', 'abp-event-ticket' ) . '</strong></p><ul>';
				foreach ( $errors as $error ) {
					echo '<li>' . esc_html( $error ) . '</li>';
				}
				echo '</ul></div>';
			}
			public function post_permanent_remove(): void {
				if ( ! check_ajax_referer( 'abpet_admin_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid security token or Insufficient permissions.', 'abp-event-ticket' ), 'type' => 'warn' ], 403 );
				}
				$post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
				if ( $post_id <= 0 ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid ID ..... !! ', 'abp-event-ticket' ), 'type' => 'warn' ], 400 );
				}
				$title      = get_the_title( $post_id );
				$link_wc_id = absint( ABPET_Function::get_post_info( $post_id, 'link_wc_id' ) );
				if ( $link_wc_id > 0 ) {
					wp_delete_post( $link_wc_id, true );
				}
				wp_delete_post( $post_id, true );
				wp_send_json_success( [ 'html' => '', 'msg' => $title . ' : ' . __( 'Permanently removed. ..... !! ', 'abp-event-ticket' ), 'type' => 'error' ] );
			}
			public function post_move_trash(): void {
				if ( ! check_ajax_referer( 'abpet_admin_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid security token or Insufficient permissions.', 'abp-event-ticket' ), 'type' => 'warn' ], 403 );
				}
				$post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
				if ( $post_id > 0 ) {
					$title      = get_the_title( $post_id );
					$link_wc_id = absint( ABPET_Function::get_post_info( $post_id, 'link_wc_id' ) );
					if ( $link_wc_id > 0 ) {
						wp_trash_post( $link_wc_id );
					}
					wp_trash_post( $post_id );
					wp_send_json_success( [ 'html' => '', 'msg' => $title . ' : ' . __( 'Moved to trash successfully...... !! ', 'abp-event-ticket' ), 'type' => 'warn' ] );
				}
				wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid  ID ..... !! ', 'abp-event-ticket' ), 'type' => 'warn' ], 400 );
			}
			public function post_restore(): void {
				if ( ! check_ajax_referer( 'abpet_admin_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid security token or Insufficient permissions.', 'abp-event-ticket' ), 'type' => 'warn' ], 403 );
				}
				$post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
				if ( $post_id > 0 ) {
					$link_wc_id = absint( ABPET_Function::get_post_info( $post_id, 'link_wc_id' ) );
					if ( $link_wc_id > 0 ) {
						wp_untrash_post( $link_wc_id );
					}
					wp_untrash_post( $post_id );
					$updated_post = [
						'ID'          => $post_id,
						'post_status' => 'publish',
					];
					wp_update_post( $updated_post );
					$title = get_the_title( $post_id );
					wp_send_json_success( [ 'html' => '', 'msg' => $title . ' : ' . __( 'Restored successfully...... !! ', 'abp-event-ticket' ), 'type' => 'success' ] );
				}
				wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid  ID ..... !! ', 'abp-event-ticket' ), 'type' => 'warn' ], 400 );
			}
			public function reload_post_list(): void {
				if ( ! check_ajax_referer( 'abpet_admin_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid security token or Insufficient permissions.', 'abp-event-ticket' ), 'type' => 'warn' ], 403 );
				}
				$post_array  = fn( $key ) => ( isset( $_POST[ $key ] ) && is_array( $_POST[ $key ] ) ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST[ $key ] ) ) : [];
				$filter_args = $post_array( 'filter_args' );
				ob_start();
				$this->post_table( $filter_args );
				$table_html = ob_get_clean();
				wp_send_json_success( [
					'html' => $table_html,
					'type' => 'success',
					'msg'  => __( 'Post List Loaded successfully...... !! ', 'abp-event-ticket' )
				] );
			}
		}
		new ABPET_Post();
	}