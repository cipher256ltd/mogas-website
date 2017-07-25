<?php
defined('ABSPATH') or die();

/**
 * @package WordPress
 * @subpackage Billio
 */

$type=esc_attr(trim($_GET['type'] ));
$errors = array();
if ( !empty($_POST) ) {
	$return = billio_configuration_form_handler($type);
	if ( is_string($return) )
		return $return;
	if ( is_array($return) )
		$errors = $return;
}

return billio_popup_configuration_form($type,$errors);

function billio_configuration_form_handler($type){

	switch($type){
		case 'button':
		$text=esc_attr($_POST['text']);
		$style=esc_attr($_POST['style']);
		$url=esc_url($_POST['url']);
		$size=esc_attr($_POST['size']);
		$target=esc_attr($_POST['target']);
		$skin=esc_attr($_POST['skin']);

		if(!empty($text)){
			
			$errors=billio_popup_send_to_editor(array('type'=>$type,'style'=>$style,'text'=>$text,'url'=>$url,'size'=>$size,'target'=>$target,'skin'=>$skin));
		}
		else{
			
			$errors=array('errors'=>array('style'=>$style,'text'=>$text,'url'=>$url,'size'=>$size,'target'=>$target,'skin'=>$skin));
		}
		break;	
		case 'icon':
		$icon=esc_attr($_POST['icon']);
		$size=esc_attr($_POST['size']);
		$color=esc_attr($_POST['color']);
		$style=esc_attr($_POST['style']);

		if(count($icon)){
			
			$errors=billio_popup_send_to_editor(array('type'=>$type,'icon'=>$icon,'size'=>$size,$icon,'color'=>$color,$icon,'style'=>$style));
		}
		else{
			
			$errors=array('errors'=>array('icon'=>$icon,'size'=>$size,$icon,'color'=>$color,$icon,'style'=>$style));
		}
		break;
		case 'counto':
		$number=intval($_POST['number']);
		$sepcolor=esc_attr($_POST['sepcolor']);

		if(!empty($number)){
			$errors=billio_popup_send_to_editor(array('type'=>$type,'number'=>$number,'sepcolor'=>$sepcolor));
		}
		else{
			
			$errors=array('errors'=>array('number'=>$number,'sepcolor'=>$sepcolor));
		}
		break;
		default:
		break;
	}
	return $errors;
}

function billio_ajax_load_script(){

	wp_dequeue_script('installer-admin');

	wp_enqueue_style( 'popup-style',get_template_directory_uri() . '/lib/css/popup.css', array(), '', 'all' );
	wp_enqueue_script( 'icon-picker',get_template_directory_uri() . '/lib/js/icon_picker.js', array('jquery'));
	wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/css/bootstrap.css', array(), '3.0' );	
	wp_enqueue_style( 'icon_picker-font',get_template_directory_uri() . '/lib/css/fontello.css', array(), '', 'all' );
	wp_enqueue_style( 'wp-color-picker' );
   	wp_localize_script( 'icon-picker', 'picker_i18nLocale', array(
      'search_icon'=>__('Search Icon','billio'),
    ) );
}



function billio_popup_configuration_form($type,$errors=array()){

	add_action( 'wp_enqueue_scripts','billio_ajax_load_script');

	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php wp_head();?>
	</head>
	<body>
		<div id="jayd-popup">
			<div id="jayd-shortcode-wrap">
				<div id="jayd-sc-form-wrap">
					<div id="jayd-sc-form-head"><h2>DT <?php print ucwords($type);?></h2></div>
					<form method="post" action="" id="jayd-sc-form">
						<table cellspacing="2" cellpadding="5" id="jayd-sc-form-table" class="form-table">
							<tbody>
								<?php 
								if($type=='button'){

									$styles=array(
										'color-primary'=>__('Primary','billio'),
										'color-secondary' => __('Secondary','billio'),
										'success' => __('Success','billio'),
										'info' => __('Info','billio'),
										'warning' => __('Warning','billio'),
										'danger'=>__('Danger','billio'),
										'ghost'=>__('Ghost Button','billio'),
										'link'=>__('Link','billio'),
										);

									$sizes = array(
										'btn-lg' => __('Large','billio'),
										'btn-default' => __('Default','billio'),
										'btn-sm' => __('Small','billio'),
										'btn-xs' => __('Extra small','billio')
										);

									$errors=wp_parse_args($errors,array('size'=>'','url'=>'','style'=>'','target'=>'','text'=>'','skin'=>'dark'));

									?>
									<tr>
										<td><label><?php _e('Button URL','billio');?></label></td>
										<td><input class="form-control" type="text" maxlength="100" name="url" id="url" class="jayd-form-input" value="<?php print $errors['url'];?>" /> 
											<span class="child-clone-row-desc"><?php _e('Add the button\'s url eg http://example.com','billio');?></span>
										</td>
									</tr>
									<tr>
										<td><label><?php _e("Button skin", 'billio');?></label></td>
										<td>
										<select class="jayd-form-select form-control" name="skin" id="skin">
										<option value="dark" <?php echo(($errors['skin']=='dark')?" selected=\"selected\"":"");?>><?php _e('Dark (default)','billio');?></option> 
										<option value="light" <?php echo(($errors['skin']=='light')?" selected=\"selected\"":"");?>><?php _e('Light','billio');?></option> 
										</select>
										<span class="child-clone-row-desc"><?php _e('Select the button\'s skin','billio');?></span> 
										</td>
									</tr>
									<tr>
										<td><label><?php _e("Button style", 'billio');?></label></td>
										<td>
										<select class="jayd-form-select form-control" name="style" id="style">
											<?php 	
											if($styles){

												foreach ( $styles as $style=>$label ){

													echo "<option value=\"".$style."\" ".(($style==$errors['style'])?" selected=\"selected\"":"").">".$label."</option>"; 
												}
											}
											?>

										</select> 
										<span class="child-clone-row-desc"><?php _e('Select the button\'s style, ie the button\'s colour','billio');?></span>
										</td>
									</tr>
								<tr>
									<td><label><?php _e("Button size", 'billio');?></label></td>
									<td><select class="jayd-form-select form-control" name="size" id="size">
										<?php 	
										if($sizes){

											foreach ( $sizes as $size=>$label ){

												echo "<option value=\"".$size."\" ".(($size==$errors['size'])?" selected=\"selected\"":"").">".$label."</option>"; 
											}
										}
										?>

									</select> <span class="child-clone-row-desc">Select the icon's size</span></td>
								</tr>
								<tr>
									<td><label><?php _e('Button Target','billio');?></label></td>
									<td><select class="jayd-form-select form-control" name="target" id="t	arget">
										<option value="_self" <?php echo(($errors['target']=='_self')?" selected=\"selected\"":"");?>><?php _e('Self','billio');?></option> 
										<option value="_blank" <?php echo(($errors['target']=='_blank')?" selected=\"selected\"":"");?>><?php _e('Blank','billio');?></option> 
									</select> <span class="child-clone-row-desc"><?php _e('Select the button\'s target','billio');?></span></td>
								</tr>
								<tr>
									<td><label><?php _e('Button Text','billio');?></label></td>
									<td><input class="form-control" type="text" name="text" maxlength="100" id="text" class="jayd-form-input" value="<?php print $errors['text'];?>"/> 
										<span class="child-clone-row-desc"><?php _e('Select the button\'s text','billio');?></span></td>
									</tr>

									<?php }
									elseif($type=='counto'){

								
									$errors=wp_parse_args($errors,array('number'=>'100','sepcolor'=>''));
								?>
								<tr>
									<td><label><?php _e('Number','billio');?></label></td>
									<td><input class="form-control" type="text" name="number" maxlength="100" id="number" class="jayd-form-input" value="<?php print $errors['number'];?>"/> 
										<span class="child-clone-row-desc"><?php _e('The value must be numeric','billio');?></span></td>
								</tr>
								<tr>
									<td><label><?php _e('Separator Color','billio');?></label></td>
									<td><input class="form-control wp-color-picker" type="text" name="sepcolor" id="sepcolor" class="jayd-form-input" value="<?php print $errors['sepcolor'];?>"/> 
								</tr>

									<?php }
									elseif($type=='icon'){

										$icons=(function_exists('detheme_font_list'))?detheme_font_list():apply_filters('detheme_font_list',array());

										$sizes = array(
											'' => __('Default','billio'),
											'size-sm' => __('Small','billio'),
											'size-md' => __('Medium','billio'),
											'size-lg' => __('Large','billio')
											);

										$errors=wp_parse_args($errors,array('size'=>'','color'=>'','style'=>'square'));

										?>
										<tr>
											<td><label><?php _e('Icon Size','billio');?></label></td>
											<td><select class="form-control jayd-form-select" name="size" id="size">
												<?php 

												if($sizes){

													foreach ( $sizes as $size=>$label ){

														echo "<option value=\"".$size."\" ".(($size==$errors['size'])?" selected=\"selected\"":"").">".$label."</option>"; 
													}
												}
												?>

											</select> <span class="child-clone-row-desc"><?php _e("Select the button's size",'billio');?></span></td>
										</tr>
										<tr>
											<td><label><?php _e('Icon Color','billio');?></label></td>
											<td><select class="jayd-form-select form-control" name="color" id="color">
												<option value="" <?php echo(($errors['color']=='')?" selected=\"selected\"":"");?>><?php _e('Default','billio');?></option> 
												<option value="primary" <?php echo(($errors['color']=='primary')?" selected=\"selected\"":"");?>><?php _e('Primary','billio');?></option> 
												<option value="secondary" <?php echo(($errors['color']=='secondary')?" selected=\"selected\"":"");?>><?php _e('Secondary','billio');?></option> 
											</select> <span class="child-clone-row-desc"><?php _e('Select the button\'s color','billio');?></span></td>
										</tr>
										<tr>
											<td><label><?php _e('Icon Style','billio');?></label></td>
											<td><select class="jayd-form-select form-control" name="style" id="style">
												<option value=""><?php _e('None','billio');?></option> 
												<option value="circle" <?php echo(($errors['style']=='circle')?" selected=\"selected\"":"");?>><?php _e('Circle','billio');?></option> 
												<option value="square" <?php echo(($errors['style']=='square')?" selected=\"selected\"":"");?>><?php _e('Square','billio');?></option> 
												<option value="ghost" <?php echo(($errors['style']=='ghost')?" selected=\"selected\"":"");?>><?php _e('Ghost','billio');?></option> 
												</select> <span class="child-clone-row-desc"><?php _e('Select the button\'s style','billio');?></span></td>
											</tr>

											<tr>
											<td><label><?php _e('Icon','billio');?></label></td>
											<td><?php if(count($icons)):?>
													<script type="text/javascript">
													jQuery(document).ready(function($){

														var options={
															icons:new Array('<?php print @implode("','",$icons);?>')
														};

														$(".icon-picker").iconPicker(options);
													});

													</script>
													<input type="text" class="icon-picker" id="icon" name="icon" value="" />
												<?php endif;?>
											</td>
										</tr>
										<?php }
										?>
									</tbody>
								</table>
								<br/>
								<center>
									<input type="submit" id="form-insert" class="btn btn-default content_jayd_button" value="<?php _e('Insert Shortcode','billio');?>">
								</center>
						</form>
					</div>
				</div>
			</div>
		</body>
		</html>
		<?php }

		function billio_popup_send_to_editor($options=array()) {

			$string="";

			switch ($options['type']){
				case 'button':	$string="[dt_button url=\"".$options['url']."\" style=\"".$options['style']."\" size=\"".$options['size']."\" skin=\"".$options['skin']."\" target=\"".$options['target']."\"]".$options['text']."[/dt_button]";
				break;
				case 'counto':	$string="[dt_counto to=\"".$options['number']."\"".
				((!empty($options['sepcolor']))?" sepcolor=\"".esc_attr($options['sepcolor'])."\"":"").
				"]".$options['number']."[/dt_counto]";
				break;
				case 'icon':
				$string.="[dticon ico=\"".$options['icon']."\"".
				((!empty($options['size']))?" size=\"".$options['size']."\"":"").
				((!empty($options['color']))?" color=\"".$options['color']."\"":"").
				((!empty($options['style']))?" style=\"".$options['style']."\"":"")."][/dticon]";
				break;

			}
			$string=preg_replace("/\r\n|\n|\r/","<br/>",$string);

			?>
			<script type="text/javascript">

			/* <![CDATA[ */
			var win = window.dialogArguments || opener || parent || top;
			if(win.tinyMCE)
			{

				win.send_to_editor('<?php echo addslashes($string); ?>');
win.tb_remove();
}
else if(win.send_to_editor)
{
	win.send_to_editor('<?php echo addslashes($string); ?>');
	win.tb_remove();
}


/* ]]> */
</script>
<?php
exit;
}
?>
