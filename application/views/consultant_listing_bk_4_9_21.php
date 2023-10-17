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
				<table class="table table-hover" id="tbl_lists">
					<thead>
					<tr>
						<th width="10%" style="text-align:center">Name</th>
						<th width="10%" style="text-align:center">Mobile No</th>
						<th width="10%" style="text-align:center">E-mail Id</th>
						<th width="10%" style="text-align:center">Technology</th>
						<th width="10%" style="text-align:center">Bank Name</th>
						<th width="10%" style="text-align:center">Status</th>
						<th width="10%" style="text-align:center">Approved</th>
						<th width="10%" style="text-align:center">Action</th>
					</tr>
					</thead>
					<tbody>
					<?php
					if (!empty($consultant)) {
						$prevDay = '';
						foreach ($consultant as $cons) {
							$status = "In active";
							if ($cons['IsActive'] == 1) {
								$status = "Active";
							}
							?>
							<tr>
								<td width="10%"> <?php echo $cons['ConsultantFirstName'] . ' ' . $cons['ConsultantLastName']; ?></td>
								<td width="10%"><?php echo $cons['ConsultantMobileNo']; ?></td>
								<td width="10%"><?php echo $cons['ConsultantEmailId']; ?></td>
								<td width="10%"><?php echo $cons['ConsultantTechnology']; ?></td>
								<td width="10%"><?php echo $cons['ConsultantBankName']; ?></td>
								<td width="10%"><?php echo $status; ?></td>
								<td width="10%" ><input id="approved" type='checkbox' disabled <?php if ($cons['isselfregisterApproved'] == 1) {
										echo "checked='checked'";
									}?>/>

								<td width="10%" data-title="Action" id="action-event" >&nbsp; &nbsp;
									<?php if ($cons['isselfregisterApproved'] != 1) {?>
										<a class='actions'href="<?= base_url().'consultant/editConsultantDetails/'.$cons['ConsultantICode'] ?>" class="pos-rel">
											<span class="glyphicon glyphicon-pencil" title="Edit" data-toggle="tooltip" data-placement="top"></span>
										</a>
										&nbsp; &nbsp;
										<a class='actions' href='javascript:void(0)' onclick="getFirstApprovePopup('<?php echo base64_encode($cons['ConsultantICode']); ?>', '<?php echo $cons['ConsultantFirstName'] . ' ' . $cons['ConsultantLastName']; ?>', '<?php echo $cons['ConsultantEmailId'] ?>', '<?php echo $cons['ConsultantMobileNo'] ?>')" class="task-close pos-rel">
											<span class="glyphicon glyphicon-ok" title="Approve" data-toggle="tooltip" data-placement="top"></span>
										</a>
									<?php } else { ?>
										<a class='actions' class="pos-rel" href='javascript:void(0)' onclick="getUnapproveConsultantPopup('<?php echo base64_encode($cons['ConsultantICode']); ?>', '<?php echo $cons['ConsultantFirstName'] . ' ' . $cons['ConsultantLastName']; ?>')">
											<span class="glyphicon glyphicon-remove"  title="Un-Approve" data-toggle="tooltip" data-placement="top"></span>
										</a>
									<?php } ?>
								</td>
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
							<small id="usernameError"></small>
							<br>
							<label for="consultantPassword">Password : </label>
							<input type="text" name="consultantPassword" id="consultantPassword" class="form-control" required>
							<small id="passwordError"></small>
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
			/*- Show the Loading Bar -*/
			jQuery("#loading-bar").show();
			jQuery.ajax({
				type: "POST",
				url: "<?php echo base_url(); ?>consultant/updateConsultantEntry",
				data: {id: id, userName: username, password: password},
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
			jQuery('#approve_consultant').modal('hide');
		}
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
		jQuery('#approve_consultant').modal('show');

	}

	function getFirstApprovePopup(id, name, email, mobile){
		var consultantName = document.getElementById('consName');
		consultantName.innerText = name;
		var consultantMobile = document.getElementById('consMobile');
		consultantMobile.innerText = mobile;
		var consultantEmail = document.getElementById('consEmail');
		consultantEmail.innerText = email;
		var approveBtn = document.getElementById('approveConsBtn');
		approveBtn.onclick = function() {
			getApproveConsultantPopup(id, name);
			jQuery('#approvePopup').modal('hide');
		};
		jQuery('#approvePopup').modal('show');

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
