<?php
use CRM_DonCorps_ExtensionUtil as E;

/**
 * Log.Purgelogscivirule API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/api-architecture/
 */
function _civicrm_api3_log_Purgelogscivirule_spec(&$spec) {
  $spec['magicword']['api.required'] = 0;
}

/**
 * Log.Purgelogscivirule API
 *
 * @param array $params
 *
 * @return array
 *   API result descriptor
 *
 * @see civicrm_api3_create_success
 *
 * @throws CRM_Core_Exception
 */
function civicrm_api3_log_Purgelogscivirule($params) {

  CRM_Core_DAO::executeQuery("DELETE FROM `civirule_rule_log` WHERE `log_date` < (CURDATE() - INTERVAL 10 DAY)");

  $returnValues = [];
  return civicrm_api3_create_success($returnValues, $params, 'DonCorps', 'Purgelogscivirule');


}
