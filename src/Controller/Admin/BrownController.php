<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\BrownRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Brown;
use App\Form\BrownType;
use App\Model\SearchData;
use App\Form\SearchType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('admin/brown', name: 'admin.brown.', methods: ['GET', 'POST'])]
#[IsGranted('ROLE_ADMIN')]
final class BrownController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, BrownRepository $brownRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(type: SearchType::class, data: $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt(key: 'page', default: 1);
            $browns = $brownRepository->findBySearch($searchData, $searchData->page, limit: 2);
            return $this->render('/admin/brown/index.html.twig', [
                'form' => $form,
                'browns' => $browns
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $browns = $brownRepository->paginateBrowns($page);
        return $this->render('/admin/brown/index.html.twig', [
            'form' => $form,
            'browns' => $browns,
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $brown = new Brown();
        $form = $this->createForm(BrownType::class, $brown);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($brown);
            $em->flush();
            $this->addFlash('success', 'Plante créée avec succès !');
            return $this->redirectToRoute('admin.brown.index');
        }
        return $this->render('admin/brown/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, BrownRepository $brownRepository): Response
    {
        $brown = $brownRepository->find($id);

        if (!$brown) {
            throw $this->createNotFoundException('Plant not found');
        }

        return $this->render('admin/brown/show.html.twig', [
            'brown' => $brown
        ]);
    }

    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Brown $brown, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(BrownType::class, $brown);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();
            $this->addFlash('success', 'La plante a été modifiée avec succès !');
            return $this->redirectToRoute('admin.brown.index');
        }

        return $this->render('admin/brown/edit.html.twig', [
            'brown' => $brown,
            'form' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function remove(Brown $brown, EntityManagerInterface $em): Response
    {
        $em->remove($brown);
        $em->flush();
        $this->addFlash('success', 'La plante a bien été supprimée');
        return $this->redirectToRoute('admin.brown.index');
    }
}
