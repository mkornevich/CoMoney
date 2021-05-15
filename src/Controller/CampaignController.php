<?php


namespace App\Controller;


use App\Filter\CampaignFilter;
use App\Form\CampaignFilterType;
use App\Repository\CampaignRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CampaignController extends AbstractController
{
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