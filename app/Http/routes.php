<?php
/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('/', ['as' => 'home', 'uses' => 'DashboardController@home']);
Route::get('/home', ['as' => 'home.home', 'uses' => 'DashboardController@home']);



/*########### AUTH ROUTES STARTS #############*/
Route::auth();
Route::get('/login',function(){
    return redirect()->route('auth.login');
});
Route::get('/auth/login', ['as' => 'auth.login', 'uses' => 'Auth\AuthController@authLoginShow','middleware' => ['guest']]);
Route::post('/auth/login', ['as' => 'auth.login', 'uses' => 'Auth\AuthController@authenticateUser','middleware' => ['guest']]);
/*########### AUTH ROUTES ENDS #############*/


Route::group(['middleware' => ['auth']], function() {

    Route::group(['prefix' => 'resources'], function () {
        Route::get('list', ['as' => 'resources.index', 'uses' => 'UserController@index', 'middleware' => ['permission:resource-list-allocate|resource-create|resource-edit|resource-disable-enable']]);
        Route::get('create', ['as' => 'resources.create', 'uses' => 'UserController@create', 'middleware' => ['permission:resource-create']]);
        Route::post('store', ['as' => 'resources.store', 'uses' => 'UserController@store', 'middleware' => ['permission:resource-create']]);
        Route::get('show/{id}', ['as' => 'resources.show', 'uses' => 'UserController@show', 'middleware' => ['permission:resource-list-allocate|view-my-project-calender']]);
        Route::get('edit/{id}', ['as' => 'resources.edit', 'uses' => 'UserController@edit', 'middleware' => ['permission:resource-edit']]);
        Route::patch('{id}', ['as' => 'resources.update', 'uses' => 'UserController@update', 'middleware' => ['permission:resource-edit']]);
        Route::post('{id}', ['as' => 'resources.disable', 'uses' => 'UserController@disable', 'middleware' => ['permission:resource-disable-enable']]);
        Route::get('getallocations', ['as' => 'resources.getallocations', 'uses' => 'UserController@getAllocations']);
        Route::get('getprojects', ['as' => 'resources.getprojects', 'uses' => 'UserController@getProjects']);
        Route::get('getbooking/{id}', ['as' => 'resources.getbooking', 'uses' => 'UserController@editBooking', 'middleware' => ['permission:project-edit']]);
        Route::get('deletebooking/{id}', ['as' => 'resources.deletebooking', 'uses' => 'UserController@deleteBooking', 'middleware' => ['permission:project-edit']]);
        Route::get('getskills/{id}', 'UserController@getSkills');
        Route::get('view/{id}', ['as' => 'resources.view', 'uses' => 'UserController@getView', 'middleware' => ['permission:resource-list-allocate']]);

        Route::get('csv/upload', ['as' => 'resources.csv.upload', 'uses' => 'UserController@csvUpload', 'middleware' => ['role:admin']]);
        Route::post('csv/upload', ['as' => 'resources.csv.upload', 'uses' => 'UserController@csvUploadStore', 'middleware' => ['role:admin']]);

        Route::get('profile/{id}', ['as' => 'resources.profile', 'uses' => 'UserController@profile']);
        Route::get('profile/current-allocation-data/{id}', ['as' => 'resources.currentallocationdata', 'uses' => 'UserController@currentAllocoationData']);

        Route::get('list/search', ['as' => 'resource.ajax.filter', 'uses'=>'UserController@ajaxFilter']);

    });


    Route::group(['prefix' => 'roles'], function () {
        Route::get('/', ['as' => 'roles.index', 'uses' => 'RoleController@index', 'middleware' => ['permission:role-list|role-create|role-edit|role-delete']]);
        Route::get('create', ['as' => 'roles.create', 'uses' => 'RoleController@create', 'middleware' => ['permission:role-create']]);
        Route::post('create', ['as' => 'roles.store', 'uses' => 'RoleController@store', 'middleware' => ['permission:role-create']]);
        Route::get('{id}', ['as' => 'roles.show', 'uses' => 'RoleController@show']);
        Route::get('edit/{id}', ['as' => 'roles.edit', 'uses' => 'RoleController@edit', 'middleware' => ['permission:role-edit']]);
        Route::patch('{id}', ['as' => 'roles.update', 'uses' => 'RoleController@update', 'middleware' => ['permission:role-edit']]);
        Route::delete('{id}', ['as' => 'roles.destroy', 'uses' => 'RoleController@destroy', 'middleware' => ['permission:role-delete']]);

        Route::get('list/search', ['as' => 'role.ajax.filter', 'uses'=>'RoleController@ajaxFilter']);
    });

    Route::group(['prefix' => 'project'], function () {
        Route::get('list', ['as' => 'project.list', 'uses' => 'ProjectController@listProject', 'middleware' => ['permission:project-list-allocate|project-create|project-edit|project-delete']]);
        Route::post('store', ['as' => 'project.store', 'uses' => 'ProjectController@createProject', 'middleware' => ['permission:project-create']]);
        Route::delete('{id}', ['as' => 'project.destroy', 'uses' => 'ProjectController@destroy', 'middleware' => ['permission:project-delete']]);
        Route::get('edit/{id}', ['as' => 'project.edit', 'uses' => 'ProjectController@getEdit', 'middleware' => ['permission:project-edit']]);
        Route::post('update', ['as' => 'project.update', 'uses' => 'ProjectController@postUpdate', 'middleware' => ['permission:project-edit']]);
        Route::get('show/{id}', ['as' => 'project.show', 'uses' => 'ProjectController@getShow', 'middleware' => ['permission:project-edit|project-list-allocate']]);
        Route::get('view/{id}', ['as' => 'project.view', 'uses' => 'ProjectController@getView', 'middleware' => ['permission:project-list-allocate']]);

        Route::post('resources/{projectId}', ['as' => 'project.resources', 'uses' => 'ProjectController@projectResources', 'middleware' => ['permission:project-edit|project-list-allocate']]);
        Route::get('allocations/{projectId}', ['as' => 'project.allocations', 'uses' => 'ProjectController@projectAllocations', 'middleware' => ['permission:project-edit|project-list-allocate']]);

        Route::post('{projectId}/allocate/resource', ['as' => 'project.allocate.resources', 'uses' => 'ProjectController@allocateProjectResource', 'middleware' => ['permission:project-edit|project-list-allocate|resource-list-allocate']]);
        Route::post('{projectId}/allocate/edit/resource', ['as' => 'project.allocate.edit.resources', 'uses' => 'ProjectController@editAllocatedProjectResource', 'middleware' => ['permission:project-edit|project-list-allocate|resource-list-allocate']]);
        Route::post('{projectId}/allocate/delete/resource', ['as' => 'project.allocate.delete.resources', 'uses' => 'ProjectController@deleteAllocatedProjectResource', 'middleware' => ['permission:project-edit|project-list-allocate|resource-list-allocate']]);
        Route::post('allocation/{allocationId}/description', ['as' => 'project.allocation.description', 'uses' => 'ProjectController@getAllocationDescription', 'middleware' => ['permission:project-edit|project-list-allocate|resource-create|resource-list-allocate']]);

        Route::get('list/search', ['as' => 'project.ajax.filter', 'uses'=>'ProjectController@ajaxFilter']);
    });

    Route::group(['prefix' => 'reports', 'as'=>'reports.','middleware' => ['permission:view-report']], function () {

        Route::get('project', ['as' => 'project.based', 'uses' => 'ReportController@projectBasedReport']);
        Route::post('project/fetch', ['as' => 'project.based.fetch', 'uses' => 'ReportController@fetchProjectBasedReport']);
        Route::get('project/export', ['as' => 'project.based.export', 'uses' => 'ReportController@exportProjectBasedReport']);

        Route::get('user', ['as' => 'user.based', 'uses' => 'ReportController@userBasedReport']);
        Route::post('user/fetch', ['as' => 'user.based.fetch', 'uses' => 'ReportController@fetchUserBasedReport']);
        Route::get('user/export', ['as' => 'user.based.export', 'uses' => 'ReportController@exportUserBasedReport']);

        Route::get('user/calender', ['as' => 'user.based.calender', 'uses' => 'ReportController@userBasedReportCalender']);
        Route::post('resources/', ['as' => 'user.based.resources', 'uses' => 'ReportController@userBasedReportResources', 'middleware' => ['permission:project-edit|project-list-allocate']]);
        Route::post('allocations', ['as' => 'user.based.allocations', 'uses' => 'ReportController@userBasedReportAllocations', 'middleware' => ['permission:project-edit|project-list-allocate']]);

        Route::get('skill', ['as' => 'skill.based', 'uses' => 'ReportController@skillBasedReport']);
        Route::post('skill/fetch', ['as' => 'skill.based.fetch', 'uses' => 'ReportController@fetchSkillBasedReport']);
        Route::get('skill/export', ['as' => 'skill.based.export', 'uses' => 'ReportController@exportSkillBasedReport']);

        Route::get('tag', ['as' => 'tag.based', 'uses' => 'ReportController@tagBasedReport']);
        Route::post('tag/fetch', ['as' => 'tag.based.fetch', 'uses' => 'ReportController@fetchTagBasedReport']);
        Route::get('tag/export', ['as' => 'tag.based.export', 'uses' => 'ReportController@exportTagBasedReport']);

    });

    Route::group(['prefix' => 'dashboard','middleware' => ['permission:view-dashboard']], function () {
        Route::get('/', ['as' => 'dashboard', 'uses' => 'DashboardController@dashboard']);
        Route::post('skill/donut', ['as' => 'dashboard.skill.donut', 'uses' => 'DashboardController@getDashboardSkillDonutData']);
        Route::post('free/resources', ['as' => 'dashboard.free.resources', 'uses' => 'DashboardController@getDashboardFreeResources']);
        Route::post('active/projects', ['as' => 'dashboard.active.projects', 'uses' => 'DashboardController@getDashboardActiveProjects']);
    });

    Route::get('my-projects', ['as' => 'myProject', 'uses' => 'ProjectController@getMyProjects']);

});
Route::get('test', ['as' => 'test', 'uses' => 'ProjectController@test']);


Route::get('getTags', 'ProjectController@getTags');
Route::get('getSkills', 'ProjectController@getSkills');
Route::get('getManagers', 'ProjectController@getManagers');
Route::get('getDevelopers', 'ProjectController@getDevelopers');
