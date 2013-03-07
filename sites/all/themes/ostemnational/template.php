<?php

drupal_add_css('https://fonts.googleapis.com/css?family=Lato:300,300italic,700,700italic', array('type' => 'external'));

function getPageID($is_front) {
  if ($is_front) {
    $page_id = 'homepage';
  }
  else {
    // Remove base path and any query string.
    global $base_path;
    list(,$path) = explode($base_path, $_SERVER['REQUEST_URI'], 2);
    list($path,) = explode('?', $path, 2);
    $path = rtrim($path, '/');
    // Construct the id name from the path, replacing slashes with dashes.
    $page_id = str_replace('/', '-', $path);
    // Construct the class name from the first part of the path only.
    list($body_class,) = explode('/', $path, 2);
  }

  return $page_id;
}

function getBodyID($is_front = false) {
	return 'page-' . getPageID($is_front);
}
