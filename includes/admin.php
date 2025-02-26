<?php
// includes/Admin.php

class Admin {
    private $nickelFile;
    private $kursFile;

    public function __construct() {
        $this->nickelFile = plugin_dir_path(dirname(__FILE__)) . 'htmlString.nickel';
        $this->kursFile = plugin_dir_path(dirname(__FILE__)) . 'htmlString.kurs';
        add_action('admin_menu', [$this, 'addAdminMenu']);
    }

    public function addAdminMenu() {
        add_menu_page(
            'Nickel Scrapper',
            'Nickel Scrapper',
            'manage_options',
            'nickel-scrapper-admin',
            [$this, 'adminPage'],
            'dashicons-chart-line'
        );
    }

    public function adminPage() {
        $nickelContent = '';
        $kursContent = '';

        if (file_exists($this->nickelFile)) {
            $nickelContent = file_get_contents($this->nickelFile);
        }

        if (file_exists($this->kursFile)) {
            $kursContent = file_get_contents($this->kursFile);
        }

        ?>
        <div class="wrap">
            <h1>Nickel Scrapper Data Files</h1>
            <div style="display: flex;">
                <div style="width: 50%; padding-right: 20px;">
                    <h2>htmlString.nickel</h2>
                    <textarea style="width: 100%; height: 400px;"><?php echo esc_textarea($nickelContent); ?></textarea>
                </div>
                <div style="width: 50%; padding-left: 20px;">
                    <h2>htmlString.kurs</h2>
                    <textarea style="width: 100%; height: 400px;"><?php echo esc_textarea($kursContent); ?></textarea>
                </div>
            </div>
        </div>
        <?php
    }
}
