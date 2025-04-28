<?php

namespace App\Controller\Partenaire;

use App\Entity\DemandesAide;
use App\Repository\DemandesAideRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DmAidePartnerController extends AbstractController
{
    #[Route('/dashboard/partenaire/demande_aide/{id_partenaire}', name: 'demande_aide')]
    public function demandeAide(
        int $id_partenaire,
        DemandesAideRepository $repository
    ): Response {
        // Forcer l'ID partenaire à 1 si nécessaire
        $id_partenaire = 1;

        $demandesAide = $repository->findByPartenaire($id_partenaire);

        return $this->render('Partenaire/dm_aide_partner/demande_aide.html.twig', [
            'demandes_aide' => $demandesAide,
            'id_partenaire' => $id_partenaire,
        ]);
    }

    #[Route('/dashboard/partenaire/demande_aide_add/{id_partenaire}', name: 'add_demande', methods: ['POST'])]
    public function addDemande(
        int $id_partenaire,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $data = $request->request->all();
        $file = $request->files->get('preuve');

        $demande = new DemandesAide();
        $demande->setIdClient($data['id_client']);
        $demande->setTypeAide($data['type_aide']);
        $demande->setDescription($data['description'] ?? '');
        $demande->setMontantDemande($data['montant_demande'] ?? 0);
        $demande->setDateDemande(new \DateTime($data['date_demande'] ?? 'now'));
        $demande->setStatus('en_attente');

        if ($file) {
            $uploadsDir = $this->getParameter('uploads_directory');
            $fileName = uniqid() . '.' . $file->guessExtension();
            $file->move($uploadsDir, $fileName);
            $demande->setPreuves($fileName);
        }

        $entityManager->persist($demande);
        $entityManager->flush();

        $this->addFlash('success', 'Demande ajoutée avec succès!');
        return $this->redirectToRoute('demande_aide', ['id_partenaire' => $id_partenaire]);
    }
}