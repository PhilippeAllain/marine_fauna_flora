<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\RedRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Red;
use App\Form\RedType;
use App\Model\SearchData;
use App\Form\SearchType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('admin/red', name: 'admin.red.', methods: ['GET', 'POST'])]
#[IsGranted('ROLE_ADMIN')]
final class RedController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, RedRepository $redRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(type: SearchType::class, data: $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt(key: 'page', default: 1);
            $reds = $redRepository->findBySearch($searchData, $searchData->page, limit: 2);
            return $this->render('/admin/red/index.html.twig', [
                'form' => $form,
                'reds' => $reds
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $reds = $redRepository->paginateReds($page);
        return $this->render('/admin/red/index.html.twig', [
            'form' => $form,
            'reds' => $reds,
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $red = new Red();
        $form = $this->createForm(RedType::class, $red);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($red);
            $em->flush();
            $this->addFlash('success', 'L\'algue rouge créée avec succès !');
            return $this->redirectToRoute('admin.red.index');
        }
        return $this->render('admin/red/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, RedRepository $redRepository): Response
    {
        $red = $redRepository->find($id);

        if (!$red) {
            throw $this->createNotFoundException('l\'Algue rouge non trouvée');
        }

        return $this->render('admin/red/show.html.twig', [
            'red' => $red
        ]);
    }

    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Red $red, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(RedType::class, $red);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();
            $this->addFlash('success', 'L\'algue rouge a été modifiée avec succès !');
            return $this->redirectToRoute('admin.red.index');
        }

        return $this->render('admin/red/edit.html.twig', [
            'red' => $red,
            'form' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function remove(Red $red, EntityManagerInterface $em): Response
    {
        $em->remove($red);
        $em->flush();
        $this->addFlash('success', 'L\'algue rouge a bien été supprimée');
        return $this->redirectToRoute('admin.red.index');
    }
}
