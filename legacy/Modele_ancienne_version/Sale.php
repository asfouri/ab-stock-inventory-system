<?php

require_once __DIR__ . '/SaleItem.php';

class Sale {
    private ?int   $id;
    private int    $vendeurId;
    private string $dateVente;
    private float  $totalHT;
    private float  $totalTTC;
    private array  $items;       // SaleItem[]

    public function __construct(
        int    $vendeurId,
        string $dateVente,
        float  $totalHT,
        float  $totalTTC,
        array  $items = [],
        ?int   $id    = null
    ) {
        $this->vendeurId = $vendeurId;
        $this->dateVente = $dateVente;
        $this->totalHT   = $totalHT;
        $this->totalTTC  = $totalTTC;
        $this->items     = $items;
        $this->id        = $id;
    }

    // --- Getters ---

    public function getId(): ?int {
        return $this->id;
    }

    public function getVendeurId(): int {
        return $this->vendeurId;
    }

    public function getDateVente(): string {
        return $this->dateVente;
    }

    public function getTotalHT(): float {
        return $this->totalHT;
    }

    public function getTotalTTC(): float {
        return $this->totalTTC;
    }

    public function getItems(): array {
        return $this->items;
    }

    // --- Setters ---

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setVendeurId(int $vendeurId): void {
        $this->vendeurId = $vendeurId;
    }

    public function setDateVente(string $dateVente): void {
        $this->dateVente = $dateVente;
    }

    public function setTotalHT(float $totalHT): void {
        $this->totalHT = $totalHT;
    }

    public function setTotalTTC(float $totalTTC): void {
        $this->totalTTC = $totalTTC;
    }

    public function setItems(array $items): void {
        $this->items = $items;
    }

    public function addItem(SaleItem $item): void {
        $this->items[] = $item;
    }
}
