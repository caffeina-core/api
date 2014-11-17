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
    $route = rtrim(Options::get('base.endpoints',APP_DIR.'/routes.php'),'/');
    // Single file
    if (is_file($route)){
      include $route;
    } else {
      // Load directory
      foreach((array)$API_VERS as $API_NAMESPACE){
        $routes = $route . rtrim('/'.$API_NAMESPACE,'/');
        if (is_dir($routes)){
            Route::group("/$API_NAMESPACE",function() use ($routes,$API_NAMESPACE){
              Event::trigger('api.before');
              array_map(function($f){include $f;},glob($routes.'/*.php'));
              Event::trigger('api.after');
            });
        }        
      }
    }
    Event::trigger('api.run',[Options::get('base.api_version','')]);
    if ($main) $main();
    Route::dispatch();
    Response::send();
  }

}