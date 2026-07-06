<?php

namespace App\Controller;

/* use App\Entity\User; */
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/')]
final class MainController extends AbstractController
{
    #[Route('', name: 'main.index')]
    public function index(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        /*$user = new User();
        $user->setEmail('john@doe.fr')
            ->setUsername('JohnDoe')
            ->setPassword($hasher->hashPassword($user, '0000'))
            ->setRoles(['ROLE_USER']);
        $em->persist($user);
        $em->flush();*/

        return $this->render('main/index.html.twig');
    }
}
