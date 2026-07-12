<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\PolychaeteRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Polychaete;
use App\Form\PolychaeteType;
use App\Model\SearchData;
use App\Form\SearchType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('admin/polychaete', name: 'admin.polychaete.', methods: ['GET', 'POST'])]
#[IsGranted('ROLE_ADMIN')]
final class PolychaeteController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, PolychaeteRepository $polychaeteRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(type: SearchType::class, data: $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt(key: 'page', default: 1);
            $polychaetes = $polychaeteRepository->findBySearch($searchData, $searchData->page, limit: 2);
            return $this->render('/admin/polychaete/index.html.twig', [
                'form' => $form,
                'polychaetes' => $polychaetes
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $polychaetes = $polychaeteRepository->paginatePolychaetes($page);
        return $this->render('/admin/polychaete/index.html.twig', [
            'form' => $form,
            'polychaetes' => $polychaetes,
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $polychaete = new Polychaete();
        $form = $this->createForm(PolychaeteType::class, $polychaete);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($polychaete);
            $em->flush();
            $this->addFlash('success', 'Polychète créé avec succès !');
            return $this->redirectToRoute('admin.polychaete.index');
        }
        return $this->render('admin/polychaete/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, PolychaeteRepository $polychaeteRepository): Response
    {
        $polychaete = $polychaeteRepository->find($id);

        if (!$polychaete) {
            throw $this->createNotFoundException('Polychète not found');
        }

        return $this->render('admin/polychaete/show.html.twig', [
            'polychaete' => $polychaete
        ]);
    }

    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Polychaete $polychaete, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PolychaeteType::class, $polychaete);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();
            $this->addFlash('success', 'Le polychète a été modifié avec succès !');
            return $this->redirectToRoute('admin.polychaete.index');
        }

        return $this->render('admin/polychaete/edit.html.twig', [
            'polychaete' => $polychaete,
            'form' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function remove(Polychaete $polychaete, EntityManagerInterface $em): Response
    {
        $em->remove($polychaete);
        $em->flush();
        $this->addFlash('success', 'Le polychète a bien été supprimé');
        return $this->redirectToRoute('admin.polychaete.index');
    }
}
