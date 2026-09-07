<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	if (!class_exists('ABPET_Frontend')) {
		class ABPET_Frontend {
			public function __construct() {
				add_filter('single_template', [$this, 'load_single_page']);
				add_filter('template_include', array($this, 'load_taxonomy_page'));
			}
			public function load_single_page($template): string {
				if (is_singular(ABPET_Function::get_cpt())) {
					nocache_headers();
					$custom_template = ABPET_Function::template_path('page/details_page.php');
					if (!empty($custom_template)) {
						return $custom_template;
					}
				}
				return (string)$template;
			}
			public function load_taxonomy_page($template): string {
				if (in_array(get_query_var('taxonomy'), ['abpet_category', 'abpet_location', 'abpet_brand', 'abpet_organizer'], true)) {
					nocache_headers();
				}
				if (is_tax('abpet_category')) {
					return ABPET_Function::template_path('page/category.php');
				}
				if (is_tax('abpet_location')) {
					return ABPET_Function::template_path('page/location.php');
				}
				if (is_tax('abpet_brand')) {
					return ABPET_Function::template_path('page/brand.php');
				}
				if (is_tax('abpet_organizer')) {
					return ABPET_Function::template_path('page/organizer.php');
				}
				return (string)$template;
			}
		}
		new ABPET_Frontend();
	}