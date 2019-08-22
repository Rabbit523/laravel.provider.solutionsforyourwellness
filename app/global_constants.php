<?php
/* Global constants for site */
define('FFMPEG_CONVERT_COMMAND', '');
define('PROJECT_NAME', 'Health & Wellness');
//define('PROJECT_URL', 'www.wellness.com');
define('PROJECT_URL', 'https://provider.solutionsforyourwellness.com');

define("ADMIN_FOLDER", "admin/");
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', base_path());
define('APP_PATH', app_path());
define('ADMIN_EMAIL', 'narotam.saini@owebest.com');
define('APP_NAME', 'Wllness');

define("API_ACCESS_KEY", "AIzaSyBFFycJOBnxFpwc5q9i87LY1VZgZJWWUY4");
define("IMAGE_CONVERT_COMMAND", "");
define('WEBSITE_URL', url('/').'/');
define('WEBSITE_JS_URL', WEBSITE_URL . 'js/');
define('WEBSITE_CSS_URL', WEBSITE_URL . 'css/');
define('WEBSITE_IMG_URL', WEBSITE_URL . 'img/');
define('WEBSITE_UPLOADS_ROOT_PATH', ROOT . DS . 'uploads' .DS );
define('WEBSITE_UPLOADS_URL', WEBSITE_URL . 'public/uploads/');
define('WEBSITE_ASSET_IMAGES_URL', WEBSITE_URL . 'public/assets/admin/images/');

define('WEBSITE_ADMIN_URL', WEBSITE_URL.ADMIN_FOLDER );
define('WEBSITE_ADMIN_IMG_URL', WEBSITE_ADMIN_URL . 'img/');
define('WEBSITE_ADMIN_JS_URL', WEBSITE_ADMIN_URL . 'js/');
define('WEBSITE_ADMIN_FONT_URL', WEBSITE_ADMIN_URL . 'fonts/');
define('WEBSITE_ADMIN_CSS_URL', WEBSITE_ADMIN_URL . 'css/');

define('SETTING_FILE_PATH', APP_PATH . DS . 'settings.php');
define('MENU_FILE_PATH', APP_PATH . DS . 'menus.php');


define('CK_EDITOR_URL', WEBSITE_URL . 'uploads/ckeditor_images/');
define('CK_EDITOR_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH . 'ckeditor_images' . DS);


define('SLIDER_URL', WEBSITE_UPLOADS_URL . 'slider/');
define('SLIDER_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'slider' . DS);


define('BLOCK_URL', WEBSITE_UPLOADS_URL . 'block/');
define('BLOCK_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'block' . DS);

define('TESTIMONIAL_URL', WEBSITE_UPLOADS_URL . 'testimonial_images/');
define('TESTIMONIAL_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'testimonial_images' . DS);

define('HOWITWORK_URL', WEBSITE_UPLOADS_URL . 'how_it_works_images/');
define('HOWITWORK_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'how_it_works_images' . DS);


define('BLOG_IMAGE_URL', WEBSITE_UPLOADS_URL . 'blog/');
define('BLOG_IMAGE_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'blog' . DS);

define('USER_PROFILE_IMAGE_URL', WEBSITE_UPLOADS_URL . 'user_profile/');
define('USER_PROFILE_IMAGE_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'user_profile' . DS);


define('MASTERS_IMAGE_URL', WEBSITE_UPLOADS_URL . 'masters/');
define('MASTERS_IMAGE_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'masters' . DS);

$config	=	array();

define('ALLOWED_TAGS_XSS', '<a><strong><b><p><br><i><font><img><h1><h2><h3><h4><h5><h6><span></div><em><table><ul><li><section><thead><tbody><tr><td>');

define('ADMIN_ID', 1);
define('SUPER_ADMIN_ROLE_ID', 1);
define('USER_ROLE_ID', 2);
define('RESELLER_ROLE_ID', 2);
define('CUSTOMER_ROLE_ID', 3);

define('SUPER_ADMIN_STAFF_ROLE_ID', 4);
define('RESELLER_STAFF_ROLE_ID', 5);
define('CUSTOMER_STAFF_ROLE_ID', 6);
define('MERCHANT_LOGIN_ID', '5CZ3Am3wgJ');
define('MERCHANT_TRANSACTION_KEY', '8b8nY48zG762xWpH');
define('COMPNY_NAME', 'Adidas Web Store');
define('COUNTRY_NAME', 'USA');
define('CUSTOMER_ID', '22467965');


/* keys for encryption  */
define('ENCRYPTION_KEY', 'qJB0rGtIn5UB1xG03efyCpOskLsdIeoY');
define('ENCRYPTION_IV', 'fedcba9876543210');

Config::set("Site.currency", "$");
Config::set("Site.currencyCode", "USD");

Config::set('defaultLanguage', 'English');
Config::set('defaultLanguageCode', 'en');


Config::set('default_language.message', 'All the fields in English language are mandatory.');

Config::set('newsletter_template_constant',array('USER_NAME'=>'USER_NAME','TO_EMAIL'=>'TO_EMAIL','WEBSITE_URL'=>'WEBSITE_URL','UNSUBSCRIBE_LINK'=>'UNSUBSCRIBE_LINK'));

Config::set('PAGE_LIST', array(
	'how-it-works'=>'How it Works',
	'jobs'=>'Search for job',
	'brochures'=>'Personal brochure sample',
	'resources'=>'Resources',
	'free-download'=>'Free downloads',
	'video-vault'=>'Video vault',
	'blog'=>'Blog',
	'tips'=>'Tip',
	'faqs'=>'faq',
	'pricing'=>'Pricing',
	'about-us'=>'About us',
	'contact-us'=>'Contact us',
	'news'=>'In the News'
));






Config::set('prefix',array(
	'1'=>'Mrs.',
	'2'=>'Mr.',
	'3'=>'Ms.',
	'4'=>'Miss.'
));

Config::set('suffix',array(
	'1'=> 'B.V.M.',
	'2'=> 'CFRE',
	'3'=>	'CLU',
	'4'=>	'CPA',
	'5'=>	'C.S.C.',
	'6'=>	'C.S.J.',
	'7'=>	'D.C.',
	'8'=>	'D.D.',
	'9'=>	'D.D.S.',
	'10'=>	'D.M.D.',
	'11'=>	'D.O.',
	'12'=>	'D.V.M.',
	'13'=>	'Ed.D.',
	'14'=>	'Esq.',
	'15'=>	'II',
	'16'=>	'III',
	'17'=>	'IV',
	'18'=>	'Inc.',
	'19'=>	'J.D.',
	'20'=>	'Jr.',
	'21'=>	'LL.D.',
	'22'=>	'Ltd.',
	'23'=>	'M.D.',
	'24'=>	'P.E.',
	'25'=>	'Ph.D.',
	'26'=>	'Ret.',
));

Config::set('securityQuestion',array(
	''=>	trans("messages.Select Security Question"),
	'1'=>	trans("messages.In what city was your father born ?"),
	'2'=>	trans("messages.In what city was your mother born ?"),
	'3'=>	trans("messages.What is your mother maiden name ?"),
	'4'=>	trans("messages.What was the name of your first pet ?"),
	'5'=>	trans("messages.What is the name of your oldest sibling ?"),
	'6'=>	trans("messages.What was the make of your first car ?"),
	'7'=>	trans("messages.What was the name of your high school ?"),
	'8'=>	trans("messages.What was your high school mascot ?"),
	'9'=>	trans("messages.Who is your favorite sports team ?"),
	'10'=>	trans("messages.What is your favorite hobby ?")
));



Config::set('designation',array(
	'' => 'Select Your Designation',
	'1'=> 'Developer',
	'2'=> 'Doctor',
	'3'=> 'Engineer',
	'4'=> 'Other',
));

Config::set('category',array(
	'' => 'Select Your Designation',
	'1'=> 'Developer',
	'2'=> 'Doctor',
	'3'=> 'Engineer',
	'4'=> 'Other',
));

Config::set('month',array(
	'1' => 'January',
	'2'=> 'February',
	'3'=> 'March',
	'4' => 'April',
	'5'=> 'May',
	'6'=> 'June',
	'7' => 'July',
	'8'=> 'August',
	'9'=> 'September',
	'10' => 'October',
	'11'=> 'November',
	'12'=> 'December',
));


//////////////// extension

define('IMAGE_EXTENSION','jpeg,jpg,png,gif,bmp');
define('PDF_EXTENSION','pdf');
define('DOC_EXTENSION','doc,xls');
define('VIDEO_EXTENSION','mpeg,avi,mp4,webm,flv,3gp,m4v,mkv,mov,moov');


define('TEXT_ADMIN_ID',1);
define('TEXT_FRONT_USER_ID',2);
define('FRONT_USER',2);
define('FITNESS_ENTHUSIAST',3);
define('FITNESS_TRAINER_DIETICIAN',4);


define('IMAGE_INFO', '<div class="mws-form-message info">
	<a class="close pull-right" href="javascript:void(0);">&times;</a>
	<ul style="padding-left:12px">
		<li>Allowed file types are gif, jpeg, png, jpg.</li>
		<li>Large files may take some time to upload so please be patient and do not hit reload or your back button</li>
	</ul>
</div>');

define('VIDEO_INFO', '<div class="mws-form-message info">
	<a class="close pull-right" href="javascript:void(0);">&times;</a>
	<ul style="padding-left:12px">
		<li>Allowed video types are '.VIDEO_EXTENSION.'</li>
		<li>Large files may take some time to upload so please be patient and do not hit reload or your back button</li>
	</ul>
</div>');

define('DOC_INFO', '<div class="mws-form-message info">
	<a class="close pull-right" href="javascript:void(0);">&times;</a>
	<ul style="padding-left:12px">
		<li>Allowed doc types are '.DOC_EXTENSION.'</li>
		<li>Large files may take some time to upload so please be patient and do not hit reload or your back button</li>
	</ul>
</div>');



Config::set('text_search',array(
	'dashboard.'		=> 'Dashboard',
	'user_managmt.'		=> 'User Management',
	'ads_manager.'		=> 'Ads Manager',
	'media_partner.'	=> 'Media Partner',
	'language_manager.'	=> 'Lanaguage Manager',
	'masters.'			=> 'Masters',
	'management.'		=> 'Management',
	'settings.'			=> 'Settings',
	'Blog.'				=> 'Blogs',
	'Block.'			=> 'Block',
	'Visitor.'			=> 'Visitor',
	'Testimonial.'		=> 'Testimonial',
	'Contact.'			=> 'Contact',
	'Slider.'			=> 'Slider',
));

/**  System document url path **/
if (!defined('SYSTEM_IMAGE_URL')) {
    define('SYSTEM_IMAGE_URL', WEBSITE_UPLOADS_URL . 'system_images/');
}

/**  System document upload directory path **/
if (!defined('SYSTEM_IMAGE_DIRECTROY_PATH')){
    define('SYSTEM_IMAGE_DIRECTROY_PATH', WEBSITE_UPLOADS_ROOT_PATH . 'system_images' . DS);
}

/**  Active Inactive global constant **/
define('ACTIVE',1);
define('INACTIVE',0);

define('PENDING', 1);
define('PROCESSING', 2);
define('AWAITINGRESPONSE', 3);
define('SUSPENDED', 4);
define('LIVE', 5);
define('CANCELLED', 6);

Config::set('service_status',array(
	'1'		=> 'Pending',
	'2'		=> 'Processing',
	'3'		=> 'Awaiting Response',
	'4'		=> 'Suspended',
	'5'		=> 'Live',
	'6'		=> 'Cancelled',
));


Config::set('administor_connection_availability_status',array(
	'1'		=> 'Pending',
	'2'		=> 'Ongoing',
	'3'		=> 'Complete',
	'4'		=> 'Cancelled',
));

Config::set('reseller_connection_availability_status',array(
	'1'		=> 'Pending',
	'4'		=> 'Cancelled',
));

define('AVAILABILITY_PENDING', 1);
define('AVAILABILITY_ONGOING', 2);
define('AVAILABILITY_COMPLETE', 3);
define('AVAILABILITY_CANCELLED', 4);

Config::set('country_list',array(
	'Ireland'	=> 'Ireland',
	'UK'		=> 'UK',
));

define('ADMINISTRACTOR_COMMENT', 1);
define('RESELLER_COMMENT', 2);
define('CUSTOMER_COMMENT', 3);
define('TICKET_CLOSED', 4);
define('TICKET_POSTPONED', 5);


Config::set('email_configuration',array(
	'username'		=> 'owebest01',
	'password'		=> 'System@123',
	'url'			=> 'https://api.sendgrid.com',
));

Config::set('date_format',array(
	'date'			=> 'd',
	'month'			=> 'm',
	'year'			=> 'Y',
	'separator'		=> '-',
));
