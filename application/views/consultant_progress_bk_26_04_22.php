<?php include 'header.php'; ?>
<?php include 'menu.php'; ?>
<style>
    .saveBtn {
        margin: 15px;
        background-color: #04AA6D;
        color: #ffffff;
        border: none;
        padding: 10px 20px;
        font-size: 17px;
        /* font-family: Raleway; */
        cursor: pointer;
    }

    .saveBtn:hover {
        opacity: 0.8;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<?php

function getTimeDiff($atime, $dtime)
{
    $nextDay = $dtime > $atime ? 1 : 0;
    $dep = EXPLODE(':', $dtime);
    $arr = EXPLODE(':', $atime);
    $diff = ABS(MKTIME(@$dep[0], @$dep[1], 0, DATE('n'), DATE('j'), DATE('y')) - MKTIME(@$arr[0], @$arr[1], 0, DATE('n'), DATE('j') + $nextDay, DATE('y')));
    $hours = FLOOR($diff / (60 * 60));
    $mins = FLOOR(($diff - ($hours * 60 * 60)) / (60));
    $secs = FLOOR(($diff - (($hours * 60 * 60) + ($mins * 60))));
    if (STRLEN($hours) < 2) {
        $hours = "0" . $hours;
    }
    if (STRLEN($mins) < 2) {
        $mins = "0" . $mins;
    }
    if (STRLEN($secs) < 2) {
        $secs = "0" . $secs;
    }
    return $hours . ':' . $mins . ':' . $secs;
}

?>
<div class="container">
    <div class="starter-template">
        <!-- Error Message -->
        <?php if ($this->session->userdata('msg') == 'progress') { ?>
            <div class="alert alert-success " role="alert">
                Successfully Added Task Progress.
            </div>
        <?php } else if (validation_errors()) { ?>
            <div class="alert alert-danger" role="alert">
                <?php echo validation_errors(); ?>
            </div>
        <?php } ?>
        <!-- Error Message -->

        <!-- Filter --->
        <div class="panel panel-warning ovrhid">
            <div class="panel-heading">
                <h3 class="panel-title">Task Progress</h3>
                <div class="pull-right">
                    <span class="clickable filter" data-toggle="tooltip" title="Project Filter" data-container="body">
                        <i class="glyphicon glyphicon-filter"></i>
                    </span>
                </div>
            </div>
            <div class="panel-body">
                <form action="<?php echo base_url() . "cosultantprogress/progress_search"; ?>"
                      id="task_progress_search_form" method="post" class="form-horizontal">
                    <div class="form-group col-sm-12" style="margin-bottom: 0px;">
                        <div class="col-md-2">
                            <label for="pro_search_filter" class="control-label ">Project Filter:</label>
                        </div>
                        <div class="col-md-5">
                            <select class="form-control" id="entry_progress_filter" name="entry_progress_filter"
                                    onchange="this.form.submit();">
                                <option value="">Select Project</option>
                                <?php foreach ($emp_project as $project_detail) { ?>
                                    <option value="<?php echo $project_detail['ProjectICode']; ?>" <?php echo ($search_project_id == $project_detail['ProjectICode']) ? ' selected ' : "" ?> >
                                        <?php echo trim($project_detail['ProjectName']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <!-- Filter --->

            <input type="hidden" name="page" id="page" value=""/>
            <?php if (count($progress_list) > 0) : ?>

            <?php endif; ?>
            <div class="form-group col-sm-12">
                <div id="no-more-tables">
                    <!-- Table Progress -->
                    <table class="table table-hover" id="tbl_list">
                        <thead>
                        <tr>
                            <th width="1%">#</th>
                            <th width="20%">Project Name</th>
                            <th width="30%">Task Description</th>
                            <th width="4%;">Estimated&nbsp;/ Pending Hours</th>
                            <th width="9%;">End Date</th>
                            <th width="25%;">Work Description</th>
                            <th width="11%;">Logged Hours / Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (count($progress_list) == 0) { ?>
                            <tr>
                                <td colspan="7">No Task Found</td>
                            </tr>
                            <?php
                        } else {
                            $sno = 0; /* Serial number */
                            foreach ($progress_list as $progress_detail) {
                                ?>
                                <tr id="task_progress_row_<?php echo $progress_detail['TaskICode']; ?>">
                                    <td data-title="#" scope="row"><?php echo $sno = $sno + 1; ?></td>
                                    <td data-title="Project Name">
                                        <input type="hidden" name="TaskICode[]"
                                               id="TaskICode_<?php echo $progress_detail['TaskICode']; ?>"
                                               value="<?php echo $progress_detail['TaskICode']; ?>"/>
                                        <input type="hidden" name="ProjectICode[]"
                                               id="ProjectICode_<?php echo $progress_detail['TaskICode']; ?>"
                                               value="<?php echo $progress_detail['ProjectICode']; ?>"/>
                                        <?php foreach ($emp_project as $project_detail) { ?>
                                            <?php echo ($progress_detail['ProjectICode'] == $project_detail['ProjectICode']) ? $project_detail['ProjectName'] : "" ?>
                                        <?php } ?>
                                        <br><br>
                                        <a href="javascript:void(0)"
                                           onclick="GetTaskHistoryDetails('<?php echo $progress_detail['ProjectICode']; ?>', '<?php echo $progress_detail['TaskICode']; ?>');"
                                           id="task_history_details" data-toggle="modal">Taks Progress</a>
                                    </td>

                                    <td data-title="Task Description" class="pos-rel task-desc">
                                        <div class="tbl_row_scroll_progr">
                                            <?php echo $progress_detail['TaskDescription']; ?>
                                        </div>
                                    </td>
                                    <td data-title="Estimated&nbsp;/ Pending Hours">
                                        <?php echo $progress_detail['EstimatedHours']; ?>
                                        / <br/>
                                        <?php
                                        if (isset($progress_detail['EstimatedHours']) && isset($progress_detail['ManHours'])) {
                                            $value = (int)$progress_detail['EstimatedHours'] - (int)$progress_detail['ManHours'];
                                            $txt = "<span ";
                                            if ($value < 0) {
                                                $txt .= " class='red_txt' ";
                                            }
                                            $txt .= " >";
                                            $txt .= sprintf("%01.2f", $value);
                                            echo $txt .= "</span>";
                                        } else {
                                            echo $progress_detail['EstimatedHours'];
                                        }
                                        ?>
                                    </td>
                                    <td data-title="End Date">
                                        <?php
                                        if (isset($progress_detail['TaskEndDate'])) {
                                            echo date('d-m-Y', strtotime($progress_detail['TaskEndDate']));
                                        }
                                        ?>
                                    </td>
                                    <td data-title="Work Description">
                                        <?php
                                        if (isset($progress_detail['WorkDescription'])) {
                                            echo $progress_detail['WorkDescription'];
                                        } else {
                                            echo "<textarea class='form-control' name='WorkDescription[" . $progress_detail['TaskICode'] . "][]' rows='7' id='WorkDescription_" . $progress_detail['TaskICode'] . "' placeholder='Work&nbsp;Description'></textarea>";
                                            echo '<div style="display:none" id = "late_entry_reson_' . $progress_detail["TaskICode"] . '">
                                                    Is Email Sent to Lead?
                                                    <input type="radio" name = "hr_' . $progress_detail['TaskICode'] . '" value="1"  class =  "hr_' . $progress_detail['TaskICode'] . '"> Yes 
                                                    <input type="radio" name = "hr_' . $progress_detail['TaskICode'] . '" value="0" checked="checked" class = "hr_' . $progress_detail['TaskICode'] . '"> No ';

                                            echo "<textarea class='form-control' name='latereason[" . $progress_detail['TaskICode'] . "][]' rows='3' id='latereason" . $progress_detail['TaskICode'] . "' placeholder='Late&nbsp;Entry&nbsp;Reason'></textarea></div>";
                                        }
                                        ?>
                                    </td>
                                    <td data-title="Logged Hours / Date">
                                        <?php
                                        $dat = date('d-m-Y', strtotime('-6 hour'));
                                        echo "<input type='text' name='ManHours[" . $progress_detail['TaskICode'] . "][]'  class='form-control timepicker' id='ManHours_" . $progress_detail['TaskICode'] . "' placeholder='hh:mm' value='08.00' /> ";
                                        echo "<p>&nbsp;</p>";
                                        echo "<input type='text' name='TaskProgressDate[" . $progress_detail['TaskICode'] . "][]'   class='form-control datepicker'  max='" . date('Y-m-d') . "' onchange='checkdataforlatereason(" . $progress_detail['TaskICode'] . ")' id='TaskProgressDate_" . $progress_detail['TaskICode'] . "' value='" . $dat . "' placeholder='dd-mm-yyyy' />";
                                        ?>
                                        <input type="hidden"
                                               id="lead_id_report_<?php echo $progress_detail['TaskICode']; ?>"
                                               value="<?php echo $progress_detail['lead_id']; ?>"/>
                                        <input type="hidden"
                                               id="latereasoncheck_<?php echo $progress_detail['TaskICode']; ?>"
                                               value="0"/>
                                        <!--btn btn-sm btn-success btn3d -->
                                        <button type="submit" class="saveBtn btn btn-sm  pull-right"
                                                onchange="checkdataforlatereason()"
                                                id="disable_check_<?php echo trim($progress_detail['TaskICode']); ?>"
                                                onclick="return progress(<?php echo trim($progress_detail['TaskICode']); ?>,<?php echo $progress_detail['ProjectICode']; ?>);">
                                            <b class="buttonload"
                                               id="loader_check_<?php echo trim($progress_detail['TaskICode']); ?>"><i
                                                        class="fa fa-spinner fa-spin"></i></b> Save
                                        </button>
                                    </td>
                                </tr>
                                <?php
                            } //foreach
                        }//else
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!--</form>-->
        </div>
    </div>
</div>

<script type="text/javascript">

    const dateProcess = (date) => {
        var parts = date.split("-");
        var date = new Date(parts[1] + "-" + parts[0] + "-" + parts[2]);
        return date.getTime();
    };

    function checkdataforlatereason(task_id) {

        var dateEntered = document.getElementById("TaskProgressDate_" + task_id + "").value;
        var dateCurrent = "<?php echo $dat; ?>";//15-09-2020
        if (dateEntered < dateCurrent) {
            jQuery("#late_entry_reson_" + task_id).show();
            jQuery("#latereasoncheck_" + task_id).val(1);
        } else if (dateEntered > dateCurrent) {
            jQuery("#late_entry_reson_" + task_id).show();
            jQuery("#latereasoncheck_" + task_id).val(1);
        } else {
            jQuery("#late_entry_reson_" + task_id).hide();
            jQuery("#latereasoncheck_" + task_id).val(0);
        }
    }

    function progress(task_id, projectICode) {

        var insert_row = 0;
        var first_column = "";
        var latereasoncheck = document.getElementById("latereasoncheck_" + task_id + "").value;

        for (var i = 0; i < document.getElementsByName('TaskICode[]').length; i++) {
            var task_code = document.getElementsByName('TaskICode[]')[i].value.trim();
            var row_number = document.getElementById("task_progress_row_" + task_code + "").rowIndex;// + parseInt(1);
            var work_desc = document.getElementsByName("WorkDescription[" + task_code + "][]").length;
            var mans_hour = document.getElementsByName("ManHours[" + task_code + "][]").length;
            var prog_date = document.getElementsByName("TaskProgressDate[" + task_code + "][]").length;

            for (var j = 0; j < prog_date; j++) {
                var work_desc_val = document.getElementsByName("WorkDescription[" + task_code + "][]")[j].value;
                var mans_hour_val = document.getElementsByName("ManHours[" + task_code + "][]")[j].value.trim();
                var prog_date_val = document.getElementsByName("TaskProgressDate[" + task_code + "][]")[j].value.trim();
                /* First Work description Id*/
                if (!first_column)
                    first_column = "WorkDescription_" + task_code + "";

                /* Vadation Only work desc enterd row*/
                if (work_desc_val) {
                    work_desc_val = work_desc_val.trim();
                    if (!work_desc_val) {
                        alert("Work Description cannot be empty in row -> " + row_number);
                        document.getElementById("WorkDescription_" + task_code + "").focus();
                        return false;
                    }
                    if (!mans_hour_val) {
                        alert("Please Enter the Logged Hours in row -> " + row_number);
                        document.getElementById("ManHours_" + task_code + "").focus();
                        return false;
                    } else if (!prog_date_val) {
                        alert("Please Enter the Logged Date in row -> " + row_number);
                        document.getElementById("TaskProgressDate_" + task_code + "").focus();
                        return false;
                    }
                    var pattern_time = /^[-+]?[0-9]+\.[0-9]+$/;
                    if (!mans_hour_val.match(pattern_time)) {
                        alert("Please check the Logged Hours Format(00.00) in row -> " + row_number);
                        document.getElementById("ManHours_" + task_code + "").focus();
                        return false;
                    }

                    var pattern_date = /^([0-9]{2})\-([0-9]{2})\-([0-9]{4})$/;
                    if (!pattern_date.test(prog_date_val)) {
                        alert("Please check the Date Format in row -> " + row_number);
                        document.getElementById("TaskProgressDate_" + task_code + "").focus();
                        return false;
                    }
                    insert_row++;
                }
            }
        }
        document.getElementById("page").value = jQuery(".paginate_button.current").html();

        if (insert_row == 0) {
            alert('Please Enter Work Description.');
            var foc = (first_column) ? jQuery("#" + first_column + "").focus() : "";
            return false;
        } else {
            if (latereasoncheck == 1) {
                var late_entry_reson = jQuery("#latereason" + task_id).val();
                var lead_id_report = jQuery("#lead_id_report_" + task_id).val();
                var hr_mail = jQuery('input[name=hr_' + task_id + ']:checked').val();


                if (jQuery.trim(late_entry_reson) == "") {
                    alert('Please Enter late reason');
                    return;
                }
            } else {
                var late_entry_reson = "";
                var lead_id_report = "";
                var hr_mail = "";
            }
            var mans_hour = jQuery("#ManHours_" + task_id + "").val();
            if (!mans_hour.trim()) {
                alert('Please enter Man Hours');
                return;
            } else {
                valid_minutes = ['00', '25', '50', '75'];
                var arr = mans_hour.split('.');
                if (parseInt(arr[0]) <= 0) {
                    alert('Please enter valid Man Hours');
                    return;
                } else if (parseInt(arr[0]) > 24) {
                    alert('Man Hours should not exceed 24 hours');
                    return;
                } else {
                    if (jQuery.inArray(arr[1], valid_minutes) == -1) {
                        alert('Please enter a valid minute:00,25,50,75');
                        return;
                    }
                }
            }
            jQuery("#loader_check_" + task_id).show();
            jQuery("#disable_check_" + task_id).prop('disabled', true);
            var task_code = task_id;
            var row_number = document.getElementById("task_progress_row_" + task_code + "").rowIndex;// + parseInt(1);
            var work_desc = document.getElementsByName("WorkDescription[" + task_code + "][]")[0].value;
            var mans_hour = jQuery("#ManHours_" + task_id + "").val();
            var prog_date = document.getElementsByName("TaskProgressDate[" + task_code + "][]")[0].value;

            jQuery.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>cosultantprogress/progress_insert_ajax",

                data: {
                    'task_code': task_code,
                    'work_desc': work_desc,
                    'mans_hour': mans_hour,
                    'prog_date': prog_date,
                    'projectICode': projectICode,
                    'late_entry_reson': late_entry_reson,
                    'lead_id_report': lead_id_report,
                    'hr_mail': hr_mail,
                    'latereasoncheck': latereasoncheck,
                    'page': 1
                },
                async: true,
                success: function (res) {
                    var msgs = jQuery.parseJSON(res).msg;
                    if (msgs.status == 0) {
                        jQuery("#loader_check_" + task_id).hide();
                        jQuery("#disable_check_" + task_id).prop('disabled', false);
                        alert(msgs.msg);
                    } else {
                        location.reload();
                    }

                }
            });


        }

    }
</script>

<script type="text/javascript">
    jQuery("#consultant_task_progress").addClass('active');
    jQuery(function () {
        jQuery('.datepicker').datetimepicker({
            timepicker: false,
            format: 'd-m-Y',
            closeOnDateSelect: true,
            maxDate: new Date(),
        });
    });

    jQuery(document).ready(function () {
        if (<?php echo count($progress_list) ?> >
        0
    )
        {
            var table = jQuery('#tbl_list').dataTable({
                "bSort": false,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                "bLengthChange": false,
                "info": false,
                "filter": false,
                "order": [[0, "asc"]]
            });
        }
        <?php if ($this->input->get('page')): ?>
        var page_no = "<?php echo $this->input->get('page'); ?>";
        var total_page = Math.ceil(parseInt(table.fnGetData().length) / parseInt(10));
        if (total_page <= page_no) {
            page_no = total_page;
        }
        table.fnPageChange(--page_no, true);
        <?php endif; ?>

    });

    function GetTaskHistoryDetails(PorIcode, TaskIcode) {
        jQuery.ajax({
            type: "POST",
            url: "<?php echo base_url(); ?>cosultantprogress/gettaskhistory",
            data: {PorjectIcode: PorIcode, TaskIcode: TaskIcode},
            success: function (res) {
                jQuery('#history_details_append').html(res);
                jQuery('#task_progress_modal').modal('show');
            }
        });
    }
</script>
<!-- Button trigger modal -->


<!-- Modal -->
<div class="modal fade" id="task_progress_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
     aria-hidden="true">
    <div class="modal-dialog" role="document" style="width:80%;  max-height: 80% !important; overflow-y: scroll;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Task History</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-hover" id="tbl_list">
                    <thead>
                    <tr>
                        <th width="1%">#</th>
                        <th width="10%">Progress Date</th>
                        <th width="30%">Work Description</th>
                        <th width="4%;">Hours</th>
                        <th width="15%;">Entered Date and Time</th>
                    </tr>
                    </thead>
                    <tbody id="history_details_append">
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<style>
    .modal-header .close {
        position: absolute;
        right: 12px;
        top: 12px;
    }

    .buttonload {
        display: none;

    }
</style>
<?php include 'footer.php'; ?>
</body>
</html>
