<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\TunicateRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tunicate;
use App\Form\TunicateType;
use App\Model\SearchData;
use App\Form\SearchType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('admin/tunicate', name: 'admin.tunicate.', methods: ['GET', 'POST'])]
#[IsGranted('ROLE_ADMIN')]
final class TunicateController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, TunicateRepository $tunicateRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(type: SearchType::class, data: $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt(key: 'page', default: 1);
            $tunicates = $tunicateRepository->findBySearch($searchData, $searchData->page, limit: 2);
            return $this->render('/admin/tunicate/index.html.twig', [
                'form' => $form,
                'tunicates' => $tunicates
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $tunicates = $tunicateRepository->paginateTunicates($page);
        return $this->render('/admin/tunicate/index.html.twig', [
            'form' => $form,
            'tunicates' => $tunicates,
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $tunicate = new Tunicate();
        $form = $this->createForm(TunicateType::class, $tunicate);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tunicate);
            $em->flush();
            $this->addFlash('success', 'Tunicate créé avec succès !');
            return $this->redirectToRoute('admin.tunicate.index');
        }
        return $this->render('admin/tunicate/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, TunicateRepository $tunicateRepository): Response
    {
        $tunicate = $tunicateRepository->find($id);

        if (!$tunicate) {
            throw $this->createNotFoundException('Tunicate not found');
        }

        return $this->render('admin/tunicate/show.html.twig', [
            'tunicate' => $tunicate
        ]);
    }

    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Tunicate $tunicate, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TunicateType::class, $tunicate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();
            $this->addFlash('success', 'Le tunicate a été modifié avec succès !');
            return $this->redirectToRoute('admin.tunicate.index');
        }

        return $this->render('admin/tunicate/edit.html.twig', [
            'tunicate' => $tunicate,
            'form' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function remove(Tunicate $tunicate, EntityManagerInterface $em): Response
    {
        $em->remove($tunicate);
        $em->flush();
        $this->addFlash('success', 'Le tunicate a bien été supprimé');
        return $this->redirectToRoute('admin.tunicate.index');
    }
}
