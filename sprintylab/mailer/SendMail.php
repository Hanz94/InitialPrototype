<?php
require_once('tweeter/Twitter.php');
require_once('class.phpmailer.php');

$order_time = date('jS M Y \a\t g:ia');

$upload_folder = './uploads/';
$path_of_uploaded_file = $upload_folder;
$max_allowed_file_size = 50;

$website = 'sprintylab.com';
$send_to = 'lakshangamage.13@cse.mrt.ac.lk';
$from = 'order';

$full_path = '/home/sprintyl/public_html/mailer';
$delete_backup = true;
$send_log = false;
error_reporting(E_ALL);

$response = file_get_contents('success_response.html');
//tweeter config


function date_stamp()
{
    global $html_output;
    $order_date = date('Y-m-d-H-i');
    return $order_date;
}

function send_attachment($file, $file_is_order = true)
{
    global $send_to, $from, $website, $delete_backup, $path_of_uploaded_file
           ,$accessToken, $accessTokenSecret, $consumerKey, $consumerSecret
           , $order_time;

    $sent = 'No';
    $subject = '[SPRINTY] New ' . ($file_is_order ? 'Order:' : 'log report:') . date_stamp();
    $body = 'New Order File' . "\n" . ' - ' . $file . "\n\n";

    $email = new PHPMailer();
    $email->From = $from . '@' . $website;
    $email->FromName = $from;
    $email->Subject = $subject;
    //$email->Body = $body;
    $email->MsgHTML(getEmailHTML());
    $email->AddAddress($send_to);

    $file_to_attach = $path_of_uploaded_file;

    $email->AddAttachment($file_to_attach);


    if ($email->Send()) {
        $sent = 'Yes';
        //echo ($file_is_order ? 'New Order' : 'Log Report') . ' sent to ' . $send_to . '.<br />';

        $twitter = new Twitter($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
        $tweet = 'New Order: From '.$_POST['name'].' on '.$order_time;
        if ($twitter->authenticate()) {
            $twitter->send(utf8_encode($tweet));
        }
        if ($file_is_order) {
            if ($delete_backup) {
                unlink($file);
                //echo 'Backup file REMOVED from disk.<br />';
            } else {
                //echo 'Backup file LEFT on disk.<br />';
            }
        }
    } else {
        //echo '<span style="color: #f00;">' . ($file_is_order ? 'New Order' : 'Log Report') . ' not sent! Please check your mail settings.</span><br />';
        $response = file_get_contents('error_response.html');
    }

    //echo 'Sent? ' . $sent;

    return $sent;
}

function write_log()
{
    global $order_filename, $date_stamp, $send_log, $label, $full_path, $response;

    $log_file = $full_path . '/backup_log.txt';
    if (!$handle = fopen($log_file, 'a+')) {
        $response = file_get_contents('error_response.html');
        echo $response;
        exit;
    }
    if (chmod($log_file, 0644) && is_writable($log_file)) {

        //echo '<h2>Sprinty Mail Sender</h2>';

        //echo '<h2>Sending new order...</h2>';
        $log_content = "\n" . $date_stamp . "\t\t\t" . send_attachment($order_filename);

        //echo '<h2>Writing log...</h2>';

        $log_header = '';
        if (filesize($log_file) == '0') {
            $log_header .= $label . "\n\n";
            $log_header .= 'Backup log' . "\n";
            $log_header .= '----------------------------------------------' . "\n";
            $log_header .= 'DATESTAMP:					MAILED' . "\n";
            $log_header .= '----------------------------------------------';

            if (fwrite($handle, $log_header) === false) {
                $response = file_get_contents('error_response.html');
                echo $response;
                exit;
            }
        }

        //echo 'Log header written: ';
        if (fwrite($handle, $log_header) === false) {
            //echo 'no<br />' . "\n";
            $response = file_get_contents('error_response.html');
            echo $response;
            exit;
        } else {
            //echo 'yes<br />' . "\n";
        }

        //echo 'Log status written: ';
        if (fwrite($handle, $log_content) === false) {
            //echo 'no<br />' . "\n";
            $response = file_get_contents('error_response.html');
            echo $response;
            exit;
        } else {
            //echo 'yes<br />' . "\n";
        }
        echo $response;
    }

    fclose($handle);

    if ($send_log) {
        //echo '<h2>Sending log...</h2>';
        send_attachment($log_file, false);
    }
}

function IsInjected($str)
{
    $injections = array('(\n+)',
        '(\r+)',
        '(\t+)',
        '(%0A+)',
        '(%0D+)',
        '(%08+)',
        '(%09+)'
    );
    $inject = join('|', $injections);
    $inject = "/$inject/i";
    if (preg_match($inject, $str)) {
        return true;
    } else {
        return false;
    }
}

function getEmailHTML()
{

    global $name_of_uploaded_file, $order_time;
    $order_time = date('jS M Y \a\t g:ia');
    $pages_per_sheet = "word/";
    $message = file_get_contents('order_template.html');
    $message = str_replace('%Date%', $order_time, $message);
    $message = str_replace('%name%', $_POST['name'], $message);
    $message = str_replace('%mobile%', $_POST['mobile'], $message);
    $message = str_replace('%file_name%', $name_of_uploaded_file, $message);
    $message = str_replace('%no_of_copies%', $_POST['no_of_copies'], $message);
    $color_type="Color";
    if ($_POST['gray_or_color'] == "gray_scale") {
        $color_type = "Gray Scale";
    }
    $message = str_replace('%color_type%', $color_type, $message);
    $message = str_replace('%paper_size%', $_POST['paper_size'], $message);
    $pages="All";
    if ($_POST['page_to_print'] == "one_page") {
        $pages =  "Pages: ".$_POST['page_number_single'];
    } else if ($_POST['page_to_print'] == "from_to"){
        $pages =  "From: ".$_POST['page_number_from']." To: ".$_POST['page_number_to'];
    }
    $message = str_replace('%pages%', $pages, $message);
    $document_type="Word";
    if ($_POST['word_or_presentation'] == "presentation") {
        $document_type = "Presentation";
        $pages_per_sheet = "ppt/";
    }
    $message = str_replace('%document_type%', $document_type, $message);
    $orientation="Potrait";
    if ($_POST['landscape_or_portrait'] == "landscape") {
        $orientation = "Landscape";
    }
    $message = str_replace('%orientation%', $orientation, $message);
    $pages_per_sheet .= $_POST['landscape_or_portrait'].'/';
    $pages_per_sheet .= $_POST['pages_per_sheet'];
    $message = str_replace('%pages_per_sheet%', $pages_per_sheet, $message);
    return $message;
}

function sendError($msg)
{
    die($msg);
}

if (isset($_POST['submit'])) {
    $name_of_uploaded_file = basename($_FILES['uploaded_file']['name']);

    //get the file extension of the file
    $type_of_uploaded_file = substr($name_of_uploaded_file,
        strrpos($name_of_uploaded_file, '.') + 1);

    $size_of_uploaded_file = $_FILES["uploaded_file"]["size"] / (1024 * 1024);

    ///------------Do Validations-------------
    if (empty($_POST['name']) || empty($_POST['mobile'])
        || empty($_POST['no_of_copies']) || empty($_POST['gray_or_color'])
        || empty($_POST['paper_size']) || empty($_POST['page_to_print'])
        || empty($_POST['word_or_presentation']) || empty($_POST['landscape_or_portrait'])
        || empty($_POST['pages_per_sheet'])
    ) {
        $errors .= "\n Name and Email are required fields. ";
    }
    if (IsInjected($send_to)) {
        $errors .= "\n Bad email value!";
    }

    if ($size_of_uploaded_file > $max_allowed_file_size) {
        $errors .= "\n Size of file should be less than $max_allowed_file_size";
    }

    //------ Validate the file extension -----
//    $allowed_ext = false;
//    for ($i = 0; $i < sizeof($allowed_extensions); $i++) {
//        if (strcasecmp($allowed_extensions[$i], $type_of_uploaded_file) == 0) {
//            $allowed_ext = true;
//        }
//    }
//
//    if (!$allowed_ext) {
//        $errors .= "\n The uploaded file is not supported file type. " .
//            " Only the following file types are supported: " . implode(',', $allowed_extensions);
//    }

    //send the email
    if (empty($errors)) {
        //copy the temp. uploaded file to uploads folder
        $path_of_uploaded_file = $upload_folder . $name_of_uploaded_file;
        $tmp_path = $_FILES["uploaded_file"]["tmp_name"];

        if (is_uploaded_file($tmp_path)) {
            if (!copy($tmp_path, $path_of_uploaded_file)) {
                $errors .= '\n error while copying the uploaded file';
            }
        }
        if (empty($errors)) {
            $date_stamp = date_stamp();
            $order_filename = $path_of_uploaded_file;
            $init = write_log();
        } else {
            sendError($errors);
        }
//        //send the email
//        $name = $_POST['name'];
//        $visitor_email = $_POST['email'];
//        $to = $your_email;
//        $subject = "New form submission";
//        $from = $your_email;
//        $text = "A user  $name has sent you this message:\n $user_message";
//
//        $message = new Mail_mime();
//        $message->setTXTBody($text);
//        $message->addAttachment($path_of_uploaded_file);
//        $body = $message->get();
//        $extraheaders = array("From" => $from, "Subject" => $subject, "Reply-To" => $visitor_email);
//        $headers = $message->headers($extraheaders);
//        $mail = Mail::factory("mail");
//        $mail->send($to, $headers, $body);
//        //redirect to 'thank-you page
//        header('Location: thank-you.html');
    } else {
        sendError($errors);
    }
}


?>