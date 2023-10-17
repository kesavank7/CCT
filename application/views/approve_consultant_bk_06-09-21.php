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
				<h3 class="panel-title">Approve Consultant</h3>
				<div class="pull-right">

				</div>
			</div>
			<div class="formBox">
				<div class="formContents">
					<p><span class="consultantDetail">Consultant Name:</span> <?php echo $consultant[0]['ConsultantFirstName'].' '.$consultant[0]['ConsultantLastName']; ?> </p>
					<p><span class="consultantDetail">Consultant Mobile:</span> <?php echo $consultant[0]['ConsultantMobileNo']; ?> </p>
					<p><span class="consultantDetail">Consultant Email:</span> <?php echo $consultant[0]['ConsultantEmailId']; ?> </p>
					<br>
					&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<button class="btn btn-green" onClick="getApproveConsultantPopup()">Approve</button>
					<button class="btn btn-red" onClick="getUnApproveConsultantPopup()">Decline</button>
				</div>
			</div>
		</div>
	</div>
</div>



<div class="modal fade" id="approve_consultant" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
	<div class="modal-dialog" role="document" style="width:40%;  max-height: 40% !important;" >
		<div class="modal-content panel panel-info ovrhid">
			<div class="modal-header panel-heading">
				<h5 class="modal-title panel-title" id="exampleModalLongTitle">Approve Consultant.</h5>
			</div>
			<div class="modal-body">
				<form id="manual_entry_form">
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
							<button class="btn btn-green" type="button" onClick="approveConsultant('<?php echo base64_encode($consultant[0]['ConsultantICode']); ?>')">Approve</button>
							<button class="btn btn-red" data-dismiss="modal">Cancel</button>
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
				<h5 class="modal-title panel-title" id="exampleModalLongTitle">Unapprove Consultant.</h5>
			</div>
			<div class="modal-body">
				<form id="manual_entry_form">
					<div class="row">
						<div class="col-md-12 text-center">
							<h4 id="consultantNameOnDelete">Are you sure you want to unapprove?</h4>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 text-center">
							<button type="button" class="btn btn-green" onClick="unApproveConsultant('<?php echo base64_encode($consultant[0]['ConsultantICode']); ?>')">Yes</button>
							<button type="button" class="btn btn-red" data-dismiss="modal">No</button>
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


<style>
	.formBox {
		width: 50%;
		margin-left: auto;
		margin-right: auto;
		margin-top: 50px;
		margin-bottom: 50px;
		box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
		padding: 40px 80px 50px 100px;
	}
	.formContents {
		margin-left: 30%;
	}

	.consultantDetail {
		font-size: 16px;
		font-weight: 600;
	}
</style>

<script>
	function approveConsultant(id) {

		var nameErr = document.getElementById('usernameError');
		var passErr = document.getElementById('passwordError');

		nameErr.style.color = passErr.style.color = 'red';

		var username = document.getElementById('consultantUsername').value;
		var password = document.getElementById('consultantPassword').value;

		var strongRegex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})");

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
					data: {id: id, userName: username, password: password},
					success: function (res)
					{
						jQuery("#loading-bar").hide();
						console.log(res);
						var result = jQuery.parseJSON(res);
						var success = "<?php echo _SUCCESS; ?>";
						if (result.status == success) {
							jQuery('.action').hide();
							jQuery('#action-event').text('-');
							jQuery('#approved').prop('checked', true);

							jQuery('.alert_messages').html('<div class="alert alert-success">' + result.message + '</div>');
						} else {
							jQuery('.alert_messages').html('<div class="alert alert-danger">' + result.message + '</div>');
						}
						jQuery("#loading-bar").hide();
					}
				});
				jQuery('#approve_consultant').modal('hide');
			} else{
				passErr.innerText = 'Please enter a valid password with atleat 8 characters , a number and a special character.';
			}
		}

	}
	function getApproveConsultantPopup(){
		jQuery('#approve_consultant').modal('show');

	}
	function getUnApproveConsultantPopup(){
		jQuery('#unapprove_consultant').modal('show');
	}
	function unApproveConsultant(id) {

		/*- Show the Loading Bar -*/
		jQuery("#loading-bar").show();
		jQuery.ajax({
			type: "POST",
			url: "<?php echo base_url(); ?>consultant/unApproveConsultant",
			data: {id: id},
			success: function (res)
			{
				jQuery("#loading-bar").hide();
				console.log(res);
				var result = jQuery.parseJSON(res);
				var success = "<?php echo _SUCCESS; ?>";
				if (result.status == success) {
					jQuery('.action').hide();
					jQuery('#action-event').text('-');
					jQuery('#approved').prop('checked', true);

					jQuery('.alert_messages').html('<div class="alert alert-success">' + result.message + '</div>');
				} else {
					jQuery('.alert_messages').html('<div class="alert alert-danger">' + result.message + '</div>');
				}
				jQuery("#loading-bar").hide();
			}
		});
		jQuery('#unapprove_consultant').modal('hide');
	}
</script>
