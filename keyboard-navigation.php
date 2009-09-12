<?php
/*
Plugin Name: Keyboard Navigation
Plugin URI: http://www.coswellproductions.com/wordpress/wordpress-plugins/
Description: Attach keyboard navigation to blog entries.
Version: 1.0
Author: John Bintz
Author URI: http://www.coswellproductions.org/wordpress/
Requires at least: 2.7
Tested up to: 2.8.4

Copyright 2008-2009 John Bintz  (email : jcoswell@coswellproductions.org)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

class KeyboardNavigation {
  function init() {
    wp_enqueue_script('prototype');
    
    add_action('wp_footer',  array(&$this, "wp_footer"));
    add_action('admin_menu', array(&$this, "admin_menu"));    
    add_action('admin_head', array(&$this, "admin_head"));
    
    $this->messages = array();
    $this->fields = array(
      'previous' => __("Previous [Left arrow]", 'keyboard-navigation'),
      'next'     => __("Next [Right arrow]",     'keyboard-navigation'),
      'first'    => __("First [Shift-Left arrow]",    'keyboard-navigation'),
      'last'     => __("Last [Shift-Right arrow]",     'keyboard-navigation'),
    );

    if (isset($_POST['kbnav'])) {
      if (is_array($_POST['kbnav'])) {
        if (isset($_POST['kbnav']['_nonce'])) {
          if (wp_verify_nonce($_POST['kbnav']['_nonce'], 'keyboard-navigation')) {
            if (isset($_POST['kbnav']['module'])) {
              $method = "handle_update_" . $_POST['kbnav']['module'];
              if (method_exists($this, $method)) { $this->{$method}($_POST['kbnav']); }
            }
          }
        }
      }
    }
  }

  function handle_update_options($info) {
    $options = array();
    
    foreach (array('selectors', 'highlight') as $field) {
      if (isset($info[$field])) { $options[$field] = $info[$field]; } 
    }
    
    update_option('keyboard-navigation-options', $options);
    
    $this->messages[] = __('Options updated.', 'keyboard-navigation');
  }

  function KeyboardNavigation() {}

  function wp_footer() {
    $plugin_dir_url = plugin_dir_url(__FILE__);
    $options = get_option('keyboard-navigation-options');
    $nonce = wp_create_nonce('keyboard-navigation');
    $options = get_option('keyboard-navigation-options');
    if (!is_array($options)) { $options = array(); }
    
    ?><script type="text/javascript">
      var s = document.createElement('script');
      s.src = '<?php echo $plugin_dir_url . 'keyboard-navigation.js' ?>';
      s.onload = function() {    
        var keyboard_navigation_fields = {};
        <?php foreach (array_keys($this->fields) as $field) {
          if (!empty($options['selectors'][$field])) { ?>
            keyboard_navigation_fields['<?php echo $field ?>'] = "<?php echo addslashes($options['selectors'][$field]) ?>";
          <?php }
        } ?>

        var highlight_selectors = <?php echo (current_user_can('edit_themes') && $options['highlight']) ? "true" : "false" ?>;
  
        var results = KeyboardNavigation.get_hrefs(keyboard_navigation_fields, highlight_selectors);
        if (results != false) { KeyboardNavigation.add_events(results); }
      };
      document.getElementsByTagName('body')[0].appendChild(s);
    </script>
  <?php }

  function admin_menu() {
    add_options_page('Keyboard Navigation', __("Keyboard Navigation", 'keyboard-navigation'), 'edit_themes', 'keyboard-navigation', array($this, "link_editor"));
  }

  function admin_head() {    
    global $plugin_page;

    if ($plugin_page == plugin_basename(dirname(__FILE__))) {
      echo '<link rel="stylesheet" href="' . plugin_dir_url(__FILE__) . '/styles.css" type="text/css" />';
    }
  }

  function link_editor() {
    $plugin_dir_url = plugin_dir_url(__FILE__);
    $nonce = wp_create_nonce('keyboard-navigation');
    $options = get_option('keyboard-navigation-options');
    if (!is_array($options)) { $options = array(); }
    
    $plugin_data = get_plugin_data(__FILE__);

    include(dirname(__FILE__) . '/admin.inc');
  }
}

$keyboard_navigation = new KeyboardNavigation();
add_action('init', array(&$keyboard_navigation, 'init'));

?>