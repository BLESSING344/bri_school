<?php

namespace App\Models;

use App\Core\Model;

/**
 * Wraps the `settings` key/value table defined in database/schema.sql:
 *
 *   CREATE TABLE IF NOT EXISTS settings (
 *       id SERIAL PRIMARY KEY,
 *       setting_key VARCHAR(100) NOT NULL UNIQUE,
 *       setting_value TEXT,
 *       updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
 *   );
 *
 * Backs the school info / academic period form in SettingsController --
 * the original dashboard/settings.php contained no PHP at all (it was
 * unmodified admin-template demo markup), so this is new functionality
 * rather than a port of prior behavior.
 */
class SettingModel extends Model
{
    protected string $table = 'settings';

    /**
     * Return all settings as an associative array [setting_key => setting_value].
     */
    public function allAsMap(): array
    {
        $rows = $this->query('SELECT setting_key, setting_value FROM settings ORDER BY setting_key')->fetchAll();

        $map = [];
        foreach ($rows as $row) {
            $map[$row['setting_key']] = $row['setting_value'];
        }

        return $map;
    }

    public function getValue(string $key, $default = null)
    {
        $stmt = $this->query('SELECT setting_value FROM settings WHERE setting_key = ?', [$key]);
        $row = $stmt->fetch();

        return $row ? $row['setting_value'] : $default;
    }

    /**
     * Upsert a setting. Postgres allows ON CONFLICT, but per project convention
     * (avoid MySQL-only ON DUPLICATE KEY UPDATE) this uses a SELECT-then-
     * INSERT-or-UPDATE pattern.
     */
    public function setValue(string $key, ?string $value): bool
    {
        $stmt = $this->query('SELECT id FROM settings WHERE setting_key = ?', [$key]);
        $existing = $stmt->fetch();

        if ($existing) {
            $update = $this->query(
                'UPDATE settings SET setting_value = ?, updated_at = CURRENT_TIMESTAMP WHERE setting_key = ?',
                [$value, $key]
            );
            return $update->rowCount() >= 0;
        }

        $insert = $this->query(
            'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)',
            [$key, $value]
        );

        return $insert->rowCount() > 0;
    }
}
