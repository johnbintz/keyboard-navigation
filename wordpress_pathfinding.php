<?php

function pathfinding_get_home_url() {
  if (function_exists('get_current_site')) { // WPMU
    $site = get_current_site();
    return preg_replace('#/$#', '', "http://" . $site->domain . $site->path);
  } else {
    return get_option('home');
  }
}

function pathfinding_get_admin_url() {
  if (function_exists('get_current_site')) { // WPMU
    return cpm_get_home_url();
  } else {
    return get_option('siteurl');
  }
}

function pathfinding_get_plugin_path() {
  return  (defined("MUPLUGINDIR") ? MUPLUGINDIR : PLUGINDIR) . '/' . preg_replace('#^.*/([^\/]*)#', '\\1', dirname(plugin_basename(__FILE__)));
}

?>