<?php  
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Woolentor_Module_Manager{

    private static $_instance = null;

    /**
     * Instance
     */
    public static function instance(){
        if( is_null( self::$_instance ) ){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor
     */
    public function __construct(){
        $this->include_file();
    }

    /**
     * [include_file] Nessary File Required
     * @return [void]
     */
    public function include_file(){

        // Wishlist
        if( woolentor_get_option( 'wishlist', 'woolentor_others_tabs', 'off' ) == 'on' ){
            $this->deactivate( 'wishsuite/wishsuite.php' );
            if( ! class_exists('WishSuite_Base') ){
                require_once( WOOLENTOR_ADDONS_PL_PATH .'includes/modules/wishlist/init.php' );
            }
        }

        // Compare
        if( woolentor_get_option( 'compare', 'woolentor_others_tabs', 'off' ) == 'on' ){
            $this->deactivate( 'ever-compare/ever-compare.php' );
            if( ! class_exists('Ever_Compare') ){
                require_once( WOOLENTOR_ADDONS_PL_PATH .'includes/modules/compare/init.php' );
            }
        }
        
        // Shopify Style Checkout page
        if( woolentor_get_option( 'enable', 'woolentor_shopify_checkout_settings', 'off' ) == 'on' ){
            require_once( WOOLENTOR_ADDONS_PL_PATH .'includes/modules/shopify-like-checkout/class.shopify-like-checkout.php' );
        }

        // Flash Sale
        if( woolentor_get_option( 'enable', 'woolentor_flash_sale_settings', 'off' ) == 'on' ){
            require_once( WOOLENTOR_ADDONS_PL_PATH .'includes/modules/flash-sale/class.flash-sale.php' );
        }

        // Backorder
        if( woolentor_get_option( 'enable', 'woolentor_backorder_settings', 'off' ) == 'on' ){
            require_once( WOOLENTOR_ADDONS_PL_PATH .'includes/modules/backorder/class.backorder.php' );
        }

        if( is_plugin_active('woolentor-addons-pro/woolentor_addons_pro.php') ){

            // Partial payment
            if( ( woolentor_get_option( 'enable', 'woolentor_partial_payment_settings', 'off' ) == 'on' ) ){
                require_once( WOOLENTOR_ADDONS_PL_PATH_PRO .'includes/modules/partial-payment/partial-payment.php' );
            }

            // Pre Orders
            if( ( woolentor_get_option( 'enable', 'woolentor_pre_order_settings', 'off' ) == 'on' ) ){
                require_once( WOOLENTOR_ADDONS_PL_PATH_PRO .'includes/modules/pre-orders/pre-orders.php' );
            }

            // GTM Conversion tracking
            if( ( woolentor_get_option( 'enable', 'woolentor_gtm_convertion_tracking_settings', 'off' ) == 'on' ) ){
                require_once( WOOLENTOR_ADDONS_PL_PATH_PRO .'includes/modules/gtm-conversion-tracking/gtm-conversion-tracking.php' );
            }

        }
        
    }

    /**
     * [deactivate] Deactivated
     * @return [void]
     */
    public function deactivate( $slug ){
        if( is_plugin_active( $slug ) ){
            return deactivate_plugins( $slug );
        }
    }


}

Woolentor_Module_Manager::instance();