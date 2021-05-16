<?php


namespace App\Controller;


use App\Entity\Campaign;
use App\Entity\Payment;
use App\Form\PaymentPayType;
use App\Repository\PaymentRepository;
use App\Updater\CampaignTotalAmountUpdater;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    /**
     * @Route("/payment/list", name="payment_list")
     */
    public function list(Request $request, PaymentRepository $paymentRepository, PaginatorInterface $paginator): Response
    {
        $paymentQB = $paymentRepository->getAllEagerByUserQB($this->getUser());
        $query = $paymentQB->getQuery();
        $pagination = $paginator->paginate($query, $request->query->getInt('page', 1), 20);

        return $this->render('payment/list.html.twig', [
            'payments' => $query->execute(),
            'pagination' => $pagination,
        ]);
    }

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