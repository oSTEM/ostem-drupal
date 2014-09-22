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

function ostemnational_block_list_alter(&$blocks) {
    if(!drupal_match_path(drupal_get_path_alias(current_path()), 'user/register')) return;
    if (!isset($_SESSION) || empty($_SESSION['azure_acs_data'])) return;
    foreach($blocks as $bid => $block) {
        if($block->module == "azure_acs") unset($blocks[$bid]);      
    }
}

function ostemnational_form_alter(&$form, &$form_state, $form_id) {
    
    if (isset($_SESSION) && !empty($_SESSION['azure_acs_data'])) {
        // Azure ACS form updates
        
        $mail = isset($_SESSION['azure_acs_data']['emailaddress']) ? $_SESSION['azure_acs_data']['emailaddress'] : '';
        $fname = isset($_SESSION['azure_acs_data']['givenname']) ? $_SESSION['azure_acs_data']['givenname'] : '';
        $lname = isset($_SESSION['azure_acs_data']['surname']) ? $_SESSION['azure_acs_data']['surname'] : '';
        $name = isset($_SESSION['azure_acs_data']['name']) ? $_SESSION['azure_acs_data']['name'] :
            (empty($fname) || empty($lname) ? '' : $fname . ' ' . $lname);
        $explode = strpos($mail, '@') === FALSE ? array('') : explode('@', $mail);
        $user = empty($mail) ? $name : $explode[0];
        
        if(!empty($name)) {
            $explode = explode(" ", $name);
            if(empty($fname)) $fname = $explode[0];
            if(empty($lname) && count($explode) > 1) $lname = $explode[count($explode)-1];
        }
        
        if(strpos($_SESSION['azure_acs_data']['identityprovider'], 'Facebook') !== FALSE
            && isset($_SESSION['azure_acs_data']['AccessToken'])) {
            $url = "https://graph.facebook.com/" . $_SESSION['azure_acs_data']['nameidentifier']
                . "?access_token=" . $_SESSION['azure_acs_data']['AccessToken'];
            $ch = curl_init();
            $timeout = 500;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $data = json_decode(curl_exec($ch));
            curl_close($ch);
            
            if(!empty($data)) {
                $bday = isset($data->birthday) ? $data->birthday : '';
                $gender = isset($data->gender) ? $data->gender : '';
                $tz = isset($data->timezone) ? $data->timezone : '';
            }
        }

        if (isset($form['account'])) {
            
            if(isset($form['account']['pass'])) {
                $form['account']['pass']['#type'] = 'hidden';
                $form['account']['pass']['#value'] = user_password();
            }
            
            if (isset($form['account']['name'])) {
                $form['account']['name']['#default_value'] = $user;
            }
            if (isset($form['account']['mail'])) {
                $form['account']['mail']['#default_value'] = $mail;
            }

            if (isset($form['field_first_name']['#language']) &&
                isset($form['field_first_name'][($form['field_first_name']['#language'])][0]['value'])) {
                $form['field_first_name'][($form['field_first_name']['#language'])][0]['value']['#default_value'] = $fname;
            }
            if (isset($form['field_last_name']['#language']) &&
                isset($form['field_last_name'][($form['field_last_name']['#language'])][0]['value'])) {
                $form['field_last_name'][($form['field_last_name']['#language'])][0]['value']['#default_value'] = $lname;
            }
            if (isset($form['field_name']['#language']) &&
                isset($form['field_name'][($form['field_name']['#language'])][0]['value'])) {
                $form['field_name'][($form['field_name']['#language'])][0]['value']['#default_value'] = $name;
            }
            if (!empty($bday) && isset($form['field_date_of_birth']['#language']) &&
                isset($form['field_date_of_birth'][($form['field_date_of_birth']['#language'])][0]['value'])) {
                $form['field_date_of_birth'][($form['field_date_of_birth']['#language'])][0]['value']['#default_value'] = $bday;
            }
        }
        
        $form['azure_acs_data'] = array(
          '#type' => 'value',
          '#value' => $_SESSION['azure_acs_data'],
        );
    }
    
    if(isset($form['subscription'])) {
        // Stripe subscription form alter

        $existingCustomer = isset($form['customer_id']) && !empty($form['customer_id']['#value']);
        
        if($existingCustomer) {
            $form['subscription']['#title'] = "Update your monthly donation";        
        }
        else {
            $form['subscription']['#title'] = "Please consider a monthly donation to oSTEM";        
        }
        
        if(isset($form['subscription']['plan_id'])) {
            $oldTitle = $form['subscription']['plan_id']['#title'];
            $form['subscription']['plan_id']['#title'] = str_replace('Subscription Plan', 'Contribution Level', $oldTitle);
            
            
            foreach($form['subscription']['plan_id']['#options'] as $optId => $optDisplay) {
                if($optDisplay == "--") {
                    if(!$existingCustomer) {
                        $form['subscription']['plan_id']['#options'][$optId] = "No Contribution";                    
                    }
                    else if($existingCustomer && $form['new_subscription']['#value']) {
                        unset($form['subscription']['plan_id']['#options'][$optId]);
                    }
                }
            }
        }
    }
}