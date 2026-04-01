<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\Adresse;
use App\Entity\Commande;
use App\Form\AdresseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdresseController extends AbstractController
{
    #[Route('/adresse', name: 'app_adresse')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        /** @var Utilisateur $user */
        $user = $this->getUser();

        return $this->render('adresse/index.html.twig', [
            'adresses' => $user->getAdresses(),
        ]);
    }

    #[Route('/adresse/new', name: 'app_adresse_new')]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $adresse = new Adresse();

        $form = $this->createForm(AdresseType::class, $adresse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $adresse->setUtilisateur($this->getUser());

            $em->persist($adresse);
            $em->flush();

            return $this->redirectToRoute('app_adresse');
        }

        return $this->render('adresse/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/adresse/{id}/edit', name: 'app_adresse_edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(Adresse $adresse, Request $request, EntityManagerInterface $em): Response
    {
        if ($adresse->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(AdresseType::class, $adresse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('app_adresse');
        }

        return $this->render('adresse/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/adresse/{id}/delete', name: 'app_adresse_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Adresse $adresse, EntityManagerInterface $em): Response
    {
        if ($adresse->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete' . $adresse->getId(), $request->request->get('_token'))) {

            // 🔥 CHECK SI UTILISÉE DANS UNE COMMANDE
            $nbCommandes = $em->getRepository(Commande::class)
                ->count(['adresse' => $adresse]);

            if ($nbCommandes > 0) {
                $this->addFlash('error', 'Impossible de supprimer une adresse utilisée dans une commande.');
                return $this->redirectToRoute('app_adresse');
            }

            $em->remove($adresse);
            $em->flush();
        }

        return $this->redirectToRoute('app_adresse');
    }
}
