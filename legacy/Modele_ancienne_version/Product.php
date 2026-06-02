<?php

class Product {
    private ?int   $id;
    private string $nom;
    private string $description;
    private float  $prixAchat;
    private float  $prixVente;
    private int    $quantiteStock;
    private int    $seuilAlerte;    // alert threshold for low stock
    private int    $categoryId;
    private int    $supplierId;

    public function __construct(
        string $nom,
        string $description,
        float  $prixAchat,
        float  $prixVente,
        int    $quantiteStock,
        int    $seuilAlerte,
        int    $categoryId,
        int    $supplierId,
        ?int   $id = null
    ) {
        $this->nom           = $nom;
        $this->description   = $description;
        $this->prixAchat     = $prixAchat;
        $this->prixVente     = $prixVente;
        $this->quantiteStock = $quantiteStock;
        $this->seuilAlerte   = $seuilAlerte;
        $this->categoryId    = $categoryId;
        $this->supplierId    = $supplierId;
        $this->id            = $id;
    }

    // --- Getters ---

    public function getId(): ?int {
        return $this->id;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getPrixAchat(): float {
        return $this->prixAchat;
    }

    public function getPrixVente(): float {
        return $this->prixVente;
    }

    public function getQuantiteStock(): int {
        return $this->quantiteStock;
    }

    public function getSeuilAlerte(): int {
        return $this->seuilAlerte;
    }

    public function getCategoryId(): int {
        return $this->categoryId;
    }

    public function getSupplierId(): int {
        return $this->supplierId;
    }

    // --- Setters ---

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setNom(string $nom): void {
        $this->nom = $nom;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    public function setPrixAchat(float $prixAchat): void {
        $this->prixAchat = $prixAchat;
    }

    public function setPrixVente(float $prixVente): void {
        $this->prixVente = $prixVente;
    }

    public function setQuantiteStock(int $quantiteStock): void {
        $this->quantiteStock = $quantiteStock;
    }

    public function setSeuilAlerte(int $seuilAlerte): void {
        $this->seuilAlerte = $seuilAlerte;
    }

    public function setCategoryId(int $categoryId): void {
        $this->categoryId = $categoryId;
    }

    public function setSupplierId(int $supplierId): void {
        $this->supplierId = $supplierId;
    }

    // --- Helpers ---

    public function isLowStock(): bool {
        return $this->quantiteStock <= $this->seuilAlerte;
    }

    public function decrementStock(int $qty): void {
        $this->quantiteStock -= $qty;
    }
}
