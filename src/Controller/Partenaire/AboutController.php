<?php

namespace App\Controller\Partenaire;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\DonsRepository;
use App\Repository\BenevolatRepository;
use App\Repository\UtilisateursRepository;
use App\Repository\EvenementsRepository;
use App\Entity\Utilisateurs;
use ServiceEntityRepository;

final class AboutController extends AbstractController
{
    #[Route('/dashboard/partenaire/about', name: 'app_partenaire_about')]
    public function index(
        DonsRepository $donRepository,
        EvenementsRepository $evenementRepository,
        UtilisateursRepository $userRepository,
        BenevolatRepository $benevolatRepository
    ): Response {
        // Récupérer tous les utilisateurs
        $utilisateurs = $userRepository->findAll();

        $pays = [];

        foreach ($utilisateurs as $utilisateur) {
            $adresse = $utilisateur->getAdresseUtilisateur(); // Assurez-vous que getAdresse() retourne une chaîne d'adresse valide

            // Vérifier si l'adresse est valide avant de l'utiliser
            if ($adresse && is_string($adresse)) {
                $paysExtrait = $this->extrairePaysParListe($adresse); // Extraire le pays de l'adresse

                if ($paysExtrait && !in_array($paysExtrait, $pays)) {
                    $pays[] = $paysExtrait; // Ajouter le pays à la liste s'il n'est pas déjà présent
                }
            }
        }

        $totalDons = $donRepository->getTotalMontant();
        $nbBenevolats = $benevolatRepository->countByStatuses(['ouvert', 'en cours']);
        $nbEvenements = $evenementRepository->count([]);
        $nbPays = count($pays); // Le nombre de pays uniques

        return $this->render('partenaire/about/index.html.twig', [
            'totalDons' => $totalDons,
            'nbBenevolats' => $nbBenevolats,
            'nbEvenements' => $nbEvenements,
            'nbPays' => $nbPays, // Le nombre de pays
        ]);
    }

    public function extrairePaysParListe(string $adresse): ?string
    {
        $paysValides = ['Tunisie', 'France', 'Allemagne', 'États-Unis', 'Espagne', 'Italie','Djerba']; // liste des pays à vérifier

        foreach ($paysValides as $pays) {
            if (stripos($adresse, $pays) !== false) {
                return $pays; // Si le pays est trouvé dans l'adresse, on le retourne
            }
        }

        return null; // Aucun pays trouvé
    }
}
