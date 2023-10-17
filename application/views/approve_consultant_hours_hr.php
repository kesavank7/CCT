<?php include 'header.php'; ?>
<style>
	input[type=number] {
		text-align: right;
	}

</style>
<?php include 'menu.php'; ?>
<div class="container">
	<div class="starter-template">
		<!-- Error Message -->
		<?php ?>
		<?php if ($this->session->userdata('msg') == 'succ') { ?>
			<div class="alert alert-success " role="alert">
				<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
				<span class="sr-only">Error:</span> Login Successfully.
			</div>
		<?php $this->session->unset_userdata('msg'); ?>
		<?php } elseif ($this->session->userdata('msg') == 'upt') { ?>
			<div class="alert alert-success " role="alert">
				Successfully Updated the Task.
			</div>
		<?php } elseif ($this->session->userdata('msg') == 'ts_export') { ?>
			<div class="alert alert-success " role="alert">
				Successfully TimeSheet exported.
			</div>
		<?php } else if (validation_errors()) { ?>
			<div class="alert alert-danger" role="alert">
				<?php echo validation_errors(); ?>
			</div>
		<?php } ?>
		<!-- Error Message -->


		<!-- Timesheet export fields -->
		<div class="add_filter">
			<div id="no-more-tbl">
				<form action="<?php echo base_url() . "ApproveConsultant/export"; ?>" id="timesheet_export_form"
					  method="post" class="form-horizontal">
					<input type="hidden" name="IsActive" id="IsActive" value="1"/>
					<input type="hidden" name="CompanyICode" id="CompanyICode" value="1"/>
					<!--input type="hidden" name="ProjectPhaseICode" id="ProjectPhaseICode" value="1" /-->
					<input type="hidden" name="Status" id="Status" value="O"/>
					<input type="hidden" name="CreatedBy" id="CreatedBy"
						   value="<?php echo $this->session->userdata('id'); ?>"/>
					<input type="hidden" name="CreatedDate" id="CreatedDate"
						   value="<?php echo date('Y-m-d H:i:s'); ?>"/>
					<table class="col-md-12 table-bordered table-striped table-condensed cf table-hover"
						   id="tbl_timesheet_form">
						<tbody>
						<tr>
							<td data-title="" width="40%">
								<div class="type-container monthly-field">
									<div class="field-inline">
										<div class="est_txt">Select Year:</div>
										<input class="form-control" type="text" value="<?php echo date('Y'); ?>"
											   name="year" id="time_sheet_year" maxlength="4"/>
									</div>
									<div class="field-inline">
										<div class="est_txt">Select Month :</div>
										<select class="form-control" id="time_sheet_month" name="month">
											<?php
												$selctedMonth = date('F');
												for ($m=1; $m<=12; $m++) {
													$month = date('F', mktime(0,0,0,$m, 1, date('Y')));
													$selctedM = strtolower($selctedMonth) == strtolower($month) ? 'selected' : '';
													echo "<option value=$month  $selctedM >$month</option>";
												}
											?>
										</select>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="type-container monthly-field">
                                    <div class="field-inline">
                                        <div class="est_txt">Approved Status :</div>
                                        <select name="approved_status" id="approved_status" class="form-control">
                                            <option value="">All</option>
                                            <option value="approved">Approved</option>
                                            <option value="unaproved">Unapproved</option>
                                        </select>
                                    </div>
                                </div>
							</td>

							<td data-title="" width="20%">
                                <div class="employee_list">
                                    <div class="container-box">
                                        <b>Consultants list:</b>
                                        <div class="employees">
                                            <?php
                                                if($emp_list) {
                                                    foreach($emp_list as $emp) {
                                                        ?>
                                            <div><input type="checkbox" attr-id="<?= $emp['ConsultantICode'] ?>" checked name="emp_ids[]" class="emp" value="<?= $emp['ConsultantICode'] ?>" /><?= $emp['ConsultantLoginUserName'] ?></div>
                                                        <?php
                                                    }
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </td>


							<td data-title="" width="60%" align="left">
								<div class="timesheet-action clreafix">
									<button type="button" class="btn btn-sm btn-blue pull-left"
											id="timesheet_preview" onclick="return false;">View
									</button>

									<button type="submit" class="btn btn-sm btn-green pull-left"
											id="timesheet_submit">Submit
									</button>
								</div>
							</td>
						</tr>
						</tbody>
					</table>
				</form>
			</div>
		</div>
		<!-- Adding Fields -->

		<!-- Filter --->
		<div class="panel panel-info ovrhid">
			<div class="panel-heading">
				<h3 class="panel-title">Project Entries</h3>
			</div>
			<div id="no-more-tables">
				<form action="<?php echo base_url() . "ApproveConsultant/hrSubmit"; ?>" method="post"
					  id="timesheet_submitFrm">
					<table class="table table-hover" id="tbl_list">
						<thead>
						<tr>
							<th>Consultant</th>
							<th>Project Name</th>
							<th>Lead Approved</th>
							<th>Accountant Approved</th>
							<th>Approved On</th>
							<th>Actual Hours</th>
							<th>Accounts Approved Hours</th>
							<th>Rate/Hour</th>
							<th>HR Approved Hours</th>
							<th>Amount</th>
							<th>Approve</th>
							<th>Action</th>
						</tr>
						</thead>
						<tbody>
							<tr>
	                            <td colspan="12" align="center">Loading...</td>
	                        </tr>
						</tbody>
					</table>
					<input type="hidden" value="" name="deletetasks">
				</form>
			</div>
		</div>
	</div>
</div>
<input type="hidden" name="old_txt" id="old_txt"/>

<!-- Using this div for removing tooltip in appending edit content -->
<div id="remove_html"></div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Choose an employee to send the approval</h5>
			</div>
			<div class="modal-body">
				<select class="form-select form-select-lg mb-3" id="sendTo" aria-label=".form-select-lg example">
					<option selected value="">Select an empolyee</option>
					<?php foreach ($users as $value) { ?>
						<option value="<?= $value['EmployeeICode'] ?>"><?= $value['LoginUserName'] ?></option>
					<?php } ?>
				</select>
				<span class="text-danger" id="sendToErr"></span>
				<br><br>
				<label for="comments">Comments</label>
                <textarea class="form-control" id="comments" rows="3"></textarea>
				<span class="text-danger" id="commentsErr"></span>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-blue" id="SendBtn">Send</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	/*
			Validate Start date and End date
		*/
	function DateCheck(StartDate, EndDate) {
		var sdate = changeDate(StartDate);
		var edate = changeDate(EndDate);
		if (sdate > edate) {
			return false;
		}
		return true;
	}

	function disable_timesheetbutton() {
		jQuery('#timesheet_submit').attr('disabled', 'disabled');
		jQuery('#timesheet_preview').attr('disabled', 'disabled');
	}

	function enable_timesheetbutton() {
		jQuery('#timesheet_submit').removeAttr('disabled');
		jQuery('#timesheet_preview').removeAttr('disabled');
	}

	function show_loader() {
		jQuery('body').append(
			'<div class="ajax-loader"><div class="overlay"><img src="public/images/ajax-loader.gif" alt="ajax-loader"></div></div>'
		);
	}

	function hide_loader() {
		jQuery('.ajax-loader').remove();
	}
</script>

<script type="text/javascript">
	jQuery(document).ready(function ($) {

		function hoursFormatConversion(val) {
			let from  = val.trim();
			from = from.replace(':','.');
			to = parseFloat(from).toFixed(2);
			return to;
		}

		function getAfterDecimalValue(val) {
			let n = val;
			let whole = Math.floor(n);
			let fraction = (n - whole) * 100;
			return fraction;
		}

		function getBeforeDecimalValue(val) {
			let n = val;
			let whole = Math.floor(n);
			return whole;
		}

		function approvedHoursForAmountCalc(val) {
			let resultVal = (getBeforeDecimalValue(val) + (getAfterDecimalValue(val) / 60 ));
			return resultVal;
		}

		function amntPerDay() {
			let closeTR = $(this).closest('tr');
			let currentEl = closeTR.find('.rate_per_hr');
			let approved_hours = closeTR.find('.approved_hours');
			let amount_per_day = closeTR.find('.amount_per_day');
			let approvedHoursConversionFormat = parseFloat(hoursFormatConversion(approved_hours.val()));
        	let amount = (parseFloat(currentEl.val()) * approvedHoursForAmountCalc(approvedHoursConversionFormat)).toFixed(2);
			// let amount = (parseFloat(currentEl.val()) * parseFloat(hoursFormatConversion(approved_hours.val()))).toFixed(2);
			amount_per_day.val(amount);
			displayTotals();
		}

		const displayTotals = function () {
			let totalHrsApproved = 0;
			let totalAmntApproved = 0;
			let totalhrsArr = [];
			$.each($('.approved_hours'), function (index, element) {
				// totalHrsApproved += Number(hoursFormatConversion(element.value));
				// totalhrsArr.push(Number(hoursFormatConversion(element.value)));
				totalhrsArr.push(hoursFormatConversion(element.value));
			});
			$.each($('.amount_per_day'), function (index, element) {
				totalAmntApproved += Number(element.value);
			});

			// $('#totalHrsApproved').text(parseFloat(totalHrsApproved).toFixed(2) + ' Hours');
			$('#totalHrsApproved').text(addHoursAndMinutes(totalhrsArr) + ' Hours');
			$('#totalAmntApproved').text(parseFloat(totalAmntApproved).toFixed(2) + ' Rs');
		}

		console.log('<?php echo $this->session->userdata('role');  ?>');
		let userRoleLoggedIn = '<?php echo $this->session->userdata('role');  ?>';
		//show the default type monthy timesheet
		$('.type-container').hide();
		$("input[type=radio][name=timesheet_type]:first").prop("checked", true).trigger("click");
		$('.monthly-field').show();

		jQuery('.datepicker').datetimepicker({
			timepicker: false,
			format: 'd-m-Y',
			closeOnDateSelect: true
		});
		if (<?php echo count($entry_list) ?> >
		0
	)
		{
			var table = jQuery('#tbl_list').dataTable({
				//"paging":   false,
				"bSort": false,
				"lengthMenu": [
					[10, 25, 50, -1],
					[10, 25, 50, "All"]
				],
				"bLengthChange": false,
				"info": false,
				"filter": false,
				//"bDestroy":true,
				//"mData": null,
				//"mDataProp": null,
				//"aoColumns": [null,{ "sType": 'num-html' },null],
				"order": [
					[0, "asc"]
				]
			});
		}
		<?php if($this->input->get('page')): ?>
		var page_no = "<?php echo $this->input->get('page'); ?>";
		var total_page = Math.ceil(parseInt(table.fnGetData().length) / parseInt(10));
		if (total_page <= page_no) {
			page_no = total_page;
		}
		table.fnPageChange(--page_no, true);
		<?php endif; ?>

		$('input[type=radio][name=timesheet_type]').change(function () {
			$('.type-container').hide();
			$('.' + this.value + '-field').show();
		});
		jQuery("#ProjectICode").change(function (e) {
			e.preventDefault();
			show_loader();
			disable_timesheetbutton();
			$('.employee_list .employees').html('');
			if (jQuery("#ProjectICode").val()) {
				var ProjectICode = jQuery("#ProjectICode").val();
				var c_url = '<?php echo base_url(); ?>';
				var TimeSheetType = $('input[type=radio][name=timesheet_type]:checked').val();
				var postData = {};
				postData.ProjectICode = ProjectICode;
				postData.timesheet_type = TimeSheetType;
				if (TimeSheetType == 'monthly') {
					postData.month = $('#time_sheet_month').val();
					postData.year = $('#time_sheet_year').val();
				} else {
					postData.fromDate = $('#time_sheet_fromDate').val();
					postData.toDate = $('#time_sheet_toDate').val();
				}
				jQuery.ajax({
					type: "POST",
					url: c_url + "ApproveConsultant/get_emp",
					data: postData
				}).done(function (data) {
					if (data) {
						console.log(data);
						$('.employee_list .employees').html('');
						data = JSON.parse(data);
						$.each(data.emp_list, function (index, emp) {
							$('.employee_list .employees').append(
								'<div><input type="checkbox" attr-id="' + emp
									.ConsultantICode +
								'" checked name="emp_ids[]" class="emp" value="' + emp
									.ConsultantICode + '"/>' + emp.ConsultantLoginUserName +
								'</div>');
						});
					}
					hide_loader();
					enable_timesheetbutton();
				});
			}
		});

		function padTo2Digits(num) {
			return num.toString().padStart(2, '0');
		}

		function formatDate(date) {
			return [
				padTo2Digits(date.getDate()),
				padTo2Digits(date.getMonth() + 1),
				date.getFullYear(),
			].join('/');
		}

		function dateFormator(dateString) {
			let AccountsApprovedOn = new Date(dateString);
			let AccountsApprovedOnDateArr = dateString
				.split(' ')[0].split('-');
			let AccountsApprovedOnDate = formatDate(new Date(
				AccountsApprovedOnDateArr[0],
				AccountsApprovedOnDateArr[1] - 1,
				AccountsApprovedOnDateArr[2]
			));
			let AccountsApprovedOnTime = AccountsApprovedOn
				.toLocaleTimeString('en-US', {
					hour: 'numeric',
					minute: 'numeric'
				});

			return AccountsApprovedOnDate + " " + AccountsApprovedOnTime;
		}

		function addHoursAndMinutes(hoursAndMinutes) {
			let sumHours = 0;
			let minutes = 0;
			let hours = 0;
			hoursAndMinutes.forEach((time) => {
				let removeextrazero = time.toString();
				let splitTime =  removeextrazero.split(".");
				let h = (splitTime[0] == '00' || splitTime[0] == '.00') ? 0 : splitTime[0];
				let m = (splitTime[1] == '00' || splitTime[1] == '.00') ? 0 : splitTime[1];
				console.log('h',h);
				console.log('m',m);
				console.log('minutes',minutes);
				minutes += parseInt(h) * 60;
				minutes += parseInt(m);
				console.log('minutes',minutes);
			});
			
			hours = Math.floor(minutes / 60);
			let min = minutes - (hours * 60);
			min = (min.toString().length == 1) ? '0'+min : min;
			console.log(typeof(min));
			return hours+":"+min;
		}

		setTimeout(function() {jQuery('#timesheet_preview').trigger('click');}, 0);

		jQuery('#timesheet_preview').click(function () {
			disable_timesheetbutton();
			let deletetasks = $('[name="deletetasks"]');
			deletetasks.val('');
			var ProjectICode = jQuery("#ProjectICode").val();
			var approvedStatus = jQuery('#approved_status').val();
			var c_url = '<?php echo base_url(); ?>';
			var TimeSheetType = 'monthly';
			var postData = {};
			postData.ProjectICode = ProjectICode;
			postData.timesheet_type = TimeSheetType;
			postData.approvedStatus = approvedStatus;
			postData.user_role = 'HR';

			postData.month = $('#time_sheet_month').val();
			postData.year = $('#time_sheet_year').val();

			var emp_ids = [];
			$("input[type=checkbox][name='emp_ids[]']").each(function () {
				if ($(this).prop('checked') === true) {
					emp_ids.push($(this).val());
				}
			});
			console.log(emp_ids);
			console.log(c_url);
			postData.emp_ids = emp_ids;
			jQuery.ajax({
				type: "POST",
				url: c_url + "ApproveConsultant/preview",
				data: postData
			}).done(function (data) {
				let rate_per_hr = $('#rate_per_hr').val();
				console.log(data);
				console.log("data");
				if (data) {
					data = JSON.parse(data);
					//alert(data.timesheet_list);
					$('#tbl_list tbody').html('');
					console.log('length = ', data.timesheet_list.length);
					if (data.timesheet_list.length < 1) {
						$('#tbl_list tbody').html('<tr><td colspan="' + ($('#tbl_list thead tr th')
							.length) + '" align="center">No Records Found.</td></tr>');
						enable_timesheetbutton();
						return;
					}

					let totalHrsApproved = 0;
					let totalAmntApproved = 0;
					let totalhrsArr = [];
					$.each(data.timesheet_list, function (index, task_detail) {
						let projectDate = new Date(task_detail.date).toLocaleDateString(
							'en-us', {
								year: "numeric",
								month: "long"
							}).split(" ");
						let billable_hours;
						let approved_hours;
						let amount_per_day;
						let approveButton;
						let appendHtml;
						let uploadAction;
						
						let taskApprovedHour = (task_detail.HrApprovedHours > 0) ?
							task_detail.HrApprovedHours : task_detail.AccountsApprovedHours;
						console.log('taskApprovedHour',typeof(taskApprovedHour),taskApprovedHour);
                    	taskApprovedHour = taskApprovedHour.toString().startsWith('.') ? '0'+taskApprovedHour : taskApprovedHour;
						rate_per_hr = parseFloat((task_detail.HrApprovedRateHour > 0) ?
							task_detail.HrApprovedRateHour : task_detail
								.HourlyRate);

						// totalHrsApproved += Number(taskApprovedHour);
						totalAmntApproved += Number(rate_per_hr * approvedHoursForAmountCalc(taskApprovedHour));
						totalAmntApproved = isNaN(totalAmntApproved) ? 0 : totalAmntApproved;
						totalhrsArr.push(taskApprovedHour.toString().slice(0));
						// totalhrsArr.push(taskApprovedHour);

						billable_hours =
							'<input type="text" id="billable_hours" value="' +
							task_detail.WorkedHours + '">';
						approved_hours =
                        `<td width="10%"><input type="text" class="approved_hours" id="approved_hours" name="approved_hours[]" value="` +
                        taskApprovedHour.replace('.',':').slice(0) +
                        `">
                        <input type="hidden" id="account_approved_hours" value="${task_detail.AccountsApprovedHours}" class="account_approved_hours"></td>`;
						amount_per_day =
							`<td width="10%">
                            <input type="number" class="amount_per_day" id="amount_per_day" name="amount_per_day[]" value="${rate_per_hr ? parseFloat(rate_per_hr * approvedHoursForAmountCalc(taskApprovedHour)).toFixed(2) : ''}">
                            </td>`;
						approveButton =
							'<button type="button" class="btn btn-sm btn-red acc_delete" id="acc_delete" data-id="' +
							task_detail.id + '">Delete</button>';
						approveCheckbox =
							'<input type="checkbox" class="accApproveCk" value="' +
							task_detail.id +
							'"><input type="hidden" name="accApprove[]" value="false" />';

						if (task_detail.IsHrApproved)
							approveCheckbox =
								'<input type="checkbox" class="accApproveCk" value="' +
								task_detail.id +
								'" checked><input type="hidden" name="accApprove[]" value="true" />';

						if(task_detail.BillSystemPushedBy != null) {
							uploadAction = '<button type="button" class="btn btn-sm btn-blue uploadApproval" data-id="' +
							task_detail.id + '">Reupload</button>';
						} else {
							uploadAction = '<button type="button" class="btn btn-sm btn-blue uploadApproval" data-id="' +
							task_detail.id + '">Upload</button>';
						}

						appendHtml = '<tr>' +
							'<td width="10%">' + task_detail.employee_name + '</td>' +
							'<td width="10%">' + task_detail.ProjectName + '</td>' +
							'<td width="10%">' + task_detail.lead_approved_by + '</td>' +
							'<td width="10%">' + task_detail.approved_by + '</td>' +
							'<td width="10%">' + dateFormator(task_detail
								.AccountsApprovedOn) +
							'</td>' +
							'<td width="10%" align="right">' + task_detail.WorkedHours
								.slice(0).replace('.',':') +
							'</td>' +
							'<td width="10%" align="right">' + task_detail
								.AccountsApprovedHours.slice(0).replace('.',':') +
							'</td>' +
							'<td width="10%"><input type="number" class="rate_per_hr" name="rate_per_hr[]" value="' +
							rate_per_hr + '"></td>' +
							approved_hours +
							amount_per_day +
							'<td width="10%">' +
							approveCheckbox +
							'</td>' +
							'<td width="10%"><input type="hidden" value="' + task_detail
								.id +
							'" class="task_progress_id" id="task_progress_id" name="task_progress_id[]">' +
							approveButton +
							uploadAction +
							'</td>' +
							'</tr>';


						$('#tbl_list tbody').append(appendHtml);
					});
					totalHrsApproved = addHoursAndMinutes(totalhrsArr);
					// if (data.total_hours != '')
					//     $('#tbl_list tbody').append(
					//         '<tr><td colspan="6" align="right" class="bold-16">Total Hours</td><td class="bold-16">' +
					//         data.total_hours + '</td></tr>');
					$('#tbl_list tbody').append(
						`<tr>
							<td align="right" colspan="3" class="bold-16">Total</td>
							<td class="bold-16" colspan="6" id="totalHrsApproved" align="right">` + totalHrsApproved + ` Hours</td>
							<td class="bold-16" id="totalAmntApproved" align="right">` + parseFloat(totalAmntApproved).toFixed(2) + ` Rs</td>
							<td colspan="2"><td>
					</tr>`
					);
					$("input[type='number']").each(function () {
						fixHrsDecimal.call(this);
						$(this).on('blur', fixHrsDecimal.bind(this));
					});
				}

				// amntPerDay()
				enable_timesheetbutton();
			});
			return false;
		});

		const fixHrsDecimal = function () {
			this.value = parseFloat(this.value).toFixed(2);
		}

		jQuery('#timesheet_export_remove').click(function () {
			disable_timesheetbutton();
			jQuery.ajax({
				type: "POST",
				url: c_url + "ApproveConsultant/preview",
				data: postData
			}).done(function (data) {
				console.log(data);
				if (data) {
					$('#tbl_list tbody').html('');
					data = JSON.parse(data);
					alert(data.timesheet_list);
					$.each(data.timesheet_list, function (index, task_detail) {
						$('#tbl_list tbody').append('<tr><td width="10%">' + task_detail
								.taskprogressdate + '</td>' +
							'<td width="10%">' + task_detail.taskprogressdate +
							'</td>' +
							'<td width="20%">' + task_detail.employee_name +
							'</td>' +
							'<td width="30%">' + task_detail.workdescription +
							'</td>' +
							'<td width="10%">' + task_detail.actual_wrked +
							'</td>' +
							'<td width="10%">' + task_detail.Hours_Worked +
							'</td>' +
							'<td width="10%">' + task_detail.approved_hrs +
							'</td></tr>'
						);
					});
				}
				enable_timesheetbutton();
			});
		});

		$(document).on('click', '#timesheet_submit', function () {
			console.log($('#timesheet_submitFrm').serialize());
			var inputs = $(".approved_hours");
			for(var i = 0; i < inputs.length; i++){
				let newval = $(inputs[i]).val().replace(':','.');
				$(inputs[i]).val(newval);
			}
			$('#timesheet_submitFrm').submit();
			return false;
		});

		$(document).on('change', '.accApproveCk', function () {
			let AccountApproveHours = $(this).closest('tr').find('.account_approved_hours').val();
			let hrApprovedHours = $(this).closest('tr').find('.approved_hours').val();
			if (!hrApprovedHours.includes(':')) {
				alert("Please enter the Approved Hours in valid format: (00:00).");
				$(this).prop('checked', false);
				return false;
			}
			var arr = hrApprovedHours.split(':');
			let accountsApproved_mins = arr[1].trim();
			var pattern_minutes = /^[0-5][0-9]+$/;
			if (accountsApproved_mins.length > 2 || !pattern_minutes.test(accountsApproved_mins)) {
				alert("Please enter a valid minute:(00-59)");
				$(this).prop('checked', false);
				return;
			}
			let accHour = parseFloat(AccountApproveHours.slice(0)).toFixed(2);
			let hrHour = parseFloat(hoursFormatConversion(hrApprovedHours)).toFixed(2);
			if(accHour < hrHour) {
				alert("Approved Hours cannot be greater than Account Approved Hours.");
				$(this).prop('checked', false);
				return false;
			}
			$(this).next('input').val($(this).prop('checked'));
			return false;
		});

		$(document).on('click', '#acc_delete', function () {
			if (confirm('Are you sure, you want delete?')) {
				let deletetasks = $('[name="deletetasks"]');
				let deletetasksVals = deletetasks.val().split(',');
				let currentEl = $(this);
				deletetasksVals.push(currentEl.attr('data-id'));
				deletetasks.val(deletetasksVals.join(','));
				currentEl.closest('tr').remove();
			}
			return false;
		});

		$(document).on('click', '.lead_approve', function () {
			let taskProgressId = $(this).siblings("#task_progress_id").val();
			let BillableHours = $(this).parent().parent().children().eq(5).children().eq(0).val();
			let c_url = '<?php echo base_url(); ?>';
			let postData = {};
			postData.approved_hours = BillableHours;
			postData.task_progress_id = taskProgressId;
			console.log(postData);
			jQuery.ajax({
				type: "POST",
				url: c_url + "ApproveConsultant/addLeadApprovedHours",
				data: postData
			}).done(function (data) {
				console.log(data);
				if (data) {

				}
			});
			return false;
		});

		$(document).on('change', '.rate_per_hr, .approved_hours', amntPerDay);
		$(document).on('change',
			'.amount_per_day', displayTotals);

		const formatValidInput = function (element) {
			if (Number(element.max) && Number(element.value) > Number(element.max)) element.value = element.max;
			if (Number(element.value) < 0) element.value = "0.00";
		}

		$(document).on('keyup', '.rate_per_hr, .amount_per_day', function () {
			formatValidInput(this);
		});

		$(document).on('change', '.rate_per_hr, .amount_per_day', function () {
			formatValidInput(this);
		});


		$(document).on('click', '.hr_approve', function () {
			// console.log("yes");
			// console.log($(this).parent().parent().children().eq(6).children().eq(0).val());
			let rate_per_hour = $('#rate_per_hr').val();
			if (parseFloat(rate_per_hour) == 0 || rate_per_hour == '') {
				$('#rate_per_hr_err').text("Please enter a rate per hour");
				return;
			} else {
				$('#rate_per_hr_err').text("");
			}
			let approved_hours = $(this).parent().parent().children().eq(6).children().eq(0).val();
			let taskProgressId = $(this).siblings("#task_progress_id").val();

			// let ProjectICode = jQuery("#ProjectICode").val();


			let amount_per_day = $(this).parent().parent().children().eq(7).children().eq(0).val();
			// let BillableHours = jQuery("#billable_hours").val();
			let c_url = '<?php echo base_url(); ?>';
			let postData = {};
			// postData.task_progress_id = ProjectICode;
			postData.hr_approved_hours = approved_hours;
			postData.rate_per_hour = rate_per_hour;
			postData.task_progress_id = taskProgressId;
			postData.amount_per_day = amount_per_day;
			// postData.emp_ids = emp_ids;
			jQuery.ajax({
				type: "POST",
				url: c_url + "ApproveConsultant/hrApprovedHours",
				data: postData
			}).done(function (data) {
				console.log(data);
				if (data) {

				}
			});
			return false;
		});


		let dataId;

		document.getElementById("tbl_list").addEventListener("click", function (e) {
			if (e.target.classList.contains("uploadApproval")) {
				dataId = e.target.getAttribute("data-id");
				$('#exampleModal').modal('show');
				$('#comments').val('');
			}
		})

		document.getElementById('SendBtn').addEventListener("click", function (e) {
			// $(".hr_approve").trigger("click");
			let sendTo = document.getElementById('sendTo').value;
			let comments = document.getElementById('comments').value;
			if (sendTo) {
				console.log(sendTo);
				document.getElementById('sendToErr').textContent = "";
				let postData = {};
				postData.month = $('#time_sheet_month').val();
				postData.year = $('#time_sheet_year').val();
				postData.id = dataId;
				postData.sendTo = sendTo;
				postData.comments = comments;
				let c_url = '<?php echo base_url(); ?>';
				jQuery.ajax({
					type: "POST",
					data: postData,
					url: c_url + "ApproveConsultant/updatePaymentApproval",
				}).done(function (data) {
					console.log(data);
					data = JSON.parse(data);
					console.log(data.bol);
					if (data.bol) {
						alert(data.msg);
					} else {
						alert(data.msg);
					}
					$('#exampleModal').modal('hide');
				});
			} else {
				console.log(sendToErr);
				document.getElementById('sendToErr').textContent =
					"Select an user to send payment approval request";
			}
		})
	});
</script>

<?php include 'footer.php'; ?>
</body>

</html>
