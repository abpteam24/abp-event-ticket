<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
	add_action( 'abpet_post_filter_template', function ( $params ) {
		if ( ! is_array( $params ) ) {
			return;
		}
		$style    = $params['style'] ?? 'grid';
		$post_ids = $params['all_post'] ?? [];

		/**
		 * Taxonomy filter definitions: meta key, shortcode attribute that pins a
		 * single term, global ON/OFF key and the term label map.
		 */
		$taxonomies = [
			'category'  => [
				'meta'     => 'abpet_category',
				'request'  => 'cat_id',
				'constant' => defined( 'ABPET_Category' ) ? ABPET_Category : [],
				'label'    => ABPET_Function::category_label(),
			],
			'location'  => [
				'meta'     => 'abpet_location',
				'request'  => 'loc_id',
				'constant' => defined( 'ABPET_Location' ) ? ABPET_Location : [],
				'label'    => ABPET_Function::location_label(),
			],
			'organizer' => [
				'meta'     => 'abpet_organizer',
				'request'  => 'organizer_id',
				'constant' => defined( 'ABPET_Organizer' ) ? ABPET_Organizer : [],
				'label'    => ABPET_Function::organizer_label(),
			],
			'brand'     => [
				'meta'     => 'abpet_brand',
				'request'  => 'brand_id',
				'constant' => defined( 'ABPET_Brand' ) ? ABPET_Brand : [],
				'label'    => ABPET_Function::brand_label(),
			],
			'speaker'   => [
				'meta'     => 'speaker',
				'request'  => 'speaker_id',
				'constant' => defined( 'ABPET_Speaker' ) ? ABPET_Speaker : [],
				'label'    => ABPET_Function::speaker_label(),
			],
		];

		// The shortcode `filter` attribute decides which dropdowns may render;
		// the global Feature ON/OFF switches can always remove one.
		$filter_keys = $params['filter_taxonomies'] ?? array_keys( $taxonomies );
		$filter_keys = is_array( $filter_keys ) ? array_map( 'sanitize_key', $filter_keys ) : [];

		$lists = [];
		foreach ( $taxonomies as $key => $data ) {
			if ( ! in_array( $key, $filter_keys, true ) || ! ABPET_Function::on_off( $key ) ) {
				continue;
			}
			// A shortcode that already pins this taxonomy to one term needs no dropdown.
			if ( ! empty( $params[ $data['request'] ] ) ) {
				continue;
			}
			$term_ids = [];
			if ( is_array( $post_ids ) && ! empty( $post_ids ) ) {
				foreach ( $post_ids as $post_id ) {
					$value = ABPET_Function::get_post_info( $post_id, $data['meta'] );
					if ( empty( $value ) ) {
						continue;
					}
					foreach ( explode( ',', (string) $value ) as $term_id ) {
						$term_id = trim( $term_id );
						if ( '' !== $term_id ) {
							$term_ids[] = $term_id;
						}
					}
				}
			}
			$lists[ $key ] = array_values( array_unique( $term_ids ) );
		}

		$has_select = false;
		foreach ( $lists as $term_ids ) {
			if ( count( $term_ids ) > 1 ) {
				$has_select = true;
				break;
			}
		}
		$show_toggle = $style === 'grid' || $style === 'list';

		// Nothing to filter and no view switch means there is no bar to render.
		if ( ! $has_select && ! $show_toggle ) {
			return;
		}
		?>
        <div class="post_top_filter">
			<?php
				foreach ( $taxonomies as $key => $data ) {
					$term_ids = $lists[ $key ] ?? [];
					if ( count( $term_ids ) <= 1 || ! is_array( $data['constant'] ) || empty( $data['constant'] ) ) {
						continue;
					} ?>
                    <label>
                        <select class="_form_control abpet_tax_filter" name="<?php echo esc_attr( $data['request'] ); ?>" aria-label="<?php echo esc_attr( $data['label'] ); ?>">
                            <option value="" selected><?php
								/* translators: %s: taxonomy label, for example "Category". */
								echo esc_html( sprintf( __( 'All %s', 'abp-event-ticket' ), $data['label'] ) );
							?></option>
							<?php foreach ( $term_ids as $current_term_id ) {
								$name = $data['constant'][ $current_term_id ]['label'] ?? '';
								if ( '' === $name ) {
									continue;
								} ?>
                                <option value="<?php echo esc_attr( $current_term_id ); ?>"><?php echo esc_html( $name ); ?></option>
							<?php } ?>
                        </select>
                    </label>
					<?php
				}
				if ( $show_toggle ) { ?>
                    <div class="_group_content">
                        <button type="button" class="_btn_light_info_xs_fs_h6 grid_view <?php echo esc_attr( $style === 'grid' ? 'abp_active' : '' ); ?>"><span class="fas fa-table-cells"></span></button>
                        <button type="button" class="_btn_light_info_xs_fs_h6 list_view <?php echo esc_attr( $style === 'list' ? 'abp_active' : '' ); ?>"><span class="fas fa-list"></span></button>
                    </div>
				<?php } ?>
        </div>
		<?php
	} );
