<?php

namespace App\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ProfilePicture", inversedBy="user")
     * @ORM\JoinColumn(nullable=true)
     */
    private $profilePicture;
    
     /**
     * @ORM\OneToMany(targetEntity="App\Entity\Picture", mappedBy="publisher")
     * @ORM\JoinColumn(nullable=true)
     */
    private $pictures;
    
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Job", inversedBy="user")
     * @ORM\JoinColumn(nullable=true)
     */
    private $job;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Event", mappedBy="creator")
     * @ORM\JoinColumn(nullable=true)
     */
    private $eventCreated;
    
    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="App\Entity\Event", inversedBy="participants")
     */
    private $events;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Recommandation", mappedBy="user")
     * @ORM\JoinColumn(nullable=true)
     */
    private $recommandations;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prename;

    /**
     * @ORM\Column(type="date")
     */
    private $birthDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nickname;
    
    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $team;
    
     /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $status;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

     public function __construct()
    {
        $this->events = new ArrayCollection();
    }
    
    
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getProfilePicture()
    {
        return $this->profilePicture;
    }

    /**
     * @param mixed $profilePicture
     */
    public function setProfilePicture($profilePicture): void
    {
        $this->profilePicture = $profilePicture;
    }
    
    /**
     * @return mixed
     */
    public function getPictures()
    {
        return $this->pictures;
    }
    
     /**
     * @return mixed
     */
    public function getEventCreated()
    {
        return $this->eventCreated;
    }

    /**
     * @param mixed $eventCreated
     */
    public function setEventCreated($eventCreated): void
    {
        $this->eventCreated = $eventCreated;
    }
    
    /**
     * @return mixed
     */
    public function getRecommandations()
    {
        return $this->recommandations;
    }

    /**
     * @param mixed $recommandation
     */
    public function setRecommandation($recommandation): void
    {
        $this->recommandations = $recommandation;
    }
    
    /**
     * @return ArrayCollection
     */
    public function getEvents(){
        
        return $this->events;
    }
    
        
    /**
     * Add a event participation
     * @param ArrayCollection $event
     */
    public function addEvent($event){
        
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->addParticipant($this);
        }
 
        return $this;    
        
    }
    
    /**
     * remove participation to an event
     * @param ArrayCollection $event
     */
    public function removeParticipation($event){
        
        if ($this->events->contains($event)) {
            $this->events->removeElement($event);
            $event->removeParticipant($this);
        }    
        
    }

    /**
     * @return mixed
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @param mixed $job
     */
    public function setJob($job): void
    {
        $this->job = $job;
    }
    
    /**
     * @return mixed
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @param mixed $picture
     */
    public function setPicture($picture): void
    {
        $this->picture = $picture;
    }


    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrename(): ?string
    {
        return $this->prename;
    }

    public function setPrename(string $prename): self
    {
        $this->prename = $prename;

        return $this;
    }
    
        public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getTeam(): ?string
    {
        return $this->team;
    }

    public function setTeam(string $team): self
    {
        $this->team = $team;

        return $this;
    }
    
        public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
    
        public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }
}
