<?php
namespace App\Controllers;

use App\Repositories\ContactRepositorie;
use App\Models\Contact;
use Config\Mail;
class ContactController{
    private $contactRepositorie;
    
    public function __construct()
    {
        $this->contactRepositorie = new ContactRepositorie;
    }

    public function index(){
        require_once __DIR__ . '/../Views/contact.php';
    }

    public function send(){
        try{
            // Test si tous les champs sont remplis
            if (!isset($_POST['name']) || !isset($_POST['email']) || !isset($_POST['message'])) {
                throw new \Exception('Tous les champs sont obligatoires');
                exit ;
            }
            // Récupérer les données du formulaire
            $name = htmlspecialchars($_POST['name']);
            $email = htmlspecialchars($_POST['email']);
            $subject = htmlspecialchars($_POST['subject']);
            $message = htmlspecialchars($_POST['message']);
            // Envoyer le message
            $contact = new Contact($name, $email, $subject, $message);
            $this->contactRepositorie->send($contact);

            // Envoyer un email de confirmation
            $to = $email;
            $mailSubject = "Confirmation de réception de votre demande";
            $mailBody = "
                    <html>
                        <head>
                            <style>
                                body {
                                    font-family: Arial, sans-serif;
                                    line-height: 1.6;
                                }
                                .header {
                                    padding: 10px;
                                }
                                .content {
                                    padding: 20px;
                                }
                                .footer {
                                    margin-top: 20px;
                                    font-size: 0.9em;
                                    color: #555;
                                }
                            </style>
                        </head>
                        <body>
                            <div class='header'>
                                <h2>Confirmation de Réception</h2>
                            </div>
                            <div class='content'>
                                <p><strong>Bonjour $name,</strong></p>
                                <p>
                                    Nous avons bien reçu votre demande avec le sujet : <strong>\"$subject\"</strong>.
                                </p>
                                <p>
                                    Notre équipe vous répondra dans les plus brefs délais. Merci de votre patience.
                                </p>
                                <p>
                                    <strong>Cordialement,</strong><br>
                                    L'équipe de support.
                                </p>
                            </div>
                            <div class='footer'>
                                <p>Ce message est généré automatiquement, merci de ne pas y répondre.</p>
                            </div>
                        </body>
                    </html>
                ";

            Mail::sendEmail($to, $name, $mailSubject, $mailBody);

            return true;

        }catch(\Exception $e){
            echo $e->getMessage();
        }
    }

    public function update($id){
        try{
            // Récupérer le contact :
            $contact = $this->contactRepositorie->findById($id);
            if(!$contact){
                throw new \Exception("Message not found");
            }
            // Récupérer les données du formulaire
            if (!isset($_POST['status'])) {
                throw new \Exception('Le champ status est obligatoire');
                exit ;
            }
            $contact->setStatus(htmlspecialchars($_POST['status']));
            $this->contactRepositorie->update($contact);
            
            if($contact->getStatus() == "archive"){
                // Envoyer un email de confirmation
                $to = $contact->getEmail();
                $name = $contact->getName();
                $mailSubject = "Problème résolu - Confirmation de clôture";
                $mailBody = "
                    <html>
                        <head>
                            <style>
                                body {
                                    font-family: Arial, sans-serif;
                                    line-height: 1.6;
                                }
                                .header {
                                    padding: 10px;
                                }
                                .content {
                                    padding: 20px;
                                }
                                .footer {
                                    margin-top: 20px;
                                    font-size: 0.9em;
                                    color: #555;
                                }
                            </style>
                        </head>
                        <body>
                            <div class='header'>
                                <h2>Confirmation de Clôture</h2>
                            </div>
                            <div class='content'>
                                <p><strong>Bonjour $name,</strong></p>
                                <p>
                                    Votre demande a été traitée et est maintenant considérée comme résolue. Nous espérons que le problème est réglé à votre satisfaction.
                                </p>
                                <p>
                                    Si vous avez d'autres questions ou préoccupations, n'hésitez pas à nous contacter à nouveau.
                                </p>
                                <p>
                                    <strong>Cordialement,</strong><br>
                                    L'équipe de support.
                                </p>
                            </div>
                            <div class='footer'>
                                <p>Ce message est généré automatiquement, merci de ne pas y répondre.</p>
                            </div>
                        </body>
                    </html>
                ";
                Mail::sendEmail($to, $name, $mailSubject, $mailBody);
            }
            return true;

        }catch(\Exception $e){
            echo $e->getMessage();
        }
    }

}