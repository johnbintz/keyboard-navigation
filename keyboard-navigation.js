var KeyboardNavigation = {};

KeyboardNavigation.get_hrefs = function(fields, admin_mode) {
  var found_properties = {};
  var any_found = false;
  var missing_properties = [];

  for (key in fields) {
    if (fields.hasOwnProperty(key)) {
      var instructions = fields[key];
      var found_nodes = $$(instructions);
      var is_found = false;
      if (found_nodes.length > 0) {
        var top_node = $(found_nodes[0]);
        while (top_node) {
          if (top_node.href) {
            found_properties[key] = top_node.href; is_found = true;
            if (admin_mode) {
              var highlight_a = new Element("a", { "title": key + ": " + instructions, "href": top_node.href, "style": "display: block; position: absolute; border: solid #f00 1px; background-color: #ff0; z-index: 1" });
              highlight_a.setOpacity(0.5);
              highlight_a.clonePosition(top_node);
              document.body.appendChild(highlight_a);
            }
            any_found = true; break;
          }
          top_node = top_node.parentNode;
        }
      }

      if (!is_found) {
        missing_properties.push(key + ": " + instructions);
      }
    }
  }

  if (admin_mode) {
    if (missing_properties.length > 0) {
      var message = "[Keyboard Navigation] Missing selectors:\n\n" + missing_properties.join("\n");
      if (top.console) {
        top.console.log(message);
      } else {
        alert(message);
      }
    }
  }

  if (any_found) {
    return found_properties;
  } else {
    return false;
  }
};

KeyboardNavigation.add_events = function(hrefs) {
  Event.observe(document, 'keyup', function(e) {
    if (!(e.ctrlKey || e.altKey || e.metaKey)) {
      var prop_to_use = null;
      switch (e.keyCode) {
        case 37:
          prop_to_use = (e.shiftKey) ? "first" : "previous";
          break;
        case 39:
          prop_to_use = (e.shiftKey) ? "last" : "next";
          break;
      }
      if (prop_to_use) {
        if (hrefs[prop_to_use]) { document.location.href = hrefs[prop_to_use]; }
      }
    }
  }, true);

  var i,il;
  ["input","textarea","select"].each(function(type) {
    var all_type = document.getElementsByTagName(type);
    for (i = 0, il = all_type.length; i < il; ++i) {
      Event.observe(all_type[i], 'keyup', function(e) { Event.stop(e); return false; });
    }
  });
};