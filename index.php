<?php

/**
 * This plugin sends out of stock products to the end of the products list.
 *
 * @package Send_Out_of_Stock_Products_to_End
 */

class Send_Out_of_Stock_Products_to_End extends WC_Plugin {

  /**
   * Constructor.
   */
  public function __construct() {
    parent::__construct('send-out-of-stock-products-to-end');

    // Add the plugin settings page.
    add_action('admin_menu', array($this, 'add_plugin_settings_page'));

    // Add the "Send Out of Stock Products to End" filter.
    add_filter('woocommerce_get_products', array($this, 'send_out_of_stock_products_to_end'));
  }

  /**
   * Adds the plugin settings page.
   */
  public function add_plugin_settings_page() {
    add_menu_page(
      'Send Out of Stock Products to End',
      'Send Out of Stock Products to End',
      'manage_options',
      'send-out-of-stock-products-to-end',
      array($this, 'plugin_settings_page')
    );
  }

  /**
   * Displays the plugin settings page.
   */
  public function plugin_settings_page() {
    ?>
    <div class="wrap">
      <h1>Send Out of Stock Products to End</h1>

      <form action="options.php" method="post">
        <?php settings_fields('send_out_of_stock_products_to_end'); ?>
        <table class="form-table">
          <tr>
            <th>
              <label for="send_out_of_stock_products_to_end_hide">Hide out of stock products?</label>
            </th>
            <td>
              <input type="checkbox" name="send_out_of_stock_products_to_end_hide" id="send_out_of_stock_products_to_end_hide" value="1">
              <p>If checked, out of stock products will be hidden from the products list.</p>
            </td>
          </tr>
        </table>

        <input type="submit" class="button-primary" value="Save Changes">
      </form>
    </div>
    <?php
  }

  /**
   * Sends out of stock products to the end of the products list.
   *
   * @param array $products The products array.
   * @return array The filtered products array.
   */
  public function send_out_of_stock_products_to_end($products) {
    $hide_out_of_stock_products = get_option('send_out_of_stock_products_to_end_hide');

    $out_of_stock_products = array_filter($products, function($product) {
      return $product->stock_quantity <= 0;
    });

    if ($hide_out_of_stock_products) {
      foreach ($products as $key => $product) {
        if (in_array($product->id, array_map(function($product) {
          return $product->id;
        }, $out_of_stock_products))) {
          array_splice($products, $key, 1);
          break;
        }
      }

      array_push($products, ...$out_of_stock_products);
    }

    return $products;
  }

}

new Send_Out_of_Stock_Products_to_End();

