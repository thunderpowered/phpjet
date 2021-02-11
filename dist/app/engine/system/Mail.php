<?php


namespace Jet\App\Engine\System;

/**
 * Class Mail
 * @package Jet\App\Engine\System
 * @deprecated until fixed
 */
class Mail
{
    /**
     * @param string $mailTo
     * @param string $mailFrom
     * @param string $subject
     * @param string $body
     * @param null $data
     * @return bool
     */
    public function sendMail(string $mailTo, string $mailFrom, string $subject, string $body, $data = null): bool
    {
        $mail = new \PHPMailer(); // create a new object (defined in Vendor)
        $mail->IsSMTP(); // enable SMTP
        $mail->CharSet = "utf-8";
        $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
        $mail->SMTPAuth = true; // authentication enabled
        $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 465; // or 587
        $mail->IsHTML(true);
        $mail->Username = \Jet\App\Engine\Config\Config::$mail['email'];
        $mail->Password = \Jet\App\Engine\Config\Config::$mail['password'];
        $mail->SetFrom($mailFrom, \Jet\App\Engine\Config\Config::$config['site_name']);
        $mail->Subject = $subject;
        $mail->Body = $body;
        if ($data) {
            foreach ($data as $cur) {
                if (empty($cur["handle"])) {

                    $cur['handle'] = "common";
                }
                $mail->AddEmbeddedImage($cur['image'], $cur['handle']);
            }
        }
        $mail->AddAddress($mailTo);
        //$mail->addReplyTo(\Jet\App\Engine\Config\Config::$config['developer_email'], $subject.' (replied)');

        if (!$mail->Send()) {
//            \Jet\App\Engine\Core\System::exceptionToFile($mail->ErrorInfo);
            return false;
        }

        return true;
    }
}