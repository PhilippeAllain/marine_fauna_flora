<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\ReptileRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Reptile;
use App\Form\ReptileType;
use App\Model\SearchData;
use App\Form\SearchType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('admin/reptile', name: 'admin.reptile.', methods: ['GET', 'POST'])]
#[IsGranted('ROLE_ADMIN')]
final class ReptileController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, ReptileRepository $reptileRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(type: SearchType::class, data: $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt(key: 'page', default: 1);
            $reptiles = $reptileRepository->findBySearch($searchData, $searchData->page, limit: 2);
            return $this->render('admin/reptile /index.html.twig', [
                'form' => $form,
                'reptiles' => $reptiles
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $reptiles = $reptileRepository->paginateReptiles($page);
        return $this->render('admin/reptile/index.html.twig', [
            'form' => $form,
            'reptiles' => $reptiles,
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {

        $reptile = new Reptile();
        $form = $this->createForm(ReptileType::class, $reptile);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($reptile);
            $em->flush();
            $this->addFlash('success', 'Reptile créé avec succès !');
            return $this->redirectToRoute('admin.reptile.index');
        }
        return $this->render('admin/reptile/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, ReptileRepository $reptileRepository): Response
    {
        $reptile = $reptileRepository->find($id);

        if (!$reptile) {
            throw $this->createNotFoundException('Reptile not found');
        }

        return $this->render('admin/reptile/show.html.twig', [
            'reptile' => $reptile
        ]);
    }

    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Reptile $reptile, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ReptileType::class, $reptile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();

            $this->addFlash('success', 'Le reptile a été modifié avec succès !');
            return $this->redirectToRoute('admin.reptile.index');
        }

        return $this->render('admin/reptile/edit.html.twig', [
            'reptile' => $reptile,
            'form' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function remove(Reptile $reptile, EntityManagerInterface $em): Response
    {
        $em->remove($reptile);
        $em->flush();
        $this->addFlash('success', 'Le reptile a bien été supprimé');
        return $this->redirectToRoute('admin.reptile.index');
    }
}
