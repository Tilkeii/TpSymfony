<?php

// src/OC/PlatformBundle/Controller/AdvertController.php

namespace CarnetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections;
use Symfony\Component\Validator\Constraints\Collection;
use UserBundle\Entity\User;
use UserBundle\Repository\ContactsRepository;

class AdvertController extends Controller
{
    public function indexAction()
    {
        return $this->render('CarnetBundle:Advert:index.html.twig', array(

        ));
    }

    public function mescontactsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $currentUserId = $this->getUser()->getId();
        $user = $em->getRepository('UserBundle:User')->find($currentUserId);
        $myContacts = $user->getMyContacts();

        return $this->render('CarnetBundle:Advert:mescontacts.html.twig', array(
            'contacts' => $myContacts
        ));
    }

    public function listerAction()
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('UserBundle:User')->findAll(); //Tous les users

        $currentUserId = $this->getUser()->getId();
        $user = $em->getRepository('UserBundle:User')->find($currentUserId);
        $myContacts = $user->getMyContacts(); //Tous les contacts du user courrant

        $listafficher = array();
        foreach($users as $user){
            if(!($myContacts->contains($user))){
                $listafficher[] = $user;
            }
        }

        return $this->render('CarnetBundle:Advert:list.html.twig', array(
            'users' => $listafficher
        ));
    }
    public function ajouterAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $currentUserId = $this->getUser()->getId();
        $user = $em->getRepository('UserBundle:User')->find($currentUserId);
        $newcontact = $em->getRepository('UserBundle:User')->find($id);
        $user->addMyContact($newcontact);

        $em->flush();

        return $this->redirectToRoute('carnet_list');
    }

    public function supprimerAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $currentUserId = $this->getUser()->getId();
        $user = $em->getRepository('UserBundle:User')->find($currentUserId);
        $oldcontact = $em->getRepository('UserBundle:User')->find($id);
        $user->removeMyContact($oldcontact);

        $em->flush();
        return $this->redirectToRoute('carnet_mescontacts');
    }

	public function profilAction(Request $request) // afficher ses info
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')
            ->find($this->getUser()->getId());

        $formBuilder = $this->get('form.factory')->createBuilder('form',$user);

        $formBuilder
            ->add('username','text',array('label'=>'Nom d\'utilisateur'))
            ->add('email','text',array('label'=>'Email'))
            ->add('nom','text',array('label'=>'Nom'))
            ->add('adresse','text',array('label'=>'Adresse'))
            ->add('telephone','number',array('label'=>'telephone'))
            ->add('siteweb','text',array('label'=>'Site web'))
            ->add('save','submit',array('label'=>'Valider'));
        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if($form->isValid()){
            $em->persist($user);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Profil modifié');

            return $this->redirectToRoute('carnet_profil');
        }

        return $this->render('CarnetBundle:Advert:affiche.html.twig', array(
            'form' => $form->createView()
        ));
    }

}

?>