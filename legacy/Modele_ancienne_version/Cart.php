<?php

require_once __DIR__ . '/CartItem.php';

class Cart {
    private array $items = [];   // CartItem[]
    private float $tauxTva;      // e.g. 0.20 for 20%

    public function __construct(float $tauxTva = 0.20) {
        $this->tauxTva = $tauxTva;
    }

    // --- Getters ---

    public function getItems(): array {
        return $this->items;
    }

    public function getTauxTva(): float {
        return $this->tauxTva;
    }

    // --- Setters ---

    public function setTauxTva(float $tauxTva): void {
        $this->tauxTva = $tauxTva;
    }

    // --- Cart operations ---

    public function addItem(CartItem $item): void {
        $id = $item->getProductId();
        if (isset($this->items[$id])) {
            $existing = $this->items[$id];
            $existing->setQuantite($existing->getQuantite() + $item->getQuantite());
        } else {
            $this->items[$id] = $item;
        }
    }

    public function removeItem(int $productId): void {
        unset($this->items[$productId]);
    }

    public function clear(): void {
        $this->items = [];
    }

    public function getTotalHT(): float {
        $total = 0.0;
        foreach ($this->items as $item) {
            $total += $item->getSousTotal();
        }
        return $total;
    }

    public function getTotalTTC(): float {
        return $this->getTotalHT() * (1 + $this->tauxTva);
    }

    public function isEmpty(): bool {
        return empty($this->items);
    }
}
