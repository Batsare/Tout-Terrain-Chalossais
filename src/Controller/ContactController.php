<?php
namespace App\Controller;


use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends Controller
{
    /**
     * @Route("/contact", name="contact")
     */
    public function contactAction(Request $request)
    {
        // Create the form according to the FormType created previously.
        // And give the proper parameters
        $form = $this->createForm('App\Type\ContactType',null,array(
            // To set the action use $this->generateUrl('route_identifier')
            'action' => $this->generateUrl('contact'),
            'method' => 'POST'
        ));

        if ($request->isMethod('POST')) {
            // Refill the fields in case the form is not valid.
            $form->handleRequest($request);
            // Ma clé privée

            $secret = "6LfTwFgUAAAAAINGiMIsKUx1OYTfgvuhn-KHjBch";

            // Paramètre renvoyé par le recaptcha

            $response = $_POST['g-recaptcha-response'];

            // On récupère l'IP de l'utilisateur

            $remoteip = $_SERVER['REMOTE_ADDR'];


            $api_url = "https://www.google.com/recaptcha/api/siteverify?secret="

                . $secret

                . "&response=" . $response

                . "&remoteip=" . $remoteip ;

            $arrContextOptions=array(
                "ssl"=>array(
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                ),
            );

            $decode = json_decode(file_get_contents($api_url, false, stream_context_create($arrContextOptions)), true);

            if ($decode['success'] == true ) {

                // C'est un humain
                if($this->sendEmail($form->getData())){

                    // Everything OK, redirect to wherever you want ! :

                    return $this->render('contact/contact.html.twig', array(
                        'form' => $form->createView(),
                        'alert' => 'Votre message a bien été envoyé. Veuillez consulter votre boîte SPAM, car il se pourrait que notre réponse soit considérée comme tel.'
                    ));
                }else{
                    // An error ocurred, handle
                    var_dump("Errooooor :(");
                }
            }



            else {

                var_dump('Robot détecté');

            }


        }

        return $this->render('contact/contact.html.twig', array(
            'form' => $form->createView()
        ));
    }

    private function sendEmail($data){
        $myappContactMail = 'contact@tout-terrain-chalossais.fr';
        $myappContactPassword = 'VxeADBHHJacS';

        // In this case we'll use the ZOHO mail services.
        // If your service is another, then read the following article to know which smpt code to use and which port
        // http://ourcodeworld.com/articles/read/14/swiftmailer-send-mails-from-php-easily-and-effortlessly
        $transport = (new Swift_SmtpTransport('host696235.onetsolutions.network', 465, 'ssl'))
            ->setUsername($myappContactMail)
            ->setPassword($myappContactPassword)
        ;

        $mailer = new Swift_Mailer($transport);

        $message = (new Swift_Message("Contact : ". $data["subject"]))
            ->setFrom(array($data["email"] => "Message de ".$data["name"]))
            ->setTo(array(
                $myappContactMail => $myappContactMail
            ))
            ->setBody(nl2br($data["message"]), 'text/html')
        ;

        return $mailer->send($message);
    }
}