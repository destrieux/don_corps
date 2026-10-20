-- Stockage des tokens d'accès aux flux iCalendar personnalisés.
-- Le token lui-même n'est jamais enregistré : seul son hash SHA-256
-- est conservé. Un token peut être activé ou révoqué individuellement.

CREATE TABLE IF NOT EXISTS civicrm_don_corps_ical_token (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    contact_id INT UNSIGNED NOT NULL,
    token_hash CHAR(64) NOT NULL,
    created_date DATETIME NOT NULL,
    last_access_date DATETIME NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,

    PRIMARY KEY (id),
    UNIQUE KEY token_hash (token_hash),
    KEY contact_id (contact_id),
    KEY is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;