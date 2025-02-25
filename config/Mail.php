<?php
namespace Config;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class Mail {
    /**
     * Fonction pour configurer PHPMailer.
     *
     * @return PHPMailer
     * @throws Exception
     */
    private static function getMailer() {
        $mail = new PHPMailer(true);
        try {
            // Configuration du serveur SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'yassinekamouss76@gmail.com'; 
            $mail->Password = 'srwh aafi svur xswl'; 
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            // Configuration de l'expéditeur
            $mail->setFrom('yassinekamouss76@gmail.com', 'Support');

            return $mail;
        } catch (Exception $e) {
            throw new Exception("Erreur de configuration de PHPMailer : " . $e->getMessage());
        }
    }

    /**
     * Fonction pour envoyer un e-mail.
     *
     * @param string $toEmail Adresse e-mail du destinataire
     * @param string $toName Nom du destinataire
     * @param string $subject Sujet de l'e-mail
     * @param string $body Contenu HTML du message
     * @return bool|string Retourne true si l'e-mail est envoyé, sinon une chaîne d'erreur
     */

    public static function sendEmail($toEmail, $toName, $subject, $body) {
        $mail = self::getMailer();
        try {
            // Configuration du destinataire
            $mail->addAddress($toEmail, $toName);

            // Contenu de l'e-mail
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            // Envoi de l'e-mail
            $mail->send();
            return true;
        } catch (Exception $e) {
            return "Erreur lors de l'envoi de l'e-mail : " . $mail->ErrorInfo;
        }
    }
}