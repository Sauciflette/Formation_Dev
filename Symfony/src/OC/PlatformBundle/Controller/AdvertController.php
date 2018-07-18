<?php
namespace OC\PlatformBundle\Controller;

use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\Skill;
use OC\PlatformBundle\Entity\AdvertSkill;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdvertController extends Controller
{
public function indexAction($page)
{
$page = ($page == "") ? 1 : $page;
// Mais on sait qu'une page doit être supérieure ou égale à 1
if ($page < 1) {
  // On déclenche une exception NotFoundHttpException, cela va afficher
  // une page d'erreur 404 (qu'on pourra personnaliser plus tard d'ailleurs)
throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
}

// Ici, on récupérera la liste des annonces, puis on la passera au template
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

$advert = new Advert;
$advert -> setContent("Recherche développeur Symfony 3");

return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
  'advert' => $advert
));
}

public function viewAction($id)
{
    $em = $this->getDoctrine()->getManager();

    $advert = $em
        ->getRepository('OCPlatformBundle:Advert')
        ->find($id);

    if (null === $advert) {
      throw new NotFoundHttpException("l'annonce d'id".$id."n'existe pas.");
    }

    $listApplications = $em
        ->getRepository('OCPlatformBundle:Application')
        ->findBy(array('advert' => $advert));

    $listAdvertSkills = $em
    ->getRepository('OCPlatformBundle:AdvertSkill')
    ->findBy(array('advert'=>$advert));

    return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
        'advert' => $advert,
        'listApplications' => $listApplications,
        'listAdvertSkills' => $listAdvertSkills
));
}

public function addAction(Request $request)
{
    // Création de l'entité Advert
    $advert = new Advert();
    $advert->setTitle('Recherche développeur Symfony.');
    $advert->setAuthor('Alexandre');
    $advert->setContent("Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…");
    // On peut ne pas définir ni la date ni la publication, car ces attributs sont définis automatiquement dans le constructeur

    //création d'Application
    $application1 = new Application();
    $application1->setAuthor('Marine');
    $application1->setContent("J'ai besoin du poste");

    //création d'Application
    $application2 = new Application();
    $application2->setAuthor('Jean');
    $application2->setContent("Je suce pour le poste");

    // Création de l'entité ImageRepository
    $image = new Image();
    $image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
    $image->setAlt('job de rêve');

    // récupération des skills
    $em = $this->getDoctrine()->getManager();
    $listSkills = $em -> getRepository('OCPlatformBundle:Skill')->findAll();

    foreach($listSkills as $skill){
        $advertSkill = new AdvertSkill();
        $advertSkill->setAdvert($advert);
        $advertSkill->setSkill($skill);
        $advertSkill->setLevel('Expert');

        $em->persist($advertSkill);
    }

    $advert->setImage($image);
    $application1->setAdvert($advert);
    $application2->setAdvert($advert);

    $em = $this -> getDoctrine()->getManager();
    $em->persist($advert);
    $em->persist($application1);
    $em->persist($application2);

    $em->flush();

    // Si la requête est en POST, c'est que le visiteur a soumis le formulaire
    if ($request->isMethod('POST')) {
      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
      return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
    }

    // Si on n'est pas en POST, alors on affiche le formulaire
    return $this->render('OCPlatformBundle:Advert:add.html.twig',array('advert'=> $advert));
}

public function editAction($id, Request $request)
{
    $em = $this->getDoctrine()->getManager();

    $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

    if (null === $advert){
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    $listCategories = $em->getRepository('OCPlatformBundle:Category')->findAll();

    // On boucle sur les catégories pour les lier à l'annonce
    foreach ($listCategories as $category) {
      $advert->addCategory($category);
    }

    // Pour persister le changement dans la relation, il faut persister l'entité propriétaire
    // Ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine

    // Étape 2 : On déclenche l'enregistrement
    $em->flush();

    if ($request->isMethod('POST')) {
    $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');
    }

      $advert = array(
       'title'   => 'Recherche développpeur Symfony',
       'id'      => $id,
       'author'  => 'Alexandre',
       'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
       'date'    => new \Datetime()
     );

     return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(
       'advert' => $advert
     ));

    return $this->render('OCPlatformBundle:Advert:edit.html.twig');
    }

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        if (null === $advert){
          throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        foreach ($advert->getCategories() as $category){
            $advert->removeCategory($category);
        }

        $em->flush();

        return $this->render('OCPlatformBundle:Advert:delete.html.twig');
    }

    public function menuAction()
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

?>
