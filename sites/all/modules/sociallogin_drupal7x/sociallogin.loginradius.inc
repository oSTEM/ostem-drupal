<?php

/**
 * @file
 * Sdk page callback file for the SocialLogin module.
 * This class should be used only for Drupal site.
 */

class LoginRadius {

  public $isauthenticated, $userprofile;

  /**
   * Function to handle LoginRadius request for Login interface.
   */
  public function getapi($apisecrete) {
    $isauthenticated = FALSE;
    global $base_url;
    if (isset($_REQUEST['token'])) {
      $validateurl = "http://hub.loginradius.com/userprofile.ashx?token=" . $_REQUEST['token'] . "&apisecrete=" . $apisecrete . "";
      $headers = array(
        'Referer' => $base_url,
        'User-Agent' => 'sociallogin_Module',
      );
      $options = array(
        'headers' => $headers,
        'max_redirects' => 5,
      );
      $result = drupal_http_request($validateurl, $options);
      $userprofile = json_decode($result->data);
      if (isset($userprofile->ID) && $userprofile->ID != '') {
        $this->isauthenticated = TRUE;
        return $userprofile;
      }
    }
  }

}

class LoginRadiusAuth {

  public $isauth, $userauth, $isHttps, $height, $width, $error;

  /**
   * Function to authenticate LoginRadius API key and secret.
   */
  public function auth($apikey, $apisecrete) {
    global $base_url;
    unset($this->error);

    if (isset($apikey)) {
      $apikey = trim($apikey);
      $apisecrete = trim($apisecrete);
      $validateurl = "https://hub.loginradius.com/getappinfo/$apikey/$apisecrete";
      $headers = array(
        'Referer' => $base_url,
        'User-Agent' => 'sociallogin_Module',
      );
      $options = array(
        'headers' => $headers,
        'max_redirects' => 5,
      );

      $result = drupal_http_request($validateurl, $options);

      if (!isset($result->error)) {
        $userauth = json_decode($result->data);
      }
      else {
        $this->error = $result->error;
        return FALSE;
      }
      if (isset($userauth->IsValid) && $userauth->IsValid) {
        $this->isauth = TRUE;
        return $userauth;
      }
      else {
        $this->error = t('API key settings recognized as invalid.');
      }
    }

    return FALSE;
  }

}
