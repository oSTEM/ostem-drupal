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

function ostemnational_form_alter(&$form, &$form_state, $form_id) {
	if($form_id == 'connector_button_form') {
		foreach($form as $key => $form_child) {
			if(array_key_exists('connector', $form_child)) $form[$key]['#value'] = 'Login with ' . $form_child['connector']['#value']['title'];
		}
	}
}

function ostemnational_connector_buttons($variables) {

  $form = $variables['form'];
  if (!$form['#has_buttons']) return NULL;
  $output = '';
  $buttons = array();
  $i == 0;
  foreach (element_children($form) as $key) {
    if ($form[$key]['#type'] == 'submit') {
      $i++;
      $class = str_replace('_', '-', $key);
      $form[$key]['#attributes']['class'][] = 'connector-button';
      $form[$key]['#attributes']['class'][] = $class;
      if($i == 1) $class .= ' first';
      $form[$key]['#prefix'] = '<span class="connector-button-wrapper ' .$class . '">';
      $form[$key]['#suffix'] = '</span>';
    }
  }
  return drupal_render_children($form);

}
