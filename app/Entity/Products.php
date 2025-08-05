<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'products')]
class Products
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Название продукта обязательно")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Название должно содержать минимум {{ limit }} символа",
        maxMessage: "Название не может превышать {{ limit }} символов"
    )]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(
        max: 2000,
        maxMessage: "Описание не может превышать {{ limit }} символов"
    )]
    private ?string $description = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank(message: "Цена обязательна")]
    #[Assert\Type(
        type: 'numeric',
        message: "Цена должна быть числом"
    )]
    #[Assert\PositiveOrZero(message: "Цена не может быть отрицательной")]
    #[Assert\LessThanOrEqual(
        value: 1000000,
        message: "Цена не может превышать {{ value }}"
    )]
    private float $price;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Assert\Length(
        max: 100,
        maxMessage: "Название категории не может превышать {{ limit }} символов"
    )]
    private ?string $category = null;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    #[Assert\Type(
        type: 'integer',
        message: "Количество должно быть целым числом"
    )]
    #[Assert\PositiveOrZero(message: "Количество не может быть отрицательным")]
    private int $availability = 0;

    #[ORM\Column(name: 'created_at')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at')]
    private DateTime $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTime();
    }

    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->updatedAt = new DateTime();
    }

    // Геттеры и сеттеры
    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getAvailability(): int
    {
        return $this->availability;
    }

    public function setAvailability(int $availability): self
    {
        $this->availability = $availability;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }
}