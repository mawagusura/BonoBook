<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class BonoboController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

            $list = $this->getUser()
                ->getMyFriends()
            ;
            return $this->render('bonoboViews/listBonobo.html.twig', array(
                'list' => $list
                ));
        }
        else {
            return $this->render('bonoboViews/index.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            ]);
        }
    }

    /**
     * @Route("/all", name="all")
     */
    public function allAction()
    {
        $all = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findAll()
        ;

        return $this->render('bonoboViews/allBonobos.html.twig', array(
            'allB' => $all
            ));
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function detailsAction($id)
    {
        $user = $this->getDoctrine()
                ->getRepository('AppBundle:User')
                ->find($id)
            ;

        if ($user == $this->getUser()){
            return $this->redirectToRoute('fos_user_profile_show');
        }
        else {
            return $this->render('bonoboViews/details.html.twig', array(
                'user' =>$user
                ));
        }
    }

    /**
     * @Route("/add/{id}", name="add")
     */
    public function addAction($id)
    {

        $user = $this->getDoctrine()
                ->getRepository('AppBundle:User')
                ->find($id)
            ;

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED') and $this->getUser() != $user) {

                $this->getUser()->addMyFriend($user);
                $this->getDoctrine()->getManager()->flush();

                return $this->render('bonoboViews/addBonobo.html.twig', array(
                'friend' => $user
                ));

        }
        throw new AccessDeniedException('This user does not have access to this section.');

    }

    /**
     * @Route("/remove/{id}", name="remove")
     */
    public function removeAction($id)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->getDoctrine()
                ->getRepository('AppBundle:User')
                ->find($id)
            ;

            $this->getUser()->removeMyFriend($user);
            $this->getDoctrine()->getManager()->flush();

            return $this->render('bonoboViews/removeBonobo.html.twig', array(
            'friend' => $user
            ));
        }
        throw new AccessDeniedException('This user does not have access to this section.');
    }


}
