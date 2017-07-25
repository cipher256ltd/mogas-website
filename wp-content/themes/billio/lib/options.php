<?php
defined('ABSPATH') or die();
if(!defined('DETHEME_INSTALLED')) define("DETHEME_INSTALLED",true);
if ( !class_exists( 'DethemeReduxFramework' ) && file_exists( get_template_directory(). '/redux-framework/ReduxCore/framework.php' ) ) {

	load_template(get_template_directory().'/redux-framework/ReduxCore/framework.php',true);

}
if ( !isset( $detheme_config ) && file_exists( get_template_directory() . '/redux-framework/option/config.php' ) ) {

	load_template(get_template_directory().'/redux-framework/option/config.php',true);
}
?>