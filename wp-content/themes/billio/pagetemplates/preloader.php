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

if($detheme_config['page_loader']):?>
<div class="modal_preloader">
	<div align="center" class="fond">
	  <div class="contener_general">
	      <div class="contener_mixte"><div class="ballcolor ball_1">&nbsp;</div></div>
	      <div class="contener_mixte"><div class="ballcolor ball_2">&nbsp;</div></div>
	      <div class="contener_mixte"><div class="ballcolor ball_3">&nbsp;</div></div>
	      <div class="contener_mixte"><div class="ballcolor ball_4">&nbsp;</div></div>
	  </div>
	</div>
</div>	
<?php endif;?>
