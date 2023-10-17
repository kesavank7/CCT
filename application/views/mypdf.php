<!DOCTYPE html>
<html>
<head>
	<style>
		#consultantTimesheet {
			font-family: Arial, Helvetica, sans-serif;
			border-collapse: collapse;
			width: 100%;
		}

		#consultantTimesheet td, #consultantTimesheet th {
			border: 1px solid #000000;
			padding: 8px;
		}

		#consultantTimesheet th {
			padding-top: 8px;
			padding-bottom: 8px;
			text-align: center;
		}

		#total {
			background-color: #bfbfbf;
			text-align: center;
		"
		}
	</style>
</head>
<body>
<table id="consultantTimesheet">
	<tr>
		<th>Client</th>
		<td>Internal</td>
	</tr>
	<tr>
		<th>Project</th>
		<td><?= $project_name ?></td>
	</tr>
	<tr>
		<th style="background-color: #bfbfbf">From</th>
		<td><?= $from ?></td>
	</tr>
	<tr>
		<th style="background-color: #bfbfbf">To</th>
		<td><?= $to ?></td>
	</tr>
	<tr>
		<th colspan="3" style="background-color: #8eb2e2">Summarized Timesheet</th>
	</tr>
	<tr style="background-color: #bfbfbf">
		<th>#</th>
		<th>Resource Name</th>
		<th>Total Hours</th>
	</tr>
	<tr style=" text-align: center;">
		<td>1</td>
		<td><?= $employee_name ?></td>
		<td><?= $hr_approved_hours ?></td>
	</tr>
	<tr>
		<th colspan="5" style="background-color: #8eb2e2"></th>
	</tr>
	<tr style="background-color: #bfbfbf">
		<th>#</th>
		<th>Date</th>
		<th>Name</th>
		<th>Task Description</th>
		<th>Hours Spent</th>
	</tr>
	<?php
	$count = 1;
	foreach ($timesheet as $sheet) { ?>
		<tr>
			<td style=" text-align: center;"><?= $count ?></td>
			<td style=" text-align: center;"><?= date("Y-M-d", strtotime($sheet['taskprogressdate'])) ?></td>
			<td><?= $project_name ?></td>
			<td><?= $sheet['workdescription'] ?></td>
			<td style=" text-align: center;"><?= $sheet['approved_hrs'] ?></td>
		</tr>
		<?php
		$count++;
	} ?>

	<tr id="total">
		<th colspan="4">Total Hours Spent</th>
		<td><?= $hr_approved_hours ?></td>
	</tr>
	<tr id="total">
		<th colspan="4">Rate Per Hour</th>
		<td><?= $hr_approved_rate_hour ?></td>
	</tr>
	<tr id="total">
		<th colspan="4"> Amount to be paid</th>
		<td><?= $hr_approved_bill_amount ?></td>
	</tr>
</table>

</body>
</html>


