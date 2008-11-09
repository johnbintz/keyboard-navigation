<?php
/*
Plugin Name: Keyboard Navigation
Plugin URI: http://www.coswellproductions.com/wordpress/keyboard-navigation/
Description: Attach keyboard navigation to blog entries.
Version: 0.1
Author: John Bintz
Author URI: http://www.coswellproductions.org/wordpress/

Copyright 2008 John Bintz  (email : jcoswell@coswellproductions.org)

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

require_once('wordpress_pathfinding.php');

class KeyboardNavigation {
  function KeyboardNavigation() {
    $this->messages = array();
    $this->fields = array(
      'previous' => __("Previous [Left arrow]", 'keyboard-navigation'),
      'next'     => __("Next [Right arrow]",     'keyboard-navigation'),
      'first'    => __("First [Shift-Left arrow]",    'keyboard-navigation'),
      'last'     => __("Last [Shift-Right arrow]",     'keyboard-navigation'),
    );

    if (isset($_POST['action'])) {
      if ($_POST['action'] == "update-fields") {
        $any_updated = false;
        foreach (array_keys($this->fields) as $field) {
          if (isset($_POST["selector-${field}"])) {
            update_option("keyboard-navigation-selector-${field}", $_POST["selector-${field}"]);
            $any_updated = true;
          }
        }
        update_option("keyboard-navigation-highlight-selected-elements", ($_POST['highlight-selected-elements'] ? "1" : "0"));
        if ($any_updated) { $this->messages[] = "Selectors updated."; }
      }
    }
  }

  function footer() {
    $plugin_url_root = pathfinding_get_admin_url() . '/' . pathfinding_get_plugin_path(); ?>
      <script type="text/javascript" src="<?php echo $plugin_url_root ?>/keyboard_navigation.js"></script>
      <script type="text/javascript">
        var keyboard_navigation_fields = {};
        <?php foreach (array_keys($this->fields) as $field) {
          $selector = get_option("keyboard-navigation-selector-${field}");
          if (!empty($selector)) { ?>
            keyboard_navigation_fields['<?php echo $field ?>'] = "<?php echo addslashes($selector) ?>";
          <?php }
        } ?>

        var highlight_selectors = <?php echo (WP_ADMIN && (get_option("keyboard-navigation-highlight-selected-elements") == 1)) ? "true" : "false" ?>;

        var results = KeyboardNavigation.get_hrefs(keyboard_navigation_fields, highlight_selectors);
        if (results != false) { KeyboardNavigation.add_events(results); }
      </script>
    <?php
  }

  function set_up_menu() {
    add_options_page('Keyboard Navigation', __("Keyboard Navigation", 'keyboard-navigation'), 5, __FILE__, array($this, "link_editor"));
  }

  function link_editor() {
    $plugin_url_root = pathfinding_get_admin_url() . '/' . pathfinding_get_plugin_path(); ?>
    <link rel="stylesheet" href="<?php echo $plugin_url_root ?>/styles.css" type="text/css" />
    <div class="wrap">
      <?php if (count($this->messages) > 0) { ?>
        <div class="updated fade">
          <?php foreach ($this->messages as $message) { ?>
            <p><?php echo $message ?></p>
          <?php } ?>
        </div>
      <?php } ?>
      <h2><?php _e("Keyboard Navigation", "keyboard-navigation") ?></h2>

      <div id="top-holder">
        <form action="" method="post">
          <input type="hidden" name="action" value="update-fields" />
          <table class="form-table">
            <?php foreach ($this->fields as $field => $name) { ?>
              <tr>
                <th scope="row"><?php echo $name ?></td>
                <td><input size="30" type="text" id="selector-<?php echo $field ?>" name="selector-<?php echo $field ?>" value="<?php echo get_option("keyboard-navigation-selector-${field}") ?>" /></td>
              </tr>
            <?php } ?>
            <tr>
              <th scope="row">Highlight selected elements on theme?</th>
              <td>
                <input type="checkbox" name="highlight-selected-elements" value="yes" <?php echo (get_option("keyboard-navigation-highlight-selected-elements") == 1) ? "checked" : "" ?> />
                <em>(overlay yellow boxes on selected theme elements, and show missing selector alerts in Firebug console or in alert dialogs)</em>
              </td>
            </tr>
            <tr>
              <td colspan="2" align="center">
                <input type="submit" value="Update Fields" />
              </td>
            </tr>
          </table>
        </form>

        <div id="top-information">
          <p><strong>Keyboard Navigation</strong> Version 0.1</p>

          <p>Copyright &copy; 2008 <a href="mailto:john@claritycomic.com?Subject=Keyboard+Navigation+Comments">John Bintz</a> |
             Released under the GNU GPL</p>

          <p>Need some sane defaults for your theme? Click one of the links below to populate the selectors on the left:</p>

          <ul>
            <li><a href="#" class="pre-populate comicpress">Default ComicPress 2.5 Theme</a></li>
          </ul>

          <p>Are you a theme author? Send your theme's navigation defaults to <a href="mailto:john@claritycomic.com?Subject=Keyboard+Navigation+Sane+Defaults">John Bintz</a> and get them included!</p>
        </div>
      </div>
      <script type="text/javascript">
        var pre_population_sets = {
          'comicpress': {
            previous: "div#menunav a span.prev",
            next:     "div#menunav a span.next",
            first:    "",
            last:     "div#header h1 a"
          }
        };
        $$('a.pre-populate').each(function(e) {
          Event.observe(e, 'click', function(evt) {
            for (set in pre_population_sets) {
              if (e.hasClassName(set)) {
                var ok = true;
                for (key in pre_population_sets[set]) {
                  var target = $('selector-' + key);
                  if (target) {
                    if (target.value != pre_population_sets[set][key]) {
                      ok = confirm("Overwrite your existing settings?"); break;
                    }
                  }
                }
                if (ok) {
                  for (key in pre_population_sets[set]) {
                    var target = $('selector-' + key);
                    if (target) { target.value = pre_population_sets[set][key]; }
                  }
                }
              }
            }
            Event.stop(evt);
          });
        });
      </script>
      <h3>How does this work?</h3>
      <p><strong>Keyboard Navigation</strong> uses CSS3 Selectors to find the hyperlinks on the page that define the previous and next links on the page, and then uses JavaScript to assign the act of hitting the left and right arrow keys on the keyboard to clicking those links. This is exactly the same approach that you would use to style a specific element on the page:</p>

      <div class="image-holder">
        <img src="<?php echo $plugin_url_root ?>/images/keyboard-navigation-next-page-id.png" alt="Firebug display of a simple targeting procedure" />
        <div>
          <p>Targeting the &lt;a href=&quot;next-page&quot;&gt; on this page is quite easy &mdash; provide the CSS selector for the id of the element:</p>

          <blockquote>
            <strong>a#next-link</strong>
          </blockquote>

          <p>If the &lt;a href&gt; does not have an id, but does have a class, or is the only child of a particular element, you can use syntax like:</p>

          <blockquote>
            <strong>div.nav div#next-link-holder a</strong>
          </blockquote>
        </div>
      </div>

      <p><strong>Keyboard Navigation</strong> extends this slightly so that, if the target element is not a &lt;a href&gt; tag, Keyboard Navigation will move up through the tree from the found element until an &lt;a href&gt; is found:</p>

      <div class="image-holder">
        <img src="<?php echo $plugin_url_root ?>/images/keyboard-navigation-next-page-traverse.png" alt="Firebug display of a more complicated targeting procedure" />
        <div>
          <p>You have two approaches for identifying the &lt;a href=&quot;next-page&quot;&gt; tag:</p>
          <ul>
            <li>Using CSS3 selectors, you can use <code>nth-child</code>:
                <blockquote>
                  <strong>div#menunav a:nth-child(2)</strong>
                </blockquote>
            </li>
            <li>Using more familiar CSS syntax, target the &lt;span class=&quot;next&quot;&gt; element, and <strong>Keyboard Navigation</strong> will move up through the DOM tree until it finds &lt;a href=&quot;next-page&quot;&gt;:
                <blockquote>
                  <strong>div#menunav a span.next</strong>
                </blockquote>
            </li>
          </ul>
        </div>
      </div>

      <p><strong>Keyboard Navigation</strong> requires that the visitor have JavaScript enabled. This plugin uses
      the Prototype JavaScript library, and including it will increase the size of your total site download.</p>
    </div>
  <?php }
}

$keyboard_navigation = new KeyboardNavigation();

wp_enqueue_script('prototype');
add_action('wp_footer', array($keyboard_navigation, "footer"));
add_action('admin_menu', array($keyboard_navigation, "set_up_menu"));

?>