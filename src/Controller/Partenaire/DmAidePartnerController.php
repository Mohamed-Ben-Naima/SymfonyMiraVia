<?php

namespace App\Controller\Partenaire;

use App\Entity\DemandesAide;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DmAidePartnerController extends AbstractController
{
    #[Route('/dashboard/partenaire/demande_aide/{id_partenaire}', name: 'demande_aide')]
    public function addDemande(Request $request, EntityManagerInterface $entityManager): Response
{
    $data = $request->request->all();
    $file = $request->files->get('preuve');

    // Check if 'id_client' exists in the request data
    if (!isset($data['id_client'])) {
        $this->addFlash('error', 'Le champ id_client est manquant.');
        return $this->redirectToRoute('demande_aide', ['id_partenaire' => 1]);
    }

    $demande = new DemandesAide();
    $demande->setIdClient($data['id_client']);
    $demande->setIdPartenaire(1); // Always set id_partenaire to 1
    $demande->setTypeAide($data['type_aide'] ?? ''); // Provide a default value if type_aide is missing
    $demande->setDescription($data['description'] ?? '');
    $demande->setMontantDemande($data['montant_demande'] ?? 0);
    $demande->setDateDemande(new \DateTime($data['date_demande'] ?? 'now'));
    $demande->setStatus('en_attente');

    // Handle file upload
    if ($file) {
        $uploadsDir = $this->getParameter('uploads_directory');
        $fileName = uniqid() . '.' . $file->guessExtension();
        $file->move($uploadsDir, $fileName);
        $demande->setPreuves($fileName);
    }

    $entityManager->persist($demande);
    $entityManager->flush();

    $this->addFlash('success', 'Demande ajoutée avec succès!');
    return $this->redirectToRoute('demande_aide', ['id_partenaire' => 1]);
}
}