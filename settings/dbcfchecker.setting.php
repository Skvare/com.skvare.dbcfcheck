<?php

use CRM_Dbcfcheck_ExtensionUtil as E;
return [
  'dbcfcheker_number_of_cf_fields' => [
    'group_name' => E::ts('DB Custom Field Checker Settings'),
    'group' => 'dbcfcheker',
    'name' => 'dbcfcheker_number_of_cf_fields',
    'type' => 'Integer',
    'add' => '5.27',
    'default' => '60',
    'title' => E::ts('Number of Custom Fields on Custom Group'),
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => E::ts('Number of Custom fields allowed to create on each custom group, to avoid any database errors.'),
    'help_text' => E::ts('Number of Custom fields allowed to create on each custom group, to avoid any database errors.'),
    'html_type' => 'Text',
    'html_attributes' => [
      'size' => 50,
      'readonly' => 'true',
    ],
  ],
];
