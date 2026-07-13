<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\SpongeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Sponge;
use App\Form\SpongeType;
use App\Model\SearchData;
use App\Form\SearchType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('admin/sponge', name: 'admin.sponge.', methods: ['GET', 'POST'])]
#[IsGranted('ROLE_ADMIN')]
final class SpongeController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, SpongeRepository $spongeRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(type: SearchType::class, data: $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt(key: 'page', default: 1);
            $sponges = $spongeRepository->findBySearch($searchData, $searchData->page, limit: 2);
            return $this->render('/admin/sponge/index.html.twig', [
                'form' => $form,
                'sponges' => $sponges
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $sponges = $spongeRepository->paginateSponges($page);
        return $this->render('/admin/sponge/index.html.twig', [
            'form' => $form,
            'sponges' => $sponges,
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $sponge = new Sponge();
        $form = $this->createForm(SpongeType::class, $sponge);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($sponge);
            $em->flush();
            $this->addFlash('success', 'Eponge créé avec succès !');
            return $this->redirectToRoute('admin.sponge.index');
        }
        return $this->render('admin/sponge/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, SpongeRepository $spongeRepository): Response
    {
        $sponge = $spongeRepository->find($id);

        if (!$sponge) {
            throw $this->createNotFoundException('Sponge not found');
        }

        return $this->render('admin/sponge/show.html.twig', [
            'sponge' => $sponge
        ]);
    }

    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Sponge $sponge, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SpongeType::class, $sponge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();
            $this->addFlash('success', 'L\'eponge a été modifiée avec succès !');
            return $this->redirectToRoute('admin.sponge.index');
        }

        return $this->render('admin/sponge/edit.html.twig', [
            'sponge' => $sponge,
            'form' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function remove(Sponge $sponge, EntityManagerInterface $em): Response
    {
        $em->remove($sponge);
        $em->flush();
        $this->addFlash('success', 'L\'eponge a bien été supprimée');
        return $this->redirectToRoute('admin.sponge.index');
    }
}
