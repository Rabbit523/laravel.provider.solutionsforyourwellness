<?php
DB::enableQueryLog();
include(app_path().'/global_constants.php');
$api = app('Dingo\Api\Routing\Router');
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/
Route::get('/base/uploder','BaseController@saveCkeditorImages');
Route::post('/base/uploder','BaseController@saveCkeditorImages'); 
Route::group(array('prefix' => ''), function() {
	Route::group(array('middleware' => 'App\Http\Middleware\GuestAdmin','namespace'=>'admin'), function() {
		Route::get('','AdminLoginController@login');
		Route::any('login', [
			'as' => 'adminlogin',
			'uses' => 'AdminLoginController@login'
		]);
		Route::any('forgotpassword', [
			'as' => 'admin_forgot_password',
			'uses' => 'AdminLoginController@forgotpassword'
		]);
		Route::any('reset_password/{validstring}', [
			'as' => 'admin_reset_password',
			'uses' => 'AdminLoginController@resetPassword'
		]);		
	});

	Route::group(array('middleware' => 'App\Http\Middleware\AuthAdmin','namespace'=>'admin'), function() {
		Route::any('dashboard', [
			'as' => 'admindashboard',
			'uses' => 'AdminPagesController@showdashboard'
		]);
		Route::any('dashboard-graph', [
			'as' => 'admindashboard_graph',
			'uses' => 'AdminPagesController@GetAdminGraphData'
		]);
		// rotes for providers starts //
    Route::any('providers', [
  		'as' => 'providers',
  		'uses' => 'AdminProvidersController@index'
  	]);
    Route::any('ajaxloadprovider', [
		'as'   => 'ajaxloadprovider',
		'uses' => 'AdminProvidersController@ajaxloadprovider'
		]);
    Route::any('add-provider', [
    'as'   => 'add-provider',
    'uses' => 'AdminProvidersController@add'
    ]);
    Route::any('edit-provider/{id}', [
		'as' => 'edit-provider',
		'uses' => 'AdminProvidersController@edit'
		]);
    Route::any('provider-status/{id}', [
		'as' 	=> 'provider-status',
		'uses'  => 'AdminProvidersController@active_status'
		]);
	Route::any('provider-status-change/{id}', [
		'as' 	=> 'provider-status-change',
		'uses'  => 'AdminProvidersController@active_status_provider'
		]);
    Route::any('delete-providers/{id}', [
		'as' 	=> 'delete-providers',
		'uses'  => 'AdminProvidersController@delete'
		]);
		Route::any('delete-provider', [
		'as' 	=> 'delete-provider',
		'uses'  => 'AdminProvidersController@delete'
		]);
		Route::any('provider-details/{id}', [
		'as' => 'provider-details',
		'uses' => 'AdminProvidersController@setting'
		]);
		Route::any('change_provider_rate', [
		'as' => 'change_provider_rate',
		'uses' => 'AdminProvidersController@change_provider_rate'
		]);
		Route::any('edit_provider_calender', array(
			'as' => 'edit_provider_calender',
			'uses' => 'AdminProvidersController@edit_provider_calender'));
		Route::any('order-excel/{id}', [
			'as'	=>	'orderexcel',
			'uses'	=>	'AdminProvidersController@downloadExcel'
				]
		);
		Route::any('provider-calender/{id}', [
			'as'	=>	'provider-calender',
			'uses'	=>	'AdminProvidersController@provider_calender_view'
				]
		);
		Route::any('provider-finance-report/{id}', [
			'as'	=>	'provider-finance-report',
			'uses'	=>	'AdminProvidersController@provider_finance_view'
				]
		);
		Route::any('edit_provider_clockout', array(
			'as' => 'edit_provider_clockout',
			'uses' => 'AdminProvidersController@edit_provider_clockout'));
		Route::any('view_security_pin', array(
			'as' => 'view_security_pin',
			'uses' => 'AdminProvidersController@view_security_pin'));
		// rotes for providers end //
		
		
		/* admin timesheet routes starts */
		Route::any('timesheet/{id}', [
			'as'	=>	'timesheet_single_view',
			'uses'	=>	'AdminTimesheetController@TimesheetRecord'
				]
		);
		Route::any('timesheet', [
			'as'	=>	'timesheet_view',
			'uses'	=>	'AdminTimesheetController@Index'
				]
		);
		Route::any('ajax-timesheet', [
			'as'	=>	'timesheet_ajax_view',
			'uses'	=>	'AdminTimesheetController@AjaxLoad'
				]
		);
		Route::any('timesheet-download', [
			'as'	=>	'timesheet_download',
			'uses'	=>	'AdminTimesheetController@DownloadExcel'
				]
		);
		Route::any('timesheet-download/{id}', [
			'as'	=>	'timesheet_download_user',
			'uses'	=>	'AdminTimesheetController@DownloadExcel'
				]
		);
		Route::any('search-timesheet', [
			'as'	=>	'timesheet_user_search',
			'uses'	=>	'AdminTimesheetController@SearchTimeSheetUser'
				]
		);
		
		//Route::any('clinic_on_map/{id}/{userid}', 'ClinicsController@clinic_on_map');
		Route::any('clinic_on_map/{id}/{userid}', [
		'as' => 'clinic_on_map',
		'uses' => 'ClinicsController@clinic_on_map'
		]);

		Route::any('cut_time/{id}/{userid}', [
		'as' => 'cut_time',
		'uses' => 'ClinicsController@CutManualTime'
		]);

		Route::any('get_provider_location', [
		'as' => 'get_provider_location',
		'uses' => 'ClinicsController@get_provider_location'
		]); 
		
		/* admin timesheet routes ends */
		
		
		// rotes for clinics starts //
		Route::any('clinic-confirmation/{validstring}', [
			'as' => 'clinic_confirmation',
			'uses' => 'ClinicsController@clinic_confirm'
		]);
		Route::any('clinics', [
			'as' => 'clinics',
			'uses' => 'ClinicsController@index'
		]);
		Route::any('ajaxloadclinic', [
		'as'   => 'ajaxloadclinic',
		'uses' => 'ClinicsController@ajaxloadclinic'
		]);
		Route::any('add-clinic', [
		'as'   => 'add-clinic',
		'uses' => 'ClinicsController@add'
		]);
		Route::any('edit-clinic/{id}', [
		'as' => 'edit-clinic',
		'uses' => 'ClinicsController@edit'
		]);
		Route::any('clinic-status/{id}', [
		'as' 	=> 'clinic-status',
		'uses'  => 'ClinicsController@active_status'
		]);
		Route::any('edit-clinic-status/{user_id}/{id}', [
		'as' 	=> 'edit_clinic_status',
		'uses'  => 'AdminPagesController@UpdateClinicStatus'
		]);
		Route::any('delete-clinic/{id}', [
		'as' 	=> 'delete-clinic',
		'uses'  => 'ClinicsController@delete'
		]);
		Route::any('delete-clinic', [
		'as' 	=> 'delete-clinics',
		'uses'  => 'ClinicsController@delete'
		]);
		Route::any('check_asign_status', [
		'as' 	=> 'check_asign_status',
		'uses'  => 'ClinicsController@check_asign_status'
		]);
		Route::any('clinic-calender', [
			'as'	=>	'clinic-calender',
			'uses'	=>	'ClinicsController@clinic_calender_view'
				]
		);
		Route::any('asign-rule/{id}', [
			'as'	=>	'asign-rules',
			'uses'	=>	'ClinicsController@AsignRule'
				]
		);
		Route::any('clinic-calender-color', [
			'as'	=>	'clinic-calender-color',
			'uses'	=>	'ClinicsController@clinic_calender_color'
				]
		);
    // clinics routes end //

    Route::any('certifications', [
  		'as' => 'certifications',
  		'uses' => 'CertificationController@index'
  	]);
		Route::any('certifications/{id}', [
  		'as' => 'view-certificates',
  		'uses' => 'CertificationController@index'
  	]);
		Route::any('add-certificate', [
  		'as' => 'add-certificate',
  		'uses' => 'CertificationController@add'
  	]);
	Route::any('add-certificate/{id}', [
  		'as' => 'add_certificate_by_id',
  		'uses' => 'CertificationController@add'
  	]);
		Route::any('edit-certificates/{id}', [
		'as' => 'edit-certificates',
		'uses' => 'CertificationController@edit'
		]);
		Route::any('certificates-status/{id}', [
		'as' 	=> 'certificates-status',
		'uses'  => 'CertificationController@active_status'
		]);
		Route::any('delete-certificate/{id}', [
		'as' 	=> 'delete-certificate',
		'uses'  => 'CertificationController@delete'
		]);
		Route::any('delete-certificates', [
		'as' 	=> 'delete-certificates',
		'uses'  => 'CertificationController@delete'
		]);
		Route::any('delete-certificates-filtered', [
		'as' 	=> 'delete-certificates-filtered',
		'uses'  => 'CertificationController@delete_simple_table_data'
		]);
		Route::any('download-certificates', [
		'as' 	=> 'download-certificates',
		'uses'  => 'CertificationController@download'
		]);

		Route::any('ajaxloadcerificate', [
		'as'   => 'ajaxloadcerificate',
		'uses' => 'CertificationController@ajaxloadcerificate'
		]);
		Route::any('certificates', [
		'as'   => 'get_filter_provider',
		'uses' => 'CertificationController@get_filter_provider'
		]);
		Route::any('ajaxload_filtered_cerificate', [
		'as'   => 'ajaxload_filtered_cerificate',
		'uses' => 'CertificationController@ajaxload_filtered_cerificate'
		]);
    Route::any('admin-status/{id}', [
		'as' 	=> 'admin-status',
		'uses'  => 'UserAdminController@active_status'
		]);
		Route::any('admins', [
      'as' => 'admins',
      'uses' => 'UserAdminController@index'
    ]);
    Route::any('add-admin', [
    'as'   => 'add-admin',
    'uses' => 'UserAdminController@add'
    ]);
    Route::any('edit-admin/{id}', [
		'as' => 'edit-admin',
		'uses' => 'UserAdminController@edit'
		]);
		Route::any('delete_admin/{id}', [
		'as' 	=> 'delete_admin',
		'uses'  => 'UserAdminController@delete'
		]);
		Route::any('delete-admin', [
		'as' 	=> 'delete-admin',
		'uses'  => 'UserAdminController@delete'
		]);
		Route::any('ajaxloadadmin', [
		'as'   => 'ajaxloadadmin',
		'uses' => 'UserAdminController@ajaxloadadmin'
		]);
		Route::any('announcement', [
		'as'   => 'announcement',
		'uses' => 'AnnouncementController@index'
		]);
		Route::any('add-announcement', [
    'as'   => 'add-announcement',
    'uses' => 'AnnouncementController@add'
    ]);
		Route::any('delete-announcements/{id}', [
		'as' 	=> 'delete-announcements',
		'uses'  => 'AnnouncementController@delete'
		]);
		Route::any('delete-announcement', [
		'as' 	=> 'delete-announcement',
		'uses'  => 'AnnouncementController@delete'
		]);
		Route::any('announcement-status/{id}', [
		'as' 	=> 'announcement-status',
		'uses'  => 'AnnouncementController@active_status'
		]);
		Route::any('edit-announcement/{id}', [
		'as' => 'edit-announcement',
		'uses' => 'AnnouncementController@edit'
		]);
		Route::any('ajaxloadannouncement', [
		'as'   => 'ajaxloadannouncement',
		'uses' => 'AnnouncementController@ajaxloadannouncement'
		]);
		Route::any('announcement-setting/{id}', [
		'as' => 'announcement-setting',
		'uses' => 'AnnouncementController@setting'
		]);
		Route::any('edit_announcement_settings/{id}', [
		'as' 	=> 'edit_announcement_settings',
		'uses' => 'AnnouncementController@edit_announcement_settings'
		]);
		Route::any('settings', [
		'as'   => 'settings',
		'uses' => 'AdditionalSettingController@settings'
		]);

		Route::any('logout', [
			'as' => 'adminlogout',
			'uses' => 'AdminLoginController@logout'
		]);
		Route::any('profile', [
			'as' => 'admin_edit_profile',
			'uses' => 'AdminLoginController@EditProfile'
		]);
		Route::any('edit_user_profile', [
			'as' => 'edit_user_profile',
			'uses' => 'AdminLoginController@edit_user_profile'
		]);
		Route::any('changepassword', array(
			'as' => 'admin_change_password',
			'uses' => 'AdminLoginController@changepassword'));

		Route::any('admin_change_pin', array(
				'as' => 'admin_change_pin',
				'uses' => 'AdminLoginController@admin_change_pin'));
				
		Route::any('admin_notification_setting', array(
				'as' => 'admin_notification_setting',
				'uses' => 'AdminLoginController@admin_notification_setting'));

		Route::any('update-logo', array(
			'as' => 'adminupdatelogo',
			'uses' => 'AdminSettingsController@updatelogo'));

		Route::any('ajaxloaduser', [
		'as' => 'ajaxloaduser',
		'uses' => 'AdminUserController@ajaxloadUser'
		]);
		Route::any('create-user', [
		'as' => 'admincreateuser',
		'uses' => 'AdminUserController@createuser'
		]);

		Route::any('users-list', [
		'as' => 'userslist',
		'uses' => 'AdminUserController@userslist'
		]);
		Route::any('edit-user/{id}', [
		'as' => 'adminedituser',
		'uses' => 'AdminUserController@editUser'
		]);

		Route::any('delete-user/{id}', [
		'as' 	=> 'admindeleteuser',
		'uses'  => 'AdminUserController@deleteUser'
		]);
		Route::any('edit-user/{id}', [
		'as' 	=> 'adminedituser',
		'uses'  => 'AdminUserController@editUser'
		]);
		Route::any('active-user/{id}',[
		'as' 	=> 'adminactiveuser',
		'uses'	=> 'AdminUserController@activeUser'
		]);
		Route::any('delete-user/', [
		'as' 	=> 'admindeletealluser',
		'uses'  => 'AdminUserController@deleteUser'
		]);
		/* Email templates routes for changing in templates */
		Route::any('email-templates', [
			'as' => 'emailtemplateslist',
			'uses' => 'EmailTemplatesController@EmailTemplatesList'
		]);
		Route::any('create-emailtemplate', [
			'as' => 'create_email_template',
			'uses' => 'EmailTemplatesController@Create'
		]);
		Route::any('edit-emailtemplate/{id}', [
			'as' => 'edit_email_template',
			'uses' => 'EmailTemplatesController@Edit'
		]);
		/* Email variables routes for changing in variables */
		Route::any('email-variables', [
			'as' => 'email_variables_list',
			'uses' => 'EmailVariablesController@VariablesList'
		]);
		Route::any('create-emailvariables', [
			'as' => 'create_email_variables',
			'uses' => 'EmailVariablesController@Create'
		]);
		Route::any('edit-emailvariables/{id}', [
			'as' => 'edit_email_variables',
			'uses' => 'EmailVariablesController@Edit'
		]);
		Route::any('email-logs', [
			'as' => 'email_logs_list',
			'uses' => 'EmailLogsController@LogsList'
		]);
		Route::any('edit-copyright', [
			'as' => 'admincopyrightedit',
			'uses' => 'AdminSettingsController@UpdateCopyright'
		]);
		/* cities routes starts */
		Route::any('clock_in_away', [
			'as' => 'clock_in_away',
			'uses' => 'ClinicsController@clock_in_away'
		]);
		Route::any('ajaxloadclock_in_away', [
		'as'   => 'ajaxloadclock_in_away',
		'uses' => 'ClinicsController@ajaxloadclock_in_away'
		]);
		Route::any('delete-clockin/{id}', [
		'as' 	=> 'delete-clockin',
		'uses'  => 'ClinicsController@delete_clockin'
		]);
		Route::any('delete-clockins', [
		'as' 	=> 'delete-clockins',
		'uses'  => 'ClinicsController@delete_clockin'
		]);
		Route::any('ajax-cities', [
			'as' => 'ajax_cities',
			'uses' => 'CitiesController@ajaxcities'
		]);
		Route::any('add-city', [
			'as' => 'add_city',
			'uses' => 'CitiesController@add'
		]);
		Route::any('edit-city/{id}', [
			'as' => 'edit_city',
			'uses' => 'CitiesController@edit'
		]);
		Route::any('delete-city/{id}', [
			'as' => 'delete_city',
			'uses' => 'CitiesController@delete'
		]);
		Route::any('delete-cities', [
			'as' => 'delete_cities',
			'uses' => 'CitiesController@delete'
		]);
		Route::any('search-user-timesheet', [
			'as' => 'search-user-timesheet',
			'uses' => 'AdminTimesheetController@SearchByDate'
		]);
		/* cities routes ends */
		/* Start routes for admin notifications */
		Route::any('clinic_filled_notification/', [
		'as' 	=> 'clinicfillednotification',
		'uses'  => 'AdminNotificationController@ClinicFilledNotifications'
		]);
		Route::any('pending_milage_info/', [
		'as' 	=> 'pendingmilageinfo',
		'uses'  => 'AdminNotificationController@ClinicPendingMileageNotifications'
		]);
		Route::any('clinic_status_complete/', [
		'as' 	=> 'clinicstatuscomplete',
		'uses'  => 'AdminNotificationController@ClinicStatusCompleteNotifications'
		]);
		Route::any('clinic_unfilled_notification/', [
		'as' 	=> 'clinicunfillednotification',
		'uses'  => 'AdminNotificationController@ClinicUnfilledNotifications'
		]);
		Route::any('clinic_time_unfilled_notification/', [
		'as' 	=> 'clinictimeunfillednotification',
		'uses'  => 'AdminNotificationController@ClinicTimeUnfilledNotifications'
		]);
		Route::any('providers_month_notification/', [
		'as' 	=> 'providersmonthnotification',
		'uses'  => 'AdminNotificationController@ProviderHoursInaMonthNotification'
		]);
		Route::any('providers_day_notification/', [
		'as' 	=> 'providersdayhnotification',
		'uses'  => 'AdminNotificationController@ProviderHoursInaDayNotification'
		]);
		Route::any('providers_clinic_notification/', [
		'as' 	=> 'providersclinicnotification',
		'uses'  => 'AdminNotificationController@ProviderHoursInaClinicNotification'
		]);
		Route::any('provider_milage_inamonth_notification/', [
		'as' 	=> 'providermilageinamonth',
		'uses'  => 'AdminNotificationController@ProviderMilageInaMonthNotification'
		]);
		Route::any('provider_milage_per_day_notification/', [
		'as' 	=> 'providermilageperday',
		'uses'  => 'AdminNotificationController@ProviderMilageInaDayNotification'
		]);
		Route::any('provider_milage_per_clinic_notification/', [
		'as' 	=> 'providermilageperclinic',
		'uses'  => 'AdminNotificationController@ProviderMilageInaClinicNotification'
		]);
		Route::any('notification-detail/{id}', [
		'as' 	=> 'notification_details',
		'uses'  => 'AdminNotificationController@GetNotificationById'
		]);
		Route::any('notifications/{id}', [
		'as' 	=> 'all_notifications',
		'uses'  => 'AdminNotificationController@AllNotifications'
		]);
		Route::any('update-notifications', [
		'as' 	=> 'updatenotifications',
		'uses'  => 'AdminNotificationController@update_notifications'
		]);
		Route::any('user-notifications', [
		'as' 	=> 'usernotifications',
		'uses'  => 'AdminNotificationController@user_notifications'
		]);
		Route::any('user-notifications-hover', [
		'as' 	=> 'user_notifications_and_update',
		'uses'  => 'AdminNotificationController@user_notifications_and_update'
		]);
		Route::any('all_super_user_notifications/', [
		'as' 	=> 'all_super_user_notifications',
		'uses'  => 'AdminNotificationController@AllNotificationsSuperUser'
		]);
		
		
		
		/*Route::any('clinic_unfilled_notification_admin', [
		'as' 	=> 'clinicunfillednotificationAdmin',
		'uses'  => 'AdminNotificationController@ClinicUnfilledNotificationsAdmin'
		]);
		Route::any('clinic_time_unfilled_notification_admin', [
		'as' 	=> 'clinictimeunfillednotificationAdmin',
		'uses'  => 'AdminNotificationController@ClinicTimeUnfilledNotificationsAdmin'
		]);
		Route::any('providers_month_notification_admin', [
		'as' 	=> 'providersmonthnotificationAdmin',
		'uses'  => 'AdminNotificationController@ProviderHoursInaMonthNotificationAdmin'
		]);
		Route::any('providers_day_notification_admin', [
		'as' 	=> 'providersdayhnotificationAdmin',
		'uses'  => 'AdminNotificationController@ProviderHoursInaDayNotificationAdmin'
		]);
		Route::any('providers_clinic_notification_admin', [
		'as' 	=> 'providersclinicnotificationAdmin',
		'uses'  => 'AdminNotificationController@ProviderHoursInaClinicNotificationAdmin'
		]);
		Route::any('provider_milage_inamonth_notification_admin', [
		'as' 	=> 'providermilageinamonthAdmin',
		'uses'  => 'AdminNotificationController@ProviderMilageInaMonthNotificationAdmin'
		]);
		Route::any('provider_milage_per_day_notification_admin', [
		'as' 	=> 'providermilageperdayAdmin',
		'uses'  => 'AdminNotificationController@ProviderMilageInaDayNotificationAdmin'
		]);
		Route::any('provider_milage_per_clinic_notification_admin', [
		'as' 	=> 'providermilageperclinicAdmin',
		'uses'  => 'AdminNotificationController@ProviderMilageInaClinicNotificationAdmin'
		]);
		Route::any('pending_milage_info_admin', [
		'as' 	=> 'pendingmilageinfoAdmin',
		'uses'  => 'AdminNotificationController@ClinicPendingMileageNotificationsAdmin'
		]);
		Route::any('clinic_filled_notification_admin', [
		'as' 	=> 'clinicfillednotificationAdmin',
		'uses'  => 'AdminNotificationController@ClinicFilledNotificationsAdmin'
		]);
		Route::any('clinic_status_complete_admin', [
		'as' 	=> 'clinicstatuscompleteAdmin',
		'uses'  => 'AdminNotificationController@ClinicStatusCompleteNotificationsAdmin'
		]);*/
		
		
		
		
		Route::any('delete_notifications', [
		'as' => 'delete_notifications',
		'uses' => 'AdminNotificationController@delete_notifications'
		]);
		Route::any('user_notification', [
		'as' => 'count_user_unread_notification',
		'uses' => 'AdminNotificationController@count_user_unread_notification'
		]);
	});
	/** Master routes **/
		Route::any('add-master', [
			'as' 	=> 'addmaster',
			'uses'	=> 'AdminMasterController@addMaster'
			]
		);
		Route::any('master', [
			'as' 	=> 'viewmaster',
			'uses'	=> 'AdminMasterController@viewMaster'
			]
		);
		Route::any('/{any}/add', [
			'as' 	=> 'addfields',
			'uses'	=> 'AdminMasterController@add'
			]
		);
		Route::any('/{key}/edit', [
			'as' 	=> 'editfields',
			'uses'	=> 'AdminMasterController@edit'
			]
		);
		Route::any('/{key}/delete', [
			'as' 	=> 'deletefields',
			'uses'	=> 'AdminMasterController@delete'
			]
		);
		Route::any('delete', [
			'as' 	=> 'deleteallfields',
			'uses'	=> 'AdminMasterController@delete'
			]
		);
		Route::any('/{any}/list', [
			'as' 	=> 'viewfields',
			'uses'	=> 'AdminMasterController@view'
			]
		);
		Route::any('/{any}/ajax-list', [
			'as' 	=> 'ajaxlist',
			'uses'	=> 'AdminMasterController@ajax_view'
			]
		);
		Route::any('/{key}/active-inactive', [
			'as' 	=> 'activeinactive',
			'uses'	=> 'AdminMasterController@activeInactive'
			]
		);
		Route::any('/download-excel/{key}', [
			'as' 	=> 'downloadexcel',
			'uses'	=> 'AdminMasterController@downloadExcel'
			]
		);
		Route::any('import-excel', [
			'as' 	=> 'importexcel',
			'uses'	=> 'AdminMasterController@importExcel'
			]
		);
		Route::any('import-export', [
			'as' 	=> 'importexport',
			'uses'	=> 'AdminMasterController@importExport'
			]
		);

});
Route::group(array('prefix' => 'front'), function() {
//Route::group(array('middleware' => 'App\Http\Middleware\GuestFront','namespace'=>'front'), function() {
	Route::any('/', [
		'as' => 'home',
		'uses' => 'PagesController@homepage'
	]);
	Route::any('/login','UserController@login');
	Route::any('login/{validstring}', [
		'as' => 'userverification',
		'uses' => 'UserController@VerifyUser'
	]);
	Route::any('register', [
		'as' => 'userregister',
		'uses' => 'UserController@register'
	]);
	Route::any('/forgot-password','UserController@forgotpassword');
	Route::any('/reset-password/{validstring}','UserController@resetPassword');
//});
Route::group(array('middleware' => 'App\Http\Middleware\AuthFront','namespace'=>'front'), function() {

	Route::any('/logout', [
		'as' => 'logout',
		'uses' => 'UserController@logout'
	]);
	Route::any('/change-password', [
		'as' => 'changepassword',
		'uses' => 'UserController@ChangePassword'
	]);
	Route::any('/profile-page', [
		'as' => 'profilepage',
		'uses' => 'UserController@ProfilePage'
	]);
	Route::any('/edit-profile', [
		'as' => 'profile',
		'uses' => 'UserController@Editprofile'
	]);
	Route::any('/profileimage-upload', [
		'as' => 'uploaduserprofilepicture',
		'uses' => 'UserController@ProfileImageUpload'
	]);
	Route::any('/dashboard', [
		'as' => 'dashboard',
		'uses' => 'PagesController@dashboard'
	]);

	Route::any('/create-session', [
		'as' => 'createsession',
		'uses' => 'ItemsController@sessionStore'
	]);
	Route::any('/add-item', [
		'as' => 'additem',
		'uses' => 'ItemsController@addItem'
	]);
	Route::any('/get-images', [
		'as' => 'getmediaimages',
		'uses' => 'ItemsController@GetMediaImages'
	]);
	Route::any('/delete-media', [
		'as' => 'deletemedia',
		'uses' => 'ItemsController@deleteMedia'
	]);
	Route::any('/edit-item/{id}', [
		'as' => 'edititem',
		'uses' => 'ItemsController@EditItem'
	]);
	Route::any('/update-item', [
		'as' => 'updateitem',
		'uses' => 'ItemsController@UpdateItem'
	]);

	Route::any('/update-items', [
		'as' => 'updateitems',
		'uses' => 'ItemsController@updateItems'
	]);
	Route::any('/upload-singleimage', [
		'as' => 'uploadsinglemediaimage',
		'uses' => 'ItemsController@UploadSingleMediaImage'
	]);
	Route::any('/upload-images', [
		'as' => 'uploadmediaimages',
		'uses' => 'ItemsController@UploadMediaImages'
	]);
	Route::any('/upload-multipleimages', [
		'as' => 'uploadmultipleimages',
		'uses' => 'ItemsController@UploadMultipleImages'
	]);
	Route::any('/upload-singleimage', [
		'as' => 'uploadsingleimage',
		'uses' => 'ItemsController@UploadSingleImage'
	]);
	Route::any('/load-more', [
		'as' => 'loadmore',
		'uses' => 'ItemsController@itemsloadmore'
	]);

	Route::any('/ajaxloaditems', [
		'as' => 'ajaxloaditems',
		'uses' => 'ItemsController@ajaxloaditems'
	]);

	Route::any('/deleteItem', [
		'as' => 'deleteItem',
		'uses' => 'ItemsController@deleteItem'
	]);

	Route::any('/deleteallitem', [
		'as' => 'deleteallitem',
		'uses' => 'ItemsController@deleteAllItem'
	]);
	/* Route::any('user-resetpassword/{validstring}', [
		'as' => 'api_user_reset_password',
		'uses' => 'UserController@resetpassword'
	]); */
	/* $api->any('user-resetpassword/{validstring}', [
			'as' => 'api_user_reset_password',
			'uses' => 'UserController@resetpassword'
		]); */

});
});


$api->version('v1', function ($api) {
    $api->get('admin/test', function () {
        return 'api is running perfectly';
    });
		$api->get('admin/hello','App\Http\Controllers\admin\AdminPagesController@hello');
	});
/* api routes starts */
$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Http\Controllers\front'],function ($api) {
		$api->post('user-login', [
			'as' => 'api_user_login',
			'uses' => 'UserController@login'
		]);
		$api->post('password-verify-securitypin', [
			'as' => 'api_password_verify',
			'uses' => 'UserController@PasswordVerifySecurityPin'
		]);
		$api->post('user-forgotpassword', [
			'as' => 'api_user_forgot_password',
			'uses' => 'UserController@forgotpassword'
		]);
		 $api->any('user-resetpassword/{validstring}', [
			'as' => 'api_user_reset_password',
			'uses' => 'UserController@resetpassword'
		]); 
		$api->post('user-profile', [
			'as' => 'api_user_profile',
			'uses' => 'UserController@Getprofile'
		]);
		$api->post('user-profileupdate', [
			'as' => 'api_user_profile_update',
			'uses' => 'UserController@UpdateProfile'
		]);
		$api->post('user-profile_photo', [
			'as' => 'api_user_profile_photo',
			'uses' => 'UserController@UpdateUserProfilePic'
		]);
		$api->post('user-changepassword', [
			'as' => 'api_user_changepassword',
			'uses' => 'UserController@changepassword'
		]);
		$api->post('user-changephone', [
			'as' => 'api_user_change_phone',
			'uses' => 'UserController@UpdatePhone'
		]);
		$api->post('user-change-email', [
			'as' => 'api_user_change_email',
			'uses' => 'UserController@UpdateEmail'
		]);
		$api->post('user-change-socialsecurity', [
			'as' => 'api_user_change_social_security',
			'uses' => 'UserController@UpdateSocialSecurity'
		]);
		$api->post('default-prep-values', [
			'as' => 'api_default_prep_array',
			'uses' => 'UserController@Default_Preptime_values'
		]);
		$api->post('user-change-preptime', [
			'as' => 'api_user_change_prep_time',
			'uses' => 'UserController@UpdatePrepTime'
		]);
		$api->post('user-change-notification-settings', [
			'as' => 'api_user_change_notification_settings',
			'uses' => 'UserController@UpdateNotificationSettings'
		]);
		$api->post('user-logout', [
			'as' => 'api_user_logout',
			'uses' => 'UserController@logout'
		]);
		$api->post('user-change-email-notification', [
			'as' => 'api_user_change_email_notification',
			'uses' => 'UserController@UpdateEmailNotificationSettings'
		]);
		$api->post('default-settings-values', [
			'as' => 'api_default_settings_values',
			'uses' => 'UserController@DefaultSettingsValues'
		]);
		$api->post('clockin-reminder-values', [
			'as' => 'api_clockin_reminder_values',
			'uses' => 'UserController@Default_clockin_values'
		]);
		$api->post('clockout-reminder-values', [
			'as' => 'api_clockout_reminder_values',
			'uses' => 'UserController@Default_clockout_values'
		]);
		$api->post('leave-location-values', [
			'as' => 'api_leave_location_values',
			'uses' => 'UserController@Default_leave_location_values'
		]);
		$api->post('update_clockout_setting', [
			'as' => 'api_update_clockout_setting',
			'uses' => 'UserController@UpdateClockOutSettings'
		]);
		$api->post('update_clockin_setting', [
			'as' => 'api_update_clockin_setting',
			'uses' => 'UserController@UpdateClockInSettings'
		]);
		$api->post('update_leave_location', [
			'as' => 'api_update_leave_location',
			'uses' => 'UserController@UpdateLeaveLocation'
		]);
		$api->post('update_disable_email_confirmation', [
			'as' => 'api_disable_email_confirmation',
			'uses' => 'UserController@DisableEmailConfirmation'
		]);
		$api->post('update_default_notification', [
			'as' => 'api_update_default_notification',
			'uses' => 'UserController@UpdateDefaultNotificationSettings'
		]);
		$api->post('update_groupby_notification', [
			'as' => 'api_update_groupby_notification',
			'uses' => 'UserController@UpdateNotificationGroupBy'
		]);
		$api->post('update_timezone_setting', [
			'as' => 'api_update_timezone_setting',
			'uses' => 'UserController@UpdateTimezoneSetting'
		]);
		$api->post('push-notification-settings', [
			'as' => 'api_push_notification_settings',
			'uses' => 'UserController@UpdatePushNotification'
		]);
		$api->post('update-system-calender-status', [
			'as' => 'api_system_calender_status',
			'uses' => 'UserController@UpdateSystemCalenderStatus'
		]);

		/* certificates routes start*/
		$api->post('certificate-upload', [
			'as' => 'api_certificate_upload',
			'uses' => 'CertificationController@Add'
		]);
		$api->post('get-certificates', [
			'as' => 'api_get_certificates',
			'uses' => 'CertificationController@GetCertificates'
		]);
		$api->post('get-certificate', [
			'as' => 'api_get_single_certificate',
			'uses' => 'CertificationController@GetCertificate'
		]);
		$api->post('delete-certificate', [
			'as' => 'api_delete_single_certificate',
			'uses' => 'CertificationController@DeleteCertificate'
		]);
		/* certificates routes ends*/
		/* announcement routes starts */
		$api->post('latest-announcement', [
			'as' => 'api_latest_announcement',
			'uses' => 'AnnouncementController@LatestAnnouncement'
		]);
		$api->post('all-announcements', [
			'as' => 'api_all_announcements',
			'uses' => 'AnnouncementController@AllAnnouncement'
		]);
		$api->post('announcement-reject', [
			'as' => 'api_announcement_reject',
			'uses' => 'AnnouncementController@DeactiveAnnouncement'
		]);
		$api->post('get-announcement', [
			'as' => 'api_get_single_announcement',
			'uses' => 'AnnouncementController@GetAnnouncement'
		]);

		/* announcement routes ends */

		/* clinics routes starts */
		$api->post('clinics', [
			'as' => 'api_clinics',
			'uses' => 'ClinicsController@GetClinics'
		]);
		$api->post('my-clinics', [
			'as' => 'api_my_clinics',
			'uses' => 'ClinicsController@MyClinics'
		]);
		$api->post('my-clinics-offline', [
			'as' => 'api_my_clinics_offline',
			'uses' => 'ClinicsController@MyClinicsOffline'
		]);
		$api->post('accept-rejected', [
			'as' => 'api_accept_rejected',
			'uses' => 'ClinicsController@AcceptOrRejectClinic'
		]);
		$api->post('rejected-accept', [
			'as' => 'api_rejected_accept',
			'uses' => 'ClinicsController@AcceptOrRejectClinics'
		]);
		$api->post('home-feeds', [
			'as' => 'api_home_feeds',
			'uses' => 'ClinicsController@HomeFeedsInformation'
		]);
		$api->post('update-clock-in', [
			'as' => 'api_update_clock_in',
			'uses' => 'ClinicsController@UpdateClockInTime'
		]);
		$api->post('update-clock-out', [
			'as' => 'api_update_clock_out',
			'uses' => 'ClinicsController@UpdateClockOutTime'
		]);
		$api->post('swipe-upcoming-clinic', [
			'as' => 'api_swipe_upcoming_clinic',
			'uses' => 'ClinicsController@SwipeUpcomingClinics'
		]);
		$api->post('swipe-other-clinic', [
			'as' => 'api_swipe_other_clinic',
			'uses' => 'ClinicsController@SwipeOtherClinics'
		]);
		$api->post('update-clinic-mileage', [
			'as' => 'api_update_clinic_mileage',
			'uses' => 'ClinicsController@Get_Provider_Mileage'
		]);
		$api->post('update-user-latlong', [
			'as' => 'api_update_user_latlong',
			'uses' => 'ClinicsController@UpdateUserLatLong'
		]);
		$api->post('sync_clinics', [
			'as' => 'api_sync_clinics',
			'uses' => 'ClinicsController@SyncClinics'
		]);
		/* Push Notifications routes Start */
		$api->get('clockout_notification', [
			'as' => 'api_clockout_notification',
			'uses' => 'PushNotificationsController@ClockOutNotification'
		]);
		$api->get('clockin_notification', [
			'as' => 'api_clockinnotification',
			'uses' => 'PushNotificationsController@ClockInNotification'
		]);
		$api->get('instant_announcement', [
			'as' => 'instantannouncement',
			'uses' => 'PushNotificationsController@InstantAnnouncementNotification'
		]);
		$api->get('group_announcement', [
			'as' => 'groupannouncement',
			'uses' => 'PushNotificationsController@AnnouncementGroupNotification'
		]);
		$api->get('clinic_update_notification', [
			'as' => 'clinicupdatenotification',
			'uses' => 'PushNotificationsController@ClinicUpdateNotification'
		]);
		/* Push Notifications routes End */

		$api->post('my-timesheet', [
			'as' => 'api_my_timesheet',
			'uses' => 'TimesheetController@Timesheet'
		]);
		$api->post('timezone_list', [
			'as' => 'api_timezone_list',
			'uses' => 'UserController@TimeZonesList'
		]);
		$api->post('update-system-calender-setting', [
			'as' => 'api_system_calender_setting',
			'uses' => 'UserController@UpdateSystemCalender'
		]);
		$api->post('update-time-format-setting', [
			'as' => 'api_update_time_format_setting',
			'uses' => 'UserController@UpdateTimeFormatSetting'
		]);


		/* clinics routes ends */
		$api->group(['middleware' => 'wellness-auth'], function ($api){

		});
    });
});





Route::group(array('middleware' => 'App\Http\Middleware\GuestUser','namespace'=>'admin'), function() {
	/*Route::any('/', [
		'as' => 'home',
		'uses' => 'PagesController@homepage'
	]);
	Route::any('/login','UserController@login');
	Route::any('login/{validstring}', [
		'as' => 'userverification',
		'uses' => 'UserController@VerifyUser'
	]);
	Route::any('register', [
		'as' => 'userregister',
		'uses' => 'UserController@register'
	]);
	Route::any('/forgot-password','UserController@forgotpassword');
	Route::any('/reset-password/{validstring}','UserController@resetPassword');*/
	
	Route::any('clinic_unfilled_notification_admin', [
		'as' 	=> 'clinicunfillednotificationAdmin',
		'uses'  => 'AdminNotificationController@ClinicUnfilledNotificationsAdmin'
		]);
		Route::any('clinic_time_unfilled_notification_admin', [
		'as' 	=> 'clinictimeunfillednotificationAdmin',
		'uses'  => 'AdminNotificationController@ClinicTimeUnfilledNotificationsAdmin'
		]);
		Route::any('providers_month_notification_admin', [
		'as' 	=> 'providersmonthnotificationAdmin',
		'uses'  => 'AdminNotificationController@ProviderHoursInaMonthNotificationAdmin'
		]);
		Route::any('providers_day_notification_admin', [
		'as' 	=> 'providersdayhnotificationAdmin',
		'uses'  => 'AdminNotificationController@ProviderHoursInaDayNotificationAdmin'
		]);
		Route::any('providers_clinic_notification_admin', [
		'as' 	=> 'providersclinicnotificationAdmin',
		'uses'  => 'AdminNotificationController@ProviderHoursInaClinicNotificationAdmin'
		]);
		Route::any('provider_milage_inamonth_notification_admin', [
		'as' 	=> 'providermilageinamonthAdmin',
		'uses'  => 'AdminNotificationController@ProviderMilageInaMonthNotificationAdmin'
		]);
		Route::any('provider_milage_per_day_notification_admin', [
		'as' 	=> 'providermilageperdayAdmin',
		'uses'  => 'AdminNotificationController@ProviderMilageInaDayNotificationAdmin'
		]);
		Route::any('provider_milage_per_clinic_notification_admin', [
		'as' 	=> 'providermilageperclinicAdmin',
		'uses'  => 'AdminNotificationController@ProviderMilageInaClinicNotificationAdmin'
		]);
		Route::any('pending_milage_info_admin', [
		'as' 	=> 'pendingmilageinfoAdmin',
		'uses'  => 'AdminNotificationController@ClinicPendingMileageNotificationsAdmin'
		]);
		Route::any('clinic_filled_notification_admin', [
		'as' 	=> 'clinicfillednotificationAdmin',
		'uses'  => 'AdminNotificationController@ClinicFilledNotificationsAdmin'
		]);
		Route::any('clinic_status_complete_admin', [
		'as' 	=> 'clinicstatuscompleteAdmin',
		'uses'  => 'AdminNotificationController@ClinicStatusCompleteNotificationsAdmin'
		]);
});




/* api routes ends */
/* Route::any('front/user-resetpassword/{validstring}', 'front\UserController@resetPassword');
Route::any('front/user-resetpassword/{validstring}', [
			'as' => 'password-success',
			'uses' => 'front\UserController@resetPassword'
		]); */
Route::any('user-resetpassword/{validstring}', [
			'as' => 'api_user_reset_password',
			'uses' => 'UserController@resetpassword'
		]);
		
Route::any('password-success', [
			'as' => 'password-success',
			'uses' => 'front\UserController@PasswordSuccess'
		]);
