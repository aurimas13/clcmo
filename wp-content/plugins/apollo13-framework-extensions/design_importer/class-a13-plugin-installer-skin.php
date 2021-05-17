<?php
/*
 * Used in current demo importer to fight off some buffer issues
 *
 * */
class A13FRAMEWORK_Plugin_Installer_Skin extends Plugin_Installer_Skin {
	/**
	 * @param string $string
	 * @param mixed  ...$args Optional text replacements.
	 */
	public function feedback($string, ...$args ) {
		if ( isset( $this->upgrader->strings[$string] ) )
			$string = $this->upgrader->strings[$string];

		if ( strpos($string, '%') !== false ) {
			$args = func_get_args();
			$args = array_splice($args, 1);
			if ( $args ) {
				$args = array_map( 'strip_tags', $args );
				$args = array_map( 'esc_html', $args );
				$string = vsprintf($string, $args);
			}
		}
		if ( empty($string) )
			return;
		echo "<br />\r\n".esc_html( $string )."<br />\r\n";
//		show_message($string); //it calls wp_ob_end_flush_all() which breaks importer
	}
}