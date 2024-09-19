<?php

use GF_Maxpay\Dependencies\Model\FixedProduct;
use GF_Maxpay\Dependencies\Model\UserInfo;
use GF_Maxpay\Dependencies\Scriney;
use GF_Maxpay\Dependencies\Util\SignatureHelper;
use GF_Maxpay\GF_Maxpay_Field;

GFForms::include_payment_addon_framework();

class GFMaxpay extends GFPaymentAddOn
{
    protected $_version = GF_MAXPAY_VERSION;
    protected $_min_gravityforms_version = '1.9.14.17';
    protected $_slug = 'gravityformsmaxpay';
    protected $_path = 'gravityformsmaxpay/maxpay.php';
    protected $_full_path = __FILE__;
    protected $_title = 'Gravity Forms Maxpay Add-On';
    protected $_short_title = 'Maxpay';
    protected $_supports_callbacks = true;
    protected $_requires_credit_card = false;

    private static $_instance = null;

    public static function get_instance()
    {
        if (self::$_instance == null) {
            self::$_instance = new GFMaxpay();
        }

        return self::$_instance;
    }

    public function init()
    {
        parent::init();
        GF_Fields::register(new GF_Maxpay_Field());
    }

    public function payment_geteway_form($user_id)
    {
        $scriney = new Scriney($this->get_plugin_setting('gravityformsmaxpay_public_key'), $this->get_plugin_setting('gravityformsmaxpay_secret_key'));

        $form = $scriney->buildButton($user_id)->setCustomProducts(
            [
                new FixedProduct(
                    'productId',
                    'Garden Table',
                    198.98,
                    'USD',
                    null,
                    null,
                    'Magic Garden Table & Set of 2 Chairs'
                )
            ]
        )->buildFrame();

        return $form;
    }

    public function plugin_settings_fields()
    {
        return array(
            array(
                'title'  => esc_html__('Maxpay Settings', 'gravityformsmaxpay'),
                'description' => $this->get_callback_hook_instructions(),
                'fields' => array(
                    array(
                        'name'              => 'gravityformsmaxpay_public_key',
                        'tooltip'           => esc_html__('Public key provided for your account by MaxPay gateway', 'gravityformsmaxpay'),
                        'label'             => esc_html__('Public key', 'gravityformsmaxpay'),
                        'type'              => 'text',
                        'class'             => 'small',
                        'required'          => true,
                    ),
                    array(
                        'name'              => 'gravityformsmaxpay_secret_key',
                        'tooltip'           => esc_html__('Secret key provided for your account by MaxPay gateway', 'gravityformsmaxpay'),
                        'label'             => esc_html__('Secret key', 'gravityformsmaxpay'),
                        'type'              => 'text',
                        'class'             => 'small',
                        'required'          => true,
                    )
                )
            )
        );
    }

    public function get_callback_hook_instructions()
    {
        ob_start();
?>
        <h4 style="margin: 0;">Callback Instructions</h4>
        <div id="maxpay-callback-instructions">
            <ol class="maxpay-callback-instructions">
                <li><?php esc_html_e('Above the list of Callback URLs.', 'gravityformsmaxpay'); ?></li>
                <li>
                    <?php esc_html_e('Enter the following URL in the "Test callback url" and "Live callback url" field:', 'gravityformsmaxpay'); ?>
                    <code><?php echo $this->get_callback_hook_url($this->get_current_feed_id()); ?></code>
                </li>
                <li>
                    <?php esc_html_e('Note: "Test callback url" only for testing purpose.', 'gravityformsmaxpay'); ?>
                </li>
            </ol>

        </div>

<?php
        return ob_get_clean();
    }

    public function get_callback_hook_url($feed_id = null)
    {

        $url = home_url('/', 'https') . '?callback=' . $this->_slug;

        if (!rgblank($feed_id)) {
            $url .= '&fid=' . $feed_id;
        }

        return $url;
    }

    private function get_entry_by_session_id($session_id, $action, $event)
    {
        $entries = GFAPI::get_entries(
            null,
            array(
                'field_filters' => array(
                    array(
                        'key'   => 'maxpay_session_id',
                        'value' => $session_id,
                    ),
                ),
            )
        );

        if (empty($entries)) {
            $action['session_id'] = $session_id;
            return $this->get_entry_not_found_wp_error('session', $action, $event);
        }
        $entry = $entries[0];

        if (!$this->is_valid_entry_for_callback($entry)) {
            return $this->get_wrong_feed_wp_error($entry['id']);
        }

        return $entry;
    }

    public function is_valid_entry_for_callback($entry)
    {
        if (empty($_GET['fid'])) {
            return true;
        }

        return rgar($this->get_payment_feed($entry), 'id') === $_GET['fid'];
    }

    public function get_wrong_feed_wp_error($entry_id)
    {
        return new WP_Error(
            'wrong_feed_for_entry',
            sprintf(__('Entry %d was not processed by feed %d. Callback cannot be processed.', 'gravityformsmaxpay'), $entry_id, $_GET['fid']),
            array('status_header' => 200)
        );
    }

    public function get_entry_not_found_wp_error($type, $action, $event)
    {
        $message     = sprintf(__('Entry for %s id: %s was not found. Callback cannot be processed.', 'gravityformsmaxpay'), $type, rgar($action, $type . '_id'));
        $status_code = 200;

        return new WP_Error('entry_not_found', $message, array('status_header' => $status_code));
    }
}
