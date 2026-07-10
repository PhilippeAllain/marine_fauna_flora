<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\EchinodermRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Echinoderm;
use App\Form\EchinodermType;
use App\Model\SearchData;
use App\Form\SearchType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('admin/echinoderm', name: 'admin.echinoderm.', methods: ['GET', 'POST'])]
#[IsGranted('ROLE_ADMIN')]
final class EchinodermController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, EchinodermRepository $echinodermRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(type: SearchType::class, data: $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt(key: 'page', default: 1);
            $echinoderms = $echinodermRepository->findBySearch($searchData, $searchData->page, limit: 2);
            return $this->render('/admin/echinoderm   /index.html.twig', [
                'form' => $form,
                'echinoderms' => $echinoderms
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $echinoderms = $echinodermRepository->paginateEchinoderms($page);
        return $this->render('/admin/echinoderm/index.html.twig', [
            'form' => $form,
            'echinoderms' => $echinoderms,
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $echinoderm = new Echinoderm();
        $form = $this->createForm(EchinodermType::class, $echinoderm);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($echinoderm);
            $em->flush();
            $this->addFlash('success', 'L\'echinoderme créé avec succès !');
            return $this->redirectToRoute('admin.echinoderm.index');
        }
        return $this->render('admin/echinoderm/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, EchinodermRepository $echinodermRepository): Response
    {
        $echinoderm = $echinodermRepository->find($id);

        if (!$echinoderm) {
            throw $this->createNotFoundException('Echinoderm not found');
        }

        return $this->render('admin/echinoderm/show.html.twig', [
            'echinoderm' => $echinoderm
        ]);
    }

    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Echinoderm $echinoderm, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(EchinodermType::class, $echinoderm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();
            $this->addFlash('success', 'Le echinoderme a été modifié avec succès !');
            return $this->redirectToRoute('admin.echinoderm.index');
        }

        return $this->render('admin/echinoderm/edit.html.twig', [
            'echinoderm' => $echinoderm,
            'form' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function remove(Echinoderm $echinoderm, EntityManagerInterface $em): Response
    {
        $em->remove($echinoderm);
        $em->flush();
        $this->addFlash('success', 'Le echinoderme a bien été supprimé');
        return $this->redirectToRoute('admin.echinoderm.index');
    }
}
