<?php
use CRM_Dbcfcheck_ExtensionUtil as E;

/**
 * Job.Customfieldcheck API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/api-architecture/
 */
function _civicrm_api3_job_Customfieldcheck_spec(&$spec) {
}

/**
 * Job.Customfieldcheck API
 *
 * @param array $params
 *
 * @return array
 *   API result descriptor
 *
 * @see civicrm_api3_create_success
 *
 * @throws API_Exception
 */
function civicrm_api3_job_Customfieldcheck($params) {
  $sqlQuery = "select id, title, table_name from civicrm_custom_group";
  $dao = CRM_Core_DAO::executeQuery($sqlQuery, CRM_Core_DAO::$_nullArray);
  $listFieldsLabel = [];
  while ($dao->fetch()) {
    $listFields = $tableFields = [];
    // get the fields of each Custom Group
    $sqlQuery = "SELECT id, label, column_name
          FROM `civicrm_custom_field`
          where custom_group_id = " . $dao->id;

    $dao2 = CRM_Core_DAO::executeQuery($sqlQuery, CRM_Core_DAO::$_nullArray);

    while ($dao2->fetch()) {
      $listFields[$dao2->id] = $dao2->column_name;
    }
    // get column present on actual custom table through information_schema
    $sqlQuery = "SELECT column_name
      FROM information_schema.columns WHERE table_schema = database()
      AND table_name = %1
      ORDER BY ordinal_position ASC";
    $inputTable = [1 => [$dao->table_name, 'String']];
    $dao3 = CRM_Core_DAO::executeQuery($sqlQuery, $inputTable);
    while ($dao3->fetch()) {
      if ($dao3->column_name == 'id' || $dao3->column_name == 'entity_id') {
        continue;
      }
      $tableFields[] = $dao3->column_name;
    }

    foreach ($listFields as $listFieldId => $fieldColumnName) {
      if (!in_array($fieldColumnName, $tableFields)) {
        // Missing Column in the Table
        $listFieldsLabel[$listFieldId] = [
          'customGroup' => $dao->title,
          'customField' => $dao2->label,
        ];
      }
    }
  }
  // update the message to show in UI alert area.
  CRM_Dbcfcheck_Utils::showAlert($listFieldsLabel);

  return civicrm_api3_create_success(TRUE, $params, 'Job', 'Customfieldcheck');
}
