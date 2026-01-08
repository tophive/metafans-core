<?php
MetafansCoreCustomizer()->register_module(
	'MetafansCoreCustomizer_Module_Advanced_Styling',
	array()
);

class MetafansCoreCustomizer_Module_Advanced_Styling extends MetafansCoreCustomizer_Module_Base {

	function __construct() {
		require_once dirname( __FILE__ ) . '/inc/page-header.php';
		require_once dirname( __FILE__ ) . '/inc/background.php';
		require_once dirname( __FILE__ ) . '/inc/footer-row.php';
	}
}
