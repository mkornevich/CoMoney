<?php


namespace App\Controller;


use App\Entity\Campaign;
use App\Filter\CampaignFilter;
use App\Form\CampaignType;
use App\Form\CampaignFilterType;
use App\Repository\CampaignRepository;
use App\Repository\ImageRepository;
use DateTimeImmutable;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CampaignController extends AbstractController
{
    /**
     * @Route("/campaign/{id<\d+>}", name="campaign_view")
     */
    public function view(Campaign $campaign): Response
    {
        return $this->render('campaign/view.html.twig', [
            'campaign' => $campaign,
        ]);
    }

    /**
     * @Route("/campaign/{id<\d+>}/edit", name="campaign_edit")
     */
    public function edit(int $id, Request $request, CampaignRepository $campaignRepository): Response
    {
        $campaign = $campaignRepository->find($id);

        if ($campaign === null) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(CampaignType::class, $campaign);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('campaign_list');
        }

        return $this->render('campaign/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/campaign/create", name="campaign_create")
     */
    public function create(Request $request, CampaignRepository $campaignRepository, ImageRepository $imageRepository): Response
    {
        $campaign = new Campaign();
        $campaign->setOwner($this->getUser());

        $form = $this->createForm(CampaignType::class, $campaign);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nowDateTime = new DateTimeImmutable();
            $campaign->setImage($imageRepository->findOneBy([]));
            $campaign->setCreatedAt($nowDateTime);
            $campaign->setUpdatedAt($nowDateTime);
            $campaignRepository->persistAndFlush($campaign);
            return $this->redirectToRoute('campaign_list');
        }

        return $this->render('campaign/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/campaign/list", name="campaign_list")
     * @Route("/", name="app_home")
     */
    public function list(Request $request, CampaignRepository $campaignRepository, PaginatorInterface $paginator, CampaignFilter $filter): Response
    {
        $builder = $campaignRepository->getAllWithJoins();

        $form = $this->createForm(CampaignFilterType::class, options: ['show_tab_filter' => $this->getUser() != null]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $filter->applyFilterForm($form, $builder);
        }

        $query = $builder->getQuery();
        $pagination = $paginator->paginate($query, $request->query->getInt('page', 1), 10);

        return $this->render('campaign/list.html.twig', [
            'campaigns' => $query->execute(),
            'pagination' => $pagination,
            'form' => $form->createView(),
        ]);
    }
}