<?php

namespace App\Controllers;

use App\Core\Controller;

/**
 * The original dashboard/settings.php was unmodified admin-template demo
 * markup (no PHP tags, no auth check, no database access, no forms) — it
 * only rendered static/decorative widgets (a fake message list, a calendar
 * placeholder, fake social stats, UI-kit tab/button/alert/modal/tooltip/
 * dropdown showcases). There was no settings business logic to preserve.
 *
 * Since this is the "Settings" page, it is gated to admins only here,
 * matching the access level implied by the rest of the admin dashboard
 * (see app/Views/layout/header.php, where the "Administration" nav section
 * — currently just User Management — is admin-only). This is a judgment
 * call, not a literal port of an original check, because none existed.
 */
class SettingsController extends Controller
{
    public function index(): void
    {
        $this->requireRole('admin');

        $this->render('settings/index', [
            'page_title' => 'Settings',
        ]);
    }
}
