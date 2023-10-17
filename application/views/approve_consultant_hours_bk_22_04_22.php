<?php include 'header.php'; ?>

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
											for ($i = 12; $i > 0; $i--) {
												$time = strtotime(sprintf('-%d months', $i));
												$label = date('F', $time);
												$selctedM = strtolower($selctedMonth) == strtolower($label) ? 'selected' : '';
												echo "<option value='$label'  $selctedM >$label</option>";
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
                                            <b>Employee list:</b>
                                            <div class="employees">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td data-title="" width="60%" align="left">
                                    <div class="timesheet-action">
                                        <!-- <button type="hidden" class="btn btn-sm btn-green" id="timesheet_export">
                                            Export
                                        </button> -->
                                        <button type="button" class="btn btn-sm btn-blue" id="timesheet_preview"
                                            onclick="return false;">Preview
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
                <!-- <div class="rate_per_hr">
                    Rate per hour: <input type="number" id="rate_per_hr" min="0" value="0"><br>
                    <span id="rate_per_hr_err" class="text-danger"></span>
                </div> -->
            </div>
            <div id="no-more-tables">
                <table class="table table-hover" id="tbl_list">
                    <thead>
                        <tr>
                            <?php if(USERROLE == 'lead'){ ?>
                            <th width="10%">Date</th>
                            <th width="10%">Day</th>
                            <th width="20%">Name</th>
                            <th width="30%">Work description</th>
                            <th width="10%">Actual Worked Hours</th>
                            <th width="10%">BillableHours</th>
                            <th width="10%">Action</th>
                            <?php }elseif (USERROLE === 'acc') {?>
                            <th width="10%">Month</th>
                            <th width="10%">Year</th>
                            <th width="10%">Name</th>
                            <th width="10%">Project I code</th>
                            <th width="10%">Actual Worked Hours</th>
                            <th width="10%">Lead Approved Hours</th>
                            <th width="10%">Approve Hours</th>
                            <th width="10%">Amount per day</th>
                            <th width="10%">Action</th>
                            <?php }elseif (USERROLE === 'hr'){ ?>
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
    let userRoleLoggedIn = '<?php echo USERROLE;  ?>';
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
        }
    });

    jQuery('#timesheet_preview').click(function() {
        disable_timesheetbutton();
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
        var emp_ids = [];
        $("input[type=checkbox][name='emp_ids[]']").each(function() {
            if ($(this).prop('checked') === true) {
                emp_ids.push($(this).val());
            }
        });
        postData.emp_ids = emp_ids;
        jQuery.ajax({
            type: "POST",
            url: c_url + "ApproveConsultant/preview",
            data: postData
        }).done(function(data) {
            let rate_per_hr = $('#rate_per_hr').val();
            if (data) {
                data = JSON.parse(data);
                console.log(data);
                //alert(data.timesheet_list);
                $('#tbl_list tbody').html('');
                $.each(data.timesheet_list, function(index, task_detail) {
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
                    if (userRoleLoggedIn === "hr") {
                        billable_hours = task_detail.actual_wrked;
                        approved_hours =
                            '<td width="10%"><input type="number" class="approved_hours" id="approved_hours" value="' +
                            task_detail.actual_wrked + '"></td>';
                        amount_per_day =
                            `<td width="10%">
								<input type="number" class="amount_per_day" id="amount_per_day" value="${ rate_per_hr ? parseFloat(rate_per_hr * task_detail.actual_wrked).toFixed(2) : ''}">
							 </td>`;
                        approveButton =
                            '<button type="button" class="btn btn-sm btn-red hr_approve" id="hr_approve" onclick="return false;">Hr Approve</button></td>';
                        appendHtml = '<tr><td width="10%">' +
                            task_detail
                            .date + '</td>' +
                            '<td width="10%">' + task_detail.day + '</td>' +
                            '<td width="20%">' + task_detail.employee_name +
                            '</td>' +
                            '<td width="30%">' + task_detail.workdescription +
                            '</td>' +
                            '<td width="10%">' + task_detail.actual_wrked +
                            '</td>' +
                            '<td width="10%">' + billable_hours + '</td>' +
                            approved_hours + amount_per_day +
                            '<td width="10%">' +
                            '<input type="hidden" value="' + task_detail
                            .TaskProgressICode +
                            '" class="task_progress_id" id="task_progress_id">' +
                            approveButton +
                            '</tr>';
                    } else if (userRoleLoggedIn === "acc") {
                        billable_hours =
                            '<input type="text" id="billable_hours" value="' +
                            task_detail.actual_wrked + '">';
                        approved_hours =
                            '<td width="10%"><input type="number" class="approved_hours" id="approved_hours" value="' +
                            task_detail.approved_hrs + '"></td>';
                        amount_per_day =
                            `<td width="10%">
								<input type="number" class="amount_per_day" id="amount_per_day" value="${ rate_per_hr ? parseFloat(rate_per_hr * task_detail.actual_wrked).toFixed(2) : ''}">
							 </td>`;
                        approveButton =
                            '<button type="button" class="btn btn-sm btn-green acc_approve" id="acc_approve" onclick="return false;">Accountant approve</button>';
                        appendHtml = '<tr><td width="10%">' +
                            projectDate[0] + '</td>' +
                            '<td width="10%">' + projectDate[1] + '</td>' +
                            '<td width="20%">' + task_detail.employee_name +
                            '</td>' +
                            '<td width="30%">' + data.ProjectICode +
                            '</td>' +
                            '<td width="10%">' + task_detail.actual_wrked +
                            '</td>' +
                            '<td width="10%">' + task_detail.approved_hrs + '</td>' +
                            approved_hours +
                            amount_per_day +
                            '<td width="10%">' +
                            '<input type="hidden" value="' + task_detail
                            .TaskProgressICode +
                            '" class="task_progress_id" id="task_progress_id">' +
                            approveButton +
                            '</tr>'
                    } else if (userRoleLoggedIn === "lead") {
                        approved_hours = '';
                        amount_per_day = '';
                        if (!task_detail.IsApproved) {
                            approveButton =
                                '<button type="button" class="btn btn-sm btn-blue lead_approve" id="lead_approve" onclick="return false;">Lead approve</button>';
                            billable_hours =
                                '<input type="text" id="billable_hours" value="' +
                                task_detail.actual_wrked + '">';
                        } else {
                            approveButton =
                                '<button type="button" class="btn btn-sm btn-green edit_approve">Edit</button>';
                            billable_hours =
                                '<input type="text" id="billable_hours" value="' +
                                task_detail.approved_hrs + '" readonly>';
                            if (task_detail.accApproved) {
                                approveButton = "Accountant Approved";
                                billable_hours = task_detail.approved_hrs;
                            }
                        }
                        appendHtml = '<tr><td width="10%">' +
                            task_detail
                            .date + '</td>' +
                            '<td width="10%">' + task_detail.day + '</td>' +
                            '<td width="20%">' + task_detail.employee_name +
                            '</td>' +
                            '<td width="30%">' + task_detail.workdescription +
                            '</td>' +
                            '<td width="10%">' + task_detail.actual_wrked +
                            '</td>' +
                            '<td width="10%">' + billable_hours + '</td>' +
                            approved_hours + amount_per_day +
                            '<td width="10%">' +
                            '<input type="hidden" value="' + task_detail
                            .TaskProgressICode +
                            '" class="task_progress_id" id="task_progress_id"><input type="hidden" value="' +
                            data.ProjectICode +
                            '" class="task_ProjectICode" id="task_ProjectICode"><input type="hidden" value="' +
                            task_detail.EmployeeICode +
                            '" class="task_EmployeeICode" id="task_EmployeeICode">' +
                            approveButton +
                            '</tr>';
                    }
                    $('#tbl_list tbody').append(appendHtml);
                });
                if (data.total_hours != '')
                    $('#tbl_list tbody').append(
                        '<tr><td colspan="6" align="right" class="bold-16">Total Hours</td><td class="bold-16">' +
                        data.total_hours + '</td></tr>');
            }

            // amntPerDay()
            enable_timesheetbutton();
        });
        return false;
    });

    jQuery('#timesheet_export_remove').click(function() {
        disable_timesheetbutton();
        jQuery.ajax({
            type: "POST",
            url: c_url + "ApproveConsultant/preview",
            data: postData
        }).done(function(data) {
            if (data) {
                $('#tbl_list tbody').html('');
                data = JSON.parse(data);
                alert(data.timesheet_list);
                $.each(data.timesheet_list, function(index, task_detail) {
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

    $(document).on('click', '.lead_approve', function() {
        let taskProgressId = $(this).siblings("#task_progress_id").val();
        let taskProjectICode = $(this).siblings("#task_ProjectICode").val();
        let taskEmployeeICode = $(this).siblings("#task_EmployeeICode").val();
        let year = $("#time_sheet_year").val();
        let month = $("#time_sheet_month").val();
        let BillableHours = $(this).parent().parent().children().eq(5).children().eq(0).val();
        let c_url = '<?php echo base_url(); ?>';
        let postData = {};

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
                document.querySelector("#timesheet_preview").dispatchEvent(new Event("click"));
                // document.querySelector("#alert").innerHTML = document.querySelector("#alert")
                //     .innerHTML;

                $("#alert").load(" #alert > *");

            }
        });
        return false;
    });


    $(document).on('click', '.edit_approve', function() {
        $(this).parent().parent().children().eq(5).children().eq(0).attr("readonly", false);

        let editButton = this;
        let taskProgressId = $(this).siblings("#task_progress_id").val();
        let taskProjectICode = $(this).siblings("#task_ProjectICode").val();
        let taskEmployeeICode = $(this).siblings("#task_EmployeeICode").val();
        let year = $("#time_sheet_year").val();
        let month = $("#time_sheet_month").val();
        let BillableHours = $(this).parent().parent().children().eq(5).children().eq(0).val();
        let c_url = '<?php echo base_url(); ?>';
        let postData = {};

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
            editButton.textContent = "Lead Approve";
            editButton.classList.remove("edit_approve");
            editButton.classList.remove("btn-green");
            editButton.classList.add("lead_approve");
            editButton.classList.add("btn-blue");
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
