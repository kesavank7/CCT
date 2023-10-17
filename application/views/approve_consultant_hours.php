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
        <div id="alert">
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
        </div>
        <!-- Error Message -->


        <!-- Timesheet export fields -->
        <div class="add_filter">
            <div id="no-more-tbl">
                <form action="<?php echo base_url() . "ApproveConsultant/export"; ?>" id="timesheet_export_form"
                    method="post" class="form-horizontal">
                    <input type="hidden" name="IsActive" id="IsActive" value="1" />
                    <input type="hidden" name="CompanyICode" id="CompanyICode" value="1" />
                    <!--input type="hidden" name="ProjectPhaseICode" id="ProjectPhaseICode" value="1" /-->
                    <input type="hidden" name="Status" id="Status" value="O" />
                    <input type="hidden" name="CreatedBy" id="CreatedBy"
                        value="<?php echo $this->session->userdata('id'); ?>" />
                    <input type="hidden" name="CreatedDate" id="CreatedDate"
                        value="<?php echo date('Y-m-d H:i:s'); ?>" />
                    <table class="col-md-12 table-bordered table-striped table-condensed cf table-hover"
                        id="tbl_timesheet_form">
                        <tbody>
                            <tr>
                                <td data-title="" width="20%">
                                    <div class="form-radio form-radio-inline">
                                        <input class="form-radio-input" type="radio" name="timesheet_type"
                                            id="timesheet_type_month" value="monthly">
                                        <label class="form-radio-label" for="timesheet_type_month">Monthly</label>
                                    </div>
                                    <div class="form-radio form-radio-inline">
                                        <input class="form-radio-input" type="radio" name="timesheet_type"
                                            id="timesheet_type_date_range" value="date_range">
                                        <label class="form-cheradiock-label" for="timesheet_type_date_range">Date
                                            Range</label>
                                    </div>
                                    <div class="type-container monthly-field">
                                        <div class="field-inline">
                                            <div class="est_txt">From Date :</div>
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
                                        <div class="field-inline">
                                            <div class="est_txt">To Date :</div>
                                            <select class="form-control" id="time_sheet_year" name="year">
                                                <?php
											$current_year = date('Y');
											for ($y = 2000; $y <= $current_year; $y++) {
												$selctedY = $current_year == $y ? 'selected' : '';
												echo "<option value='$y'  $selctedY >$y</option>";
											}
											?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="type-container date_range-field">
                                        <div class="field-inline">
                                            <div class="est_txt">From Date :</div>
                                            <?php
										$dat = date('d-m-Y', strtotime('-6 hour'));
										?>
                                            <input type="text" name="fromDate" id="time_sheet_fromDate"
                                                class="form-control datepicker" placeholder="Form Date"
                                                value="<?php echo $dat; ?>" />
                                        </div>
                                        <div class="field-inline">
                                            <div class="est_txt">To Date :</div>
                                            <input type="text" name="toDate" id="time_sheet_toDate"
                                                class="form-control datepicker" placeholder="To Date"
                                                value="<?php echo $dat; ?>" />
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    </br>
                                    <select class="form-control" id="ProjectICode" name="ProjectICode">
                                        <option value="">Select Project</option>
                                        <?php foreach ($emp_project as $project_detail) { ?>
                                        <option value="<?php echo $project_detail['ProjectICode']; ?>">
                                            <?php echo trim($project_detail['ProjectName']); ?>
                                        </option>
                                        <?php } ?>
                                    </select>
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
                                    <div class="timesheet-action">
                                        <button type="hidden" class="btn btn-sm btn-green" id="timesheet_export">
                                            Export
                                        </button>
                                        <button type="button" class="btn btn-sm btn-blue" id="timesheet_preview">Preview
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
                <table class="table table-hover" id="tbl_list">
                    <thead>
                        <tr>
                            <?php if($this->session->userdata('role') == 'Lead'){ ?>
                            <th width="10%">Date</th>
                            <th width="10%">Day</th>
                            <th width="15%">Name</th>
                            <th width="15%">Project Name</th>
                            <th width="20%">Work description</th>
                            <th width="10%">Actual Worked Hours</th>
                            <th width="10%">BillableHours</th>
                            <th width="10%">Action</th>
                            <?php }elseif ($this->session->userdata('role') == 'Account') {?>
                            <th width="10%">Month</th>
                            <th width="10%">Year</th>
                            <th width="10%">Name</th>
                            <th width="10%">Project I code</th>
                            <th width="10%">Actual Worked Hours</th>
                            <th width="10%">Lead Approved Hours</th>
                            <th width="10%">Approve Hours</th>
                            <th width="10%">Amount per day</th>
                            <th width="10%">Action</th>
                            <?php }elseif ($this->session->userdata('role') == 'HR'){ ?>
                            <th width="10%">Month</th>
                            <th width="10%">Year</th>
                            <th width="10%">Name</th>
                            <th width="10%">Project I code</th>
                            <th width="10%">Actual Worked Hours</th>
                            <th width="10%">Lead Approved Hours</th>
                            <th width="10%">Accountant Approved Hours</th>
                            <th width="10%">Approve Hours</th>
                            <th width="10%">Amount per day</th>
                            <th width="10%">Action</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $colspan = 10;
                            if($this->session->userdata('role') == 'Lead')
                                $colspan = 8;
                            elseif($this->session->userdata('role') == 'Account')
                                $colspan = 9;
                        ?>
                        <tr>
                            <td colspan="<?= $colspan ?>" align="center">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="old_txt" id="old_txt" />

<!-- Using this div for removing tooltip in appending edit content -->
<div id="remove_html"></div>
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
    jQuery('#timesheet_export').attr('disabled', 'disabled');
    jQuery('#timesheet_preview').attr('disabled', 'disabled');
}

function enable_timesheetbutton() {
    jQuery('#timesheet_export').removeAttr('disabled');
    jQuery('#timesheet_preview').removeAttr('disabled');
    hide_loader();
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
jQuery(document).ready(function($) {


    function amntPerDay() {
        let rate_per_hr = $('#rate_per_hr').val();
        let amntPerDayArray = $('.amount_per_day');
        for (let index = 0; index < amntPerDayArray.length; index++) {
            let approved_hours = amntPerDayArray[index].parentElement.parentElement.children[6].children[0]
                .value;
            amntPerDayArray[index].value = parseFloat(rate_per_hr * approved_hours).toFixed(2);
        }
    }
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
    ) {
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

    $('input[type=radio][name=timesheet_type]').change(function() {
        $('.type-container').hide();
        $('.' + this.value + '-field').show();
    });
    jQuery("#ProjectICode").change(function(e) {
        e.preventDefault();
        show_loader();
        disable_timesheetbutton();
        $('.employee_list .employees').html('');
        // if (jQuery("#ProjectICode").val()) {
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
            }).done(function(data) {
                if (data) {
                    $('.employee_list .employees').html('');
                    data = JSON.parse(data);
                    $.each(data.emp_list, function(index, emp) {
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
        // }
    });


    setTimeout(function() {jQuery('#timesheet_preview').trigger('click');}, 0);

    jQuery('#timesheet_preview').click(function() {
        show_loader();
        disable_timesheetbutton();
        let ProjectICode = jQuery("#ProjectICode").val();
        let c_url = '<?php echo base_url(); ?>';
        let TimeSheetType = $('input[type=radio][name=timesheet_type]:checked').val();
        let postData = {};
        postData.ProjectICode = ProjectICode;
        postData.timesheet_type = TimeSheetType;
        postData.approvedStatus = '';
        postData.user_role = 'Lead';
        if (TimeSheetType == 'monthly') {
            postData.month = $('#time_sheet_month').val();
            postData.year = $('#time_sheet_year').val();
        } else {
            postData.fromDate = $('#time_sheet_fromDate').val();
            postData.toDate = $('#time_sheet_toDate').val();
        }
        postData.emp_ids = [];
        $("input[type=checkbox][name='emp_ids[]']").each(function() {
            if ($(this).prop('checked') === true) {
                postData.emp_ids.push($(this).val());
            }
        });

        jQuery.ajax({
            type: "POST",
            url: c_url + "ApproveConsultant/preview",
            data: postData
        }).done(function(data) {
            if (data) {
                data = JSON.parse(data);
				$('#tbl_list tbody').html('');
                let approvedTimeSheets = data.timesheet_list.filter((sheet) => sheet
                    .IsApproved);
                let unApprovedTimeSheets = data.timesheet_list.filter((sheet) => !sheet
                    .IsApproved);
                if (unApprovedTimeSheets.length > 0)
                    unApprovedTimeSheets.forEach(timesheet => appendUnApprovedTable(timesheet,
                        timesheet.ProjectICode));
                if (approvedTimeSheets.length > 0)
                    approvedTimeSheets.forEach(timesheet => appendApprovedTable(timesheet,
                        timesheet.ProjectICode));
                if (data.total_hours != '') {
                    $('#tbl_list tbody').append(
                        '<tr><td colspan="6" align="right" class="bold-16">Total Hours</td><td class="bold-16">' +
                        data.total_hours.replace('.',':') + '</td></tr>');
				} else if (data.actual_worked_hours != '') {
					$('#tbl_list tbody').append(
                        '<tr><td colspan="5" align="right" class="bold-16">Total Hours</td><td class="bold-16">' +
                        data.actual_worked_hours.replace('.',':') + '</td><td class="bold-16">' +
                        data.lead_approved_hours.replace('.',':') + '</td></tr>');
				}
			
            }
            hide_loader();
        });
        enable_timesheetbutton();
    });


    const appendApprovedTable = function(timesheeet, ProjectICode) {
        let appendHtml;
        // let billable_hours =
        //     `<input type="number" id="billable_hours" value="${timesheeet.approved_hrs}" max="${timesheeet.actual_wrked}" onkeyup="if(this.value > ${timesheeet.actual_wrked}) { this.value = ${timesheeet.actual_wrked}; }" readonly>`;
        let billable_hours =
            `<input type="text" id="billable_hours" class="billable_hours" value="${(timesheeet.approved_hrs.startsWith('.')) ? '0'+timesheeet.approved_hrs.replace('.',':') : timesheeet.approved_hrs.replace('.',':')}" max="${timesheeet.actual_wrked}"  readonly>`;
        let approveButton = '<button type="button" class="btn btn-sm btn-green edit_approve">Edit</button>';

        if (timesheeet.accApproved) {
            approveButton = "Accountant Approved";
            billable_hours = timesheeet.approved_hrs;
        }

        appendHtml = `
			<tr>
				<td width="10%">${timesheeet.date}</td>
				<td width="10%">${timesheeet.day}</td>
				<td width="15%">${timesheeet.employee_name}</td>
                <td width="15%">${timesheeet.project_name}</td>
				<td width="20%">${timesheeet.workdescription}</td>
				<td width="10%">${timesheeet.actual_wrked.replace('.',':')}</td>
				<td width="10%" class="billable_hours_td">${billable_hours}</td>
				<td width="10%">
					<input type="hidden" value="${timesheeet.TaskProgressICode}" class="task_progress_id" id="task_progress_id">
					<input type="hidden" value="${ProjectICode}" class="task_ProjectICode" id="task_ProjectICode">
					<input type="hidden" value="${timesheeet.EmployeeICode}" class="task_EmployeeICode" id="task_EmployeeICode">
					${approveButton}
				</td>
			</tr>
		`;

        $('#tbl_list tbody').append(appendHtml);
    }


    const appendUnApprovedTable = function(timesheeet, ProjectICode) {
        let appendHtml;
        // let billable_hours =
        //     `<input type="number" id="billable_hours" value="${timesheeet.actual_wrked}" max="${timesheeet.actual_wrked}" onkeyup="if(this.value > ${timesheeet.actual_wrked}) { this.value = ${timesheeet.actual_wrked}; }">`;
        let billable_hours =
            `<input type="text" id="billable_hours" value="${(timesheeet.actual_wrked.startsWith('.')) ? '0'+timesheeet.actual_wrked.replace('.',':') : timesheeet.actual_wrked.replace('.',':')}" class="billable_hours" max="${timesheeet.actual_wrked}" >
            <input type="hidden" id="actual_wrked_hrs" value="${timesheeet.actual_wrked}" class="actual_wrked_hrs">`;
        let approveButton =
            '<button type="button" class="btn btn-sm btn-blue lead_approve" id="lead_approve" onclick="return false;">Lead approve</button>';

        appendHtml = `
			<tr>
				<td width="10%">${timesheeet.date}</td>
				<td width="10%">${timesheeet.day}</td>
				<td width="15%">${timesheeet.employee_name}</td>
                <td width="15%">${timesheeet.project_name}</td>
				<td width="20%">${timesheeet.workdescription}</td>
				<td width="10%">${timesheeet.actual_wrked.replace('.',':')}</td>
				<td width="10%" class="billable_hours_td">${billable_hours}</td>
				<td width="10%">
					<input type="hidden" value="${timesheeet.TaskProgressICode}" class="task_progress_id" id="task_progress_id">
					<input type="hidden" value="${ProjectICode}" class="task_ProjectICode" id="task_ProjectICode">
					<input type="hidden" value="${timesheeet.EmployeeICode}" class="task_EmployeeICode" id="task_EmployeeICode">
					${approveButton}
				</td>
			</tr>
		`;

        $('#tbl_list tbody').append(appendHtml);
    }


    const formatValidInput = function(element) {
        if (Number(element.value) > Number(element.max)) element.value = element.max;
        if (Number(element.value) < 0) element.value = "0.00";
    }

    $(document).on('keyup', '.billable_hours', function() {
        formatValidInput(this);
    });

    $(document).on('change', '.billable_hours', function() {
        formatValidInput(this);
    });


    jQuery('#timesheet_export_remove').click(function() {
        show_loader();
        disable_timesheetbutton();
        let ProjectICode = jQuery("#ProjectICode").val();
        let c_url = '<?php echo base_url(); ?>';
        let TimeSheetType = $('input[type=radio][name=timesheet_type]:checked').val();
        let postData = {};
        postData.ProjectICode = ProjectICode;
        postData.timesheet_type = TimeSheetType;
        postData.approvedStatus = '';
        postData.user_role = 'Lead';
        if (TimeSheetType == 'monthly') {
            postData.month = $('#time_sheet_month').val();
            postData.year = $('#time_sheet_year').val();
        } else {
            postData.fromDate = $('#time_sheet_fromDate').val();
            postData.toDate = $('#time_sheet_toDate').val();
        }
        postData.emp_ids = [];
        $("input[type=checkbox][name='emp_ids[]']").each(function() {
            if ($(this).prop('checked') === true) {
                postData.emp_ids.push($(this).val());
            }
        });
        jQuery.ajax({
            type: "POST",
            url: c_url + "ApproveConsultant/export",
            data: postData
        }).done(function(data) {
            console.log('success expo');
            // hide_loader();
            enable_timesheetbutton();
        });
        enable_timesheetbutton();
    });

    $(document).on('click', '.lead_approve', function() {
        $(this).attr('disabled','disabled');
        document.querySelectorAll('.lead_approve').forEach(elem => {
            elem.disabled = true;
        });
        document.querySelectorAll('.edit_approve').forEach(elem => {
            elem.disabled = true;
        });

        let taskProgressId = $(this).siblings("#task_progress_id").val();
        let taskProjectICode = $(this).closest('tr').find(".task_ProjectICode").val();
        let taskEmployeeICode = $(this).siblings("#task_EmployeeICode").val();
        let year = $("#time_sheet_year").val();
        let month = $("#time_sheet_month").val();
        let BillableHours = $(this).closest('tr').find('.billable_hours').val().trim();
        let c_url = '<?php echo base_url(); ?>';
        let postData = {};

        let actual_wrked_hrs = $(this).closest('tr').find('.actual_wrked_hrs').val();
        var pattern_time = /^[-+]?[0-9]+\:[0-9]+$/;
        if (!BillableHours.match(pattern_time)) {
            alert("Please check the Billable Hours Format(00:00) in row");
            // document.getElementById("ManHours_" + task_code + "").focus();
            return false;
        }
        if(BillableHours.includes(':')) {
            BillableHours = BillableHours.replace(':','.')
        }
        actual_wrked_hrs = parseFloat(Number(actual_wrked_hrs).toFixed(2));
        if(BillableHours > actual_wrked_hrs) {
            alert("Please check the Billable Hours cannot be greater than Actual Worked Hours.");
            return false;
        } else {
            if (!BillableHours.trim()) {
                alert('Please enter Man Hours');
                return false;
            } else {
                var arr = BillableHours.split('.');
                if ((parseInt(arr[0]) <= 0) && (parseInt(arr[1]) <= 0)) {
                    alert('Please enter valid Hours');
                    return false;
                }else {
                    var pattern_minutes = /^[0-5]?[0-9]+$/;
                    if (!arr[1].match(pattern_minutes)) {
                        alert("Please enter a valid minutes");
                        return false;
                    }
                }
            }
        }

        postData.approved_hours = BillableHours;
        postData.task_progress_id = taskProgressId;
        postData.task_ProjectICode = taskProjectICode;
        postData.task_EmployeeICode = taskEmployeeICode;
        postData.year = year;
        postData.month = month;

        jQuery.ajax({
            type: "POST",
            url: c_url + "ApproveConsultant/addLeadApprovedHours",
            data: postData
        }).done(function(data) {
            console.log(data);
            if (data) {
                document.querySelector("#timesheet_preview").dispatchEvent(new Event(
                    "click"));
                // document.querySelector("#alert").innerHTML = document.querySelector("#alert")
                //     .innerHTML;

                $("#alert").load(" #alert > *");

            }
        });
        return false;
    });


    $(document).on('click', '.edit_approve', function() {
        $(this).attr('disabled','disabled');
        document.querySelectorAll('.edit_approve').forEach(elem => {
            elem.disabled = true;
        });
        document.querySelectorAll('.lead_approve').forEach(elem => {
            elem.disabled = true;
        });
        $(this).closest('tr').find('.billable_hours').attr("readonly", false);

        let editButton = this;
        let taskProgressId = $(this).siblings("#task_progress_id").val();
        let taskProjectICode = $(this).siblings("#task_ProjectICode").val();
        let taskEmployeeICode = $(this).siblings("#task_EmployeeICode").val();
        let year = $("#time_sheet_year").val();
        let month = $("#time_sheet_month").val();
        let BillableHours = $(this).closest('tr').find('.billable_hours').val();
        let c_url = '<?php echo base_url(); ?>';
        let postData = {};

        if(BillableHours.includes(':')) {
            BillableHours = BillableHours.replace(':','.')
        }

        postData.approved_hours = BillableHours;
        postData.task_progress_id = taskProgressId;
        postData.task_ProjectICode = taskProjectICode;
        postData.task_EmployeeICode = taskEmployeeICode;
        postData.year = year;
        postData.month = month;

        jQuery.ajax({
            type: "POST",
            url: c_url + "ApproveConsultant/editLeadApproval",
            data: postData
        }).done(function(data) {
            console.log(data);
            // editButton.textContent = "Lead Approve";
            // editButton.classList.remove("edit_approve");
            // editButton.classList.remove("btn-green");
            // editButton.classList.add("lead_approve");
            // editButton.classList.add("btn-blue");
            document.querySelector("#timesheet_preview").dispatchEvent(new Event("click"));
            $("#alert").load(" #alert > *");
        });
        return false;
    });

    $(document).on('change', '#rate_per_hr', function() {
        amntPerDay();
    })

    $(document).on('change', '#approved_hours', function() {
        amntPerDay();
    })


    $(document).on('click', '.hr_approve', function() {
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
        }).done(function(data) {
            if (data) {

            }
        });
        return false;
    });
});
</script>

<?php include 'footer.php'; ?>
</body>

</html>
