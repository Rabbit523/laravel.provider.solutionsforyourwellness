<?php
				$segment2	=	Request::segment(1);
				$segment3	=	Request::segment(2);
				$segment4	=	Request::segment(3);
				$segment5	=	Request::segment(4);
			?>
<aside id="left-sidebar-nav">
    <ul id="slide-out" class="side-nav fixed leftside-navigation">
    <li class="user-details cyan darken-2">
    <div class="row">
        <div class="col col s4 m4 l4">
					@if(Auth::user()->image)
							  <img src="{{ URL::asset('public/uploads/users/'.Auth::user()->image) }}" alt="" class="circle responsive-img valign profile-image">
					@else
						  <img src="{{ URL::asset('public/assets/admin/images/avatar.jpg') }}" alt="" class="circle responsive-img valign profile-image">
					@endif

        </div>
        <div class="col col s8 m8 l8">
            <ul id="profile-dropdown" class="dropdown-content">
                <!-- <li><a href="#"><i class="mdi-action-face-unlock"></i>{{ trans("Profile") }} </a>
                </li> -->
                <li><a href="{{URL::route('admin_edit_profile')}}"><i class="mdi-action-settings"></i>{{ trans("Settings") }} </a>
                </li>

                <li class="divider"></li>
                <li><a href="{{URL::route('adminlogout')}}"><i class="mdi-hardware-keyboard-tab"></i>{{ trans("Logout") }} </a>
                </li>
            </ul>
            <a class="btn-flat dropdown-button waves-effect waves-light white-text profile-btn" href="javascript:void(0)" data-activates="profile-dropdown"> {{Auth::user()->first_name}}<i class="mdi-navigation-arrow-drop-down right"></i></a>
            <p class="user-roal">{{ trans("Administrator") }}</p>
        </div>
    </div>
    </li>
    <li class="{{ ($segment3 == 'dashboard') ? 'bold active' : '' }}"><a href="{{ URL::route('admindashboard') }}" class="waves-effect waves-cyan"><i class="mdi-action-dashboard"></i> {{ trans("Dashboard") }}</a>
    </li>
	<li class="{{ ($segment3 == 'clinics' || $segment3 == 'add-clinic' || $segment3 == 'edit-clinic' || $segment3 == 'asign-rule') ? 'bold active' : '' }}"><a href="{{ URL::route('clinics') }}" class="waves-effect waves-cyan"><i class="mdi-action-wallet-travel"></i> {{ trans("Clinics") }}</a>
    </li>
	<li class="{{ ($segment3 == 'announcement' || $segment3 == 'add-announcement' || $segment3 == 'edit-announcement'|| $segment3 == 'announcement-setting') ? 'bold active' : '' }}"><a href="{{ URL::route('announcement') }}" class="waves-effect waves-cyan"><i class="mdi-action-settings-voice"></i> {{ trans("Announcements") }}</a>
    </li>
	<li class="{{ ($segment3 == 'providers' || $segment3 == 'add-provider' || $segment3 == 'edit-provider' || $segment3 == 'provider-details' || $segment3 == 'provider-calender' || $segment3 == 'provider-finance-report') ? 'bold active' : '' }}"><a href="{{ URL::route('providers') }}" class="waves-effect waves-cyan"><i class="mdi-maps-local-hospital"></i> {{ trans("Providers") }}</a>
    </li>
	<li class="{{ ($segment3 == 'certifications' || $segment3 == 'add-certificate' || $segment3 == 'edit-certificates') ? 'bold active' : '' }}"><a href="{{ URL::route('certifications') }}" class="waves-effect waves-cyan"><i class="mdi-editor-insert-drive-file"></i> {{ trans("Certificates") }}</a>
    </li>
	<li class="{{ ($segment3 == 'timesheet') ? 'bold active' : '' }}"><a href="{{ URL::route('timesheet_view') }}" class="waves-effect waves-cyan"><i class="mdi-maps-local-atm"></i> {{ trans("Timesheet") }}</a>
    </li>
	<li class="{{ ($segment3 == 'admins') ? 'bold active' : '' }}"><a href="{{ URL::route('admins') }}" class="waves-effect waves-cyan"><i class="mdi-action-account-circle"></i> {{ trans("Admins") }}</a>
    </li>	
	
    <li class="{{ ($segment3 == 'email-templates' || $segment3 == 'create-emailtemplate' || $segment3 == 'edit-emailtemplate') ? 'bold active' : '' }}"><a href="{{ URL::route('emailtemplateslist') }}" class="waves-effect waves-cyan"><i class="mdi-action-list"></i> {{ trans("Email Templates") }}</a>
    </li>
    </li> 
		<li class="{{ ($segment3 == 'settings' || $segment3 == 'edit-settings') ? 'bold active' : '' }}"><a href="{{ URL::route('settings') }}" class="waves-effect waves-cyan"><i class="mdi-action-settings"></i> {{ trans("Settings") }}</a>
    </li>

</ul>
    <a href="#" data-activates="slide-out" class="sidebar-collapse btn-floating btn-medium waves-effect waves-light hide-on-large-only cyan"><i class="mdi-navigation-menu"></i></a>
</aside>
