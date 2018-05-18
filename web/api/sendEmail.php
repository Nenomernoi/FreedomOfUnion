<?php

require_once('PHPMailer/class.phpmailer.php');

function sendEmail($user, $email, $subject, $body) {

    // $emailSender = 'freedomoruniongame@gmail.com';
    $emailSender = 'freedomorunion.nr.studio@gmail.com';

    $mail = new PHPMailer(true);  // the true param means it will throw exceptions on errors, which we need to catch
    $mail->IsSMTP();  // telling the class to use SMTP
    try {
        $mail->Host = "smtp.gmail.com";  // SMTP server for the GMAIL server
        $mail->Port = 465;                    // SMTP port for the GMAIL server
        $mail->CharSet = 'UTF-8';
        $mail->Username = $emailSender;  // GMAIL username
        $mail->Password = "zxc1410vbn1242";             // GMAIL password
        $mail->SMTPDebug = 0;        // 1 to enables SMTP debug (for testing), 0 to disable debug (for production)
        $mail->SMTPAuth = true;    // enable SMTP authentication
        $mail->SMTPSecure = "ssl";  // ssl required for Gmail
        //   $mail->AddReplyTo("nenozig@gmail.com","First Last");
        $mail->SetFrom($emailSender, 'Admin "Freedom or Union"');
        $mail->AddAddress($email, $user);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->isHTML(true);

        $mail->Send();
    } catch (phpmailerException $e) {

        $url = 'https://api.sendgrid.com/';
        $user = 'app96975387@heroku.com';
        $pass = 'rcrezxiu3483';


        $params = array(
            'api_user' => $user,
            'api_key' => $pass,
            'to' => $email,
            'subject' => $subject,
            'html' => $body,
            'text' => 'Message from Freedom of Union',
            'from' => $emailSender
        );

        $request = $url . 'api/mail.send.json';

        $session = curl_init($request);
        curl_setopt($session, CURLOPT_POST, true);
        curl_setopt($session, CURLOPT_POSTFIELDS, $params);
        curl_setopt($session, CURLOPT_HEADER, false);
        curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

        $res = curl_exec($session);
        curl_close($session);
    }
}
