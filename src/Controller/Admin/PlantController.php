<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\PlantRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Plant;
use App\Form\PlantType;
use App\Model\SearchData;
use App\Form\SearchType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('admin/plant', name: 'admin.plant.', methods: ['GET', 'POST'])]
#[IsGranted('ROLE_ADMIN')]
final class PlantController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, PlantRepository $plantRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(type: SearchType::class, data: $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt(key: 'page', default: 1);
            $plants = $plantRepository->findBySearch($searchData, $searchData->page, limit: 2);
            return $this->render('/admin/plant/index.html.twig', [
                'form' => $form,
                'plants' => $plants
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $plants = $plantRepository->paginatePlants($page);
        return $this->render('/admin/plant/index.html.twig', [
            'form' => $form,
            'plants' => $plants,
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $plant = new Plant();
        $form = $this->createForm(PlantType::class, $plant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($plant);
            $em->flush();
            $this->addFlash('success', 'Plante créée avec succès !');
            return $this->redirectToRoute('admin.plant.index');
        }
        return $this->render('admin/plant/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, PlantRepository $plantRepository): Response
    {
        $plant = $plantRepository->find($id);

        if (!$plant) {
            throw $this->createNotFoundException('Plant not found');
        }

        return $this->render('admin/plant/show.html.twig', [
            'plant' => $plant
        ]);
    }

    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Plant $plant, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PlantType::class, $plant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();
            $this->addFlash('success', 'La plante a été modifiée avec succès !');
            return $this->redirectToRoute('admin.plant.index');
        }

        return $this->render('admin/plant/edit.html.twig', [
            'plant' => $plant,
            'form' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function remove(Plant $plant, EntityManagerInterface $em): Response
    {
        $em->remove($plant);
        $em->flush();
        $this->addFlash('success', 'La plante a bien été supprimée');
        return $this->redirectToRoute('admin.plant.index');
    }
}
