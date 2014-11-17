<?php
return [

  /**
   * BASE CONFIGS
   */

  'base' => [
    // Pass the endpoints directory.
    //   API routes will be loaded in {endpoints}/{api_version}/*.php
    'endpoints' => APP_DIR.'endpoints',
    'api_version' => 'v1',
  ],

  /**
   * SECURITY
   */
  'security' => [
    'cors'      => true,
  ],

  /**
   * DATABASE
   */
  'database' => [
    'enable'    => false,
    'DSN'       => false,
    // If you provide a DSN other parameters will be ignored   
    'name'      => 'testdb',
    'host'      => 'localhost',
    'port'      => 3306,
    'user'      => 'root',
    'password'  => 'root',
  ],

  /**
   * EXTRA
   */
  'extra' => [
    'timezone'  => 'Europe/Rome',
  ],
];