<?php

class CartItem {
    private int   $productId;
    private string $productNom;
    private float  $prixUnitaire;
    private int    $quantite;

    public function __construct(
        int    $productId,
        string $productNom,
        float  $prixUnitaire,
        int    $quantite
    ) {
        $this->productId    = $productId;
        $this->productNom   = $productNom;
        $this->prixUnitaire = $prixUnitaire;
        $this->quantite     = $quantite;
    }

    // --- Getters ---

    public function getProductId(): int {
        return $this->productId;
    }

    public function getProductNom(): string {
        return $this->productNom;
    }

    public function getPrixUnitaire(): float {
        return $this->prixUnitaire;
    }

    public function getQuantite(): int {
        return $this->quantite;
    }

    // --- Setters ---

    public function setProductId(int $productId): void {
        $this->productId = $productId;
    }

    public function setProductNom(string $productNom): void {
        $this->productNom = $productNom;
    }

    public function setPrixUnitaire(float $prixUnitaire): void {
        $this->prixUnitaire = $prixUnitaire;
    }

    public function setQuantite(int $quantite): void {
        $this->quantite = $quantite;
    }

    // --- Helpers ---

    public function getSousTotal(): float {
        return $this->prixUnitaire * $this->quantite;
    }
}
