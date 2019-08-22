<!-- ================================================
    Scripts
    ================================================ -->

  <!-- jQuery Library -->
  {{ Html::script('public/assets/admin/js/plugins/jquery-1.11.2.min.js') }}
  <!--materialize js-->
  {{ Html::script('public/assets/admin/js/materialize.min.js') }}
  <!--prism-->
  {{ Html::script('public/assets/admin/js/plugins/prism/prism.js') }}
  <!--scrollbar-->
  {{ Html::script('public/assets/admin/js/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}
  {{ Html::script('public/assets/admin/js/jquery.validate.js') }}

      <!--plugins.js - Some Specific JS codes for Plugin Settings-->
	  {{ Html::script('public/assets/admin/js/plugins.min.js') }}
    <!--custom-script.js - Add your own theme custom JS-->
	{{ Html::script('public/assets/admin/js/custom-script.js') }}
	
	<script type="text/javascript">
	$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
    }
	});
	$("#resetpasswordform").validate({
   		rules:{
   			newpassword:{
   				required:true
   			},
   			confirmpassword:{
   				required:true,
				qualTo: '#newpassword'
   			},
   		},
       errorElement: 'div',
   		//submitHandler: user_reset_password,
   	});
   	/* function user_reset_password(){
		$.ajax({
			type: "POST",
			url:"user_reset_password",
			data: $("#resetpasswordform").serialize(),
			success: function(res){
				if(res==1){
					$(".loader").hide();
					var message	  =	'Congratulation you have succefully changed your password.';
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
   			 } */
</script>