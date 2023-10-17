<?php include 'header.php'; ?>
<?php include 'menu.php'; ?>
<!--<html>-->
<!--<head>-->
<!--	<title> Send Email Codeigniter </title>-->
<!--</head>-->
<!--<body>-->
<?php
echo $this->session->flashdata('email_sent');
echo form_open(  base_url() . '/Sendingemail_Controller/send_mail');
?>
<input type = "email" name = "email" required />
<input type = "submit" value = "SEND MAIL">
<?php
echo form_close();
?>
<?php include 'footer.php'; ?>
</body>
</html>
