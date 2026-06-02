<?php

class User {
    private ?int    $id;
    private string  $nom;
    private string  $prenom;
    private string  $email;
    private string  $motDePasse;
    private string  $role;       // 'admin' | 'vendeur' | 'fournisseur'
    private ?string $createdAt;

    public function __construct(
        string  $nom,
        string  $prenom,
        string  $email,
        string  $motDePasse,
        string  $role,
        ?int    $id        = null,
        ?string $createdAt = null
    ) {
        $this->nom        = $nom;
        $this->prenom     = $prenom;
        $this->email      = $email;
        $this->motDePasse = $motDePasse;
        $this->role       = $role;
        $this->id         = $id;
        $this->createdAt  = $createdAt;
    }

    // --- Getters ---

    public function getId(): ?int {
        return $this->id;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getPrenom(): string {
        return $this->prenom;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getMotDePasse(): string {
        return $this->motDePasse;
    }

    public function getRole(): string {
        return $this->role;
    }

    public function getCreatedAt(): ?string {
        return $this->createdAt;
    }

    // --- Setters ---

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setNom(string $nom): void {
        $this->nom = $nom;
    }

    public function setPrenom(string $prenom): void {
        $this->prenom = $prenom;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function setMotDePasse(string $motDePasse): void {
        $this->motDePasse = password_hash($motDePasse, PASSWORD_BCRYPT);
    }

    public function setRole(string $role): void {
        $this->role = $role;
    }

    public function setCreatedAt(string $createdAt): void {
        $this->createdAt = $createdAt;
    }

    // --- Helpers ---

    public function verifyPassword(string $plainPassword): bool {
        return password_verify($plainPassword, $this->motDePasse);
    }

    public function isAdmin(): bool {
        return $this->role === 'admin';
    }

    public function isVendeur(): bool {
        return $this->role === 'vendeur';
    }

    public function isFournisseur(): bool {
        return $this->role === 'fournisseur';
    }
}
