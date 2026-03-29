<?php
/**
 * MailHandler Class
 * Handles email sending using PHPMailer with configuration from .env
 */

require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailHandler {
    private $config;

    public function __construct() {
        $this->config = [
            'MAIL_USERNAME' => 'test@gmail.com',
            'MAIL_PASSWORD' => 'APP_PASSWORD',
            'MAIL_RECIPIENT' => 'test@gmail.com',
            'SMTP_HOST' => 'smtp.gmail.com',
            'SMTP_PORT' => 587,
            'SMTP_ENCRYPTION' => 'tls'
        ];
    }

    /**
     * Send email with given content
     */
    public function sendEmail($to, $subject, $htmlContent, $altContent = '', $fromName = 'Sirac Koleji', $replyTo = null, $attachments = []) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $this->config['SMTP_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['MAIL_USERNAME'];
            $mail->Password = $this->config['MAIL_PASSWORD'];
            $mail->SMTPSecure = $this->config['SMTP_ENCRYPTION'] === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = (int)$this->config['SMTP_PORT'];
            $mail->CharSet = 'UTF-8';

            $mail->setFrom($this->config['MAIL_USERNAME'], $fromName);
            $mail->addAddress($to);

            if ($replyTo) {
                $mail->addReplyTo($replyTo['email'], $replyTo['name']);
            }

            // Add attachments
            foreach ($attachments as $attachment) {
                $mail->addAttachment($attachment['tmp_name'], $attachment['name']);
            }

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlContent;
            $mail->AltBody = $altContent ?: strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $htmlContent));

            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log("Mail sending failed: " . $mail->ErrorInfo);
            throw $e;
        }
    }

    /**
     * Get recipient email from config
     */
    public function getRecipientEmail() {
        return $this->config['MAIL_RECIPIENT'];
    }
}
?>