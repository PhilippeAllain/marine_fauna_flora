<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\BirdRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Bird;
use App\Form\BirdType;
use App\Model\SearchData;
use App\Form\SearchType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('admin/bird', name: 'admin.bird.', methods: ['GET', 'POST'])]
#[IsGranted('ROLE_ADMIN')]
final class BirdController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, BirdRepository $birdRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(type: SearchType::class, data: $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt(key: 'page', default: 1);
            $birds = $birdRepository->findBySearch($searchData, $searchData->page, limit: 2);
            return $this->render('/admin/bird/index.html.twig', [
                'form' => $form,
                'birds' => $birds
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $birds = $birdRepository->paginateBirds($page);
        return $this->render('/admin/bird/index.html.twig', [
            'form' => $form,
            'birds' => $birds,
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $bird = new Bird();
        $form = $this->createForm(BirdType::class, $bird);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($bird);
            $em->flush();
            $this->addFlash('success', 'Oiseau créé avec succès !');
            return $this->redirectToRoute('admin.bird.index');
        }
        return $this->render('admin/bird/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, BirdRepository $birdRepository): Response
    {
        $bird = $birdRepository->find($id);

        if (!$bird) {
            throw $this->createNotFoundException('Bird not found');
        }

        return $this->render('admin/bird/show.html.twig', [
            'bird' => $bird
        ]);
    }

    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Bird $bird, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(BirdType::class, $bird);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();
            $this->addFlash('success', 'L\'oiseau a été modifié avec succès !');
            return $this->redirectToRoute('admin.bird.index');
        }

        return $this->render('admin/bird/edit.html.twig', [
            'bird' => $bird,
            'form' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function remove(Bird $bird, EntityManagerInterface $em): Response
    {
        $em->remove($bird);
        $em->flush();
        $this->addFlash('success', 'L\'oiseau a bien été supprimé');
        return $this->redirectToRoute('admin.bird.index');
    }
}
