<?php


namespace App\Controller;


use App\Entity\Campaign;
use App\Entity\Tag;
use App\Filter\CampaignFilter;
use App\Form\CampaignFilterType;
use App\Repository\CampaignRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
        $qb = $campaignRepository->createQueryBuilder('campaign')
            ->addSelect(['user', 'subject', 'mainImage'])
            ->leftJoin('campaign.owner', 'user')
            ->leftJoin('campaign.subject', 'subject')
            ->leftJoin('campaign.image', 'mainImage')
            ->leftJoin('campaign.tags', 'tag');

        $form = $this->createForm(CampaignFilterType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $filter->applyFilters($qb, $form);
        }



        $qb->groupBy('campaign.id');
        $query = $qb->getQuery();
        $pagination = $paginator->paginate($query, $request->query->getInt('page', 1), 10);

        return $this->render('campaign/list.html.twig', [
            'campaigns' => $query->execute(),
            'pagination' => $pagination,
            'form' => $form->createView(),
        ]);
    }
}