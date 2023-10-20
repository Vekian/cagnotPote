<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Entity\Campaign;
use App\Form\PaymentType;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/payment')]
class PaymentController extends AbstractController
{
    #[Route('/{id}/new', name: 'app_payment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Campaign $campaign, EntityManagerInterface $entityManager): Response
    {
        $amount = $request->request->get('amount');
        $payment = new Payment();
        $form = $this->createForm(PaymentType::class, $payment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $stripeSecretKey = 'sk_test_51NuFINFuMIbivSImr6RUMV5yDQ5K0U4QJDezXxYHMCCqNCR1HVUEMRpquIUWpUEODdlNXkvMgMVgeOr4v1MDNtzg00vghaisyq';
            $stripe = new \Stripe\StripeClient($stripeSecretKey);
            try {
                $paymentIntent = $stripe->paymentIntents->create([
                    'amount' => $payment->getAmount(),
                    'currency' => 'eur',
                    // In the latest version of the API, specifying the `automatic_payment_methods` parameter is optional because Stripe enables its functionality by default.
                    'automatic_payment_methods' => [
                        'enabled' => true,
                    ],
                ]);
                $output = [
                    'clientSecret' => $paymentIntent->client_secret,
                ];
            }
            catch (Error $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }

            $payment->getParticipant()->setCampaign($campaign);
            $entityManager->persist($payment);
            $entityManager->flush();

            return $this->redirectToRoute('app_campaign_show', ['id' => $campaign->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('payment/new.html.twig', [
            'payment' => $payment,
            'form' => $form,
            'campaign' => $campaign,
            'amount' => $amount,
        ]);
    }
}
