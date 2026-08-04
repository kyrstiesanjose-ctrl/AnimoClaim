<?php
/* config/mail_config.php
   Gmail SMTP credentials for AnimoClaim's outgoing mail (OTPs + invoices).
   email: animoclaim@gmail.com
   https://myaccount.google.com/apppasswords  */

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'animoclaim@gmail.com');
// Gmail remove whitespaaces for given pw 
define('SMTP_PASS', str_replace(' ', '', 'csno tetz eihc qcii'));
define('MAIL_FROM', 'animoclaim@gmail.com');
define('MAIL_FROM_NAME', 'AnimoClaim');
