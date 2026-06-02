<?php
// Modele/Utilisateur.php

abstract class Utilisateur {
    protected int $id;
    protected string $nom;
    protected string $prenom;
    protected string $email;
    protected string $role;
    protected int $actif;

    public function __construct(array $data) {
        $this->id = (int) ($data['id'] ?? 0);
        $this->nom = trim((string) ($data['nom'] ?? ''));
        $this->prenom = trim((string) ($data['prenom'] ?? ''));
        $this->email = trim((string) ($data['email'] ?? ''));
        $this->role = trim((string) ($data['role'] ?? ''));
        $this->actif = (int) ($data['actif'] ?? 1);
    }

    public static function fromArray(array $data): self {
        return match ($data['role'] ?? '') {
            'admin' => new Admin($data),
            'vendeur' => new Vendeur($data),
            'fournisseur' => new Fournisseur($data),
            default => throw new InvalidArgumentException('Role utilisateur inconnu.'),
        };
    }

    public function getId(): int {
        return $this->id;
    }

    public function getNomComplet(): string {
        return trim($this->prenom . ' ' . $this->nom);
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getRole(): string {
        return $this->role;
    }

    public function isActif(): bool {
        return $this->actif === 1;
    }

    public function getClasse(): string {
        return static::class;
    }

    public function toSessionData(): array {
        return [
            'user_id' => $this->getId(),
            'user_nom' => $this->getNomComplet(),
            'user_role' => $this->getRole(),
            'user_class' => $this->getClasse(),
        ];
    }

    abstract public function getRoleLabel(): string;

    abstract public function canAccessCaisse(): bool;
}

class Admin extends Utilisateur {
    public function getRoleLabel(): string {
        return 'Administrateur';
    }

    public function canAccessCaisse(): bool {
        return true;
    }
}

class Vendeur extends Utilisateur {
    public function getRoleLabel(): string {
        return 'Vendeur';
    }

    public function canAccessCaisse(): bool {
        return true;
    }
}

class Fournisseur extends Utilisateur {
    public function getRoleLabel(): string {
        return 'Fournisseur';
    }

    public function canAccessCaisse(): bool {
        return false;
    }
}
