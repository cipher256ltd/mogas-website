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
 * @subpackage  Field_Color_Gradient
 * @author      Luciano "WebCaos" Ubertini
 * @author      Daniel J Griffiths (Ghost1227)
 * @author      Dovy Paukstys
 * @version     3.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Don't duplicate me!
if( !class_exists( 'DethemeReduxFramework_color_nocheck' ) ) {

    /**
     * Main ReduxFramework_link_color class
     *
     * @since       1.0.0
     */
    class DethemeReduxFramework_color_nocheck extends DethemeReduxFramework {
    
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

            echo '<input data-id="'.$this->field['id'].'" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . ']" id="' . $this->field['id'] . '-color" class="redux-color redux-color-init ' . $this->field['class'] . '"  type="text" value="' . $this->value . '"  data-default-color="' . ( isset($this->field['default']) ? $this->field['default'] : "" ) . '" />';
        
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
        
            wp_enqueue_script(
                'redux-field-color-js', 
                DethemeReduxFramework::$_url . 'inc/fields/color/field_color.min.js', 
                array( 'jquery', 'wp-color-picker' ),
                time(),
                true
            );

            wp_enqueue_style(
                'redux-field-color-js', 
                DethemeReduxFramework::$_url . 'inc/fields/color/field_color.css', 
                time(),
                true
            ); 
               
        }

        public function output() {

            if (isset($this->field['output']) && !empty($this->field['output'])) {

                $keys = implode(",", $this->field['output']);
                $style = '';
                if ( !empty( $this->value ) ) {

                    $style .= $keys."{";
                    $style .= 'color:'.$this->value.';';
                    $style .= '}';
                    $this->parent->outputCSS .= $style;  
                }
            }
        }

    }
}
?>
