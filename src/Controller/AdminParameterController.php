<?php

namespace App\Controller;

use App\Entity\Utilisateurs; // Assurez-vous que l'entité est correcte
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Mpdf\Mpdf;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/admin')]
final class AdminParameterController extends AbstractController
{
    private $mpdf;
    private $logger;
    private $entityManager;

    public function __construct(LoggerInterface $logger, EntityManagerInterface $entityManager)
    {
        $this->mpdf = new Mpdf(['tempDir' => sys_get_temp_dir()]);
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }

    #[Route('/parameter', name: 'app_admin_settings', methods: ['GET'])]
    public function index(SessionInterface $session): Response
    {
        $startTime = $session->get('start_time');
        if (!$startTime) {
            $startTime = time();
            $session->set('start_time', $startTime);
        }

        $elapsedTime = time() - $startTime;
        $timeSpent = $this->formatTime($elapsedTime);

        $averageTimeSeconds = $elapsedTime / max(1, $session->get('user_count', 1));
        $averageTime = $this->formatTime($averageTimeSeconds);

        $activeUsers = $this->entityManager->getRepository(Utilisateurs::class)
            ->count(['status' => 'actif']);

        return $this->render('admin_parameter/index.html.twig', [
            'controller_name' => 'AdminParameterController',
            'pageTitle' => 'Paramètre',
            'time_spent' => $timeSpent,
            'average_time' => $averageTime,
            'active_users' => $activeUsers,
        ]);
    }

    #[Route('/generate-report', name: 'app_admin_generate_report', methods: ['POST'])]
    public function generateReport(Request $request, SessionInterface $session): JsonResponse
    {
        $reportType = $request->request->get('reportType');
        $reportPeriod = $request->request->get('reportPeriod');

        $this->logger->debug('Generate Report Parameters', [
            'reportType' => $reportType,
            'reportPeriod' => $reportPeriod,
        ]);

        if (!in_array($reportType, ['users', 'system']) || !in_array($reportPeriod, ['today', 'week', 'month', 'custom'])) {
            $this->logger->error('Invalid report parameters', [
                'reportType' => $reportType,
                'reportPeriod' => $reportPeriod,
            ]);
            return new JsonResponse(['status' => 'error', 'message' => 'Type de rapport ou période invalide'], 400);
        }

        $startTime = $session->get('start_time', time());
        $elapsedTime = time() - $startTime;
        $timeSpent = $this->formatTime($elapsedTime);

        $averageTimeSeconds = $elapsedTime / max(1, $session->get('user_count', 1));
        $averageTime = $this->formatTime($averageTimeSeconds);

        $partners = [];
        if ($reportType === 'users') {
            $queryBuilder = $this->entityManager->getRepository(Utilisateurs::class)
                ->createQueryBuilder('u')
                ->where('u.typeUtilisateur = :type')
                ->setParameter('type', 'Partenaire');

            // Filtrer par période
            if ($reportPeriod === 'today') {
                $queryBuilder->andWhere('u.dateInscription >= :start')
                    ->setParameter('start', new \DateTime('today'));
            } elseif ($reportPeriod === 'week') {
                $queryBuilder->andWhere('u.dateInscription >= :start')
                    ->setParameter('start', (new \DateTime())->modify('-1 week'));
            } elseif ($reportPeriod === 'month') {
                $queryBuilder->andWhere('u.dateInscription >= :start')
                    ->setParameter('start', (new \DateTime())->modify('-1 month'));
            }

            $partners = $queryBuilder->getQuery()->getResult();
            $partners = array_map(function ($partner) {
                return [
                    'nom' => $partner->getNom(),
                    'prenom' => $partner->getPrenom(),
                    'email' => $partner->getEmail(),
                    'telephone' => $partner->getTelephone(),
                    'statut_verification' => $partner->getStatutVerification(),
                    'status' => $partner->getStatus(),
                    'adresse_utilisateur' => $partner->getAdresseUtilisateur(),
                ];
            }, $partners);
        }

        $data = [
            'type' => $reportType,
            'period' => $reportPeriod,
            'metrics' => $reportType === 'system' ? [
                'active_users' => $this->entityManager->getRepository(Utilisateurs::class)
                    ->count(['status' => 'actif']),
                'average_time' => $averageTime,
                'time_spent' => $timeSpent,
            ] : [],
            'partners' => $partners,
        ];

        return new JsonResponse(['status' => 'success', 'data' => $data]);
    }

    #[Route('/download-report', name: 'app_admin_download_report', methods: ['GET'])]
    public function downloadReport(Request $request, SessionInterface $session): Response
    {
        $reportType = $request->query->get('reportType');
        $reportPeriod = $request->query->get('reportPeriod');

        $this->logger->debug('Download Report Parameters', [
            'reportType' => $reportType,
            'reportPeriod' => $reportPeriod,
            'user' => $this->getUser() ? $this->getUser()->getUserIdentifier() : 'Not logged in',
        ]);

        if (!in_array($reportType, ['users', 'system']) || !in_array($reportPeriod, ['today', 'week', 'month', 'custom'])) {
            $this->logger->error('Invalid download parameters', [
                'reportType' => $reportType,
                'reportPeriod' => $reportPeriod,
            ]);
            $this->addFlash('error', sprintf('Type de rapport (%s) ou période (%s) invalide.', $reportType, $reportPeriod));
            return $this->redirectToRoute('app_admin_settings');
        }

        $startTime = $session->get('start_time', time());
        $elapsedTime = time() - $startTime;
        $timeSpent = $this->formatTime($elapsedTime);

        $averageTimeSeconds = $elapsedTime / max(1, $session->get('user_count', 1));
        $averageTime = $this->formatTime($averageTimeSeconds);

        $partners = [];
        if ($reportType === 'users') {
            $queryBuilder = $this->entityManager->getRepository(Utilisateurs::class)
                ->createQueryBuilder('u')
                ->where('u.typeUtilisateur = :type')
                ->setParameter('type', 'Partenaire');

            if ($reportPeriod === 'today') {
                $queryBuilder->andWhere('u.dateInscription >= :start')
                    ->setParameter('start', new \DateTime('today'));
            } elseif ($reportPeriod === 'week') {
                $queryBuilder->andWhere('u.dateInscription >= :start')
                    ->setParameter('start', (new \DateTime())->modify('-1 week'));
            } elseif ($reportPeriod === 'month') {
                $queryBuilder->andWhere('u.dateInscription >= :start')
                    ->setParameter('start', (new \DateTime())->modify('-1 month'));
            }

            $partners = $queryBuilder->getQuery()->getResult();
            $partners = array_map(function ($partner) {
                return [
                    'nom' => $partner->getNom(),
                    'prenom' => $partner->getPrenom(),
                    'email' => $partner->getEmail(),
                    'telephone' => $partner->getTelephone(),
                    'statut_verification' => $partner->getStatutVerification(),
                    'status' => $partner->getStatus(),
                    'adresse_utilisateur' => $partner->getAdresseUtilisateur(),
                ];
            }, $partners);
        }

        $data = [
            'type' => $reportType,
            'period' => $reportPeriod,
            'metrics' => $reportType === 'system' ? [
                'active_users' => $this->entityManager->getRepository(Utilisateurs::class)
                    ->count(['status' => 'actif']),
                'average_time' => $averageTime,
                'time_spent' => $timeSpent,
            ] : [],
            'partners' => $partners,
            'generated_at' => (new \DateTime())->format('Y-m-d H:i:s'),
        ];

        try {
            $this->logger->debug('Rendering template for PDF');
            $template = $reportType === 'users' ? 'admin_parameter/partner_report_template.html.twig' : 'admin_parameter/system_report_template.html.twig';
            $html = $this->renderView($template, [
                'report' => $data,
            ]);

            $this->logger->debug('Generating PDF');
            $this->mpdf->WriteHTML($html);
            $filename = sprintf('report_%s_%s.pdf', $reportType, date('Ymd_His'));
            $pdfOutput = $this->mpdf->Output('', 'S');

            $this->logger->debug('Returning PDF response');
            return new Response(
                $pdfOutput,
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
                ]
            );
        } catch (\Exception $e) {
            $this->logger->error('PDF generation failed', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->addFlash('error', 'Erreur lors de la génération du PDF : ' . $e->getMessage());
            return $this->redirectToRoute('app_admin_settings');
        }
    }

    #[Route('/debug-user', name: 'app_admin_debug_user', methods: ['GET'])]
    public function debugUser(): Response
    {
        $user = $this->getUser();
        return new JsonResponse([
            'user' => $user ? $user->getUserIdentifier() : 'Not logged in',
            'roles' => $user ? $user->getRoles() : [],
        ]);
    }

    private function formatTime(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%dh %dm', $hours, $minutes);
        } elseif ($minutes > 0) {
            return sprintf('%dm %ds', $minutes, $seconds);
        } else {
            return sprintf('%ds', $seconds);
        }
    }
}