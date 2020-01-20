<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MovieRepository")
 */
class Movie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"movie.list", "movie.detail"})
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"movie.list", "movie.detail"})
     */
    protected $title;

    /**
     * @ORM\Column(type="smallint")
     * @Groups({"movie.list", "movie.detail"})
     */
    protected $year;

    /**
     * @ORM\Column(type="string", length=9, unique=true)
     * @Groups({"movie.list", "movie.detail"})
     */
    protected $imdbId;

    /**
     * @ORM\Column(type="string", length=20)
     * @Groups("movie.detail")
     */
    protected $type;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups("movie.detail")
     */
    protected $poster;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Rating", mappedBy="movie", orphanRemoval=true)
     * @Groups("movie.ratings")
     */
    protected $ratings;

    public function __construct()
    {
        $this->ratings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getImdbId(): ?string
    {
        return $this->imdbId;
    }

    public function setImdbId(string $imdbId): self
    {
        $this->imdbId = $imdbId;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPoster(): ?string
    {
        return $this->poster;
    }

    public function setPoster(?string $poster): self
    {
        if ($poster !== 'N/A') {
            $this->poster = $poster;
        }

        return $this;
    }

    /**
     * @return Collection|Rating[]
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(Rating $rating): self
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings[] = $rating;
            $rating->setMovie($this);
        }

        return $this;
    }

    public function removeRating(Rating $rating): self
    {
        if ($this->ratings->contains($rating)) {
            $this->ratings->removeElement($rating);
            // set the owning side to null (unless already changed)
            if ($rating->getMovie() === $this) {
                $rating->setMovie(null);
            }
        }

        return $this;
    }

    /**
     * @Groups("movie.detail")
     */
    public function getRateAvg(): float
    {
        $rateQty = count($this->ratings);
        $rateSum = array_sum($this->ratings->map(function ($rating) {
            return $rating->getRate();
        })->toArray());

        return $rateSum / $rateQty;
    }
}
