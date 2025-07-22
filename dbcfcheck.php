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
 *
 * Monitors custom field pages and disables new field creation when
 * the table row size approaches MySQL's limit of 65,535 bytes.
 */
function dbcfcheck_civicrm_pageRun(&$page) {
  if (get_class($page) == 'CRM_Custom_Page_Field' && $page->getVar('_gid')) {
    try {
      $customGroupId = $page->getVar('_gid');
      $rowSize = (int)CRM_Dbcfcheck_Utils::getRowSizeOfTable($customGroupId);

      // Set conservative limit: 1000 bytes buffer from MySQL's 65535 limit
      $maxRowSize = 65535;
      $buffer = 1000;
      $threshold = $maxRowSize - $buffer;

      if ($rowSize > $threshold) {
        $template = CRM_Core_Smarty::singleton();
        $percentUsed = round(($rowSize / $maxRowSize) * 100, 1);

        $message = ts('Adding new fields is disabled for this custom group. Current row size: %1 bytes (%2% of MySQL limit). Adding new fields may exceed the maximum row size of %3 bytes, causing SQL errors in searches and transactions.', [
          1 => number_format($rowSize),
          2 => $percentUsed,
          3 => number_format($maxRowSize)
        ]);

        $template->assign('disabled_new_field_message', $message);
        $template->assign('disabled_new_field', TRUE);
        $template->assign('current_row_size', $rowSize);
        $template->assign('max_row_size', $maxRowSize);
        $template->assign('row_size_percent', $percentUsed);
      }
    }
    catch (Exception $e) {
      // Log error but don't break the page
      Civi::log()->error('DBCFCheck: Error calculating row size for custom group ' . $customGroupId . ': ' . $e->getMessage());
    }
  }
}
