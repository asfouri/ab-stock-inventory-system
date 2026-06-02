<?php

class Category {
    private ?int   $id;
    private string $nom;
    private string $description;

    public function __construct(
        string $nom,
        string $description = '',
        ?int   $id          = null
    ) {
        $this->nom         = $nom;
        $this->description = $description;
        $this->id          = $id;
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
}
