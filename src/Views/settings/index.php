<div class="row column_title">
    <div class="col-md-12">
        <div class="page_title">
            <h2>Settings</h2>
            <p class="text-muted">School information shown across the system, and the current academic period.</p>
        </div>
    </div>
</div>

<form method="POST" action="/dashboard/settings">
    <input type="hidden" name="action" value="update_settings">

    <div class="row">
        <div class="col-md-6">
            <div class="white_shd full margin_bottom_30">
                <div class="full graph_head">
                    <div class="heading1 margin_0">
                        <h2>School Information</h2>
                    </div>
                </div>
                <div class="table_section padding_infor_info">
                    <div class="form-group">
                        <label>School Name</label>
                        <input type="text" class="form-control" name="school_name" value="<?php echo htmlspecialchars($settings['school_name'] ?? ''); ?>" placeholder="BRI International School">
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" class="form-control" name="school_address" value="<?php echo htmlspecialchars($settings['school_address'] ?? ''); ?>" placeholder="e.g. Kampala, Uganda">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" class="form-control" name="school_phone" value="<?php echo htmlspecialchars($settings['school_phone'] ?? ''); ?>">
                    </div>
                    <div class="form-group mb-0">
                        <label>Email</label>
                        <input type="email" class="form-control" name="school_email" value="<?php echo htmlspecialchars($settings['school_email'] ?? ''); ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="white_shd full margin_bottom_30">
                <div class="full graph_head">
                    <div class="heading1 margin_0">
                        <h2>Academic Period</h2>
                    </div>
                </div>
                <div class="table_section padding_infor_info">
                    <div class="form-group">
                        <label>Current Term</label>
                        <select class="form-control" name="current_term">
                            <option value="">Not set</option>
                            <?php foreach (['Term 1', 'Term 2', 'Term 3'] as $term): ?>
                                <option value="<?php echo $term; ?>" <?php echo ($settings['current_term'] ?? '') === $term ? 'selected' : ''; ?>><?php echo $term; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Used as the default term where relevant across the system.</small>
                    </div>
                    <div class="form-group mb-0">
                        <label>Current Academic Year</label>
                        <input type="text" class="form-control" name="current_academic_year" value="<?php echo htmlspecialchars($settings['current_academic_year'] ?? date('Y')); ?>" placeholder="<?php echo date('Y'); ?>">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> Save Settings
            </button>
        </div>
    </div>
</form>
