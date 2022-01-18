<?php

use CRM_Dbcfcheck_ExtensionUtil as E;

class CRM_Dbcfcheck_Utils {
  /**
   * Function to show alert Message.
   * @param $messages
   */
  public static function showAlert($messages) {
    CRM_Dbcfcheck_Utils::setAlertMessage($messages);
  }

  /**
   * @return mixed
   */
  public static function getAlertMessage() {
    $domainID = CRM_Core_Config::domainID();
    $settings = Civi::settings($domainID);

    return $settings->get('job_alert_cf_message');
  }

  /**
   * @param $messages
   */
  public static function setAlertMessage($messages) {
    $domainID = CRM_Core_Config::domainID();
    $settings = Civi::settings($domainID);
    $settings->set('job_alert_cf_message', $messages);
  }
}