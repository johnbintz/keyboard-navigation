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
    <div id="top-information">
      <p><?php printf(__('<strong>Keyboard Navigation</strong> Version %s', 'keyboard-navigation'), $plugin_data['Version']) ?></p>

      <p><?php _e('Copyright &copy; 2008-2009', 'keyboard-navigation') ?> <a href="<?php echo $plugin_data['AuthorURI'] ?>">John Bintz</a> | <?php _e('Released under the GNU GPL', 'keyboard-navigation') ?></p>

      <p><?php _e('Need some sane defaults for your theme? Click one of the links below to populate the selectors on the left:', 'keyboard-navigation') ?></p>

      <ul>
        <li><a href="#" class="pre-populate comicpress_28"><?php _e('Default ComicPress 2.8 Theme', 'keyboard-navigation') ?></a></li>
        <li><a href="#" class="pre-populate comicpress_25"><?php _e('Default ComicPress 2.5 Theme', 'keyboard-navigation') ?></a></li>
      </ul>
    </div>

    <form method="post">
      <input type="hidden" name="kbnav[module]" value="options" />
      <input type="hidden" name="kbnav[_nonce]" value="<?php echo $nonce ?>" />
      <table class="form-table">
        <?php foreach ($this->fields as $field => $name) { ?>
          <tr>
            <th scope="row"><?php echo $name ?></td>
            <td><input type="text"
                       name="kbnav[selectors][<?php echo $field ?>]"
                       value="<?php echo $options['selectors'][$field] ?>" /></td>
          </tr>
        <?php } ?>
        <tr>
          <th scope="row"><?php _e('Highlight selected elements on theme?', 'keyboard-navigation') ?></th>
          <td>
            <label>
              <input type="checkbox" 
                     name="kbnav[highlight]"
                     value="yes"
                     <?php echo $options['highlight'] ? "checked" : "" ?> />
              <em><?php _e('(overlay yellow boxes on selected theme elements, and show missing selector alerts in Firebug console or in alert dialogs)', 'keyboard-navigation') ?></em>
            </label>
          </td>
        </tr>
        <tr>
          <td colspan="2" align="center">
            <input type="submit" class="button" value="<?php _e('Update Fields', 'keyboard-navigation') ?>" />
          </td>
        </tr>
      </table>
    </form>
  </div>
  <script type="text/javascript">
    var pre_population_sets = {
      'comicpress_25': {
        previous: "div#menunav a span.prev",
        next:     "div#menunav a span.next",
        first:    "",
        last:     "div#header h1 a"
      },
      'comicpress_28': {
        previous: ".navi-prev",
        next:     ".navi-next",
        first:    ".navi-first",
        last:     ".navi-last"
      }
    };
    
    $$('a.pre-populate').each(function(link) {
      Event.observe(link, 'click', function(e) {
        for (set in pre_population_sets) {
          if (link.hasClassName(set)) {
            var ok = true;
            var asked = false;
            var targets = {};
            for (key in pre_population_sets[set]) {
              targets[key] = $$('input[name*=' + key + ']').pop();
              if (!asked) {
                if (targets[key]) {
                  if (targets[key].value != pre_population_sets[set][key]) {
                    ok = confirm("<?php _e('Overwrite your existing settings?', 'keyboard-navigation') ?>");
                    asked = true;
                  }
                }
              }
            }
            if (ok) {
              for (key in pre_population_sets[set]) {
                if (targets[key]) { targets[key].value = pre_population_sets[set][key]; }
              }
            }
          }
        }
        Event.stop(e);
      });
    });
  </script>
  <h3><?php _e('How does this work?', 'keyboard-navigation') ?></h3>
  <p>
    <?php _e('<strong>Keyboard Navigation</strong> uses CSS Selectors to find the hyperlinks on the page that define the previous and next links on the page,', 'keyboard-navigation') ?>
    <?php _e('and then uses JavaScript to assign the act of hitting the left and right arrow keys on the keyboard to clicking those links.', 'keyboard-navigation') ?>
    <?php _e('This is exactly the same approach that you would use to style a specific element on the page:', 'keyboard-navigation') ?>
  </p>

  <div class="image-holder">
    <img src="<?php echo $plugin_dir_url ?>/images/keyboard-navigation-next-page-id.png" alt="<?php _e('Firebug display of a simple targeting procedure', 'keyboard-navigation') ?>" />
    <div>
      <p>
        <?php _e('Targeting the &lt;a href=&quot;next-page&quot;&gt; on this page is quite easy &mdash; provide the CSS selector for the id of the element:', 'keyboard-navigation') ?>
      </p>

      <blockquote>
        <strong>a#next-link</strong>
      </blockquote>

      <p>
        <?php _e('If the &lt;a href&gt; does not have an id, but does have a class, or is the only child of a particular element, you can use syntax like:', 'keyboard-navigation') ?>
      </p>

      <blockquote>
        <strong>div.nav div#next-link-holder a</strong>
      </blockquote>
    </div>
    <br class="clear" />    
  </div>

  <p>
    <?php _e('<strong>Keyboard Navigation</strong> extends this slightly so that, if the target element is not a &lt;a href&gt; tag,', 'keyboard-navigation') ?>
    <?php _e('Keyboard Navigation will move up through the tree from the found element until an &lt;a href&gt; is found:', 'keyboard-navigation') ?>
  </p>

  <div class="image-holder">
    <img src="<?php echo $plugin_dir_url ?>/images/keyboard-navigation-next-page-traverse.png" alt="<?php _e('Firebug display of a more complicated targeting procedure', 'keyboard-navigation') ?>" />
    <div>
      <p>
        <?php _e('You have two approaches for identifying the &lt;a href=&quot;next-page&quot;&gt; tag:', 'keyboard-navigation') ?>
      </p>
      <ul>
        <li>
          <?php _e('Using CSS 3 selectors, you can use <code>nth-child</code>:', 'keyboard-navigation') ?>
          <blockquote>
            <strong>div#menunav a:nth-child(2)</strong>
          </blockquote>
        </li>
        <li>
          <?php _e('Using more familiar CSS syntax, target the &lt;span class=&quot;next&quot;&gt; element, and <strong>Keyboard Navigation</strong> will move up through the DOM tree until it finds &lt;a href=&quot;next-page&quot;&gt;:', 'keyboard-navigation') ?>
          <blockquote>
            <strong>div#menunav a span.next</strong>
          </blockquote>
        </li>
      </ul>
    </div>
    <br class="clear" />
  </div>

  <p>
    <?php _e('<strong>Keyboard Navigation</strong> requires that the visitor have JavaScript enabled. This plugin uses', 'keyboard-navigation') ?>
    <?php _e('the Prototype JavaScript library, and including it will increase the size of your total site download. Additionally,', 'keyboard-navigation') ?>
    <?php _e('if other plugins <strong>load their own copies of the Prototype library</strong>, both plugins <strong>will break</strong>.', 'keyboard-navigation') ?>
  </p>
</div>
