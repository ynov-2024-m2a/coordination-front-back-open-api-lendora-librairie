<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['getAllBooks', 'getAllAuthors'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['getAllBooks', 'getAllAuthors'])]
    private ?\DateTimeInterface $releaseDate = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['getAllBooks', 'getAllAuthors'])]
    private ?string $blurb = null;

    /**
     * @var Collection<int, Genre>
     */
    #[ORM\ManyToMany(targetEntity: Genre::class, inversedBy: 'authors')]
    #[Groups(['getAllBooks'])]
    private Collection $genre;

    #[ORM\OneToOne(inversedBy: 'book', cascade: ['persist', 'remove'])]
    private ?Picture $coverBook = null;

    #[ORM\ManyToOne(inversedBy: 'authors')]
    #[Groups(['getAllBooks'])]
    private Author $author;

    public function __construct()
    {
        $this->genre = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(\DateTimeInterface $releaseDate): static
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getBlurb(): ?string
    {
        return $this->blurb;
    }

    public function setBlurb(string $blurb): static
    {
        $this->blurb = $blurb;

        return $this;
    }

    /**
     * @return Collection<int, Genre>
     */
    public function getGenre(): Collection
    {
        return $this->genre;
    }

    public function addGenre(Genre $genre): static
    {
        if (!$this->genre->contains($genre)) {
            $this->genre->add($genre);
        }

        return $this;
    }

    public function removeGenre(Genre $genre): static
    {
        $this->genre->removeElement($genre);

        return $this;
    }

    public function getCoverBook(): ?Picture
    {
        return $this->coverBook;
    }

    public function setCoverBook(?Picture $coverBook): static
    {
        $this->coverBook = $coverBook;

        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): static
    {
        $this->author = $author;

        return $this;
    }
}
