<?php


namespace App\Controller;


use App\Entity\Campaign;
use App\Entity\Payment;
use App\Form\PaymentPayType;
use App\Repository\PaymentRepository;
use App\Updater\CampaignTotalAmountUpdater;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    /**
     * @Route("/campaign/{campaign_id<\d+>}/pay", name="payment_pay")
     * @Entity("campaign", expr="repository.find(campaign_id)")
     */
    public function pay(Campaign $campaign, Request $request, PaymentRepository $paymentRepository, CampaignTotalAmountUpdater $updater): Response
    {
        $payment = Payment::create($campaign, $this->getUser());

        $form = $this->createForm(PaymentPayType::class, $payment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $paymentRepository->persistAndFlush($payment);
            $updater->update($campaign);
            return $this->redirectToRoute('campaign_view', ['id' => $campaign->getId()]);
        }

        return $this->render('payment/pay.html.twig', [
            'form' => $form->createView(),
            'payment' => $payment,
        ]);
    }
}