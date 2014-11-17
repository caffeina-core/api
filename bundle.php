<?php

// Sanity Check
if (!defined('APP_DIR')) throw new Exception('API server is not correctly configured.');

// Load Options
Options::loadPHP(APP_DIR.'/config.php');

// Security
date_default_timezone_set(Options::get('extra.timezone',date_default_timezone_get()));
if (Options::get('security.cors',true)) Response::enableCORS();

// Setup database
if (Options::get('database.enabled',false)) {
  if ($DSN = Options::get('database.DSN',false)){
    SQL::connect($DSN,
      Options::get('database.user',null),
      Options::get('database.password',null)
    );
  } else {
    SQL::connect(
      'mysql'
      .':host='.Options::get('database.host')
      .';dbname='.Options::get('database.name','test')
      .';port='.Options::get('database.port',3306),
      Options::get('database.user',null),
      Options::get('database.password',null)
    );

    // Enable UTF-8
    Event::on('core.sql.connect',function(){
      SQL::exec('SET NAMES "UTF8"');
    });
  }
}

// Setup Events
Event::on(404,function(){
  return API::error("Resource not found.",404);
});
