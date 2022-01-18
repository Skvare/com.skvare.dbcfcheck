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
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function dbcfcheck_civicrm_xmlMenu(&$files) {
  _dbcfcheck_civix_civicrm_xmlMenu($files);
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
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function dbcfcheck_civicrm_postInstall() {
  _dbcfcheck_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function dbcfcheck_civicrm_uninstall() {
  _dbcfcheck_civix_civicrm_uninstall();
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
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function dbcfcheck_civicrm_disable() {
  _dbcfcheck_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function dbcfcheck_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _dbcfcheck_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function dbcfcheck_civicrm_managed(&$entities) {
  _dbcfcheck_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function dbcfcheck_civicrm_caseTypes(&$caseTypes) {
  _dbcfcheck_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function dbcfcheck_civicrm_angularModules(&$angularModules) {
  _dbcfcheck_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function dbcfcheck_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _dbcfcheck_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function dbcfcheck_civicrm_entityTypes(&$entityTypes) {
  _dbcfcheck_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_thems().
 */
function dbcfcheck_civicrm_themes(&$themes) {
  _dbcfcheck_civix_civicrm_themes($themes);
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
    $html .= '<tr><td colspan="2">These field(s) column are not present in actual custom table, you need to delete these fields</td></tr>';
    $html .= '</table>';

    if (!empty($html)) {
      $messages[] =
        new CRM_Utils_Check_Message(
          __FUNCTION__,
          $html,
          ts('Custom Field Checker'),
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
  if (get_class($page) == 'CRM_Custom_Page_Field') {
    $numberOfFields = civicrm_api3('Setting', 'getvalue', [
      'name' => "dbcfcheker_number_of_cf_fields",
      'group' => 'DB Custom Field Checker Settings',
    ]);
    $currentCount = '';
    try {
      $currentCount = civicrm_api3('CustomField', 'getcount', [
        'custom_group_id' => $page->getVar('_gid'),
      ]);
    }
    catch (CiviCRM_API3_Exception $exception) {

    }
    if (!empty($numberOfFields) && !empty($currentCount)) {
      if ($currentCount >= $numberOfFields) {
        $template = CRM_Core_Smarty::singleton();
        $message = ts('Adding New fields is disabled for this custom group, Number of custom field allowed on each custom groups are %1, you have reached to the limit on this custom group.', [1 => $numberOfFields]);
        $template->assign('disabled_new_field_message', $message);
        $template->assign('disabled_new_field', TRUE);
      }
    }
  }
}
