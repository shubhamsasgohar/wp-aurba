<?php

namespace GF_Maxpay;

use GF_Field;
use GFMaxpay;

class GF_Maxpay_Field extends GF_Field
{

    public $type = 'maxpay_creditcard';


    public function get_form_editor_field_title()
    {
        return esc_attr__('Maxpay', 'gravityformsmaxpay');
    }

    public function get_form_editor_field_description()
    {
        return esc_attr__('Collects payments securely via maxpay payment gateway.', 'gravityformsmaxpay');
    }

    public function get_form_editor_button()
    {
        return array(
            'group' => 'pricing_fields',
            'text'  => $this->get_form_editor_field_title()
        );
    }

    function get_form_editor_field_settings()
    {
        return array(
            'conditional_logic_field_setting',
            'label_setting',
            'css_class_setting',
        );
    }

    public function is_conditional_logic_supported()
    {
        return true;
    }

    public function get_field_input($form, $value = '', $entry = null)
    {
        $form_id         = $form['id'];

        $input = "<div id='gf_maxpay_container_{$form_id}'>" . GFMaxpay::get_instance()->payment_geteway_form("cus-" . md5(time())) . "</div>";
        return $input;
    }
}
