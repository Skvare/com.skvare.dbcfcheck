<?php

require_once 'dbcfcheck.civix.php';
// phpcs:disable
use CRM_Dbcfcheck_ExtensionUtil as E;
// phpcs:enable

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function dbcfcheck_civicrm_config(&$config) {
  _dbcfcheck_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function dbcfcheck_civicrm_install() {
  _dbcfcheck_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function dbcfcheck_civicrm_enable() {
  _dbcfcheck_civix_civicrm_enable();
}

/**
 * @param $messages
 * @param array $statusNames
 * @param bool $includeDisabled
 */
function dbcfcheck_civicrm_check(&$messages, $statusNames = [], $includeDisabled = FALSE) {
  // get the all messages generated using scheduled job
  $job_alert_message = CRM_Dbcfcheck_Utils::getAlertMessage();
  $html = '';
  if ($job_alert_message) {
    // format the message
    $html = '<table>';
    $html .= '<tr><th>Custom Group</th><th>Missing field column</th></tr>';
    foreach ($job_alert_message as $fieldID => $fieldDetail) {
      $html .= '<tr><td>' . $fieldDetail['customGroup'] . '</td><td> (' . $fieldID . ') ' . $fieldDetail['customField'] . '</td></tr>';
    }
    $html .= '<tr><td colspan="2">These field(s) column are not present in actual custom table, you need to delete these fields.</td></tr>';
    $html .= '</table>';

    if (!empty($html)) {
      $messages[] =
        new CRM_Utils_Check_Message(
          __FUNCTION__,
          $html,
          E::ts('Custom Field Checker'),
          \Psr\Log\LogLevel::ERROR,
          'fa-bug'
        );
    }
  }
}

/**
 * Implementation of hook_civicrm_pageRun
 */
function dbcfcheck_civicrm_pageRun(&$page) {
  if (get_class($page) == 'CRM_Custom_Page_Field' && $page->getVar('_gid')) {
    $rowSize = (int)CRM_Dbcfcheck_Utils::getRowSizeOfTable($page->getVar('_gid'));
    // set default limit to 1000 char minus from 65535.
    if ($rowSize > 64535) {
      $template = CRM_Core_Smarty::singleton();
      $message = ts('Adding New fields is disabled for this custom group, based on the MySQL Row size limit of max 65535 and the type of fields used in this custom group, current calculated row size is %1, adding new fields into the custom group will fail, which leads to a SQL error in search, online transaction.', [1 => $rowSize]);
      $template->assign('disabled_new_field_message', $message);
      $template->assign('disabled_new_field', TRUE);
    }
  }
}
