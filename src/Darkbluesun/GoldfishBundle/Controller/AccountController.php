<?php
namespace Darkbluesun\GoldfishBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

use Symfony\Component\EventDispatcher\EventDispatcher,
    Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken,
    Symfony\Component\Security\Http\Event\InteractiveLoginEvent,
    Symfony\Component\Security\Core\User\UserInterface;

use Darkbluesun\GoldfishBundle\Form\Type\RegistrationType;
use Darkbluesun\GoldfishBundle\Form\Model\Registration;
use Darkbluesun\GoldfishBundle\Entity\Workspace;

class AccountController extends Controller
{
    public function registerAction()
    {
        $registration = new Registration();
        $form = $this->createForm(new RegistrationType(), $registration, array(
            'action' => $this->generateUrl('account_create'),
        ));

        return $this->render(
            'DarkbluesunGoldfishBundle:Default:register.html.twig',
            array('form' => $form->createView())
        );
    }

    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new RegistrationType(), new Registration());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $registration = $form->getData();
            $user = $registration->getUser();
            $user->addRole($em->getRepository('DarkbluesunGoldfishBundle:Role')->findOneByRole('ROLE_USER'));
            $em->persist($user);

            // Create a new private workspace for the new user
            $workspace = new Workspace();
            $workspace->setName($user->getEmail()."'s Workspace");
            $workspace->addUser($user);
            $workspace->setPrivate(true);
            $em->persist($workspace);

            $em->flush();

            $this->authenticateUser($user);

            return $this->redirect($this->generateUrl('goldfish_homepage'));
        }

        return $this->render(
            'DarkbluesunGoldfishBundle:Default:register.html.twig',
            array('form' => $form->createView())
        );      
    }

    private function authenticateUser(UserInterface $user)
    {
        $providerKey = 'secured_area'; // your firewall name
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());
        $this->get('security.context')->setToken($token);
        $this->get('session')->set('_security_main',serialize($token));
    }  

    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContextInterface::AUTHENTICATION_ERROR
            );
        } elseif (null !== $session && $session->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
            $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContextInterface::LAST_USERNAME);

        return $this->render(
            'GraceWebBundle:Account:login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $lastUsername,
                'error'         => $error,
            )
        );
    }
}