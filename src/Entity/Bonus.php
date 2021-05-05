<?php


namespace App\Entity;


use ContainerK33OIA1\getConsole_Command_ConfigDebugService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Entity
 * @ORM\Table(name="bonus")
 */
class Bonus
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="Campaign")
     * @ORM\JoinColumn(name="campaign_id", referencedColumnName="id")
     */
    private Campaign $campaign;

    /**
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(
     *     name="user_bonus",
     *     joinColumns={@ORM\JoinColumn(name="bonus_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     * )
     */
    private Collection $usersWhoHave;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, name="activate_amount")
     */
    private string $activateAmount;

    /**
     * @ORM\Column(type="string")
     */
    private string $name;

    /**
     * @ORM\Column(type="text")
     */
    private string $description;

    public function __construct()
    {
        $this->usersWhoHave = new ArrayCollection();
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
     * @return Campaign
     */
    public function getCampaign(): Campaign
    {
        return $this->campaign;
    }

    /**
     * @param Campaign $campaign
     */
    public function setCampaign(Campaign $campaign): void
    {
        $this->campaign = $campaign;
    }

    /**
     * @return Collection
     */
    public function getUsersWhoHave(): Collection
    {
        return $this->usersWhoHave;
    }

    /**
     * @param Collection $usersWhoHave
     */
    public function setUsersWhoHave(Collection $usersWhoHave): void
    {
        $this->usersWhoHave = $usersWhoHave;
    }

    /**
     * @return string
     */
    public function getActivateAmount(): string
    {
        return $this->activateAmount;
    }

    /**
     * @param string $activateAmount
     */
    public function setActivateAmount(string $activateAmount): void
    {
        $this->activateAmount = $activateAmount;
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


}