<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'orders')]
class Orders
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: ProdOrders::class)]
    #[ORM\JoinColumn(name: 'prod_order_id', referencedColumnName: 'id', nullable: false)]
    private ProdOrders $prod_order_id;

    #[ORM\ManyToOne(targetEntity: Products::class)]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: false)]
    private Products $product;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    #[Assert\Type(
        type: 'integer',
        message: "Количество должно быть целым числом"
    )]
    #[Assert\PositiveOrZero(message: "Количество не может быть отрицательным")]
    private int $quantity = 0;

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
    public function getId(): int|null
    {
        return $this->id;
    }

    public function getProdOrderId(): ProdOrders
    {
        return $this->prod_order_id;
    }

    public function setProdOrders(ProdOrders $prod_order_id): self
    {
        $this->prod_order_id = $prod_order_id;
        return $this;
    }

    public function getProduct(): Products
    {
        return $this->product;
    }

    public function setProduct(Products $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
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