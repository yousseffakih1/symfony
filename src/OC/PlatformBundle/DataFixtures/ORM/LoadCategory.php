<?php
namespace OC\PlatformBundle\DataFixtures\ORM\LoadCategory;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Category;


/**
 *
 */
class LoadCategory implements FixtureInterface
{
  function load(ObjectManager $manager){
    $names  = array(
      'Développement web',
      'Développement mobile',
      'Graphisme',
      'Intégration',
      'Réseau'
    );

    foreach ($names as $name) {
      $categorie = new Category();
      $categorie->setName($name);

      $manager->persist($categorie);
    }
    $manager->flush();
  }
}



 ?>
