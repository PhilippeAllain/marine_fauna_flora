<?php

namespace App\Controller\Admin;

use App\Repository\SeaRepository;
use App\Entity\Sea;
use App\Form\SeaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Model\SearchData;
use App\Form\SearchType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/sea', name: 'admin.sea.', methods: ['GET'])]
#[IsGranted('ROLE_ADMIN')]
final class SeaController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, SeaRepository $seaRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(type: SearchType::class, data: $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt(key: 'page', default: 1);
            $seas = $seaRepository->findBySearch($searchData, $searchData->page, limit: 2);
            return $this->render('admin/sea/index.html.twig', [
                'form' => $form,
                'seas' => $seas                 
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $seas = $seaRepository->paginateSeas($page);
        return $this->render('admin/sea/index.html.twig', [
            'form' => $form,
            'seas' => $seas,
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        // Logic to handle form submission and create a new sea entry

        $sea = new Sea();
        $form = $this->createForm(SeaType::class, $sea);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($sea);
            $em->flush();
            $this->addFlash('success', 'Terme du glossaire créé avec succès !');
            return $this->redirectToRoute('admin.sea.index');
        }
        return $this->render('admin/sea/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, SeaRepository $seaRepository): Response
    {
        $sea = $seaRepository->find($id);

        if (!$sea) {
            throw $this->createNotFoundException('Sea not found');
        }

        return $this->render('admin/sea/show.html.twig', [
            'sea' => $sea
        ]);
    }

    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Sea $sea, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SeaType::class, $sea);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $em->flush();

            $this->addFlash('success', 'Le terme du glossaire a été modifié avec succès !');
            return $this->redirectToRoute('admin.sea.index');
        }

        return $this->render('admin/sea/edit.html.twig', [
            'sea' => $sea,
            'form' => $form
        ]);
    }

        #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function remove(Sea $sea, EntityManagerInterface $em): Response
    {
        $em->remove($sea);
        $em->flush();
        $this->addFlash('success', 'Le terme du glossaire a bien été supprimé');
        return $this->redirectToRoute('admin.sea.index');
    }
}
