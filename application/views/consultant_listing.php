<?php include 'header.php';?>

<?php include 'menu.php';?>

<div class="container">
	<div class="starter-template">
		<?php if ($this->session->flashdata('success')) {?>
			<div class="alert alert-success " role="alert">
				<?php echo $this->session->flashdata('success'); ?>
			</div>
		<?php }?>
		<?php if ($this->session->flashdata('warning')) {?>
			<div class="alert alert-danger " role="alert">
				<?php echo $this->session->flashdata('warning'); ?>
			</div>
		<?php }?>
		<div class="alert_messages">
		</div>

		<div class="loading-bar" id="loading-bar">
			<div class="loading-inner">
				<div class="progress">
				  <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
					<span class="sr-only">100% Complete</span>
				  </div>
				</div>
			</div>
		</div>



		<div class="panel panel-info ovrhid">
			<div class="panel-heading">
				<h3 class="panel-title">Consultant Details</h3>
				<div class="pull-right">

				</div>
			</div>
			<!-- Table Entrying -->
			<br>
			<div id="no-more-tables" class="table-responsive" style="text-align:center">
				<!-- <table class="col-md-12 table-bordered table-striped table-condensed cf table-hover" id="tbl_list"> -->
				<form action="<?php echo current_url(); ?>" method="get">
					<div class="col-md-2">
						<button type="submit" name="refresh" value="1" class="btn btn-sm btn-primary">Refresh</button>
					</div>
					<div class="col-md-2"></div>
					<div class="col-md-4"></div>
					<div class="col-md-2">
						<div class="form-group">
							<select name="status" id="status_filter" class="form-control">
								<option value="all" <?php echo (!$this->input->get('status') || $this->input->get('status') == 'all') ? 'selected' : ''; ?>>All Consultants</option>
								<option value="active" <?php echo ($this->input->get('status') == 'active') ? 'selected' : ''; ?>>Active</option>
								<option value="inactive" <?php echo ($this->input->get('status') == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
								<?php if($this->session->userdata('role') == 'HR') { ?>
									<option value="pending" <?php echo ($this->input->get('status') == 'pending') ? 'selected' : ''; ?>>Approval Pending</option>
									<!-- <option value="approved" <?php //echo ($this->input->get('status') == 'approved') ? 'selected' : ''; ?>>Approved</option> -->
								<?php } ?>
								<option value="not_utilized" <?php echo ($this->input->get('status') == 'not_utilized') ? 'selected' : ''; ?>>Not Utilized</option>
								<option value="not_utilized_30_days" <?php echo ($this->input->get('status') == 'not_utilized_30_days') ? 'selected' : ''; ?>>Not Utilized more than 30 days</option>
							</select>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<button type="submit" class="btn btn-sm btn-info">Filter</button>
						</div>
					</div>
				</form>
				<table class="table table-hover" id="tbl_lists">
					<thead>
					<tr>
						<th width="5%">S.No.</th>
						<th width="10%" style="text-align:center">Name</th>
						<th width="10%" style="text-align:center">Mobile No</th>
						<th width="10%" style="text-align:center">E-mail Id</th>
						<th width="10%" style="text-align:center">Resume</th>
						<?php if($this->session->userdata('role') == 'Account') { ?>
							<th width="20%" style="text-align:center">Documents</th>
						<?php } ?>
						<th width="10%" style="text-align:center">Technology</th>
						<?php if($this->session->userdata('role') != 'Lead') { ?>
							<th width="5%" style="text-align:center">Bank Name</th>
						<?php } ?>
						<th width="5%" style="text-align:center">Status</th>
						<?php if($this->session->userdata('role') == 'HR') { ?>
							<th width="10%" style="text-align:center">Approved</th>
						<?php } ?>
						<?php if($this->session->userdata('role') == 'HR' || $this->session->userdata('role') == 'Lead') { ?>
							<th width="10%" style="text-align:center">Action</th>
						<?php } ?>
					</tr>
					</thead>
					<tbody>
					<?php
					if (!empty($consultant)) {
						$prevDay = '';
						$i = 0;
						foreach ($consultant as $cons) {
							$status = "Inactive";
							if (isset($cons['IsActive']) && $cons['IsActive'] == 1) {
								$status = "Active";
							}
							?>
							<tr>
								<td width="5%"><?php echo ++$i; ?></td>
								<td width="10%"> <?php echo $cons['ConsultantFirstName'] . ' ' . $cons['ConsultantLastName']; ?></td>
								<td width="10%"><?php echo $cons['ConsultantMobileNo']; ?></td>
								<td width="10%"><?php echo $cons['ConsultantEmailId']; ?></td>
								<td width="10%">
									<?php if(pathinfo(base_url().$cons['resume_path'], PATHINFO_EXTENSION)){ ?>	
										<a class='actions'href="<?= base_url().$cons['resume_path'] ?>" class="pos-rel" download>
												<?php 
												$path = base_url().$cons['resume_path'];
	
												$file1 = basename($path);
												
											
												
												// echo pathinfo($file1, PATHINFO_EXTENSION);
												echo $file1;
												?>
										</a>
									<?php } else { ?>
										-
									<?php } ?>
								</td>

								<?php if($this->session->userdata('role') == 'Account') { ?>
									<td width="20%">
										<ul style="list-style-type: none;">
											<li>
												<?php if($cons['PanDocument']) { ?>
													<a href="<?php echo base_url().$cons['PanDocument']; ?>" target="_blank">PAN Card</a>
												<?php } else { ?>
													<a style="pointer-events: none; opacity: 0.5;">PAN Card</a>
												<?php } ?>
											</li>
											<li>
												<?php if($cons['AadharDocument']) { ?>
													<a href="<?php echo base_url().$cons['AadharDocument']; ?>" target="_blank">Aadhar Card</a>
												<?php } else { ?>
													<a style="pointer-events: none; opacity: 0.5;">Aadhar Card</a>
												<?php } ?>
											</li>
											<li>
												<?php if($cons['BankDocument']) { ?>
													<a href="<?php echo base_url().$cons['BankDocument']; ?>" target="_blank">Bank Statement / Cheque</a>
												<?php } else { ?>
													<a style="pointer-events: none; opacity: 0.5;">Bank Statement / Cheque</a>
												<?php } ?>
											</li>
											<li>
												<?php if($cons['NDADocument']) { ?>
													<a href="<?php echo base_url().$cons['NDADocument']; ?>" target="_blank">NDA Document</a>
												<?php } else { ?>
													<a style="pointer-events: none; opacity: 0.5;">NDA Document</a>
												<?php } ?>
											</li>
										</ul>
									</td>
								<?php } ?>

								<td width="10%"><?php 
									$techId = $cons['ConsultantTechnology'];
									foreach ($technologies as $tech) {
										if($tech['TechDomainid'] == $techId) {
											echo $tech['TechDomainName'];
										}
									}
								if ($cons['OtherSkills']) { echo ' ('.$cons['OtherSkills'].')' ; } else {} ?></td>
								<?php if($this->session->userdata('role') != 'Lead') { ?>
									<td width="5%"><?php echo $cons['ConsultantBankName']; ?></td>
								<?php } ?>
								<td width="5%"><?php echo $status; ?></td>
								<?php if($this->session->userdata('role') == 'HR') { ?>
									<td width="10%" ><input id="approved" type='checkbox' disabled <?php if ($cons['isselfregisterApproved'] == 1) { echo "checked='checked'"; } ?>/>
									</td>
								<?php } ?>
								<?php if($this->session->userdata('role') == 'HR' || $this->session->userdata('role') == 'Lead') { ?>

								<td width="10%" data-title="Action" id="action-event" >&nbsp; &nbsp;
									
										<a class='actions'href="<?= base_url().'consultant/editConsultantDetails/'.$cons['ConsultantICode'] ?>" class="pos-rel">
											<?php if($this->session->userdata('role') == 'Lead') { ?>
												<span class="glyphicon glyphicon-eye-open" title="View" data-toggle="tooltip" data-placement="top"></span>
											<?php } else {?>
												<span class="glyphicon glyphicon-pencil" title="Edit" data-toggle="tooltip" data-placement="top"></span>
											<?php } ?>
										</a>
										&nbsp; &nbsp;
										<?php if ($cons['isselfregisterApproved'] != 1) {?>
										<a class='actions' href='javascript:void(0)' onclick="getFirstApprovePopup('<?php echo base64_encode($cons['ConsultantICode']); ?>', '<?php echo $cons['ConsultantFirstName'] . ' ' . $cons['ConsultantLastName']; ?>', '<?php echo $cons['ConsultantEmailId'] ?>', '<?php echo $cons['ConsultantMobileNo'] ?>', '<?php echo $cons['isselfregisterApproved'] ?>')" class="task-close pos-rel">
											<span class="glyphicon glyphicon-ok" title="Approve" data-toggle="tooltip" data-placement="top"></span>
										</a>
									<?php } else { ?>
										<?php if($this->session->userdata('role') == 'HR') { ?>
											<a class='actions' class="pos-rel" href='javascript:void(0)' onclick="getUnapproveConsultantPopup('<?php echo base64_encode($cons['ConsultantICode']); ?>', '<?php echo $cons['ConsultantFirstName'] . ' ' . $cons['ConsultantLastName']; ?>')">
												<span class="glyphicon glyphicon-remove"  title="Un-Approve" data-toggle="tooltip" data-placement="top"></span>
											</a>
										<?php } ?>
									<?php } ?>
								</td>
								<?php } ?>
							</tr>
							<?php
						}
					}?>
					<tbody>
				</table>
			</div>
			<p>&nbsp;</p>
		</div>
	</div>
</div>
</div>


<div class="modal fade" id="approve_consultant" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
	<div class="modal-dialog" role="document" style="width:40%;  max-height: 40% !important;" >
		<div class="modal-content panel panel-info ovrhid">
			<div class="modal-header panel-heading">
				<h5 class="modal-title panel-title" id="exampleModalLongTitle">Approve Consultant</h5>
			</div>
			<div class="modal-body">
				<form acton="">
					<div class="row">
						<div class="col-md-12 text-center">
							<h5 id="consultantNameOnModel">Please enter the username and password for the consultant.</h5>
							<br>
							<br>
						</div>
						<div class="col-md-12">
							<label for="consultantUsername">Username : </label>
							<input type="text" name="consultantUsername" id="consultantUsername" class="form-control" required>
							<small id="usernameError" class="text-danger"></small>
							<br>
							<label for="consultantPassword">Password : </label>
							<input type="text" name="consultantPassword" id="consultantPassword" class="form-control" required>
							<small id="passwordError" class="text-danger"></small>
							<br>
							<label for="HourlyRate">Hourly Rate : </label>
							<input type="text" name="HourlyRate" id="HourlyRate" class="form-control">
							
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 text-center">
							<button class="btn btn-success" type="button" id="approveConsultantBtn">Approve</button>
							<button class="btn btn-danger" data-dismiss="modal">Cancel</button>
							<br>
							<br>
						</div>
					</div>
				</form>
			</div>

		</div>
	</div>
</div>

<div class="modal fade" id="unapprove_consultant" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
	<div class="modal-dialog" role="document" style="width:40%;  max-height: 40% !important;" >
		<div class="modal-content panel panel-info ovrhid">
			<div class="modal-header panel-heading">
				<h5 class="modal-title panel-title" id="exampleModalLongTitle">Unapprove Consultant</h5>
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="manual_entry_form">
					<div class="row">
						<div class="col-md-12 text-center">
							<h4 id="consultantNameOnUnapproveModel">Are you sure you want to Unapprove?</h4>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 text-center">
							<button type="button" class="btn btn-secondary" id="unapproveConsultantBtn" >Yes</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
						</div>
					</div>
				</form>
			</div>

		</div>
	</div>
</div>

<div class="modal fade" id="approvePopup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
	<div class="modal-dialog" role="document" style="width:40%;  max-height: 40% !important;" >
		<div class="modal-content panel panel-info ovrhid">
			<div class="modal-header panel-heading">
				<h5 class="modal-title panel-title" id="exampleModalLongTitle">Approve Consultant.</h5>
			</div>
			<div class="modal-body">
				<form id="manual_entry_form">
					<div class="row">
						<div class="col-md-12 text-center">
							<p><span class="consultantDetail">Consultant Name:</span> <span id="consName"></span> </p>
							<p><span class="consultantDetail">Consultant Mobile:</span> <span id="consMobile"></span> </p>
							<p><span class="consultantDetail">Consultant Email:</span> <span id="consEmail"></span> </p>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 text-center">
							<button type="button" class="btn btn-success" id="approveConsBtn">Approve</button>
							<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
							<br>
							<br>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php include 'footer.php';?>
</body>
</html>
<script type="text/javascript">

	function approveConsultant(id) {
		var nameErr = document.getElementById('usernameError');
		var passErr = document.getElementById('passwordError');

		var username = document.getElementById('consultantUsername').value;
		var password = document.getElementById('consultantPassword').value;
		var HourlyRate = document.getElementById('HourlyRate').value;

		var strongRegex = new RegExp("^((?=.*[a-zA-Z0-9])(?=.*[!@#$%^&*()-.]).{8,16})$");

		if(username.length <= 0) {
			nameErr.innerText = 'Please enter the username.';
		}
		else {
			nameErr.innerText = '';
		}
		if(password.length <= 0) {
			passErr.innerText = 'Please enter the password.';
		}
		else {
			passErr.innerText = '';
		}
		if(username.length != 0 && password.length != 0) {
			if(strongRegex.test(password)){
				/*- Show the Loading Bar -*/
				jQuery("#loading-bar").show();
				jQuery.ajax({
					type: "POST",
					url: "<?php echo base_url(); ?>consultant/updateConsultantEntry",
					data: {id: id, userName: username, password: password, HourlyRate: HourlyRate},
					success: function (res)
					{
						jQuery("#loading-bar").hide();
						console.log(res);
						var result = jQuery.parseJSON(res);
						var success = "<?php echo _SUCCESS; ?>";
						if (result.status == success) {
							jQuery('.action').hide();
							// jQuery('#action-event').text('-');
							jQuery('#approved').prop('checked', true);

							jQuery('.alert_messages').html('<div class="alert alert-success">' + result.message + '</div>');
							location.reload();
						} else {
							jQuery('.alert_messages').html('<div class="alert alert-danger">' + result.message + '</div>');
						}
						jQuery("#loading-bar").hide();
					}
				});
				jQuery('div#approve_consultant').modal('hide');
			} else {
				passErr.innerText = 'Please enter a valid password with atleat 8 characters , a number and a special character.';
			}
		}
	}

	function reApproveConsultant(id) {
		jQuery("#loading-bar").show();
		jQuery.ajax({
			type: "POST",
			url: "<?php echo base_url(); ?>consultant/reApproveConsultant",
			data: {id: id},
			success: function (res)
			{
				jQuery("#loading-bar").hide();
				console.log(res);
				var result = jQuery.parseJSON(res);
				var success = "<?php echo _SUCCESS; ?>";
				if (result.status == success) {
					jQuery('.action').hide();
					// jQuery('#action-event').text('-');
					jQuery('#approved').prop('checked', true);

					jQuery('.alert_messages').html('<div class="alert alert-success">' + result.message + '</div>');
					location.reload();
				} else {
					jQuery('.alert_messages').html('<div class="alert alert-danger">' + result.message + '</div>');
				}
			},
			error: function(err) {
				jQuery("#loading-bar").hide();
			}
		});
	}

	function unapproveConsultant(id) {

		/*- Show the Loading Bar -*/
		jQuery("#loading-bar").show();
		jQuery.ajax({
			type: "POST",
			url: "<?php echo base_url(); ?>consultant/unapproveConsultant",
			data: {id: id},
			success: function (res)
			{
				jQuery("#loading-bar").hide();
				console.log(res);
				var result = jQuery.parseJSON(res);
				var success = "<?php echo _SUCCESS; ?>";
				if (result.status == success) {
					jQuery('.action').hide();
					// jQuery('#action-event').text('-');
					jQuery('#approved').prop('checked', true);

					jQuery('.alert_messages').html('<div class="alert alert-success">' + result.message + '</div>');
					location.reload();
				} else {
					jQuery('.alert_messages').html('<div class="alert alert-danger">' + result.message + '</div>');
				}
				jQuery("#loading-bar").hide();
			}
		});
		jQuery('#unapprove_consultant').modal('hide');

	}

	function getApproveConsultantPopup(id, name){
		var nameTag = document.getElementById('consultantNameOnModel');
		nameTag.innerText = 'Please enter the username and password for '+name+'.';
		var button = document.getElementById('approveConsultantBtn');
		button.onclick = function() {
			approveConsultant(id);
		};
		jQuery('div#approve_consultant').modal('show');

	}

	function getFirstApprovePopup(id, name, email, mobile, isselfregisterApproved = ''){
		var consultantName = document.getElementById('consName');
		consultantName.innerText = name;
		var consultantMobile = document.getElementById('consMobile');
		consultantMobile.innerText = mobile;
		var consultantEmail = document.getElementById('consEmail');
		consultantEmail.innerText = email;
		var approveBtn = document.getElementById('approveConsBtn');
				
		if(isselfregisterApproved == '' || isselfregisterApproved == 0) {
			approveBtn.onclick = function() {
				getApproveConsultantPopup(id, name);
				jQuery('#approvePopup').modal('hide');
			};
			jQuery('#approvePopup').modal('show');	
		} else {
			reApproveConsultant(id);
		}

	}

	function getUnapproveConsultantPopup(id, name){
		var nameTag = document.getElementById('consultantNameOnUnapproveModel');
		nameTag.innerText = 'Are you sure you want to unapprove '+name+'?';
		var button = document.getElementById('unapproveConsultantBtn');
		button.onclick = function() {
			unapproveConsultant(id);
		};
		jQuery('#unapprove_consultant').modal('show');
	}

</script>
