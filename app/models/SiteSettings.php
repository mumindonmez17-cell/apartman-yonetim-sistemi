<?php

class SiteSettings extends Model {
    public function getSettings() {
        return $this->db->query("SELECT * FROM site_settings WHERE id = 1")->fetch();
    }

    public function updateSiteName($siteName) {
        $stmt = $this->db->prepare("UPDATE site_settings SET site_name = ? WHERE id = 1");
        return $stmt->execute([$siteName]);
    }
}
