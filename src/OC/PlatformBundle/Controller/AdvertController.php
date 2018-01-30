<?php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\AdvertSkill;

class AdvertController extends Controller
{
  public function indexAction($page)
    {

		if($page<1){
			throw new NotFoundHttpException('page "'.$page.'" iinexitante');
		}
    // Notre liste d'annonce en dur
    $listAdverts = array(
      array(
        'title'   => 'Recherche développpeur Symfony',
        'id'      => 1,
        'author'  => 'Alexandre',
        'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
        'date'    => new \Datetime()),
      array(
        'title'   => 'Mission de webmaster',
        'id'      => 2,
        'author'  => 'Hugo',
        'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
        'date'    => new \Datetime()),
      array(
        'title'   => 'Offre de stage webdesigner',
        'id'      => 3,
        'author'  => 'Mathieu',
        'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
        'date'    => new \Datetime())
    );
    $repository = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('OCPlatformBundle:Advert');
   $devs = $repository->getAdvertWithCategories(array('Intégration', 'Graphisme'))  ;
    // exit(var_dump($devs));

      return $this->render('OCPlatformBundle:Advert:index.html.twig',array('listAdverts'=>$listAdverts));
    }

	public function viewAction($id, Request $request){
    $em = $this->getDoctrine()->getManager();
    $advert =  $em->getRepository('OCPlatformBundle:Advert')->find($id);
    if(null ==  $advert){
      throw new NotFoundHttpException("L'annonce d id ".$id." n'exite pas.");
    }
    $listApplication = $em->getRepository('OCPlatformBundle:Application')->findBy(array(
            'advert'=>$advert,

          ));
    $listAdvertSkills = $em
               ->getRepository('OCPlatformBundle:AdvertSkill')
               ->findBy(array('advert' => $advert))
             ;

    /*$advert = array(
       'title'   => 'Recherche développpeur Symfony2',
       'id'      => $id,
       'author'  => 'Alexandre',
       'content' => 'Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…',
       'date'    => new \Datetime()
     );*/

	 return $this->render('OCPlatformBundle:Advert:view.html.twig',array('advert'=>$advert,  'listApplication' => $listApplication,'listAdvertSkills' => $listAdvertSkills));
	}

	public function addAction(Request $request){

    $antispam = $this->get('oc_platform.antispam');

    $text = "...";
    if($antispam->isSpam($text)){

      throw new  \Exception("Votre message a été détecter  comme spam !");

    }
    $em = $this->getDoctrine()->getManager();
    $advert = new Advert();
    $advert->setTitle('Recherche développeur Symfony.');
    $advert->setAuthor('Alexandre');
    $advert->setDate(new \DateTime());

    $advert->setContent("Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…");

    $listSkills = $em->getRepository('OCPlatformBundle:Skill')->findAll();
    foreach ($listSkills as $listSkill) {
        $advertSkills = new AdvertSkill();
        $advertSkills->setAdvert($advert);
        $advertSkills->setSkill($listSkill);
        $advertSkills->setLevel('Expert');
        $em->persist($advertSkills);
    }



    $image = new Image();
    $image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
    $image->setAlt('Job de rêve');

    $advert->setImage($image);

    $applicaition1 =  new Application();
    $applicaition1->setAuthor('Piere');
    $applicaition1->setContent("je suis trés motivé");
    $applicaition1->setAdvert($advert);

    $applicaition2 =  new Application();
    $applicaition2->setAuthor('Marine');
    $applicaition2->setContent("j'ai toutes les qualtés requises");
    $applicaition2->setAdvert($advert);




    $em->persist($advert);

    $em->persist($applicaition1);
    $em->persist($applicaition2);

    $em->flush();

	 if($request->isMethod('POST')){
	 	$request->getSession()->getFlashBag()->add('notice','Annonce bien enregitrée');
		return $this->redirectToRoute('oc_platform_view' ,array('id'=>5));
	 }
	 return $this->render('OCPlatformBundle:Advert:add.html.twig');
	}

	public function editAction($id, Request $request){

		if($request->isMethod('POST')){
	 	$request->getSession()->getFlashBag()->add('notice','Annonce bien modifier');
		return $this->redirectToRoute('oc_platform_view' ,array('id'=>$id));
	 }
   $em = $this->getDoctrine()->getManager();
   $advert =  $em->getRepository('OCPlatformBundle:Advert')->find($id);
   if(null ==  $advert){
     throw new NotFoundHttpException("L'annonce d id ".$id." n'exite pas.");
   }
   $listCategories = $em->getRepository('OCPlatformBundle:Category')->findAll();

   foreach($listCategories as $categorie){
     $advert->addCategorie($categorie);
   }

   $em->flush();

   return $this->render('OCPlatformBundle:Advert:edit.html.twig',array('advert'=>$advert));
	}

	 public function deleteAction($id){
     $em = $this->getDoctrine()->getManager();
    $advert =  $em->getRepository('OCPlatformBundle:Advert')->find($id);
    if(null ==  $advert){
      throw new NotFoundHttpException("L'annonce d id ".$id." n'exite pas.");
    }
    foreach ($advert->getCategorie() as $categorie) {
        $advert->removeCategorie($categorie);
    }
      //  $em->remove($advert);
      $em->flush();

	     return $this->render('OCPlatformBundle:Advert:delete.html.twig',array('id'=>$id));
	}

  public function menuAction($limit)
  {
    // On fixe en dur une liste ici, bien entendu par la suite
    // on la récupérera depuis la BDD !
    $listAdverts = array(
      array('id' => 2, 'title' => 'Recherche développeur Symfony'),
      array('id' => 5, 'title' => 'Mission de webmaster'),
      array('id' => 9, 'title' => 'Offre de stage webdesigner')
    );

    return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
      // Tout l'intérêt est ici : le contrôleur passe
      // les variables nécessaires au template !
      'listAdverts' => $listAdverts
    ));
  }
}
