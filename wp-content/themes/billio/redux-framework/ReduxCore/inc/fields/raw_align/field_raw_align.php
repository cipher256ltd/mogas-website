<?php
class DethemeReduxFramework_raw_align extends DethemeReduxFramework {

    /**
     * Field Constructor.
     *
     * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
     *
     * @since ReduxFramework 3.0.4
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
    function render() {
        echo '<fieldset id="'.$this->args['opt_name'].'-'.$this->field['id'].'" class="redux-field redux-container-'.$this->field['type'].' '.$this->field['class'].'" data-id="'.$this->field['id'].'">';

        if ( !empty( $this->field['include'] ) && file_exists( $this->field['include'] ) ) {
            //i n c l u d e( $this->field['include'] );
            locate_template( $this->field['include'],true );
        }
        if ( !empty( $this->field['content'] ) && isset( $this->field['content'] ) ) {
            echo $this->field['content'];
        }

        do_action('redux-field-raw-'.$this->args['opt_name'].'-'.$this->field['id']);

        echo '</fieldset>';
    }
}
