<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>CGVAK - Synergy</title>

	<!-- Bootstrap -->
	<link rel="icon" href="<?php echo base_url(); ?>public/favicon.png">
	<link href="<?php echo base_url(); ?>public/css/bootstrap.min.css" rel="stylesheet"/>
	<link href="<?php echo base_url(); ?>public/css/starter-template.css" rel="stylesheet"/>
	<link href="<?php echo base_url(); ?>public/css/bootstrap-3d.css" rel="stylesheet"/>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/css/form.css">
	<link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js"></script>
	<script>

		function checkForm(form)
		{
			if(!form.terms.checked) {
				alert("Please indicate that you accept the Agreement");
				form.terms.focus();
				return false;
			}else{
				// console.log(document.getElementsByClassName(".submitTab"));
				return true;
				// console.log(nextPrev(1));
				// return nextPrev(1);
				

			}
		}

	</script>

<body>
<div class="container">
	<nav class="navbar navbar-inverse navbar-fixed-top" id="menu_bar">
		<div class="container">
			<div class="navbar-header">
				<a class="navbar-brand" href="<?php echo base_url(); ?>">
					<img src="<?php echo base_url(); ?>public/images/logo/logo.png" alt="CG-Vak Logo" name="cgvak_logo"
						 class="header_logo"/>
				</a>
			</div>
		</div>
	</nav>
	<div id="formAlert"  class="row">
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
			<?php if($this->session->flashdata('success')){ ?>
				<div id='card' class="animated fadeIn">
					<div id='upper-side'>
						<svg version="1.1" id="checkmark" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" xml:space="preserve">
                        <path d="M131.583,92.152l-0.026-0.041c-0.713-1.118-2.197-1.447-3.316-0.734l-31.782,20.257l-4.74-12.65
                    c-0.483-1.29-1.882-1.958-3.124-1.493l-0.045,0.017c-1.242,0.465-1.857,1.888-1.374,3.178l5.763,15.382
                    c0.131,0.351,0.334,0.65,0.579,0.898c0.028,0.029,0.06,0.052,0.089,0.08c0.08,0.073,0.159,0.147,0.246,0.209
                    c0.071,0.051,0.147,0.091,0.222,0.133c0.058,0.033,0.115,0.069,0.175,0.097c0.081,0.037,0.165,0.063,0.249,0.091
                    c0.065,0.022,0.128,0.047,0.195,0.063c0.079,0.019,0.159,0.026,0.239,0.037c0.074,0.01,0.147,0.024,0.221,0.027
                    c0.097,0.004,0.194-0.006,0.292-0.014c0.055-0.005,0.109-0.003,0.163-0.012c0.323-0.048,0.641-0.16,0.933-0.346l34.305-21.865
                    C131.967,94.755,132.296,93.271,131.583,92.152z" />
							<circle fill="none" stroke="#ffffff" stroke-width="5" stroke-miterlimit="10" cx="109.486" cy="104.353" r="32.53" />
                    </svg>
						<h3 id='status'>
							Success
						</h3>
					</div>
					<div id='lower-side'>
						<p id='message'>
							Your account has been successfully registered.
						</p>
					</div>
				</div>
			<?php }else{ ?>
				<form action="<?php echo base_url() . 'consultant/register_consultant'; ?>" id="consultant_details" method="post"
					  class="form-horizontal" onsubmit="return checkForm(this);" enctype="multipart/form-data">
					<h2>Please Register</h2>
					<hr class="colorgraph">
					<div class="tab">
						<h2 class="fs-title">Create your account</h2>
						<p><input type="text" name="consultant_first_name" placeholder="Invoice - First Name*" value="<?php echo set_value('consultant_first_name'); ?>"
								  oninput="this.className = ''"/>
							<?php echo form_error('consultant_first_name',"<small class=' text-danger'>","</small>"); ?>
						</p>

						<p><input type="text" name="consultant_last_name" placeholder="Invoice - Last Name*" value="<?php echo set_value('consultant_last_name'); ?>"
								  oninput="this.className = ''"/>
							<?php echo form_error('consultant_last_name',"<small class='text-danger'>","</small>"); ?>
						</p>

						<p><input type="text" name="consultant_name" placeholder="Consultant Name*" value="<?php echo set_value('consultant_name'); ?>"
						oninput="this.className = ''"/>
						<?php echo form_error('consultant_name',"<small class='text-danger'>","</small>"); ?>
						</p>

						<p><input type="text" name="consultant_email" id="consultant_email" placeholder="Email*" value="<?php echo set_value('consultant_email'); ?>"
								  oninput="this.className = ''"/>
							<?php echo form_error('consultant_email',"<small class='text-danger'>","</small>"); ?>
							<small class="text-danger emailError">
								<?php if(isset($email_already_exists)){ ?>
									<?php echo $email_already_exists; ?>
								<?php } ?>
							</small>
						</p>
						<p><input type="text" name="consultant_phone_number" placeholder="Phone" value="<?php echo set_value('consultant_phone_number'); ?>"/>
							<?php echo form_error('consultant_phone_number',"<small class='text-danger'>","</small>"); ?>
							<small class='text-danger phoneNumberError'></small>
						</p>
						<p><input type="text" name="consultant_mobile_number" placeholder="Mobile*" value="<?php echo set_value('consultant_mobile_number'); ?>"
								  oninput="this.className = ''"/>
							<?php echo form_error('consultant_mobile_number',"<small class='text-danger'>","</small>"); ?>
							<small class='text-danger mobileNumberError'></small>
						</p>
						<p><select name="consultant_technology" id="consultant_technology" placeholder="Select Primary Skills*" oninput="this.className = ''">
							<option disabled selected >Select Primary Skills</option>
								<?php
									foreach($technologies as $technology ){
										echo '<option value="'.$technology['TechDomainid'].'">'.$technology['TechDomainName'].'</option>';
									}
								?>
						</select>
						<?php echo form_error('consultant_technology',"<small class='text-danger'>","</small>"); ?>
						<small class='text-danger technologyError'></small>
						</p>
						
						<p><input type="text" name="consultant_other_skills" placeholder="Other Skills*" value="<?php echo set_value('consultant_other_skills'); ?>"
								  oninput="this.className = ''"/>
							<?php echo form_error('consultant_other_skills',"<small class='text-danger'>","</small>"); ?>
						</p>
						<p><input type="text" name="consultant_reference" placeholder="Reference" value="<?php echo set_value('consultant_reference'); ?>"/>
							<?php echo form_error('consultant_reference',"<small class='text-danger'>","</small>"); ?>
						</p>
					</div>
					<div class="tab">
						<h2 class="fs-title">Address Details</h2>
						<p><input type="text" name="consultant_current_address1" placeholder="1234 Main St*" value="<?php echo set_value('consultant_current_address1'); ?>"
								  oninput="this.className = ''"/>
							<?php echo form_error('consultant_current_address1',"<small class='text-danger'>","</small>"); ?>
						</p>
						<p><input type="text" name="consultant_current_address2" value="<?php echo set_value('consultant_current_address2'); ?>"
								  placeholder="Apartment, studio, or floor*"
								  oninput="this.className = ''"/>
							<?php echo form_error('consultant_current_address2',"<small class='text-danger'>","</small>"); ?>
						</p>
						<p><input type="text" name="consultant_current_address3" placeholder="Address*" value="<?php echo set_value('consultant_current_address3'); ?>"
								  oninput="this.className = ''"/>
							<?php echo form_error('consultant_current_address3',"<small class='text-danger'>","</small>"); ?>
						</p>
						<p><input type="text" name="consultant_current_city" placeholder="City*" value="<?php echo set_value('consultant_current_city'); ?>"
								  oninput="this.className = ''"/>
							<?php echo form_error('consultant_current_city',"<small class='text-danger'>","</small>"); ?>
						</p>
						<p><input type="text" name="consultant_current_state" placeholder="State*" value="<?php echo set_value('consultant_current_state'); ?>"
								  oninput="this.className = ''"/>
							<?php echo form_error('consultant_current_state',"<small class='text-danger'>","</small>"); ?>
						</p>
						<p><input type="text" name="consultant_current_country" placeholder="Country*" value="<?php echo set_value('consultant_current_country'); ?>"
								  oninput="this.className = ''"/>
							<?php echo form_error('consultant_current_country',"<small class='text-danger'>","</small>"); ?>
						</p>
						<p><input type="text" name="consultant_pincode" placeholder="Pin Code*" value="<?php echo set_value('consultant_pincode'); ?>"
								  oninput="this.className = ''"/>
							<?php echo form_error('consultant_pincode',"<small class='text-danger'>","</small>"); ?>
							<small class='text-danger pincodeError'></small>
						</p>
						<p>
							<textarea name="consultant_alternative_address" placeholder=" Alternative Address" value="<?php echo set_value('consultant_alternative_address'); ?>"></textarea>
							<?php echo form_error('consultant_alternative_address',"<small class='text-danger'>","</small>"); ?>
						</p>
					</div>
					<div class="tab">
					<h5 style="color:red"><strong>Note:</strong> Upload File size must be less than 500KB </h5>
						<h2 class="fs-title">Upload Resume</h2>
						<p>
							<input type="file" name="resumeupload" accept=".doc,.docx,.pdf" onchange="validateFileSize(this)" value="<?php echo set_value('resumeupload'); ?>"
								   oninput="this.className = ''"/>
							<small class="text-danger resumeuploadError">
								<?php if(isset($imgTypeError)){ ?>
									<?php echo strip_tags($imgTypeError); ?>
								<?php } ?>
							</small>
						</p>
						<h2 class="fs-title">Picture Upload</h2>
						<p>
							<input type="file" name="pictureupload" accept=".jpg,.jpeg,.png" onchange="validateFileSize(this)" value="<?php echo set_value('pictureupload'); ?>"
								   oninput="this.className = ''"/>
							<small class="text-danger pictureuploadError">
								<?php if(isset($imgTypeError)){ ?>
									<?php echo strip_tags($imgTypeError); ?>
								<?php } ?>
							</small>
						</p>

						<h2 class="fs-title">PAN Card</h2>
						<p>
							<input type="file" name="pancard" onchange="validateFileSize(this)" value="<?php echo set_value('pancard'); ?>"
								   oninput="this.className = ''"/>
							<small class="text-danger pancardError">
								<?php if(isset($pancardTypeError)){ ?>
									<?php echo strip_tags($pancardTypeError); ?>
								<?php } ?>
							</small>
						</p>
						<h2 class="fs-title">Aadhar Card</h2>
						<p>
							<input type="file" name="aadharcard" onchange="validateFileSize(this)" value="<?php echo set_value('aadharcard'); ?>"
								   oninput="this.className = ''"/>
							<small class="text-danger aadharcardError">
								<?php if(isset($aadharcardTypeError)){ ?>
									<?php echo strip_tags($aadharcardTypeError); ?>
								<?php } ?>
							</small>
						</p>
						<h2 class="fs-title">Bank Statement / Cheque Book</h2>
						<h6><strong>Note:</strong> Bank Statement (or) Cheque Leaf (or) Bank Pass book with clear mention of Bank name; Account holder name; Account number and IFSC code</h6>
						<p>
							<input type="file" name="bank_statement" onchange="validateFileSize(this)" value="<?php echo set_value('bank_statement'); ?>"
								   oninput="this.className = ''"/>
							<small class="text-danger bank_statementError">
								<?php if(isset($bank_statementTypeError)){ ?>
									<?php echo strip_tags($bank_statementTypeError); ?>
								<?php } ?>
							</small>
						</p>
						<h2 class="fs-title">NDA Document</h2>
						<p>
							<input type="file" name="nda_document" onchange="validateFileSize(this)" value="<?php echo set_value('nda_document'); ?>"
								   oninput="this.className = ''"/>
							<small class="text-danger nda_documentError">
								<?php if(isset($nda_documentTypeError)){ ?>
									<?php echo strip_tags($nda_documentTypeError); ?>
								<?php } ?>
							</small>
						</p>

					</div>
					<div class="tab submitTab">
						<h2 class="fs-title">Bank Details</h2>
						<p><input type="text" name="consultant_pan_number" placeholder="PAN Number*" value="<?php echo set_value('consultant_pan_number'); ?>"
								  oninput="this.className = ''"/>
							<?php echo form_error('consultant_pan_number',"<small class='text-danger'>","</small>"); ?>
						</p>
						<p><input type="text" name="consultant_bank_name" placeholder="Bank Name*" value="<?php echo set_value('consultant_bank_name'); ?>"
								  oninput="this.className = ''"/>
							<?php echo form_error('consultant_bank_name',"<small class='text-danger'>","</small>"); ?>
						</p>
						<p><input type="text" name="consultant_bank_account_number" placeholder="Bank Account Number*" value="<?php echo set_value('consultant_bank_account_number'); ?>"
								  oninput="this.className = ''"/>
							<?php echo form_error('consultant_bank_account_number',"<small class='text-danger'>","</small>"); ?>
							<small class='text-danger bankAccNoError'></small>
						</p>
						<p><input type="text" name="consultant_bank_ifsc_code" placeholder="IFSC Code*" value="<?php echo set_value('consultant_bank_ifsc_code'); ?>"
								  oninput="this.className = ''"/>
							<?php echo form_error('consultant_bank_ifsc_code',"<small class='text-danger'>","</small>"); ?>
						</p>
						<p><input type="text" name="consultant_alternative_bank_name" value="<?php echo set_value('consultant_alternative_bank_name'); ?>"
								  placeholder="Alternative Bank Name"/>
							<?php echo form_error('consultant_alternative_bank_name',"<small class='text-danger'>","</small>"); ?>
						</p>
						<p><input type="text" name="consultant_alternative_bank_account_number" value="<?php echo set_value('consultant_alternative_bank_account_number'); ?>"
								  placeholder="Alternative Bank Account Number"/>
							<?php echo form_error('consultant_alternative_bank_account_number',"<small class='text-danger'>","</small>"); ?>
							<small class='text-danger alternativeBankAccNoError'></small>
						</p>
						<p><input type="text" name="consultant_alternative_bank_ifsc_code" placeholder="IFSC Code" value="<?php echo set_value('consultant_alternative_bank_ifsc_code'); ?>"/>
							<?php echo form_error('consultant_alternative_bank_ifsc_code',"<small class='text-danger'>","</small>"); ?>
						</p>
						<p>
							<input  type="checkbox" name="terms" />Check here to indicate that you have read and agree to the terms of the <a href="#" data-toggle="modal" data-target="#t_and_c_m">CG-VAK Consultant Agreement</a>
						</p>
					</div>
					<div style="overflow:auto;">
						<div style="float:right;">
							<button type="button" id="prevBtn" onclick="nextPrev(-1)">Previous</button>
							<button type="button" id="nextBtn" onclick="nextPrev(1)">Next</button>
							<button type="submit" id="submit" style="display:none">submit</button>
						</div>
					</div>
					<!-- Circles which indicates the steps of the form: -->
					<div style="text-align:center;margin-top:40px;">
						<span class="step"></span>
						<span class="step"></span>
						<span class="step"></span>
						<span class="step"></span>
					</div>
				</form>
			<?php } ?>
		</div>
	</div>
</div>
<?php include 'footer.php'; ?>

<script>

	var currentTab = 0;
	showTab(currentTab);
	errorChecking();

	function errorChecking(){
		var x = document.getElementsByClassName("tab");
		var c =  x.length;
		var errorCount;
		var tabHeading;
		var invalidEntry = "";

		for(i = 0; i < c; i++){
			errorCount = 0;
			for(j=0; j < x[i].childNodes.length; j++){
				p = x[i].childNodes[j];
				if (p.className == "fs-title") {
					tabHeading = p.innerText;
				}
				for(k=0; k < p.childNodes.length; k++){
					if (p.childNodes[k].className == "text-danger") {
						errorCount++;
					}
				}
			}

			if(errorCount !== 0) {
				if(errorCount === 1) {
					invalidEntry = invalidEntry + "<div class='alert alert-danger' role='alert'> In tab " + tabHeading + " " + errorCount + " field is invalid entry </div>";
				}else{
					invalidEntry = invalidEntry + "<div class='alert alert-danger' role='alert'> In tab " + tabHeading + " " + errorCount + " fields are invalid entries </div>";
				}
				document.getElementById("formAlert").innerHTML = invalidEntry;
			}
		}
	}

	function showTab(n) {
		var x = document.getElementsByClassName("tab");

		x[n].style.display = "block";
		if (n === 0) {
			document.getElementById("prevBtn").style.display = "none";
		} else {
			document.getElementById("prevBtn").style.display = "inline";
		}
		if (n === (x.length - 1)) {
			document.getElementById("nextBtn").style.display = 'none';
			document.getElementById("submit").style.display = 'inline';
		} else {
			document.getElementById("nextBtn").style.display = 'inline';
			document.getElementById("submit").style.display = 'none';
		}
		fixStepIndicator(n)
	}


	function nextPrev(n) {
		var x = document.getElementsByClassName("tab");
		// console.log(x);
		// console.log(currentTab);
		// console.log(x.length);
		// console.log(x[currentTab]);
		if (n === 1 && !validateForm()) return false;
		x[currentTab].style.display = "none";
		currentTab = currentTab + n;
		if (currentTab >= x.length) {
			// document.getElementById("regForm").submit();
			return false;
		}
		showTab(currentTab);
	}

	function nextPrev_test(n) {

		var x = document.getElementsByClassName("tab");
		if (n === 1 && !validateForm()) return false;
		if(currentTab !== x.length-1){
			x[currentTab].style.display = "none";
		}
		currentTab = currentTab + n;
		if (currentTab >= x.length) {
			document.getElementById("consultant_details").submit();
			return false;
		}
		showTab(currentTab);
	}


	const validateTab = function (tab) {
		console.log(tab.className.split(" "));
		console.log(tab.value);
	}

	const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	const regexMobileNumber = /^[6-9]\d{9}$/;
	const regexPincode = new RegExp("^[0-9]{6}$");
	const regexBankAccNo = /^\d{9,18}$/;
	$(`#consultant_email`).change(function() {
  		// alert( $(`#consultant_email`).val() );		

					if ($(`#consultant_email`).val() !== "" && re.test($(`#consultant_email`).val())) {
						jQuery.ajax({
							type: "POST",
							url: "<?php echo base_url(); ?>consultant/email_exists_through_ajax",
							data: {consultant_email: $(`#consultant_email`).val()},
							success: function (res)
							{
								// console.log(typeof res);
								if(res === 'true'){
									jQuery('.emailError').text('This email ID is already in use')
									$(`#consultant_email`).addClass("invalid");
									$("#nextBtn").attr("disabled", true);
								}else{
									$("#nextBtn").removeAttr("disabled");
									jQuery('.emailError').text('')
									$(`#consultant_email`).removeClass("invalid");
								}
							}
						});
					} else if($(`#consultant_email`).val() == "") {
						jQuery('.emailError').text('This field cannot be empty')
						$(`#consultant_email`).addClass("invalid");
						$("#nextBtn").attr("disabled", true);
					} else if(!re.test($(`#consultant_email`).val())){
						jQuery('.emailError').text('Please enter a valid email address')
						$(`#consultant_email`).addClass("invalid");
						$("#nextBtn").attr("disabled", true);
					}
				
	});
	function validateForm() {
		var x, y, z, i, j, valid = true;
		x = document.getElementsByClassName("tab");
		y = x[currentTab].getElementsByTagName("input");
		z = x[currentTab].getElementsByTagName("select");
		
		for (i = 0; i < y.length; i++) {
			if (currentTab === 0) {
				if (y[i].name !== 'consultant_phone_number') {
					if (y[i].name !== 'consultant_reference') {
						if (y[i].value === "") {
							y[i].className += " invalid";
							valid = false;
						}

						if(y[i].name === 'consultant_mobile_number'){
							if (y[i].value !== '' && !regexMobileNumber.test(y[i].value)) {
								jQuery('.mobileNumberError').text('Please enter a valid phone number input')
								y[i].className += " invalid";
								valid = false;
							} else if(y[i].value === ''){
								y[i].className += " invalid";
								valid = false;
							}else{
								jQuery('.mobileNumberError').text('')
								y[i].className = "";
							}
						}
					}
				}else{
					if (y[i].value !== '' && !regexMobileNumber.test(y[i].value)) {
						jQuery('.phoneNumberError').text('Please enter a valid phone number input')
						y[i].className += " invalid";
						valid = false;
					} else{
						jQuery('.phoneNumberError').text('')
						y[i].className = "";
					}
				}
			}
			if (currentTab === 1) {
				if (y[i].value === "" && y[i].name !== 'consultant_alternative_address') {
					y[i].className += " invalid";
					valid = false;
				}

				if(y[i].name === 'consultant_pincode' && y[i].value !== ""){
					if(!regexPincode.test(y[i].value)){
						jQuery('.pincodeError').text('Please enter a valid Pin code')
						y[i].className += " invalid";
						valid = false;
					}else{
						jQuery('.pincodeError').text('')
						y[i].className = "";
					}
				}
			}
			if (currentTab === 2) {
				if (y[i].value === "") {
					y[i].className += " invalid";
					valid = false;
				}else{
					if(y[i].name === "pictureupload"){
						var position_of_dot = y[i].value.lastIndexOf('.')+1;
						var validExt = ['gif','jpg','png','jpeg'];

						var img_ext = y[i].value.substring(position_of_dot);
						// console.log(img_ext);

						var validaionResult = validExt.includes(img_ext);
						// console.log(validaionResult);

						if(!validaionResult){
							jQuery('.pictureuploadError').text('Please upload a valid image file');
							y[i].className += " invalid";
							valid = false;
						}else{
							jQuery('.pictureuploadError').text('');
							y[i].className = "";
						}
					} else if(y[i].name !== 'resumeupload') {
						var position_of_dot = y[i].value.lastIndexOf('.')+1;
						var validExt = ['pdf', 'jpg', 'jpeg', 'png'];

						var img_ext = y[i].value.substring(position_of_dot);
						// console.log(img_ext);

						var validaionResult = validExt.includes(img_ext);
						// console.log(validaionResult);

						if(!validaionResult){
							jQuery('.'+y[i].name+'Error').text('Please upload a valid Image / Document [allowed formats - jpg, jpeg, png, pdf]');
							y[i].className += " invalid";
							valid = false;
						}else{
							jQuery('.'+y[i].name+'Error').text('');
							y[i].className = "";
						}
					} else {
						var position_of_dot = y[i].value.lastIndexOf('.')+1;
						var validExt = ['doc','docx','pdf'];

						var img_ext = y[i].value.substring(position_of_dot);
						// console.log(img_ext);

						var validaionResult = validExt.includes(img_ext);
						// console.log(validaionResult);

						if(!validaionResult){
							jQuery('.resumeuploadError').text('Please upload a valid Document file');
							y[i].className += " invalid";
							valid = false;
						}else{
							jQuery('.resumeuploadError').text('');
							y[i].className = "";
						}
					}
				}
			}
			if (currentTab === 3) {
				if (y[i].name !== 'consultant_alternative_bank_name') {
					if (y[i].name !== 'consultant_alternative_bank_account_number') {
						if (y[i].name !== 'consultant_alternative_bank_ifsc_code') {
							if (y[i].value === "") {
								y[i].className += " invalid";
								valid = false;
							}else{
								if(y[i].name === 'consultant_bank_account_number'){
									if(!regexBankAccNo.test(y[i].value)){
										jQuery('.bankAccNoError').text('Please enter a valid Bank Account Number');
										y[i].className += " invalid";
										valid = false;
									}else{
										jQuery('.bankAccNoError').text('');
										y[i].className = "";
									}
								}
							}
						}
					}else{
						if (y[i].value !== "" && !regexBankAccNo.test(y[i].value)) {
							jQuery('.alternativeBankAccNoError').text('Please enter a valid Bank Account Number');
							y[i].className += " invalid";
							valid = false;
						}else{
							jQuery('.alternativeBankAccNoError').text('');
							y[i].className = "";
						}
					}
				}
			}
		}
		for (j = 0; j < z.length; j++) {
			if (currentTab === 0) {
				if(z[j].name === 'consultant_technology'){
					if (z[j].value === 'Select Primary Skills') {
						jQuery('.technologyError').text('Please select a primary skills')
						z[j].className += " invalid";
						valid = false;
					}else{
						jQuery('.technologyError').text('')
						z[j].className = "";
					}
				}
			}
		}
		if (valid) {
			document.getElementsByClassName("step")[currentTab].className += " finish";
		}
		return valid;
	}

	function fixStepIndicator(n) {
		var i, x = document.getElementsByClassName("step");
		for (i = 0; i < x.length; i++) {
			x[i].className = x[i].className.replace(" active", "");
		}
		x[n].className += " active";
	}

	function validateFileSize(fileData) {
			const errorClass = fileData.name + "Error";
			const errorMessage = document.getElementById("errorMessage");
			if (fileData.files.length > 0) {
				const file = fileData.files[0];
				const fileSizeInKB = file.size / 1024;
				if (fileSizeInKB > 500) {
					jQuery('.' + errorClass).text('File size must be less than 500KB.');
					fileData.value = "";
				} else {
					jQuery('.' + errorClass).text('');
				}
			}
		}
</script>

</body>
</html>
