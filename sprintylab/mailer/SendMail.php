<?php
require("../db/config.inc.php");
require_once('tweeter/twitter.class.php');
require_once('class.phpmailer.php');
include_once("analytics/analyticstracking.php");

date_default_timezone_set('Asia/Colombo');
$order_time = date('jS M Y \a\t g:ia');

$upload_folder = './uploads/';
$path_of_uploaded_file = $upload_folder;
$max_allowed_file_size = 25;

$website = 'sprintylab.com';
$send_to = 'support@sprintylab.com';
$from = 'order';

$full_path = '/home/sprintyl/public_html/mailer';
$delete_backup = true;
$send_log = false;
error_reporting(E_ALL);

//$response = file_get_contents('success_response.html');

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
           , $order_id, $total_files, $order_time;

    $sent = 'No';
    $subject = '[SPRINTY] New ' . ($file_is_order ? 'Order:' : 'log report:') . date_stamp();
    //$body = 'New Order File' . "\n" . ' - ' . $file . "\n\n";

    $email = new PHPMailer();
    $email->From = $from . '@' . $website;
    $email->FromName = $from;
    $email->Subject = $subject;
    //$email->Body = $body;
    $email->MsgHTML(getEmailHTML());
    $email->AddAddress($send_to);
    $email->AddAddress('sprintylab@gmail.com');


    for($i=0; $i<$total_files; $i++) {
        $file_to_attach = $path_of_uploaded_file[$i];
        $email->AddAttachment($file_to_attach);
    }

    if ($email->Send()) {
        $sent = 'Yes';
        //echo ($file_is_order ? 'New Order' : 'Log Report') . ' sent to ' . $send_to . '.<br />';

        $twitter = new Twitter($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
        $tweet = "New Order:\nFrom ".$_POST['name']."\nOrder ID: ".str_pad($order_id, 4, '0', STR_PAD_LEFT)."\n".$order_time;
        try {
            if ($twitter->authenticate()) {
                $twitter->send(utf8_encode($tweet));
            }
        } catch (TwitterException $ex){

        }
        if ($file_is_order) {
            if ($delete_backup) {
                for($i=0; $i<$total_files; $i++) {
                    unlink($path_of_uploaded_file[$i]);
                }
                //unlink($file);
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
        echo getSuccessResponse();
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
    global $file_names,
           $color_type, $paper_side, $pages, $document_type, $orientation,
           $pages_per_sheet, $add_info, $name_of_uploaded_file, $order_time, $total_files;
    $order_time = date('jS M Y \a\t g:ia');
    $pages_per_sheet = "word/";
    $message = file_get_contents('order_template.html');
    $message = str_replace('%Date%', $order_time, $message);
    $message = str_replace('%name%', $_POST['name'], $message);
    $message = str_replace('%mobile%', $_POST['mobile'], $message);
    $file_names = "";
    for($i=0; $i<$total_files; $i++) {
        $file_names .= $name_of_uploaded_file[$i].",\n";
    }
    $message = str_replace('%file_name%', $file_names, $message);
    $message = str_replace('%no_of_copies%', $_POST['no_of_copies'], $message);
    $color_type="Color";
    if ($_POST['gray_or_color'] == "gray_scale") {
        $color_type = "Gray Scale";
    }
    $message = str_replace('%color_type%', $color_type, $message);
    $message = str_replace('%paper_size%', $_POST['paper_size'], $message);
    $paper_side="No";
    if ($_POST['paper_side'] == "yes") {
        $paper_side = "Yes";
    }
    $message = str_replace('%paper_side%', $paper_side, $message);
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
    $add_info = 'None';
    if (!empty($_POST['additional_information'])){
        $add_info = $_POST['additional_information'];
    }
    $message = str_replace('%additional_information%', $add_info, $message);

    $message = str_replace('%order_id%', str_pad(saveOrder(), 4, '0', STR_PAD_LEFT), $message);

    return $message;
}

function saveOrder()
{
    global $errors, $db, $order_id;
    $name = $_POST['name'];
    $phone = $_POST['mobile'];
    if (!checkCustomerExists($name, $phone)){
        addNewCustomer($name, $phone);
    }
    $customer_id = getCustomerId($name, $phone);
    $seller_id = '1';
    $date = date('Y-m-d H:i:s');
    $query = "INSERT INTO CustomerOrder 
(customer_id, seller_id, date) 
VALUES ( :customer_id, :seller_id, :date)";
    //Again, we need to update our tokens with the actual data:
    $query_params = array(
        ':customer_id' => $customer_id,
        ':seller_id' => $seller_id,
        ':date' => $date
    );

    try {
        $stmt   = $db->prepare($query);
        $result	= $stmt->execute($query_params);
    }
    catch (PDOException $ex) {
        $errors .= "Database Error1. Please Try Again!";
        sendError($errors);
    }
    $order_id = $db->lastInsertId();
    saveOrderFiles($order_id);
    return $order_id;
}

function saveOrderFiles($order_id){
    global $name_of_uploaded_file, $total_files,$file_names,
           $color_type, $paper_side, $pages, $document_type, $orientation,
           $pages_per_sheet, $add_info, $errors, $db;
    $no_of_copies = $_POST['no_of_copies'];
    $paper_size = $_POST['paper_size'];

    $query = "INSERT INTO OrderFile 
                (order_id, file_name, no_of_copies, color_type, paper_size, both_side, pages_to_print, orientation, additional_information ) 
                VALUES ( :order_id, :file_name, :no_of_copies, :color_type, :paper_size, :both_side, :pages_to_print, :orientation, :additional_information)";
    //Again, we need to update our tokens with the actual data:

    for ($i=0; $i < $total_files; $i++){
        $query_params = array(
            ':order_id' => $order_id,
            ':file_name' => $name_of_uploaded_file[$i],
            ':no_of_copies' => $no_of_copies,
            ':color_type' => $color_type,
            ':paper_size' => $paper_size,
            ':both_side' => $paper_side,
            ':pages_to_print' => $pages,
            ':orientation' => $orientation,
            ':additional_information' => $add_info
        );

        try {
            $stmt   = $db->prepare($query);
            $result	= $stmt->execute($query_params);
        }
        catch (PDOException $ex) {
            $errors .= "Database Error1. Please Try Again!";
            sendError($errors);
        }
    }
}

function addNewCustomer($name, $phone){
    global $errors, $db;
    $query = "INSERT INTO Customer 
(customer_name, phone) VALUES ( :cusname, :phone)";
    //Again, we need to update our tokens with the actual data:
    $query_params = array(
        ':cusname' => $name,
        ':phone' => $phone
    );

    try {
        $stmt   = $db->prepare($query);
        $result	= $stmt->execute($query_params);
    }
    catch (PDOException $ex) {
        $errors .= "Database Error1. Please Try Again!";
        sendError($errors);
    }
}

function getCustomerId($name, $phone){
    global $errors, $db;
    $query = " 
            SELECT *
            FROM Customer 
            WHERE 
                customer_name= :username 
                AND phone = :phone
        ";

    $query_params = array(
        ':username' => $name,
        ':phone' => $phone);

    try {
        $stmt   = $db->prepare($query);
        $result = $stmt->execute($query_params);
    }
    catch (PDOException $ex) {

        $errors .= "Database Error1. Please Try Again!";
        sendError($errors);
    }

    $row = $stmt->fetch();
    return $row['customer_id'];
}

function checkCustomerExists($name, $phone){
    global $errors, $db;
    $query = " 
            SELECT *
            FROM Customer 
            WHERE 
                customer_name= :username 
                AND phone = :phone
        ";

    $query_params = array(
        ':username' => $name,
        ':phone' => $phone);

    try {
        $stmt   = $db->prepare($query);
        $result = $stmt->execute($query_params);
    }
    catch (PDOException $ex) {

        $errors .= "Database Error1. Please Try Again!";
        sendError($errors);
    }

    $row = $stmt->fetch();
    if ($row) {
        return true;
    } else {
        return false;
    }
}

function getSellerMail($seller_id){
    global $errors, $db;
    $query = " 
            SELECT *
            FROM Seller 
            WHERE 
                seller_id= :id 
        ";

    $query_params = array(
        ':id' => $seller_id);

    try {
        $stmt   = $db->prepare($query);
        $result = $stmt->execute($query_params);
    }
    catch (PDOException $ex) {
        $errors .= "Database Error1. Please Try Again!";
        sendError($errors);
    }
    $row = $stmt->fetch();
    return $row['seller_email'];
}

function sendError($msg)
{
    global $errors;
    $response = file_get_contents('error_response.html');
    $response = str_replace('%error_txt%', $errors, $response);
    echo $response;
    die($msg);
}

function getSuccessResponse(){
    global $file_names,
           $color_type, $paper_side, $pages, $document_type, $orientation,
           $pages_per_sheet, $add_info, $name_of_uploaded_file, $order_time, $total_files, $order_id;
    $message = file_get_contents('success_response.html');
    $message = str_replace('%Date%', $order_time, $message);
    $message = str_replace('%name%', $_POST['name'], $message);
    $message = str_replace('%mobile%', $_POST['mobile'], $message);
    $message = str_replace('%file_name%', $file_names, $message);
    $message = str_replace('%no_of_copies%', $_POST['no_of_copies'], $message);
    $message = str_replace('%color_type%', $color_type, $message);
    $message = str_replace('%paper_size%', $_POST['paper_size'], $message);
    $message = str_replace('%paper_side%', $paper_side, $message);
    $message = str_replace('%pages%', $pages, $message);
    $message = str_replace('%document_type%', $document_type, $message);
    $message = str_replace('%orientation%', $orientation, $message);
    $message = str_replace('%pages_per_sheet%', $pages_per_sheet, $message);
    $message = str_replace('%additional_information%', $add_info, $message);
    $message = str_replace('%order_id%', str_pad($order_id, 4, '0', STR_PAD_LEFT), $message);
    return $message;
}

if (isset($_POST['submit'])) {
    $total_files = count($_FILES['uploaded_file']['name']);

//    $name_of_uploaded_file = basename($_FILES['uploaded_file']['name']);
//
//    //get the file extension of the file
//    $type_of_uploaded_file = substr($name_of_uploaded_file,
//        strrpos($name_of_uploaded_file, '.') + 1);
//
//    $size_of_uploaded_file = $_FILES["uploaded_file"]["size"] / (1024 * 1024);

    ///------------Do Validations-------------
    if (empty($_POST['name']) || empty($_POST['mobile'])
        || empty($_POST['no_of_copies']) || empty($_POST['gray_or_color'])
        || empty($_POST['paper_size']) || empty($_POST['page_to_print'])
        || empty($_POST['word_or_presentation']) || empty($_POST['landscape_or_portrait'])
        || empty($_POST['pages_per_sheet']) || empty($_POST['paper_side'])
    ) {
        $errors .= "\n All the required fields are not filled. ";
    }
    if (IsInjected($send_to)) {
        $errors .= "\n Bad email value!";
    }
    $size_of_uploaded_file = array_sum($_FILES["uploaded_file"]["size"]) / (1024 * 1024);
    if ($size_of_uploaded_file > $max_allowed_file_size) {
        $errors .= "\n Size of file should be less than "."$max_allowed_file_size"."MB";
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
        $path_of_uploaded_file = array();
        $name_of_uploaded_file = array();
        for($i=0; $i<$total_files; $i++) {
            $tmp_path = $_FILES['uploaded_file']['tmp_name'][$i];
            if ($tmp_path != ""){
                //Setup our new file path
                $newFilePath = $upload_folder. $_FILES['uploaded_file']['name'][$i];

                if (is_uploaded_file($tmp_path)) {
                    if (!copy($tmp_path, $newFilePath)) {
                        $errors .= '\n error while copying the uploaded file';
                    }
                }
                array_push($path_of_uploaded_file,$newFilePath);
                array_push($name_of_uploaded_file,$_FILES['uploaded_file']['name'][$i]);
                //Upload the file into the temp dir
            }
        }


        //$path_of_uploaded_file = $upload_folder . $name_of_uploaded_file;
        //$tmp_path = $_FILES["uploaded_file"]["tmp_name"];

//        if (is_uploaded_file($tmp_path)) {
//            if (!copy($tmp_path, $path_of_uploaded_file)) {
//                $errors .= '\n error while copying the uploaded file';
//            }
//        }
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