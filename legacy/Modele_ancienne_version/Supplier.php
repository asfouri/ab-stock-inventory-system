<?php

class Supplier {
    private ?int   $id;
    private string $nom;
    private string $email;
    private string $telephone;
    private string $adresse;

    public function __construct(
        string $nom,
        string $email     = '',
        string $telephone = '',
        string $adresse   = '',
        ?int   $id        = null
    ) {
        $this->nom       = $nom;
        $this->email     = $email;
        $this->telephone = $telephone;
        $this->adresse   = $adresse;
        $this->id        = $id;
    }

    // --- Getters ---

    public function getId(): ?int {
        return $this->id;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getTelephone(): string {
        return $this->telephone;
    }

    public function getAdresse(): string {
        return $this->adresse;
    }

    // --- Setters ---

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setNom(string $nom): void {
        $this->nom = $nom;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function setTelephone(string $telephone): void {
        $this->telephone = $telephone;
    }

    public function setAdresse(string $adresse): void {
        $this->adresse = $adresse;
    }
}
