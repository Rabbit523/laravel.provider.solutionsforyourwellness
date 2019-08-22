<header id="header" class="page-topbar">
    <!-- start header nav-->
    <div class="navbar-fixed">
        <nav class="navbar-color">
            <div class="nav-wrapper">
                <ul class="left">
                  <li><h1 class="logo-wrapper"><a href="{{URL::route('admindashboard')}}" class="brand-logo darken-1">
                    <img src="{{ URL::asset('public/assets/admin/images/logo.png') }}" alt="logo"></a> <span class="logo-text">{{PROJECT_NAME}}</span></h1></li>
                </ul>
				
					<ul class="right hide-on-med-and-down">
					  <li><a href="javascript:void(0);" class="waves-effect waves-block waves-light toggle-fullscreen"><i class="mdi-action-settings-overscan"></i></a>
					  </li>
						<li id="notify"><a href="javascript:void(0);" class="waves-effect waves-block waves-light notification-button" data-activates="notifications-dropdown"><i class="mdi-social-notifications"><small class="" id="notifications"></small></i>
						</a>
						</li>
					</ul>
					 @php($notifications = CustomHelper::user_notifications(Auth::user()->id))
					 <div id="notifications-dropdown" class="dropdown-content" style="max-height:300px;overflow-y:scroll">
						<ul id="all_notifications">
						  <li>
							<h5><a href="{{ URL::route('all_notifications',Auth::user()->id) }}">View all Notifications</a>
							@if(count($notifications->toArray())>0)</h5>
						  </li>
						  <li class="divider"></li>
						  @else
							<p style="padding-left:4%">Not available</p>
						  @endif
						</ul>
						@php($countnotifications = CustomHelper::count_unread_notifications(Auth::user()->id))
						@if($countnotifications>4)
							<ul id="load_more_ul">
								<li><a href="javascript:void(0)" id="load_more">
								Load more
								<i class="mdi-plus"></i></a></li>
							</ul>	
						@endif
					</div>	
            </div>
        </nav>
    </div>
	
    <!-- end header nav-->
</header>
<script type="text/javascript">
   $(document).ready(function(){
	  var interval;
	  var user_id = {{ Auth::user()->id }}
		function callAjax() {
		  $.ajax({
		   type: "POST",
		   url:"{{URL::route('count_user_unread_notification')}}",
		   data: {
			"_token": "{{ csrf_token() }}",
			"user_id": user_id,
		  },
			success: function (data) {
				if(data > 0){
					$("#notify small").addClass("notification-badge");
					$('#notifications').html(data);// first set the value 
				}
					interval = setTimeout(callAjax, 5000);   
			}
		});
		}
		callAjax();
		//clearTimeout(interval);
  }); 
  
 $(document).ready(function(){ 
		var user_id = {{ Auth::user()->id }};
		var limit_start = $("#limit_start").text();
		var limit_from = $("#limit_from").text();
		  $.ajax({
			   type: "POST",
			   url:"{{URL::route('usernotifications')}}",
			   data: {
				"_token": "{{ csrf_token() }}",
				"user_id": user_id,
				"offset": parseInt(limit_start),
				"limit": parseInt(limit_from),
			  },
				success: function (data) {
					if(data){
						var arr = data.split("~");
						/* $('#limit_start').text(arr[1]);
						$('#limit_from').text(arr[2]); */
						$('#all_notifications').html(arr[0]);
						if(arr[3] == 0){
							$("#load_more_ul").hide();
						}
					}
				}
			});
		$('#all_notifications').hover(function(){
			var user_id = {{ Auth::user()->id }};
			var limit_start = $("#limit_start").text();
			var limit_from = $("#limit_from").text();
			var stop_hover = $("#stop_hover").text();
		if(stop_hover == 0){
				$.ajax({
				   type: "POST",
				   url:"{{URL::route('user_notifications_and_update')}}",
				   data: {
					"_token": "{{ csrf_token() }}",
					"user_id": user_id,
					"offset": parseInt(limit_start),
					"limit": parseInt(limit_from),
				  },
					success: function (data) {
						if(data){
							var arr = data.split("~");
							$('#limit_start').text(arr[1]);
							$('#limit_from').text(arr[2]);
							$('#stop_hover').text(arr[3]);
							$('#all_notifications').html(arr[0]);
							if(arr[4] == 0){
								$("#load_more_ul").hide();
							}
						}
					}
				});
			}
		});
		$('#load_more').click(function() {
		var user_id = {{ Auth::user()->id }};
		var limit_start = $("#limit_start").text();
		var limit_from = $("#limit_from").text();
		  $.ajax({
			   type: "POST",
			   url:"{{URL::route('user_notifications_and_update')}}",
			   data: {
				"_token": "{{ csrf_token() }}",
				"user_id": user_id,
				"offset": parseInt(limit_start),
				"limit": parseInt(limit_from),
			  },
				success: function (data) {
					if(data){
						var arr = data.split("~");
						$('#limit_start').text(arr[1]);
						$('#limit_from').text(arr[2]);
						$('#all_notifications').append(arr[0]);
						if(arr[4] == 0){
							$("#load_more_ul").hide();
						} 
					}
				}
			});
		});
  });
  function notification_delete(e){
		 var notification_id = $(e).attr("data-id");
		 $.ajax({
		   type: "POST",
		   url:"{{URL::route('delete_notifications')}}",
		   data: {
			"_token": "{{ csrf_token() }}",
			"notification_id": notification_id,
		  },
		   success: function(res){
			 if(res==1){
			   var message	 =	'Notification successfully deleted.';
				$('#notification_li_'+notification_id).remove();
			   return false;
			 }else if(res==0){
			   var errormessage				=	'Error occured.';
			 }
		   }
		});
  }
</script>
<span id="limit_start" class="hide">0</span>
<span id="limit_from" class="hide">4</span>
<span id="stop_hover" class="hide">0</span>