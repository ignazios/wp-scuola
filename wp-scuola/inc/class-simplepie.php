<?php
if ( ! class_exists( 'SimplePie', false ) ) :

	if (version_compare(get_bloginfo('version'), '6.7', '>=')) {
		return;
	} else {
		require 'class-simplepiepre_67.php';
	}
endif;