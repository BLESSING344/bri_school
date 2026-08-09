<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\SettingModel;

class SettingsController extends Controller
{
    private const FIELDS = [
        'school_name',
        'school_address',
        'school_phone',
        'school_email',
        'current_term',
        'current_academic_year',
    ];

    public function index(): void
    {
        $this->requireRole('admin');

        $settings = new SettingModel();

        if ($this->isPost()) {
            $this->handlePost($settings);
        }

        $this->render('settings/index', [
            'page_title' => 'Settings',
            'settings' => $settings->allAsMap(),
        ]);
    }

    private function handlePost(SettingModel $settings): void
    {
        if ($this->input('action') !== 'update_settings') {
            return;
        }

        foreach (self::FIELDS as $field) {
            $settings->setValue($field, trim((string) $this->input($field, '')));
        }

        $this->redirect('/dashboard/settings?success=' . rawurlencode('Settings updated successfully'));
    }
}
