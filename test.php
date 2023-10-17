<?php
/* Source File Name and Path */
$remote_file = '/public/images/invoices/human resource/Internal_-_Synergy_Automation_Gokul_6_2022.pdf';

/* FTP Account */
$ftp_host = 'ftp://172.16.0.77'; /* host */
$ftp_user_name = 'pass'; /* username */
$ftp_user_pass = 'passcgpass@123'; /* password */


/* New file name and path for this file */
$local_file =  base_url().'pdf/Internal_-_Synergy_Automation_Gokul_6_2022.pdf';

/* Connect using basic FTP */
$connect_it = ftp_connect( $ftp_host );

/* Login to FTP */
$login_result = ftp_login( $connect_it, $ftp_user_name, $ftp_user_pass );

/* Download $remote_file and save to $local_file */
if ( ftp_get( $connect_it, $local_file, $remote_file, FTP_BINARY ) ) {
	echo "WOOT! Successfully written to $local_file\n";
}
else {
	echo "Doh! There was a problem\n";
}

/* Close the connection */
ftp_close( $connect_it );
?>
