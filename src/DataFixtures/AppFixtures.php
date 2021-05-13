<?php

namespace App\DataFixtures;

use App\Entity\Bonus;
use App\Entity\Campaign;
use App\Entity\CampaignRate;
use App\Entity\CampaignSubject;
use App\Entity\Comment;
use App\Entity\CommentLike;
use App\Entity\Image;
use App\Entity\Payment;
use App\Entity\Post;
use App\Entity\Tag;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private ObjectManager $manager;

    private Generator $faker;

    private UserPasswordEncoderInterface $passwordEncoder;

    /**
     * @var Tag[]
     */
    private array $tags = [];

    /**
     * @var Image[]
     */
    private array $images = [];

    /**
     * @var User[]
     */
    private array $users = [];

    private User $adminUser;

    /**
     * @var CampaignSubject[]
     */
    private array $campaignSubjects = [];

    /**
     * @var Campaign[]
     */
    private array $campaigns = [];

    /**
     * @var Comment[]
     */
    private array $comments = [];

    /**
     * @var Bonus[]
     */
    private array $bonuses = [];

    /**
     * @var Payment[]
     */
    private array $payments = [];

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->faker = Factory::create('ru_RU');
        $this->faker->seed(14879225);

        $this->loadTags();
        $this->loadImages();
        $this->loadUsers();
        $this->loadCampaignSubjects();
        $this->loadCampaigns();
        $this->loadCampaignRates();
        $this->loadComments();
        $this->loadCommentLikes();
        $this->loadPosts();
        $this->loadBonuses();
        $this->loadPayments();

        $this->loadCampaignTotalAmounts();
        $this->loadBonusesToUsers();
    }

    private function loadBonusesToUsers()
    {
        foreach ($this->bonuses as $bonus) {
            $usersWhoHave = $bonus->getUsersWhoHave();
            foreach ($this->users as $user) {
                $userPayed = $this->getUserPayedAmountForCampaign($user, $bonus->getCampaign());
                if (bccomp($userPayed, $bonus->getActivateAmount()) > -1) {
                    $usersWhoHave->add($user);
                }
            }
        }
        $this->manager->flush();
    }

    private function getUserPayedAmountForCampaign(User $user, Campaign $campaign): string
    {
        $payed = '0';
        foreach ($this->payments as $payment) {
            if (
                $payment->getCampaign()->getId() == $campaign->getId() &&
                $payment->getUser()->getId() == $user->getId()
            ) {
                $payed = bcadd($payed, $payment->getAmount());
            }
        }
        return $payed;
    }

    private function loadCampaignTotalAmounts()
    {
        foreach ($this->payments as $payment) {
            $campaign = $payment->getCampaign();
            $campaign->setTotalAmount(bcadd($campaign->getTotalAmount(), $payment->getAmount()));
        }
        $this->manager->flush();
    }

    private function loadPayments()
    {
        foreach ($this->campaigns as $campaign) {
            $count = $this->faker->randomFloat(0, 0, 5);
            $users = $this->faker->randomElements($this->users, $count);
            foreach ($users as $user) {
                $payment = new Payment();
                $payment->setCampaign($campaign);
                $payment->setUser($user);
                $payment->setAmount($this->faker->randomFloat(0, 1, 25));
                $payment->setIsConfirmed(true);

                $createdAt = DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-1 years'));
                $payment->setCreatedAt($createdAt);
                $payment->setUpdatedAt($createdAt);
                $this->payments[] = $payment;
                $this->manager->persist($payment);
            }
        }
        $this->manager->flush();
    }

    private function loadBonuses()
    {
        foreach ($this->campaigns as $campaign) {
            $count = $this->faker->randomFloat(0, 0, 15);
            for ($i = 0; $i < $count; $i++) {
                $bonus = new Bonus();
                $bonus->setCampaign($campaign);
                $bonus->setActivateAmount(($i + 1) * 4);
                $bonus->setName($this->faker->words(3, true));
                $bonus->setDescription($this->faker->text);
                $this->bonuses[] = $bonus;
                $this->manager->persist($bonus);
            }
        }
        $this->manager->flush();
    }

    private function loadPosts()
    {
        foreach ($this->campaigns as $campaign) {
            $count = $this->faker->randomFloat(0, 0, 20);
            for ($i = 0; $i < $count; $i++) {
                $post = new Post();
                $post->setCampaign($campaign);
                if ($this->faker->randomElement([true, false])) {
                    $post->setImage($this->faker->randomElement($this->images));
                }
                $post->setName($this->faker->words(3, true));
                $post->setText($this->faker->text(300));

                $createdAt = DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-1 years'));
                $post->setCreatedAt($createdAt);
                $post->setUpdatedAt($createdAt);
                $this->manager->persist($post);
            }
        }
        $this->manager->flush();
    }

    private function loadCommentLikes()
    {
        foreach ($this->comments as $comment) {
            $count = $this->faker->randomFloat(0, 0, 10);
            $users = $this->faker->randomElements($this->users, $count);
            foreach ($users as $user) {
                $value = $this->faker->randomElement([-1, 1]);
                if ($value === 1) {
                    $comment->setLikedCount($comment->getLikedCount() + 1);
                } else {
                    $comment->setDislikedCount($comment->getDislikedCount() + 1);
                }
                $commentLike = new CommentLike();
                $commentLike->setUser($user);
                $commentLike->setComment($comment);
                $commentLike->setValue($value);
                $this->manager->persist($commentLike);
            }
        }
        $this->manager->flush();
    }

    private function loadComments()
    {
        foreach ($this->campaigns as $campaign) {
            $count = $this->faker->randomFloat(0, 5, 30);
            $users = $this->faker->randomElements($this->users, $count, true);
            foreach ($users as $user) {
                $comment = new Comment();
                $comment->setCampaign($campaign);
                $comment->setAuthor($user);
                $comment->setText($this->faker->text(300));
                $createdAt = $this->faker->dateTimeBetween('-2 years');
                $comment->setCreatedAt(DateTimeImmutable::createFromMutable($createdAt));
                $this->comments[] = $comment;
                $this->manager->persist($comment);
            }
        }
        $this->manager->flush();
    }

    private function loadCampaignRates()
    {
        foreach ($this->campaigns as $campaign) {
            $count = $this->faker->randomFloat(0, 0, 10);
            $usersWhoRate = $this->faker->randomElements($this->users, $count);
            foreach ($usersWhoRate as $user) {
                $rate = $this->faker->randomFloat(0, 1, 5);
                $campaignRate = new CampaignRate();
                $campaignRate->setCampaign($campaign);
                $campaignRate->setUser($user);
                $campaignRate->setValue($rate);
                $this->manager->persist($campaignRate);
            }
        }
        $this->manager->flush();
    }

    private function loadCampaigns()
    {
        for ($i = 1; $i <= 30; $i++) {
            $campaign = new Campaign();

            $campaign->setOwner($this->faker->randomElement($this->users));
            $campaign->setName($this->faker->words(4, true));
            $campaign->setDescription($this->faker->text);
            $campaign->setSubject($this->faker->randomElement($this->campaignSubjects));

            $campaign->setImage($this->faker->randomElement($this->images));

            $campaign->setRating($this->faker->randomFloat(1, 0, 5));

            $this->fillCollection($campaign->getTags(),
                $this->faker->randomElements($this->tags, 5, false));

            $this->fillCollection($campaign->getGalleryImages(),
                $this->faker->randomElements($this->images, 5, false));

            $campaign->setYoutubeVideoKey('KewfYKJy8YU');
            $campaign->setTargetAmount($this->faker->randomNumber(3, true) . '');
            $campaign->setTotalAmount('0');

            $endFundraisingAt = $this->faker->dateTimeBetween('1 years', '3 years');
            $campaign->setEndFundraisingAt(DateTimeImmutable::createFromMutable($endFundraisingAt));

            $createdAt = DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-2 years'));
            $campaign->setCreatedAt($createdAt);
            $campaign->setUpdatedAt($createdAt);

            $this->campaigns[] = $campaign;
            $this->manager->persist($campaign);
        }
        $this->manager->flush();
    }

    private function fillCollection(Collection $collection, array $appendItems)
    {
        foreach ($appendItems as $item) {
            $collection->add($item);
        }
    }

    private function loadCampaignSubjects()
    {
        for ($i = 1; $i <= 10; $i++) {
            $campaignSubject = new CampaignSubject();
            $campaignSubject->setName($this->faker->words(2, true));
            $this->campaignSubjects[] = $campaignSubject;
            $this->manager->persist($campaignSubject);
        }
        $this->manager->flush();
    }

    private function loadUsers()
    {
        $user = new User();
        $user->setEmail('mkornevich@gmail.com');
        $user->setFullName('Корневич Максим Александрович');
        $user->setPassword($this->passwordEncoder->encodePassword($user, "mkornevich"));
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $user->setUsername('mkornevich');
        $this->adminUser = $user;
        $this->manager->persist($user);

        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setEmail($this->faker->email);
            $user->setFullName($this->faker->name);
            $user->setPassword($this->passwordEncoder->encodePassword($user, "password$i"));
            $user->setRoles(['ROLE_USER']);
            $user->setUsername($this->faker->userName);
            $this->users[] = $user;
            $this->manager->persist($user);
        }
        $this->manager->flush();
    }

    private function loadImages()
    {
        for ($i = 1; $i <= 50; $i++) {
            $image = new Image();
            $image->setPath($this->faker->imageUrl());
            $this->images[] = $image;
            $this->manager->persist($image);
        }
        $this->manager->flush();
    }

    private function loadTags()
    {
        for ($i = 1; $i <= 30; $i++) {
            $tag = new Tag();
            $tag->setName($this->faker->words(2, true));
            $this->tags[] = $tag;
            $this->manager->persist($tag);
        }
        $this->manager->flush();
    }
}
