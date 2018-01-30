<?php
namespace OC\PlatformBundle\Email;

use OC\PlatformBundle\Entity\Application;
/**
 *
 */
class ApplicationMailer
{
  /**
  * @var \Swift_Mailer
  **/
  private $mailer;


  public function __construct(\Swift_Mailer $mailer)
  {
    $this->mailer = $mailer;
  }
  public function sendNewNotification(Application $application){
    $message = new  \Swift_Mailer(
        'nouvelle candidature',
        'Vous aveze reçu une nouvelle candidature '
      );
    $message
        ->addTo($application->getAdvert()->getAuthor())
        ->addFrom('youssef.fakih@gmail.com');

    $this->$mailer->send($message);

  }


}





 ?>
