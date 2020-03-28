<?php


namespace App\Controller;


use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="rejestracja")
     */
    public function registerAction (Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $form = $this->createForm(RegistrationType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();

            $user->setPassword($passwordEncoder->encodePassword(
                $user,
                $user->getPassword()
            ));
            $user->setRoles(['USER_ROLE']);

            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Rejestracja zakończyła się sukcesem!'
            );
            return $this->redirect("/login");
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}