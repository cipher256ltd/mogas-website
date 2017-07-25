<?php
class DethemeReduxFramework_menu extends DethemeReduxFramework{	
	
	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @since ReduxFramework 1.0.0
	*/
	function __construct( $field = array(), $value ='', $parent ) {
    
        $this->args=$parent->args;
		$this->parent = $parent;
		$this->field = $field;
		$this->value = $value;
		
            
    }
	


	/**
	 * Field Render Function.
	 *
	 * Takes the vars and outputs the HTML for the field in the settings
	 *
	 * @since ReduxFramework 1.0.0
	*/
	function render(){

		$sortable = (isset($this->field['sortable']) && $this->field['sortable']) ? ' select2-sortable"' : "";
		if (!empty($sortable)) { // Dummy proofing  :P
			$this->field['multi'] = true;
		}

		$nav_menus = wp_get_nav_menus( array('orderby' => 'name') );

		if ($nav_menus) {
			$multi = (isset($this->field['multi']) && $this->field['multi']) ? ' multiple="multiple"' : "";
			
			if (!empty($this->field['width'])) {
				$width = ' style="'.$this->field['width'].'"';
			} else {
				$width = ' style="width: 40%;"';
			}	

			$nameBrackets = "";
			if (!empty($multi)) {
				$nameBrackets = "[]";
			}


			$placeholder = (isset($this->field['placeholder'])) ? esc_attr($this->field['placeholder']) : __( 'Select an item', 'redux-framework' );

			if ( isset($this->field['select2']) ) { // if there are any let's pass them to js
				$select2_params = json_encode($this->field['select2']);
				$select2_params = htmlspecialchars( $select2_params , ENT_QUOTES);
				
				echo '<input type="hidden" class="select2_params" value="'. $select2_params .'">';
			}
		
			$sortable = (isset($this->field['sortable']) && $this->field['sortable']) ? ' select2-sortable"' : "";
			echo '<select '.$multi.' id="'.$this->field['id'].'-select" data-placeholder="'.$placeholder.'" name="'.$this->args['opt_name'].'['.$this->field['id'].']'.$nameBrackets.'" class="redux-select-item '.$this->field['class'].$sortable.'"'.$width.' rows="6">';
				echo '<option></option>';




				foreach($nav_menus as $menu){
					if (is_array($this->value)) {
						$selected = (is_array($this->value) && in_array($menu->term_id, $this->value))?' selected="selected"':'';					
					} else {
						$selected = selected($this->value, $menu->term_id, false);
					}
					echo '<option value="'.$menu->term_id.'"'.$selected.'>'.$menu->name.'</option>';
				}//foreach
			echo '</select>';			
		} else {
			echo '<strong>'.__('No items of this type were found.', 'redux-framework').'</strong>';
		}

	}//function

	
	/**
	 * Enqueue Function.
	 *
	 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
	 *
	 * @since ReduxFramework 1.0.0
	*/
	function enqueue(){

		wp_enqueue_script(
			'field-select-js', 
			DethemeReduxFramework::$_url.'inc/fields/select/field_select.js',
			array('jquery', 'select2-js'),
			time(),
			true
		);		

		wp_enqueue_style(
			'redux-field-select-css', 
			DethemeReduxFramework::$_url.'inc/fields/select/field_select.css', 
			time(),
			true
		);			

	}//function

}//class