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

  /**
   * Function get Row size for custom group table.
   *
   * @param int $customGroupId The custom group ID
   * @return int|null The estimated row size in bytes, or null on error
   * @throws CRM_Core_Exception
   */
  public static function getRowSizeOfTable($customGroupId) {
    if (empty($customGroupId) || !is_numeric($customGroupId)) {
      throw new CRM_Core_Exception('Invalid custom group ID provided');
    }

    // Check cache first (cache for 5 minutes)
    $fieldCount = civicrm_api3('CustomField', 'getcount', [
      'custom_group_id' => $customGroupId,
    ]);
    $cacheKey = "dbcfcheck_rowsize_{$customGroupId}_{$fieldCount}";
    $cache = CRM_Utils_Cache::singleton();
    $cachedSize = $cache->get($cacheKey);

    if ($cachedSize !== NULL) {
      return (int)$cachedSize;
    }

    try {
      $config = CRM_Core_Config::singleton();
      $dsnArray = DB::parseDSN(CRM_Utils_SQL::autoSwitchDSN($config->dsn));
      $database = $dsnArray['database'];

      $tableName = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_CustomGroup', $customGroupId, 'table_name');

      if (empty($tableName)) {
        throw new CRM_Core_Exception("No table found for custom group ID: {$customGroupId}");
      }

      // Escape values for SQL injection protection
      $database = CRM_Core_DAO::escapeString($database);
      $tableName = CRM_Core_DAO::escapeString($tableName);

      $sql = "SELECT SUM(col_sizes.col_size) AS EST_MAX_ROW_SIZE
          FROM (
          SELECT
              cols.TABLE_SCHEMA,
              cols.TABLE_NAME,
              cols.COLUMN_NAME,
              CASE cols.DATA_TYPE
                  WHEN 'tinyint' THEN 1
                  WHEN 'smallint' THEN 2
                  WHEN 'mediumint' THEN 3
                  WHEN 'int' THEN 4
                  WHEN 'bigint' THEN 8
                  WHEN 'float' THEN IF(cols.NUMERIC_PRECISION > 24, 8, 4)
                  WHEN 'double' THEN 8
                  WHEN 'decimal' THEN ((cols.NUMERIC_PRECISION - cols.NUMERIC_SCALE) DIV 9)*4  + (cols.NUMERIC_SCALE DIV 9)*4 + CEIL(MOD(cols.NUMERIC_PRECISION - cols.NUMERIC_SCALE,9)/2) + CEIL(MOD(cols.NUMERIC_SCALE,9)/2)
                  WHEN 'bit' THEN (cols.NUMERIC_PRECISION + 7) DIV 8
                  WHEN 'year' THEN 1
                  WHEN 'date' THEN 3
                  WHEN 'time' THEN 3 + CEIL(cols.DATETIME_PRECISION /2)
                  WHEN 'datetime' THEN 5 + CEIL(cols.DATETIME_PRECISION /2)
                  WHEN 'timestamp' THEN 4 + CEIL(cols.DATETIME_PRECISION /2)
                  WHEN 'char' THEN cols.CHARACTER_OCTET_LENGTH
                  WHEN 'binary' THEN cols.CHARACTER_OCTET_LENGTH
                  WHEN 'varchar' THEN IF(cols.CHARACTER_OCTET_LENGTH > 255, 2, 1) + cols.CHARACTER_OCTET_LENGTH
                  WHEN 'varbinary' THEN IF(cols.CHARACTER_OCTET_LENGTH > 255, 2, 1) + cols.CHARACTER_OCTET_LENGTH
                  WHEN 'tinyblob' THEN 9
                  WHEN 'tinytext' THEN 9
                  WHEN 'blob' THEN 10
                  WHEN 'text' THEN 10
                  WHEN 'mediumblob' THEN 11
                  WHEN 'mediumtext' THEN 11
                  WHEN 'longblob' THEN 12
                  WHEN 'longtext' THEN 12
                  WHEN 'enum' THEN 2
                  WHEN 'set' THEN 8
                  ELSE 0
              END AS col_size
          FROM INFORMATION_SCHEMA.COLUMNS cols
        WHERE TABLE_NAME = '{$tableName}'
        AND TABLE_SCHEMA = '{$database}'
      ) AS col_sizes
      GROUP BY col_sizes.TABLE_SCHEMA, col_sizes.TABLE_NAME";

      $rowSize = CRM_Core_DAO::singleValueQuery($sql);

      if ($rowSize === NULL) {
        // Table might not exist or have no columns
        Civi::log()->warning("DBCFCheck: No row size calculated for table {$tableName} (custom group {$customGroupId})");
        return 0;
      }

      // Cache the result for 5 minutes
      $cache->set($cacheKey, $rowSize, 300);

      return (int)$rowSize;
    }
    catch (Exception $e) {
      Civi::log()->error("DBCFCheck: Error calculating row size for custom group {$customGroupId}: " . $e->getMessage());
      throw new CRM_Core_Exception("Error calculating table row size: " . $e->getMessage());
    }
  }
}
