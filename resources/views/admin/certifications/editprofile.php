<?php  $login = $this->session->userdata('is_admin_login');
if($login != 1){
	redirect('admin');
}  ?>
<div class="main-content">
				<div class="main-content-inner">
					<div class="breadcrumbs ace-save-state" id="breadcrumbs">
						<ul class="breadcrumb">
							<li>
								<i class="ace-icon fa fa-home home-icon"></i>
								<a href="#">Dashboard</a>
							</li>

							<li class="active">User Profile</li>
						</ul><!-- /.breadcrumb -->


					</div>

					<div class="page-content">

						<div class="row">
							<div class="col-xs-12">
							<div>

								<div>
									<div id="user-profile-3" class="user-profile row">
										<div class="col-sm-offset-1 col-sm-10">


											<?php $attributes = array('name' => 'myform','class'=>'form-horizontal','id'=>'myform','method'=>'post');
							echo form_open_multipart('',$attributes); ?>
							<div class="panel panel-default">
								<?php echo $this->session->flashdata('editadminmessage');?>
								<div class="panel-heading">Personal Information</div>
									<div class="panel-body">
											  <?php echo form_label('First Name<span class = "error">*</span>');?>
												<?php echo form_input(array('id' => 'firstname', 'name' => 'firstname','class'=>'form-control control3','value' => $admindata[0]["firstname"]));?>
													<span class="text-danger"><?php echo form_error('firstname'); ?></span><br>
												<?php echo form_label('Last Name<span class = "error">*</span>');?>
												<?php echo form_input(array('id' => 'lastname', 'name' => 'lastname','class'=>'form-control control3','value' => $admindata[0]["lastname"]));?>
													<span class="text-danger"><?php echo form_error('lastname'); ?></span><br>
												<?php echo form_label('Email<span class = "error">*</span>');?>
												<?php echo form_input(array('id' => 'email', 'name' => 'email','class'=>'form-control control3','value' => $admindata[0]["email"]));?>
													<span class="text-danger"><?php echo form_error('email'); ?></span><br>
												<?php echo form_label('Username<span class = "error">*</span>');?>
												<?php echo form_input(array('name' => 'username','class'=>'form-control control3','value' => $admindata[0]["username"]));?>
													<span class="text-danger"><?php echo form_error('username'); ?></span><br>
												<?php echo form_label('Contact<span class = "error">*</span>');?>
												<?php echo form_input(array('id' => 'contact', 'name' => 'contact','class'=>'form-control control3','value' => $admindata[0]["contact"]));?>
													<span class="text-danger"><?php echo form_error('contact'); ?></span><br>



							</div>

						</div>
							<?php echo form_input(array('type'=>'submit','class'=>'btn-success btn','value'=>'Update'));?>
							<?php echo anchor('admin/edit-profile','Cancel',array('class'=>'btn-danger btn'))?>
							<?php echo form_close(); ?>
										</div><!-- /.span -->
									</div><!-- /.user-profile -->
								</div>

								<!-- PAGE CONTENT ENDS -->
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.page-content -->
					</div>
				</div>
			</div><!-- /.main-content -->
