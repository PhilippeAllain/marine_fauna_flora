<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\GreenRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Green;
use App\Form\GreenType;
use App\Model\SearchData;
use App\Form\SearchType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('admin/green', name: 'admin.green.', methods: ['GET', 'POST'])]
#[IsGranted('ROLE_ADMIN')]
final class GreenController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, GreenRepository $greenRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(type: SearchType::class, data: $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt(key: 'page', default: 1);
            $greens = $greenRepository->findBySearch($searchData, $searchData->page, limit: 2);
            return $this->render('/admin/green/index.html.twig', [
                'form' => $form,
                'greens' => $greens
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $greens = $greenRepository->paginateGreens($page);
        return $this->render('/admin/green/index.html.twig', [
            'form' => $form,
            'greens' => $greens,
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $green = new Green();
        $form = $this->createForm(GreenType::class, $green);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($green);
            $em->flush();
            $this->addFlash('success', 'Plante créée avec succès !');
            return $this->redirectToRoute('admin.green.index');
        }
        return $this->render('admin/green/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, GreenRepository $greenRepository): Response
    {
        $green = $greenRepository->find($id);

        if (!$green) {
            throw $this->createNotFoundException('Plant not found');
        }

        return $this->render('admin/green/show.html.twig', [
            'green' => $green
        ]);
    }

    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Green $green, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(GreenType::class, $green);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();
            $this->addFlash('success', 'La plante a été modifiée avec succès !');
            return $this->redirectToRoute('admin.green.index');
        }

        return $this->render('admin/green/edit.html.twig', [
            'green' => $green,
            'form' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function remove(Green $green, EntityManagerInterface $em): Response
    {
        $em->remove($green);
        $em->flush();
        $this->addFlash('success', 'La plante a bien été supprimée');
        return $this->redirectToRoute('admin.green.index');
    }
}
