<?php
require dirname( __DIR__, 3 ) . '/wp-load.php';
$q = new WP_Query(
	array(
		'post_type'      => 'abpet_post',
		'post_status'    => 'publish',
		'posts_per_page' => 30,
	)
);
foreach ( $q->posts as $p ) {
	$t = get_post_meta( $p->ID, 'abpet_template', true );
	echo $p->ID . ' | ' . $t . ' | ' . get_permalink( $p->ID ) . ' | ' . $p->post_title . PHP_EOL;
}
echo 'home=' . home_url() . PHP_EOL;
echo 'site=' . get_option( 'siteurl' ) . PHP_EOL;
