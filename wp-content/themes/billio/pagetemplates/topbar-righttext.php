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

$text=function_exists('icl_t') ? icl_t('billio', 'right-top-bar-text', $detheme_config['dt-right-top-bar-text']):$detheme_config['dt-right-top-bar-text'];
?>
<div class="right-menu"><div class="topbar-text"><?php print (!empty($text))? do_shortcode($text):"";?></div></div>