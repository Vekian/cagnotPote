<?php

namespace App\Controller;

use App\Repository\CampaignRepository;
use App\Repository\PaymentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CampaignRepository $repository, PaymentRepository $paymentRepository): Response
    {
        $campaigns = $repository->findAll();
        foreach($campaigns as $campaign) {
            $participants =$paymentRepository->findParticipations($campaign->getId());
            $totalAmount = 0;
            $totalParticipants = 0;
            foreach($participants as $participant) {
                $totalAmount += $participant['amount'];
                $totalParticipants += 1;
            }
            $campaign->setTotalParticipants($totalParticipants);
            $campaign->setTotalAmount($totalAmount);
            $campaign->setPercentage();
        }

        return $this->render('index.html.twig', [
            'campaigns' => $campaigns,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(): Response
    {
        return $this->render('create.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/payment', name: 'payment')]
    public function payment(): Response
    {
        return $this->render('payment.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/show', name: 'show')]
    public function show(): Response
    {
        return $this->render('show.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
