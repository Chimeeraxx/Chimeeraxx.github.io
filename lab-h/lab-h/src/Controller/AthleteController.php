<?php

namespace App\Controller;

use App\Entity\Athlete;
use App\Form\AthleteType;
use App\Repository\AthleteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/athlete')]
final class AthleteController extends AbstractController
{
    #[Route(name: 'app_athlete_index', methods: ['GET'])]
    public function index(AthleteRepository $athleteRepository): Response
    {
        return $this->render('athlete/index.html.twig', [
            'athletes' => $athleteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_athlete_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $athlete = new Athlete();
        $form = $this->createForm(AthleteType::class, $athlete);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($athlete);
            $entityManager->flush();

            return $this->redirectToRoute('app_athlete_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('athlete/new.html.twig', [
            'athlete' => $athlete,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_athlete_show', methods: ['GET'])]
    public function show(Athlete $athlete): Response
    {
        return $this->render('athlete/show.html.twig', [
            'athlete' => $athlete,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_athlete_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Athlete $athlete, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AthleteType::class, $athlete);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_athlete_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('athlete/edit.html.twig', [
            'athlete' => $athlete,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_athlete_delete', methods: ['POST'])]
    public function delete(Request $request, Athlete $athlete, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$athlete->getId(), $request->request->get('_token'))) {
            $entityManager->remove($athlete);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_athlete_index', [], Response::HTTP_SEE_OTHER);
    }
}
