<?php
// Modele/BaseModel.php

abstract class BaseModel {
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }
}
