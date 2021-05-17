<?php


namespace App\Controller;


use App\Entity\Campaign;
use App\Filter\CampaignFilter;
use App\Form\CampaignType;
use App\Form\CampaignFilterType;
use App\Repository\CampaignRepository;
use App\Repository\ImageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function edit(Campaign $campaign, Request $request): Response
    {
        $this->denyAccessUnlessGranted('edit', $campaign);

        $form = $this->createForm(CampaignType::class, $campaign, [
            'show_owner_field' => $this->isGranted('edit_owner', $campaign),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('campaign_view', ['id' => $campaign->getId()]);
        }

        return $this->render('campaign/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/campaign/create", name="campaign_create")
     * @IsGranted("ROLE_USER")
     */
    public function create(Request $request, CampaignRepository $campaignRepository, ImageRepository $imageRepository): Response
    {
        $campaign = Campaign::create($this->getUser());

        $form = $this->createForm(CampaignType::class, $campaign, [
            'show_owner_field' => $this->isGranted('edit_owner', $campaign),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $campaign->setImage($imageRepository->findOneBy([]));
            $campaign->setGalleryImages(new ArrayCollection($imageRepository->findBy([], limit: 5)));
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