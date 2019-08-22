<!-- jQuery Library -->
<?php
use App\Http\Controllers\BaseController;
?>

{{ Html::script('public/assets/admin/js/jquery.fancybox.js') }}
{{ Html::script('public/assets/admin/js/chosen.jquery.js') }}
{{ Html::script('public/assets/admin/js/jquery.validate.js') }}

<script src="https://maps.googleapis.com/maps/api/js?key={{BaseController::GetAdminSettingsValue('google_map_api')}}&libraries=places&callback=initialize" async defer></script>

<script src="https://cdn.ckeditor.com/4.7.2/standard/ckeditor.js"></script>


<!--materialize js-->
{{ Html::script('public/assets/admin/js/materialize.js') }}
<!--scrollbar-->
{{ Html::script('public/assets/admin/js/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}

<!--plugins.js - Some Specific JS codes for Plugin Settings-->
{{ Html::script('public/assets/admin/js/plugins.js') }}
<!--custom-script.js - Add your own theme custom JS-->
{{ Html::script('public/assets/admin/js/custom-script.js') }}
{{ Html::script('public/assets/admin/js/plugins/data-tables/js/jquery.dataTables.min.js') }}
{{ Html::script('public/assets/admin/js/plugins/data-tables/data-tables-script.js') }}
{{ Html::script('public/assets/admin/js/plugins/prism/prism.js') }}
{{ Html::script('public/assets/admin/js/select2.min.js') }}
{{ Html::script('public/assets/admin/js/clcokpicker2.js') }}


{{ Html::script('public/assets/admin/js/plugins/sparkline/jquery.sparkline.min.js') }}
{{ Html::script('public/assets/admin/js/plugins/sparkline/jquery.sparkline.min.js') }}
{{ Html::script('public/assets/admin/js/plugins/chartjs/chart.min.js') }}





<script type="text/javascript">
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
</script>
<script>
		/* this function used for  insert contant, when we click on  insert variable button */
    function InsertHTML() {

		var constant = $('#constants').val();

		if(constant != ''){
			var newconstant = '{'+constant+'}';
			var oEditor = CKEDITOR.instances["body"] ;
			oEditor.insertHtml(newconstant) ;
			options  = $('#action_constants').val();
			if(options){
				options  = options+","+newconstant;
			}else{
				options  = newconstant;
			}
			$('#action_constants').val(options);
		}
  }
</script>
<script type="text/javascript">
  $(document).ready(function(){
    $("#templatename").blur(function(){
			var templatename = 	$('#templatename').val();
			 templatename	 =	templatename.toLowerCase();
			 templatename	 =	templatename.replace(" ","_");
			 $('#action').val(templatename);
		});
    $("#SelectedProvider").select2({
			placeholder: "Select providers...",
			allowClear : true
	});
  $("#Timezoneuser").select2({
      maximumSelectionSize: 1,
    placeholder: "Select timezone...",
    allowClear : true
});
$("#providers").select2({
    placeholder: "Select providers...",
    allowClear : true
});
$("#manual_providers").select2({
    placeholder: "Select manual provider...",
    allowClear : true
});
$("#cities").select2({
    placeholder: "Select cities in which you want to publish announcement...",
    allowClear : true
});
$("#timezone").select2({
  maximumSelectionSize: 1,
  placeholder: "Select clinic timezone...",
  allowClear : true
});
$("#providertimezone").select2({
  maximumSelectionSize: 1,
  placeholder: "Select timezone...",
  allowClear : true
});
$("#manualprovider").select2({
  placeholder: "Select a provider to manually asign this clinic...",
  allowClear : true
});
$("#notification_alert").select2({
  maximumSelectionSize: 1,
  placeholder: "Push Notification",
  allowClear : true
});
$("#email_alert").select2({
  maximumSelectionSize: 1,
  placeholder: "Email Notification",
  allowClear : true
});
$("#provider_id_1").select2({
  maximumSelectionSize: 1,
  placeholder: "Choose primary provider",
  allowClear : true
});
$("#provider_id_2").select2({
  maximumSelectionSize: 1,
  placeholder: "Choose med tech provider",
  allowClear : true
});
$("#provider_id_3").select2({
  placeholder: "Choose other providers",
  allowClear : true
});
	  $('#searchInput').keydown(function(event){
		if(event.keyCode == 13) {
		  event.preventDefault();
		  return false;
		}
	  });
  });
</script>
<script type="text/javascript">
   $('#input_starttime').clockpicker({
     placement: 'bottom',
     align: 'left',
     twelvehour: false
   });
   $('#input_endtime').clockpicker({
     placement: 'bottom',
     align: 'left',
     twelvehour: false
   });
   $('#input_preptime').clockpicker({
     placement: 'bottom',
     align: 'left',
     twelvehour: false
   });
   $('.datepicker').pickadate();
 </script>


<script>
function GetrandomString(clicked_element)
{
    var self = $(clicked_element);
    var random_string = generateRandomString(10);
    $('input[name=password]').val(random_string);
    $('input[name=password]').focus();
    //self.remove();
}
function generateRandomString(string_length)
{
    var characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
    var string = '';
    for(var i = 0; i <= string_length; i++)
    {
        var rand = Math.round(Math.random() * (characters.length - 1));
        var character = characters.substr(rand, 1);
        string = string + character;
    }
    return string;
}

// START code to remove duplicates from dropdowns for provider
  var all_providers   = '';
  var other_number_of_opions = 0;
  $(document).ready(function() {
    // get all options first and save to a variable
    all_providers   = $("[id^=provider_id_1]").html();
    $('#provider_id_1').select2("disable");
    $('#provider_id_2').select2("disable");
    $('#provider_id_3').select2("disable");
    // trigger when any change happen
    $(document).on('change', "[id^=provider_id_]", function(){
      remove_select_providers();
    });
    $(document).on('blur', "#personnel", function(){
        var personnel = $(this).val();
        if(personnel < 1){
          $('#provider_id_1').select2('val', '');
          $('#provider_id_1').select2("disable");
        }else{
          $("#provider_id_1").select2({
            maximumSelectionSize: 1,
            placeholder: "Choose primary provider",
            allowClear : true
          });
        }
        if(personnel < 2){
          $('#provider_id_2').select2('val', '');
          $('#provider_id_2').select2("disable");
        }else{
          $("#provider_id_2").select2({
            maximumSelectionSize: 1,
            placeholder: "Choose med tech provider",
            allowClear : true
          });
        }
        var other_number_of_opions = personnel-2;
        if(other_number_of_opions > 0){
          $("#provider_id_3").select2({
            maximumSelectionSize: parseInt(other_number_of_opions),
            placeholder: "Choose other providers",
            allowClear : true
          });
        }else{
          $('#provider_id_3').select2('val', '');
          $('#provider_id_3').select2("disable");
        }
    });

  });
  function remove_select_providers(){
    select_providers  = new Array();

    // reinitialize array and then push selected value to array to compare
    $("[id^=provider_id_]").each(function(){
      input = $(this);
      current_selected_val  = $(this).val();
      select_providers.push(current_selected_val);
      $(this).html(all_providers);
      $(this).val(current_selected_val);
      input.closest("label").html('');
    });
    // check dropdowns and remove selected value from new or old ones
    $("[id^=provider_id_]").each(function(){
      current_val   = $(this).val();
      $(this).find('option').each(function(){
        // check if already selected, and not its current value and not blank as well
        if(jQuery.inArray($(this).attr('value'), select_providers) > -1 && current_val != $(this).attr('value') && $(this).attr('value')!=""){
          $(this).remove();
        }
      });
    });
  }
// END code to remove duplicates from dropdowns for players
</script>

<script type="text/javascript">
  $(document).ready(function(){
    $('#table-datatables').on('click', '.delete_record_btn', function(){
      var delete_link = $(this).data('url');
      var message			=	'Are you sure you want to delete this record?';
      var modalbody 		= 	$('#message_modal').html();
    	$('#message_modal').html();
    	$('body').append(modalbody);
      $('.modal-content p').html(message);
    	$('#confirm_ok_btn').attr('href',delete_link);
      $("#confirm_modal").openModal();
  });
  $('.delete_record_btn').on('click', function(){
    var delete_link = $(this).data('url');
    var message			=	'Are you sure you want to delete this record?';
    var modalbody 		= 	$('#message_modal').html();
    $('#message_modal').html();
    $('body').append(modalbody);
    $('.modal-content p').html(message);
    $('#confirm_ok_btn').attr('href',delete_link);
    $("#confirm_modal").openModal();
});
  $('#table-datatables').on('click', '.change_status_btn', function(){
    var data_url  =   $(this).data('url');
    var data_msg  =   $(this).data('msg');
    var message	  =	  data_msg;
    var modalbody = 	$('#message_modal').html();
    $('#message_modal').html();
    $('body').append(modalbody);
    $('.modal-content p').html(message);
    $('#confirm_ok_btn').attr('href',data_url);
    $("#confirm_modal").openModal();
});
  });
  </script>

<script type="text/javascript">
  $(document).ready(function(){
		  $(".fancybox").fancybox({
			loop : true,
			'navigation':true,
			'type': 'image'
		});
    /* Delete All providers data */
			$("#dlt_providers").click(function(){
				if($(".checked_certificates:checked").length>0){
          if(confirm("Are you sure you want to delete this?")){
						$("#provider_form").attr('action','{{URL::route("delete-provider")}}').submit();
          }
          else{
              return false;
          }
				}else{
					alert('Please select atleast one record');
				}
		});
    /* Delete All Certificates Data */
			$("#dlt_certificates").click(function(){
				if($(".checked_certificates:checked").length>0){
          if(confirm("Are you sure you want to delete this?")){
						$("#certificate_forms").attr('action','{{URL::route("delete-certificates-filtered")}}').submit();
          }
          else{
              return false;
          }
				}else{
					alert('Please select atleast one record');
				}
		});
    /* Delete All Certificates Data */
      $("#dlt_announcement").click(function(){
        if($(".checked_announcement:checked").length>0){
          if(confirm("Are you sure you want to delete this?")){
            $("#announcement_form").attr('action','{{URL::route("delete-announcement")}}').submit();
          }
          else{
              return false;
          }
        }else{
          alert('Please select atleast one record');
        }
    });
    /* Delete All clinics Data */
      $("#dlt_clinics").click(function(){
        if($(".checked_clinics:checked").length>0){
          if(confirm("Are you sure you want to delete this?")){
            $("#clinics_form").attr('action','{{URL::route("delete-clinics")}}').submit();
          }
          else{
              return false;
          }
        }else{
          alert('Please select atleast one record');
        }
    });
	/* Delete All clinics Data */
      $("#dlt_recordss").click(function(){
        if($(".checked_clinics:checked").length>0){
          if(confirm("Are you sure you want to delete this?")){
            $("#record_form").attr('action','{{URL::route("delete-clockins")}}').submit();
          }
          else{
              return false;
          }
        }else{
          alert('Please select atleast one record');
        }
    });
	/* Delete All cities */
      $("#delete_all_cities").click(function(){
        if($(".checked_clinics:checked").length>0){
          if(confirm("Are you sure you want to delete this?")){
            $("#cities_form").attr('action','{{URL::route("delete_cities")}}').submit();
          }
          else{
              return false;
          }
        }else{
          alert('Please select atleast one record');
        }
    });
    $("#delete_all_admin").click(function(){
      if($(".checked_admin:checked").length>0){
        if(confirm("Are you sure you want to delete this?")){
          $("#admin_form").attr('action','{{URL::route("delete-admin")}}').submit();
        }
        else{
            return false;
        }
      }else{
        alert('Please select atleast one record');
      }
  });
    
	$( ".change_rate" ).click(function() {
     var provider_id = $(this).attr("data-id");
     var rate = $('#rate').val();
     $.ajax({
       type: "POST",
       url:"{{URL::route('change_provider_rate')}}",
       data: {
        "_token": "{{ csrf_token() }}",
        "provider_id": provider_id,
        "rate": rate,
      },
       success: function(res){
         if(res==1){
           var message				      =	'Provider rate updated successfully.';
           $('.success p').html(message);
           $('.success').css('display','block');
           window.setTimeout(function () {
           $('.success').css('display','none');
            }, 4000);
           return false;
         }else if(res==0){
           var errormessage				=	'Error occured.';
           $('.error p').html(errormessage);
           window.setTimeout(function () {
           $('.error').css('display','none');
            }, 4000);
           $('.error').css('display','block');
         }
       }
     });
  });
    var provider_table 	=	$('#provider_table').DataTable({
     "ajax": "{{URL::route('ajaxloadprovider')}}",
     "paging": true,
     "bLengthChange": false,
     "ordering": true,
     "info": true,
     "autoWidth": false,
     processing: true,
     serverSide: true,
     "order": [[ 0, "desc" ]],
     columnDefs: [
       { targets: [0,7]},
     ],
     "columns": [
       { "data": "sno","orderable":false,"width":"2%"},
       { "data": "name","orderable":true,"width":"10%"},
       { "data": "email" ,"orderable":true,"width":"10%"},
       { "data": "phone" ,"orderable":true,"width":"10%"},
       { "data": "provider_type" ,"orderable":true,"width":"10%"},
	   { "data": "address" ,"orderable":true,"width":"17%"},
       { "data": "status" ,"orderable":true,"width":"10%"},
       { "data": "action" ,"orderable":false,"width":"30%"},
     ],

    });
    ajaxloadclinic_url = "{{URL::route('ajaxloadclinic')}}";
    var clinic_table 	=	$('#clinic_table').DataTable({
     "ajax": ajaxloadclinic_url,
     "paging": true,
     "bLengthChange": false,
     "ordering": true,
     "info": true,
     "autoWidth": false,
     processing: true,
     serverSide: true,
     "orderMulti": false, // for disable multi column order
     "dom": '<"top"i>rt<"bottom"lp><"clear">',
     "order": [[ 0, "desc" ]],
     columnDefs: [
       { targets: [0,8]},
     ],
     "columns": [
       { "data": "sno","orderable":false},
       { "data": "name" ,"orderable":true},
       { "data": "phone" ,"orderable":true},
       { "data": "address","orderable":true},
       { "data": "time" ,"orderable":true},
       { "data": "date" ,"orderable":true},
       { "data": "rule" ,"orderable":false},
       { "data": "personnel" ,"orderable":false},
	   { "data": "accepted" ,"orderable":false},
       { "data": "action" ,"orderable":false,},
     ],

    });
	
	// Timesheet Overall record dataTables
	var timesheet_records_datatable 	=	$('#timesheet_records_datatable').DataTable({
     "ajax": "{{URL::route('timesheet_ajax_view')}}",
     "paging": true,
     "bLengthChange": false,
     "ordering": true,
     "info": true,
     "autoWidth": false,
     processing: true,
     serverSide: true,
     "order": [[ 0, "desc" ]],
     columnDefs: [
       { targets: [0,7]},
     ],
     "columns": [
       { "data": "sno","orderable":false,"width":"2%"},
       { "data": "name","orderable":false,"width":"12%"},
       { "data": "rate","orderable":false,"width":"12%"},
       { "data": "total_time" ,"orderable":false,"width":"12%"},
       { "data": "total_mileage" ,"orderable":false,"width":"12%"},
       { "data": "drive_time_total" ,"orderable":false,"width":"15%"},
	   { "data": "total_price" ,"orderable":false,"width":"15%"},
       { "data": "action" ,"orderable":false,"width":"20%"},
     ],

    });
	oTables = $('#timesheet_records_datatable').DataTable();
            $('#btnSearch').click(function () {
            var SelectedArray = new Array();
            var SelectedArray = $('#SelectedProvider').val();
              oTables.columns(1).search(SelectedArray);
              oTables.draw();
            });
    //Apply Custom search on jQuery DataTables here
            oTables = $('#clinic_table').DataTable();
            $('#btnSearch').click(function () {
            var SelectedArray = new Array();
            var SelectedArray = $('#SelectedProvider').val();
              oTables.columns(1).search(SelectedArray);
              oTables.draw();
            });
	var cities_table 	=	$('#cities_table').DataTable({
		 "ajax": "{{URL::route('ajax_cities')}}",
		 "paging": true,
		 "bLengthChange": false,
		 "ordering": true,
		 "info": true,
		 "autoWidth": false,
		 processing: true,
		 serverSide: true,
		 "order": [[ 0, "desc" ]],
		 columnDefs: [
		   { targets: [0,4]},
		 ],
     "columns": [
       { "data": "sno","orderable":false},
       { "data": "city_name","orderable":true},
       { "data": "description" ,"orderable":true},
       { "data": "date" ,"orderable":true},
       { "data": "action" ,"orderable":false,},
     ],
	});
	$("#city_form").validate({
		rules:{
			city_name:{
				required:true
			},
		},
		errorElement: 'div',
	});
	$("#edit_city_form").validate({
		rules:{
			city_name:{
				required:true
			},
		},
		errorElement: 'div',
	});
	$("#user_email_form").validate({
		rules:{
			templatename:{
				required:true
			},
			subject:{
				required:true
			},
			constants:{
				required:true
			},
			action_constants:{
				required:true
			},
			body:{
				required:true
			},
		},
		errorElement: 'div',
	});
  });
    //alert(provider_id);
  $(document).ready(function(){
    ajaxloadcerificate_url = "{{URL::route('ajaxloadcerificate')}}";

    var certificate_table 	=	$('#certificate_table').DataTable({
     "ajax": ajaxloadcerificate_url,
     "paging": true,
     "bLengthChange": false,
     "ordering": true,
     "info": true,
     "autoWidth": false,
     processing: true,
     serverSide: true,
     "orderMulti": false, // for disable multi column order
     "dom": '<"top"i>rt<"bottom"lp><"clear">',
     "type": "POST",
     "order": [[ 0, "desc" ]],
     "datatype": "json",
     columnDefs: [
       { targets: [0,4]},
     ],
     "columns": [
       { "data": "sno","orderable":false},
       { "data": "name","orderable":true},
       { "data": "subject" ,"orderable":true},
       { "data": "file" ,"orderable":false},
       { "data": "description" ,"orderable":true},
       { "data": "uploaded at" ,"orderable":true},
       { "data": "action" ,"orderable":false,},
     ],
	 'fnDrawCallback':function(){
										$(".fancybox").fancybox({loop : true,
										'navigation':true,
										'type': 'image'
										});
									}

    });

    //Apply Custom search on jQuery DataTables here
            oTable = $('#certificate_table').DataTable();
            $('#btnSearch').click(function () {
            var realvalues = new Array();
            var realvalues = $('#SelectedProvider').val();
              //Apply search for Country // DataTable column index 3
              oTable.columns(1).search(realvalues);
              //hit search on server
              oTable.draw();
            });


  });

  $(document).ready(function(){
    var admin_table 	=	$('#admin_table').DataTable({
     "ajax": "{{URL::route('ajaxloadadmin')}}",
     "paging": true,
     "bLengthChange": false,
     "ordering": true,
     "info": true,
     "autoWidth": false,
     processing: true,
     serverSide: true,
     "order": [[ 0, "desc" ]],
     columnDefs: [
       { targets: [0,5]},
     ],
     "columns": [
       { "data": "sno","orderable":false},
       { "data": "name","orderable":true},
       { "data": "email" ,"orderable":true},
       { "data": "phone" ,"orderable":true},
       { "data": "status" ,"orderable":true},
       { "data": "action" ,"orderable":false,},
     ],

    });
  });
  
  $(document).ready(function(){
    var clockin_away_table 	=	$('#clockin_away_table').DataTable({
     "ajax": "{{URL::route('ajaxloadclock_in_away')}}",
     "paging": true,
     "bLengthChange": false,
     "ordering": true,
     "info": true,
     "autoWidth": false,
     processing: true,
     serverSide: true,
     "order": [[ 0, "desc" ]],
     columnDefs: [
       { targets: [0,6]},
     ],
     "columns": [
       { "data": "sno","orderable":false},
       { "data": "clinic_name","orderable":true},
       { "data": "provider_name" ,"orderable":true},
       { "data": "clocked_in_lat" ,"orderable":true},
       { "data": "clocked_in_long" ,"orderable":true},
       { "data": "clinic_date" ,"orderable":true},
       { "data": "action" ,"orderable":false,},
     ],

    });
  });

  $(document).ready(function(){
    var announcement_table 	=	$('#announcement_table').DataTable({
     "ajax": "{{URL::route('ajaxloadannouncement')}}",
     "paging": true,
     "bLengthChange": false,
     "ordering": true,
     "info": true,
     "autoWidth": false,
     processing: true,
     serverSide: true,
     "order": [[ 0, "desc" ]],
     columnDefs: [
       { targets: [0,6]},
     ],
     "columns": [
       { "data": "sno","orderable":false,'Width':'3%'},
       { "data": "title","orderable":true,'Width':'12%'},
       { "data": "image" ,"orderable":false,'Width':'10%'},
       { "data": "description" ,"orderable":true,'Width':'30%'},
	   { "data": "uploaded" ,"orderable":true,'Width':'12%'},
       { "data": "status" ,"orderable":true,'Width':'10%'},
       { "data": "action" ,"orderable":false,'Width':'22%'},
     ],
     'fnDrawCallback':function(){
                     $(".fancybox").fancybox({loop : true,
                     'navigation':true,
                     'type': 'image'
                     });
                   }

    });
    $("#change_password_form").validate({
		rules:{
			old_password:{
				required:true
			},
			new_password:{
				required:true,
			},
			confirm_password:{
				required:true,
				equalTo: '#new_password'
			},
		},
    errorElement: 'div',
		submitHandler: changepassword
	});
	function changepassword(){
		$(".loader").show();
					$.ajax({
						type: "POST",
						url:"{{URL::route('admin_change_password')}}",
						data: $("#change_password_form").serialize(),
						success: function(res){
							if(res==1){
								$(".loader").hide();
								var message				      =	'Congratulation you have succefully changed your password.';
								$('.success p').html(message);
                $('.success').css('display','block');
                $('#old_password').val('');
                $('#new_password').val('');
                $('#confirm_password').val('');
                window.setTimeout(function () {
                $('.success').css('display','none');
                 }, 4000);
								return false;
							}else if(res==0){
                var errormessage				=	'Current password is not correct.';
                $('.error p').html(errormessage);
                window.setTimeout(function () {
                $('.error').css('display','none');
                 }, 4000);
                $('.error').css('display','block');
							}else if(res==-1){
								$(".loader").hide();
							}
						}
					});
			 }
       $("#change_security_form").validate({
   		rules:{
   			old_pin:{
   				required:true
   			},
   			new_pin:{
   				required:true,
   			},
   			confirm_pin:{
   				required:true,
   				equalTo: '#new_pin'
   			},
   		},
       errorElement: 'div',
   		submitHandler: change_security_pin
   	});
   	function change_security_pin(){
   		$(".loader").show();
   					$.ajax({
   						type: "POST",
   						url:"{{URL::route('admin_change_pin')}}",
   						data: $("#change_security_form").serialize(),
   						success: function(res){
   							if(res==1){
   								$(".loader").hide();
   								var message	  =	'Congratulation you have succefully changed your pin.';
   								$('.success p').html(message);
                   $('.success').css('display','block');
                   $('#old_pin').val('');
                   $('#new_pin').val('');
                   $('#confirm_pin').val('');
                   window.setTimeout(function () {
                   $('.success').css('display','none');
                    }, 4000);
   								return false;
   							}else if(res==0){
                   var errormessage				=	'Current password is not correct.';
                   $('.error p').html(errormessage);
                   window.setTimeout(function () {
                   $('.error').css('display','none');
                    }, 4000);
                   $('.error').css('display','block');
   							}else if(res==-1){
   								$(".loader").hide();
   							}
   						}
   					});
   			 }
       $("#edit_user_profile").validate({
   		rules:{
   			first_name:{
   				required:true
   			},
   			last_name:{
   				required:true,
   			},
        email:{
   				required:true,
          email: true
   			},
        phone:{
   				required:true,
          number: true,
   			},
   		},
      errorElement: 'div',
   	  });
       $("#provider_form").validate({
   		rules:{
   			first_name:{
   				required:true
   			},
   			last_name:{
   				required:true,
   			},
        email:{
   				required:true,
          email: true
   			},
        phone:{
   				required:true,
          number: true,
   			},
        address:{
   				required:true,
   			},
        password:{
   				required:true,
   			},
        social_security_number:{
   				required:true,
          number: true,
          minlength: 4,
          maxlength: 4
   			},
        provider_type:{
          required:true,
        },
        hourly_rate:{
          required:true,
          number: true,
        },
   		},
      errorElement: 'div',
   	});
    $("#edit_provider_form").validate({
     rules:{
       first_name:{
         required:true
       },
       last_name:{
         required:true,
       },
       email:{
         required:true,
         email: true
       },
       phone:{
         required:true,
         number:true
       },
       address:{
         required:true,
       },
       provider_type:{
         required:true,
       },
       hourly_rate:{
         required:true,
       },
     },
     errorElement: 'div',
   });
   $("#clinic_form").validate({
    rules:{
      name:{
        required:true,
      },
      phone:{
        required:true,
        number:true
      },
      date:{
        required:true,
      },
      time:{
        required:true,
      },
      preptime:{
        number:true,
      },
      providers:{
        required:true,
      },
      estimated_duration:{
        required:true,
        number:true,
      },
      personnel:{
        required:true,
      },
      location:{
        required:true,
      }, 
    },
	errorElement: 'div',
	/* submitHandler: function(clinic_form) {
			var providers = $('#providers').val();
			var manualprovider = $('#manualprovider').val();
			if(providers == null && manualprovider == null){
				$("#providers_error").text('Please select atleast 1 value between providers & manual providers');
				$("html, body").animate({ scrollTop: 0 }, "slow");
				return false;
			}else{
				clinic_form.submit();
			}
	  } */
    });
    $("#asign_rules").validate({
     rules:{
       name:{
         required:true,
       },
     },
     errorElement: 'div',
     });
    $("#edit_clinic_form").validate({
     rules:{
       name:{
         required:true,
       },
       phone:{
         required:true,
         number:true
       },
       date:{
         required:true,
       },
       time:{
         required:true,
       },
       preptime:{
         number:true,
       },
       providers:{
         required:true,
       },
       estimated_duration:{
         required:true,
         number:true,
       },
       personnel:{
         required:true,
       },
     },
     errorElement: 'div',
	 /* submitHandler: function(clinic_form) {
			var providers = $('#providers').val();
			if(providers == null){
				$("#providers_error").text('Please select providers ');
				$("html, body").animate({ scrollTop: 0 }, "slow");
				return false;
			}else{
				edit_clinic_form.submit();
			}
	  } */
     });
   $("#add_certificate_form").validate({
    rules:{
      user_id:{
        required:true
      },
      subject:{
        required:true,
      },
      description:{
        required:true,
      },
      file:{
        required:true,
      },
    },
    errorElement: 'div',
  });
  $("#edit_certificate_form").validate({
   rules:{
     user_id:{
       required:true
     },
     subject:{
       required:true,
     },
     description:{
       required:true,
     },
   },
   errorElement: 'div',
  });
  $("#add_admin_form").validate({
     rules:{
       first_name:{
         required:true
       },
       last_name:{
         required:true,
       },
       email:{
         required:true,
         email: true
       },
       phone:{
         required:true,
         number:true
       },
       password:{
         required:true,
       },
       confirm_password:{
         required:true,
         equalTo: '#password'
       },
       security_pin:{
         required:true,
         number:true,
         minlength: 4,
         maxlength: 4
       },
     },
     errorElement: 'div',
    });
    $("#edit_admin_form").validate({
       rules:{
         first_name:{
           required:true
         },
         last_name:{
           required:true,
         },
         email:{
           required:true,
           email:true
         },
         phone:{
           required:true,
           number:true
         },
       },
       errorElement: 'div',
      });
      $("#announcement_form").validate({
       rules:{
         title:{
           required:true
         },
         description:{
           required:true,
         },
       },
       errorElement: 'div',
     });
     $("#edit_announcement_form").validate({
      rules:{
        title:{
          required:true
        },
        description:{
          required:true,
        },
      },
      errorElement: 'div',
    });

    $("#edit_provider_calender").validate({
		rules:{
			mileage_info:{
				required:true
			},
			drive_time:{
				required:true,
			},
			time_card:{
				required:true,
			},
		},
    errorElement: 'div',
		submitHandler: edit_provider_calender
	});
	function edit_provider_calender(){
		$(".loader").show();
					$.ajax({
						type: "POST",
						url:"{{URL::route('edit_provider_calender')}}",
						data: $("#edit_provider_calender").serialize(),
						success: function(res){
							$("html, body").animate({
								scrollTop: 0
							}, 600);
							if(res==1){
								$(".loader").hide();
								var message			 =	'Provider mileage and drive time data successfully updated.';
								$('.success p').html(message);
                $('.success').css('display','block');
                window.setTimeout(function () {
                $('.success').css('display','none');
                 }, 4000);
								return false;
							}else if(res==0){
                var errormessage				=	'Error occured.';
                $('.error p').html(errormessage);
                window.setTimeout(function () {
                $('.error').css('display','none');
                 }, 4000);
                $('.error').css('display','block');
							}else if(res==-1){
								$(".loader").hide();
							}
						}
					});
			 }
       $("#edit_provider_clockout").validate({
   		rules:{
   			clockout_time:{
   				required:true
   			},
   			clockout_distance:{
   				required:true,
   			},
   		},
       errorElement: 'div',
   		submitHandler: edit_provider_clockout
   	});
   	function edit_provider_clockout(){
   		$(".loader").show();
   					$.ajax({
   						type: "POST",
   						url:"{{URL::route('edit_provider_clockout')}}",
   						data: $("#edit_provider_clockout").serialize(),
   						success: function(res){
							$("html, body").animate({
								scrollTop: 0
							}, 600);
   							if(res==1){
   								$(".loader").hide();
   								var message			 =	'Provider clock out settings successfully updated.';
   								$('.success p').html(message);
                   $('.success').css('display','block');
                   window.setTimeout(function () {
                   $('.success').css('display','none');
                    }, 4000);
   								return false;
   							}else if(res==0){
                   var errormessage				=	'Error occured.';
                   $('.error p').html(errormessage);
                   window.setTimeout(function () {
                   $('.error').css('display','none');
                    }, 4000);
                   $('.error').css('display','block');
   							}else if(res==-1){
   								$(".loader").hide();
   							}
   						}
   					});
   			 }
         $("#view_security_pin").validate({
        rules:{
          password:{
            required:true
          },
        },
         errorElement: 'div',
        submitHandler: view_security_pin
      });
      function view_security_pin(){
        $(".loader").show();
              $.ajax({
                type: "POST",
                url:"{{URL::route('view_security_pin')}}",
                data: $("#view_security_pin").serialize(),
                success: function(res){
                  if(res==0){
                    var errormessage				=	'Incorrect password !!!!';
                    $('.error p').html(errormessage);
                    window.setTimeout(function () {
                    $('.error').css('display','none');
                     }, 4000);
                    $('.error').css('display','block');
                    $("#security_pin_model").leanModal().closeModal();
                    window.setTimeout(function () {
                      location.reload(true);
                  }, 1000);
                  }else{
                    $("#security_pin_model").leanModal().closeModal();
                     $('.maskable').html(res);
                     $('#show_security_pin').hide();
                    return false;
                  }
                }
              });
           }

   $('.selectall').click(function() {
    if ($(this).is(':checked')) {
        $('input:checkbox').prop('checked', true);
    } else {
        $('input:checkbox').prop('checked', false);
    }
  });
   $('.selectall_announcement').click(function() {
    if ($(this).is(':checked')) {
        $('.announcement_checkbox').prop('checked', true);
    } else {
        $('.announcement_checkbox').prop('checked', false);
    }
  });
  $('.selectallcity').click(function() {
   if ($(this).is(':checked')) {
       $('.city_checkbox').prop('checked', true);
   } else {
       $('.city_checkbox').prop('checked', false);
   }
 });
  $('#Deleteall').click(function(){
    if(confirm("Are you sure you want to delete this?")){
      var values = [];
      $('#table-datatables table').find('input[type=checkbox]:checked').each(function(){
          values.push($(this).val());
    });
    $.ajax({
      type: "POST",
      url:"{{URL::route('delete-certificates')}}",
      data:"chk_ids="+values,
      success: function(res){
        if(res==1){
          location.reload(true);
          $(".loader").hide();
          var message	  =	'Certificates successfully deleted.';
          $('.success p').html(message);
           $('.success').css('display','block');
           window.setTimeout(function () {
           $('.success').css('display','none');
            }, 4000);
          return false;
        }else if(res==0){
           var errormessage				=	'Please select atleast one checkbox.';
           $('.error p').html(errormessage);
           window.setTimeout(function () {
           $('.error').css('display','none');
            }, 4000);
           $('.error').css('display','block');
        }else if(res==-1){
          $(".loader").hide();
        }
      },
  error: function(XMLHttpRequest, textStatus, errorThrown) {
      alert("Status: " + textStatus); alert("Error: " + errorThrown);
  }
    });
    }
    else{
        return false;
    }

    });
    $('#DownloadZip').click(function(){
      if($(".checked_certificates:checked").length>0){
        if(confirm("Are you sure you want to download zip of selected records?")){
          var values = [];
          $('#table-datatables table').find('input[type=checkbox]:checked').each(function(){
              values.push($(this).val());
        });
        $.ajax({
          type: "POST",
          url:"{{URL::route('download-certificates')}}",
          data:"chk_ids="+values,
          success: function(res){
			  console.log(res);
              window.location.href = res;
          },
        });
        }
        else{
            return false;
        }
      }else{
        alert('Please select atleast one record');
      }

      });
	$('#download_all_certificates').click(function(){
		var provider_id = $(this).data('provider');
        $.ajax({
          type: "POST",
          url:"{{URL::route('download-certificates')}}",
          data:"provider_id="+provider_id,
          success: function(res){
			  if(res == 0){
					alert('No records found.');
			  }else{
					window.location.href = res;
			    }
		  }
        });
      });
  });
function delete_confirm(){
  if(confirm('are you sure you want to delete this record')){
    return true;
  }
  else{
      return false;
  }
}
function changestatus(){
  if(confirm('are you sure you want to change status')){
    return true;
  }
  else{
      return false;
  }
}

</script>

<script>
$(document).ready(function(){
    // the "href" attribute of .modal-trigger must specify the modal ID that wants to be triggered
    $('.dlt').click(function(){
      $('#modal1').openModal();
    });

  });
  
</script>
<div id="message_modal">
<div id="confirm_modal" class="modal">
  <div class="modal-header">
    <div class="modal-title" id="modal-title">
      <h5 align="center"></h5>
    </div>
  </div>
  <div class="modal-content">
    <p></p>
  </div>
  <div class="modal-footer">
    <a href="javascript:void(0)" class="waves-effect waves-red btn-flat modal-action modal-close">Cancel</a>
    <a href="javascript:void(0)" id="confirm_ok_btn" class="waves-effect waves-green btn-flat modal-action modal-close">Confirm</a>
  </div>
</div>
</div>
