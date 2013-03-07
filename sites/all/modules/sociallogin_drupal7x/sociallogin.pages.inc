<?php
/**
 * @file
 * User page callbacks for the sociallogin module.
 */

/**
 * Menu callback Manage sociallogin identities for the specified user.
 */
function sociallogin_user_identities($account) {
  drupal_set_title(format_username($account));
  $header = array(t('Social Login'), t('Operations'));
  $rows = array();
  $result = db_query("SELECT * FROM {authmap} WHERE module='sociallogin' AND uid=:uid", array(':uid' => $account->uid));
  foreach ($result as $identity) {
    $rows[] = array(
      check_plain($identity->authname),
      l(t('Delete'),
      'user/' . $account->uid . '/sociallogin/delete/' . $identity->aid),
    );
  }
  $build['sociallogin_table'] = array(
    '#theme' => 'table',
    '#header' => $header,
    '#rows' => $rows,
  );
  return $build;
}

/**
 * Menu callback Delete the specified sociallogin identity from the system.
 */
function sociallogin_user_delete_form($form, $form_state, $account, $aid = 0) {
  $authname = db_query("SELECT authname FROM {authmap} WHERE uid = :uid AND aid = :aid AND module = 'sociallogin'", array(
    ':uid' => $account->uid,
    ':aid' => $aid,
  ))
  ->fetchField();
  return confirm_form(array(), t('Are you sure you want to delete the Social Login %authname for %user?', array('%authname' => $authname, '%user' => $account->name)), 'user/' . $account->uid . '/sociallogin');
}

/**
 * Menu callback Delete form submit for sociallogin identity from the system.
 */
function sociallogin_user_delete_form_submit($form, &$form_state) {
  $query = db_delete('authmap')
    ->condition('uid', $form_state['build_info']['args'][0]->uid)
    ->condition('aid', $form_state['build_info']['args'][1])
    ->condition('module', 'sociallogin')
    ->execute();
  if ($query) {
    drupal_set_message(t('Social Login identity deleted.'));
  }
  $form_state['redirect'] = 'user/' . $form_state['build_info']['args'][0]->uid . '/sociallogin';
}
