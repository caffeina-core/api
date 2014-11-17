<?php

class API {
  
  public static function error($message,$status=501){
    Event::trigger('api.error',[$message,$status]);
    Response::status($status);
    Response::json([
      'error'   => [
        'type'     => 'fatal',
        'status'   => $status,
        'message'  => $message,
      ],
    ]);
    Response::send();
    exit;
  }

  public static function run(callable $main = null){
    $API_VERS = Options::get('base.api_version','');
    // Load Routes
    $route_file = rtrim(Options::get('base.endpoints',APP_DIR.'/routes.php'),'/');
    // Single file
    if (is_file($route_file)){
      include $route_file;
    } else {
      // Load directory
      $route_file .= rtrim('/'.$API_VERS,'/');
      if (is_dir($route_file)){
          Route::group("/$API_VERS",function() use ($route_file,$API_VERS){
            Event::trigger('api.before');
            array_map(function($f){include $f;},glob($route_file.'/*.php'));
            Event::trigger('api.after');
          });
      }
    }
    Event::trigger('api.run',[Options::get('base.api_version','')]);
    if ($main) $main();
    Route::dispatch();
    Response::send();
  }

}