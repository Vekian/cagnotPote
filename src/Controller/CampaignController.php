<?php

namespace App\Controller;

use App\Entity\Campaign;
use App\Form\CampaignType;
use App\Repository\CampaignRepository;
use App\Repository\ParticipantRepository;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/campaign')]
class CampaignController extends AbstractController
{
    #[Route('/', name: 'app_campaign_index', methods: ['GET'])]
    public function index(CampaignRepository $campaignRepository): Response
    {
        $campaigns = $campaignRepository->findAll();
        return $this->render('campaign/index.html.twig', [
            'campaigns' => $campaigns,
        ]);
    }

    #[Route('/new', name: 'app_campaign_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $campaign = new Campaign();
        $form = $this->createForm(CampaignType::class, $campaign);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $campaign->setId();
            $entityManager->persist($campaign);
            $entityManager->flush();
            return $this->redirectToRoute('app_campaign_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('campaign/new.html.twig', [
            'campaign' => $campaign,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_campaign_show', methods: ['GET'])]
    public function show(Campaign $campaign, PaymentRepository $paymentRepository, ParticipantRepository $participantRepository): Response
    {
        $participants =$paymentRepository->findParticipations($campaign->getId());
        // $participants = $participantRepository->findBy(['campaign'=> $campaign]);
        $totalAmount = 0;
        $totalParticipants = 0;
        foreach($participants as $participant) {
            $totalAmount += $participant['amount'];
            $totalParticipants += 1;
        }
        $percentage = round(($totalAmount / $campaign->getGoal()) * 100, 1);


        return $this->render('campaign/show.html.twig', [
            'campaign' => $campaign,
            'participants' => $participants,
            'totalAmount' => $totalAmount,
            'totalParticipants' => $totalParticipants,
            'percentage' => $percentage
        ]);
    }

    #[Route('/{id}/edit', name: 'app_campaign_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Campaign $campaign, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CampaignType::class, $campaign);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_campaign_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('campaign/edit.html.twig', [
            'campaign' => $campaign,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_campaign_delete', methods: ['POST'])]
    public function delete(Request $request, Campaign $campaign, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $campaign->getId(), $request->request->get('_token'))) {
            $entityManager->remove($campaign);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_campaign_index', [], Response::HTTP_SEE_OTHER);
    }
}
