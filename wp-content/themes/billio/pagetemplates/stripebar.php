<?php
defined('ABSPATH') or die();

/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme and one
 * of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query,
 * e.g., it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Billio
 * @since Billio 1.0
 */

global $detheme_config;

if ($detheme_config['dt_stripebar_on']) :
?>
	<script src="<?php echo get_template_directory_uri(); ?>/lib/woahbar/woahbar.js" type="text/javascript"></script>
    <div class="woahbar" id="woahbar">
		<p class="woahbar_message">
			<?php echo stripslashes($detheme_config['dt_stripebar_msg']); ?> 
		</p>
	    <a class="close-notify" onclick="woahbar_hide();"><i class="icon-up-open"></i></a>
	    <input id="dt_stripebar_delay" type="hidden" value="<?php echo $detheme_config['dt_stripebar_delay']; ?>" />
	</div>
	<div class="woahbar-stub">
	    <a class="show-notify" onclick="woahbar_show();"><i class="icon-down-open"></i></a>
	</div>

<?php endif; //if ($detheme_config['dt_stripebar_on']) ?>