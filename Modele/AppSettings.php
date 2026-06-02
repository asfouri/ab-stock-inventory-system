<?php
// Modele/AppSettings.php

class AppSettings extends BaseModel {

    public function getAll(): array {
        try {
            $stmt = $this->db->query("SELECT cle, valeur FROM parametres_app");
            $settings = [];
            foreach ($stmt->fetchAll() as $row) {
                $settings[$row['cle']] = $row['valeur'];
            }
            return $settings;
        } catch (PDOException) {
            return [];
        }
    }

    public function updateMany(array $settings): bool {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO parametres_app (cle, valeur) VALUES (?, ?)
                 ON DUPLICATE KEY UPDATE valeur = VALUES(valeur)"
            );

            $this->db->beginTransaction();
            foreach ($settings as $key => $value) {
                $stmt->execute([$key, (string) $value]);
            }
            $this->db->commit();
            return true;
        } catch (PDOException) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }
}
