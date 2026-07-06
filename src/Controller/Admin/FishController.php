<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\FishRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Fish;
use App\Form\FishType;
use App\Model\SearchData;
use App\Form\SearchType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('admin/fish', name: 'admin.fish.', methods: ['GET', 'POST'])]
#[IsGranted('ROLE_ADMIN')]
final class FishController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, FishRepository $fishRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(type: SearchType::class, data: $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt(key: 'page', default: 1);
            $fishes = $fishRepository->findBySearch($searchData, $searchData->page, limit: 2);
            return $this->render('admin/fish/index.html.twig', [
                'form' => $form,
                'fishes' => $fishes
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $fishes = $fishRepository->paginateFishes($page);
        return $this->render('admin/fish/index.html.twig', [
            'form' => $form,
            'fishes' => $fishes,
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {

        $fish = new Fish();
        $form = $this->createForm(FishType::class, $fish);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($fish);
            $em->flush();
            $this->addFlash('success', 'Poisson créé avec succès !');
            return $this->redirectToRoute('admin.fish.index');
        }
        return $this->render('admin/fish/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, FishRepository $fishRepository): Response
    {
        $fish = $fishRepository->find($id);

        if (!$fish) {
            throw $this->createNotFoundException('Fish not found');
        }

        return $this->render('admin/fish/show.html.twig', [
            'fish' => $fish
        ]);
    }

    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Fish $fish, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(FishType::class, $fish);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();

            $this->addFlash('success', 'Le poisson a été modifié avec succès !');
            return $this->redirectToRoute('admin.fish.index');
        }

        return $this->render('admin/fish/edit.html.twig', [
            'fish' => $fish,
            'form' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function remove(Fish $fish, EntityManagerInterface $em): Response
    {
        $em->remove($fish);
        $em->flush();
        $this->addFlash('success', 'Le poisson a bien été supprimé');
        return $this->redirectToRoute('admin.fish.index');
    }
}
