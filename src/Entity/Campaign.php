<?php


namespace App\Entity;


use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CampaignRepository")
 * @ORM\Table(name="campaign")
 */
class Campaign
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="User");
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private User $owner;

    /**
     * @ORM\OneToMany(targetEntity="Payment", mappedBy="campaign")
     */
    private Collection $payments;

    /**
     * @ORM\Column(type="string")
     */
    private string $name;

    /**
     * @ORM\Column(type="text")
     */
    private string $description;

    /**
     * @ORM\Column(type="text")
     */
    private string $text;

    /**
     * @ORM\ManyToOne(targetEntity="CampaignSubject");
     * @ORM\JoinColumn(name="subject_id", referencedColumnName="id")
     */
    private CampaignSubject $subject;

    /**
     * @ORM\ManyToMany(targetEntity="Tag")
     * @ORM\JoinTable(
     *     name="campaign_tag",
     *     joinColumns={@ORM\JoinColumn(name="campaign_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     * )
     */
    private Collection $tags;

    /**
     * @ORM\ManyToOne(targetEntity="Image")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    private Image $image;

    /**
     * @ORM\ManyToMany(targetEntity="Image")
     * @ORM\JoinTable(
     *     name="campaign_image",
     *     joinColumns={@ORM\JoinColumn(name="campaign_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="image_id", referencedColumnName="id")}
     * )
     */
    private Collection $galleryImages;

    /**
     * @ORM\Column(type="string", name="youtube_video_key")
     */
    private string $youtubeVideoKey;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, name="target_amount")
     */
    private string $targetAmount;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, name="total_amount")
     */
    private string $totalAmount;

    /**
     * @ORM\Column(type="decimal", precision=2, scale=1, name="rating")
     */
    private string $rating;

    /**
     * @ORM\Column(type="datetime_immutable", name="end_fundraising_at")
     */
    private DateTimeImmutable $endFundraisingAt;

    /**
     * @ORM\Column(type="datetime_immutable", name="created_at")
     */
    private DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", name="updated_at")
     */
    private DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->galleryImages = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return User
     */
    public function getOwner(): User
    {
        return $this->owner;
    }

    /**
     * @param User $owner
     */
    public function setOwner(User $owner): void
    {
        $this->owner = $owner;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return CampaignSubject
     */
    public function getSubject(): CampaignSubject
    {
        return $this->subject;
    }

    /**
     * @param CampaignSubject $subject
     */
    public function setSubject(CampaignSubject $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return Collection
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * @param Collection $tags
     */
    public function setTags(Collection $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * @return Image
     */
    public function getImage(): Image
    {
        return $this->image;
    }

    /**
     * @param Image $image
     */
    public function setImage(Image $image): void
    {
        $this->image = $image;
    }



    /**
     * @return Collection
     */
    public function getGalleryImages(): Collection
    {
        return $this->galleryImages;
    }

    /**
     * @param Collection $galleryImages
     */
    public function setGalleryImages(Collection $galleryImages): void
    {
        $this->galleryImages = $galleryImages;
    }

    /**
     * @return string
     */
    public function getYoutubeVideoKey(): string
    {
        return $this->youtubeVideoKey;
    }

    /**
     * @param string $youtubeVideoKey
     */
    public function setYoutubeVideoKey(string $youtubeVideoKey): void
    {
        $this->youtubeVideoKey = $youtubeVideoKey;
    }

    /**
     * @return string
     */
    public function getTargetAmount(): string
    {
        return $this->targetAmount;
    }

    /**
     * @param string $targetAmount
     */
    public function setTargetAmount(string $targetAmount): void
    {
        $this->targetAmount = $targetAmount;
    }

    /**
     * @return string
     */
    public function getTotalAmount(): string
    {
        return $this->totalAmount;
    }

    public function getTotalAmountPercentage(): string
    {
        return bcdiv(
            bcmul(100, $this->totalAmount),
            $this->targetAmount, 0);
    }

    /**
     * @param string $totalAmount
     */
    public function setTotalAmount(string $totalAmount): void
    {
        $this->totalAmount = $totalAmount;
    }

    /**
     * @return string
     */
    public function getRating(): string
    {
        return $this->rating;
    }

    /**
     * @param string $rating
     */
    public function setRating(string $rating): void
    {
        $this->rating = $rating;
    }



    /**
     * @return DateTimeImmutable
     */
    public function getEndFundraisingAt(): DateTimeImmutable
    {
        return $this->endFundraisingAt;
    }

    /**
     * @param DateTimeImmutable $endFundraisingAt
     */
    public function setEndFundraisingAt(DateTimeImmutable $endFundraisingAt): void
    {
        $this->endFundraisingAt = $endFundraisingAt;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeImmutable $createdAt
     */
    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTimeImmutable $updatedAt
     */
    public function setUpdatedAt(DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }
}