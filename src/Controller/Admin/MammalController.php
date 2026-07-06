<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\MammalRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Mammal;
use App\Form\MammalType;
use App\Model\SearchData;
use App\Form\SearchType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('admin/mammal', name: 'admin.mammal.', methods: ['GET', 'POST'])]
#[IsGranted('ROLE_ADMIN')]
final class MammalController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, MammalRepository $mammalRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(type: SearchType::class, data: $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt(key: 'page', default: 1);
            $mammals = $mammalRepository->findBySearch($searchData, $searchData->page, limit: 2);
            return $this->render('/admin/mammal/index.html.twig', [
                'form' => $form,
                'mammals' => $mammals
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $mammals = $mammalRepository->paginateMammals($page);
        return $this->render('/admin/mammal/index.html.twig', [
            'form' => $form,
            'mammals' => $mammals,
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $mammal = new Mammal();
        $form = $this->createForm(MammalType::class, $mammal);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($mammal);
            $em->flush();
            $this->addFlash('success', 'Mammifère créé avec succès !');
            return $this->redirectToRoute('admin.mammal.index');
        }
        return $this->render('admin/mammal/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, MammalRepository $mammalRepository): Response
    {
        $mammal = $mammalRepository->find($id);

        if (!$mammal) {
            throw $this->createNotFoundException('Mammal not found');
        }

        return $this->render('admin/mammal/show.html.twig', [
            'mammal' => $mammal
        ]);
    }

    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Mammal $mammal, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(MammalType::class, $mammal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();
            $this->addFlash('success', 'Le mammifère a été modifié avec succès !');
            return $this->redirectToRoute('admin.mammal.index');
        }

        return $this->render('admin/mammal/edit.html.twig', [
            'mammal' => $mammal,
            'form' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function remove(Mammal $mammal, EntityManagerInterface $em): Response
    {
        $em->remove($mammal);
        $em->flush();
        $this->addFlash('success', 'Le mammifère a bien été supprimé');
        return $this->redirectToRoute('admin.mammal.index');
    }
}
