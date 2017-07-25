<?php
/**
 * Redux Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Redux Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Redux Framework. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     ReduxFramework
 * @subpackage  Field_Multi_Text
 * @author      Daniel J Griffiths (Ghost1227)
 * @author      Dovy Paukstys
 * @version     3.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Don't duplicate me!
if( !class_exists( 'DethemeReduxFramework_career_multi_text' ) ) {

    /**
     * Main ReduxFramework_multi_text class
     *
     * @since       1.0.0
     */
    class DethemeReduxFramework_career_multi_text extends DethemeReduxFramework {
    
        /**
         * Field Constructor.
         *
         * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
         *
         * @since       1.0.0
         * @access      public
         * @return      void
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
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function render() {

            $this->add_text = ( isset($this->field['add_text']) ) ? $this->field['add_text'] : __( 'Add More', 'redux-framework');
            
            $this->show_empty = ( isset($this->field['show_empty']) ) ? $this->field['show_empty'] : true;
            $sortable = (isset($this->field['sortable']) && $this->field['sortable']) ? (bool)$this->field['sortable'] : false;

            echo '<ul id="' . $this->field['id'] . '-ul" class="redux-career-multi-text '.($sortable?'multi-text-sortable':'').'">';

                if( isset( $this->value ) && is_array( $this->value ) ) {

                    $i=0;
                    foreach( $this->value as $k => $value ) {

                        $value=wp_parse_args($value,array('label'=>'','icon'=>''));

                        if( $value['label']!=''){
                            echo '<li>'.($value['icon']!=''?'<div class="icon-selection-preview"><i class="'.esc_attr( $value['icon'] ).'"></i></div>':'').'<input type="text" id="' . $this->field['id'] . '-' . $k . '" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . ']['.$i.'][label]" value="' . esc_attr( $value['label'] ) . '" class="regular-text ' . $this->field['class'] . '" /> '.
                        '<input type="hidden" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . ']['.$i.'][icon]" value="'.esc_attr( $value['icon'] ).'" class="icon-field" />'.
                        '<a href="javascript:;" class="redux-career-icon-edit">'.__('Edit Icon','redux-framework').'</a> <a href="javascript:void(0);" class="deletion redux-career-multi-text-remove">' . __( 'Remove', 'redux-framework' ) . '</a>'.
                        ($sortable?' <span class="compact drag ui-sortable-handle"><i class="el-icon-move icon-large"></i></span> ':'').'</li>';
                            
                            $i++;
                        }
                    }
                } elseif($this->show_empty == true ) {
                   echo '<li><input type="text" id="' . $this->field['id'] . '" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . '][0][label]" value="" class="regular-text ' . $this->field['class'] . '" /> '.
                    '<input type="hidden" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . '][0][icon]" value="" class="icon-field" /><a href="javascript:;" class="redux-career-icon-edit">'.__('Edit Icon','redux-framework').'</a> <a href="javascript:void(0);" class="deletion redux-career-multi-text-remove">' . __( 'Remove', 'redux-framework' ) . '</a>'.
                    ($sortable?' <span class="compact drag ui-sortable-handle"><i class="el-icon-move icon-large"></i></span> ':'').'</li>';
                }
            
                echo '<li style="display:none;"><input type="text" id="' . $this->field['id'] . '" name="" value="" class="regular-text" /> '.
                '<input type="hidden" name="" value="" class="icon-field" />'.
                '<a href="javascript:;" class="redux-career-icon-edit">'.__('Edit Icon','redux-framework').'</a> <a href="javascript:void(0);" class="deletion redux-career-multi-text-remove">' . __( 'Remove', 'redux-framework') . '</a>'.
                ($sortable?' <span class="compact drag ui-sortable-handle"><i class="el-icon-move icon-large"></i></span> ':'').'</li>';

            echo '</ul>';
            $this->field['add_number'] = ( isset( $this->field['add_number'] ) && is_numeric( $this->field['add_number'] ) ) ? $this->field['add_number'] : 1;
            echo '<a href="javascript:void(0);" class="button button-primary redux-career-multi-text-add" data-add_number="'.$this->field['add_number'].'" data-id="' . $this->field['id'] . '-ul" data-name="' . $this->args['opt_name'] . '[' . $this->field['id'] . ']">' . $this->add_text . '</a><br/>';

            if(@function_exists('detheme_font_list')):

            $icons=detheme_font_list();
            if($icons){

            ob_start();
?>
<script type="text/javascript">
jQuery(document).ready(function($){
    'use strict';

    var options={
            icons:new Array('<?php print @implode("','",$icons);?>'),
            onUpdate:function(e){

                var par=this.closest('li'),fieldinput=par.find('.icon-field'),preview=par.find('.icon-selection-preview i');
                fieldinput.val(e);
                if(!preview.length){
                    preview=$('<i/>');
                    preview.prependTo(par);
                    preview.wrap('<div class="icon-selection-preview"></div>');

                }
                preview.removeClass().addClass(e);
            }
        };

        try{
            $("#<?php print $this->field['id'];?>-ul li .redux-career-icon-edit").iconPicker(options);
        }
        catch(err){
        }

});
</script>
<?php      print ob_get_clean();     
            }
            endif;

            $this->enqueue();

        }   

        /**
         * Enqueue Function.
         *
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function enqueue() {

            wp_enqueue_script( 'icon-picker',get_template_directory_uri() . '/lib/js/icon_picker.js', array('jquery'));
            wp_localize_script( 'icon-picker', 'picker_i18nLocale', array(
              'search_icon'=>__('Search Icon','financia'),
            ) );
     
        }

    }   
}