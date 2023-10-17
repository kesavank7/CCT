<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');


define('_DB_SYNERGY','Cgvak_Synergy_System.dbo.');
define('_DB_NAME_SYNERGY','Cgvak_Synergy_System');
define('_DB_EMPLOYEE','Cgvak_Synergy_System.dbo.');
define('_DB_NAME_EMPLOYEE','Cgvak_Synergy_System');

//TABLES

define('TBL_COURSE', 'CGVak_courses');
define('TBL_SELF_LEARNING_SOURCE','CGvak_Selflearning_Source');
define('TBL_TECHNOLOGY_PLATFORM','CGvak_Technology_platform');
define('TBL_TECHNOLOGY','cgvak_technology');
define('TBL_CONSULTANT_MASTER','CGVak_Consultant_Master');
define('TBL_CONSULTANT_TASK_ENTRY','CGVak_Consultant_Project_Tasks');
define('TBL_CONSULTANT_PROJECT_PROGRESS','CGVak_Consultant_Project_Tasks_Progress');
define('TBL_CONSULTANT_MONTHLY_APPROVAL','CGvak_Consultant_Monthly_Timesheet_Approvals');
define('TBL_PROJECT_MASTER','CGVak_Project_Master');
define('TBL_TASK_SCENARIO','CGvak_Project_tasks_TestScenario');
define('TBL_PROJECT_PHASE','CGVak_Project_Phase');
define('TBL_PROJECT_PHASE_MASTER','CGVak_PhaseType_Master');
define('TBL_PROJECT_TASK','CGVak_Project_Tasks');
define('TBL_TASK_TYPE','CGVak_TaskType_Master');
define('TBL_PROJECT_MEMBERS','CGVak_Project_Members');
define('TBL_LATEENTRY_REASON','cgvak_employee_lateentry_reasons');
//END TABLES

define('_ACTIVE',1);
define('_IN_ACTIVE',0);
define('_SELF_LEARNING_ID',1);
define('_PROJECT_STATUS_COMPLETED',8);
define('_DEPARTMENT_SOFTWARE_DEVELOPMENT',5);
define('_APPROVED',1);
define('_DEFAULT_ITEMS_PER_PAGE',10);
define('RECORDS_NUM_LINKS',5);
define('_SUPPORT_TO_OTHER_PROJECT',6);
define('_SELF_LEARN_USING_WEBSITES',3);


define('_SUCCESS',200);
define('_FAILED',400);

define('_DEFAULT_WORK_HOURS','08.00');
define('_SERVER_IP', "172.16.0.100");
define('_DB_USER', "Produser");
define('_DB_PASSWORD', "cgvak");
define('_DB_NAME', "Cgvak_Synergy_System");

define('HR_MAIL','kaviarchanaa@cgvakindia.com');
/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code



// moving folder
define('INVOICE_DOC_FROM',FCPATH.'public\invoice_documents\invoiceDocTemplate.xls');
// define('INVOICE_DOC_TO','D:\xampp\htdocs\OfzProjects\CodeIgniter\CCT\public\invoice_documents\invoicDoc\\');
// define('INVOICE_DOC_TO','\\\\172.16.0.77/payment_approval_system_testing/public/images/invoices/human resource/');
define('INVOICE_DOC_TO', 'http://172.16.0.77:8085/payment_approval_system_testing/public/images/invoices/human resource/');
// define('INVOICE_DOC_PATH','E:\PHP SITES\payment_approval_system\public\images/invoices/human resource/');
define('INVOICE_DOC_PATH', 'http://172.16.0.77:8085/payment_approval_system_testing/public/images/invoices/human resource/');

define('FTP_SERVER', '172.16.0.77');
define('FTP_PORT', 31294);
define('FTP_USERNAME', 'pass');
define('FTP_PASSWORD', 'passcgpass@123');


// Current user
//define('USERNAME','kesavanr');
//define('USERROLE','hr');
