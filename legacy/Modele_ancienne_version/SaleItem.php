<?php

class SaleItem {
    private ?int  $id;
    private int   $saleId;
    private int   $productId;
    private string $productNom;   // snapshot at time of sale
    private int   $quantite;
    private float $prixUnitaire;  // snapshot at time of sale

    public function __construct(
        int    $saleId,
        int    $productId,
        string $productNom,
        int    $quantite,
        float  $prixUnitaire,
        ?int   $id = null
    ) {
        $this->saleId       = $saleId;
        $this->productId    = $productId;
        $this->productNom   = $productNom;
        $this->quantite     = $quantite;
        $this->prixUnitaire = $prixUnitaire;
        $this->id           = $id;
    }

    // --- Getters ---

    public function getId(): ?int {
        return $this->id;
    }

    public function getSaleId(): int {
        return $this->saleId;
    }

    public function getProductId(): int {
        return $this->productId;
    }

    public function getProductNom(): string {
        return $this->productNom;
    }

    public function getQuantite(): int {
        return $this->quantite;
    }

    public function getPrixUnitaire(): float {
        return $this->prixUnitaire;
    }

    // --- Setters ---

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setSaleId(int $saleId): void {
        $this->saleId = $saleId;
    }

    public function setProductId(int $productId): void {
        $this->productId = $productId;
    }

    public function setProductNom(string $productNom): void {
        $this->productNom = $productNom;
    }

    public function setQuantite(int $quantite): void {
        $this->quantite = $quantite;
    }

    public function setPrixUnitaire(float $prixUnitaire): void {
        $this->prixUnitaire = $prixUnitaire;
    }

    // --- Helpers ---

    public function getSousTotal(): float {
        return $this->prixUnitaire * $this->quantite;
    }
}
