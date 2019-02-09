<?php

namespace App\Http\Controllers;

require_once dirname(__DIR__)."/app/Http/Controllers/Constants/ids.constants.php";

date_default_timezone_set('America/Sao_Paulo');

header("Access-Control-Allow-Origin: *");

ini_set('max_execution_time', -1);

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('api/v1/doc', function() use ($router) {
    return "Aguarde...";
});


/** -------------------- User -------------------- */

$router->group(['prefix' => 'api/v1/'], function($router){

    $router->get('user/{id}','UserController@getUser');

    $router->get('user/','UserController@getAllUsers');

    $router->post('user','UserController@createUser');

    $router->put('user/{id}','UserController@updateUser');

    $router->delete('user/{id}','UserController@deleteUser');
});


/** -------------------- Intelligence Category -------------------- */

$router->group(['prefix' => 'api/v1/'], function () use ($router) {

    $router->get('intelligenceCategory','IntelligenceCategoryController@getAllIntelligenceCategories');

    $router->get('intelligenceCategory/{id}','IntelligenceCategoryController@getIntelligenceCategory');

    $router->post('intelligenceCategory','IntelligenceCategoryController@createIntelligenceCategory');

    $router->put('intelligenceCategory/{id}','IntelligenceCategoryController@updateIntelligenceCategory');

    $router->delete('intelligenceCategory/{id}','IntelligenceCategoryController@deleteIntelligenceCategory');
});


/** -------------------- Intelligence -------------------- */

$router->group(['prefix' => 'api/v1/'], function($router){

    $router->get('intelligence/{id}','IntelligenceController@getIntelligence');

    $router->post('intelligence','IntelligenceController@createIntelligence');

    $router->post('sheetColumns','IntelligenceController@getSheetColumns');

    $router->put('intelligence/{id}','IntelligenceController@updateIntelligence');

    $router->delete('intelligence/{id}','IntelligenceController@deleteIntelligence');

    $router->get('intelligence/user/{id}','IntelligenceController@getAllIntelligencesPerUser');

    $router->get('processInputs/{inputs}/intelligenceId/{intelligenceId}','IntelligenceController@getOutput');

});


/** -------------------- Intelligence Log -------------------- */

$router->group(['prefix' => 'api/v1/'], function($router){

    $router->get('intelligenceLog/{id}','IntelligenceLogController@getIntelligenceLog');

    $router->post('intelligenceLog','IntelligenceLogController@createIntelligenceLog');

    $router->get('intelligenceLog/intelligence/{id}','IntelligenceLogController@getAllIntelligenceLogsPerIntelligence');
});


/** -------------------- Type Log -------------------- */

$router->group(['prefix' => 'api/v1/'], function($router){

    $router->get('logType/{id}','LogTypeController@getLogType');

    $router->post('logType','LogTypeController@createLogType');

    $router->put('logType/{id}','LogTypeController@updateLogType');

    $router->delete('logType/{id}','LogTypeController@deleteLogType');

    $router->get('logType','LogTypeController@getAllLogTypes');
});


/** -------------------- Classification -------------------- */

$router->group(['prefix' => 'api/v1/'], function($router){

    $router->get('classification/intelligence/{id}','ClassificationController@getClassificationsPerIntelligence');

});


/** -------------------- Intelligence File Types -------------------- */

$router->group(['prefix' => 'api/v1/'], function($router){

    $router->get('intelligenceDataType/','IntelligenceDataTypeController@getIntelligenceDataTypes');

});
