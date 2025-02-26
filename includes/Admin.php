<?php
// includes/Admin.php

class Admin {
    private $scraper;

    public function __construct(Scraper $scraper) {
        $this->scraper = $scraper;
        add_action('admin_menu', [$this, 'add_admin_menu']);
    }

    public function add_admin_menu() {
        add_menu_page(
            'Nickel Scrapper Settings',
            'Nickel Scrapper',
            'manage_options',
            'nickel-scrapper-settings',
            [$this, 'settings_page']
        );
    }

    public function settings_page() {
        $scrapedData = $this->scraper->getScrapedData();
        ?>
        <div class="wrap">
            <h1>Nickel Scrapper Settings | dbanie &copy; 2025, Kekayaan Intelektual Karunia Gusti Allah SWT.</h1>

            <h2>Harga Acuan Mineral, Data by ESDM</h2>
            <?php if (!empty($scrapedData['nickel_theads']) && !empty($scrapedData['nickel_tbodys'])) : ?>
                <table class="wp-list-table widefat fixed striped table-view-list">
                    <thead>
                        <tr>
                            <?php foreach ($scrapedData['nickel_theads'] as $header) : ?>
                                <th style="font-weight: 600;"><?php echo esc_html($header); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($scrapedData['nickel_tbodys'] as $commodity => $values) : ?>
                            <tr>
                                <td><?php echo esc_html($commodity); ?></td>
                                <?php foreach ($values as $value) : ?>
                                    <td><?php echo esc_html($value); ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>Data nikel tidak tersedia.</p>
            <?php endif; ?>

            <h2>Kurs Nilai Tukar, Data by Bank Indonesia</h2>
            <?php if (!empty($scrapedData['kurs_theads']) && !empty($scrapedData['kurs_tbodys'])) : ?>
                <table class="wp-list-table widefat fixed striped table-view-list">
                    <thead>
                        <tr>
                            <?php foreach ($scrapedData['kurs_theads'] as $header) : ?>
                                <th style="font-weight: 600;"><?php echo esc_html($header); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($scrapedData['kurs_tbodys'] as $row) : ?>
                            <tr>
                                <?php foreach ($row as $cell) : ?>
                                    <td><?php echo esc_html($cell); ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>Data kurs tidak tersedia.</p>
            <?php endif; ?>

            <h2>Shortcode | Penggunaan, Cara Pakai</h2>
            <table class="wp-list-table widefat fixed striped table-view-list">
                <thead>
                    <tr>
                        <th style="font-weight: 600;">Shortcode</th>
                        <th style="font-weight: 600;">Description</th>
                        <th style="font-weight: 600;">Example</th>
                        <th style="font-weight: 600;">Value</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>`[dbanie-nickel-code mineral='Nikel <small>(USD/dmt)</small>']`</td>
                        <td>Get the latest Nickel value.</td>
                        <td>`[dbanie-nickel-code mineral='Nikel <small>(USD/dmt)</small>']`</td>
                        <td>
                            <?php
                            $nickelValue = do_shortcode("[dbanie-nickel-code mineral='Nikel <small>(USD/dmt)</small>']");
                            echo esc_html($nickelValue);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>`[dbanie-nickel-month]`</td>
                        <td>Get the latest Nickel month.</td>
                        <td>`[dbanie-nickel-month]`</td>
                        <td>
                            <?php
                            $nickelMonth = do_shortcode('[dbanie-nickel-month]');
                            echo esc_html($nickelMonth);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>`[dbanie-kurs-mata kode='0']`</td>
                        <td>Get kurs currency by code.</td>
                        <td>`[dbanie-kurs-mata kode='0']`</td>
                        <td>
                            <?php
                            $kursMata = do_shortcode("[dbanie-kurs-mata kode='0']");
                            echo esc_html($kursMata);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>`[dbanie-kurs-nilai kode='0']`</td>
                        <td>Get kurs value by code.</td>
                        <td>`[dbanie-kurs-nilai kode='0']`</td>
                        <td>
                            <?php
                            $kursNilai = do_shortcode("[dbanie-kurs-nilai kode='0']");
                            echo esc_html($kursNilai);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>`[dbanie-kurs-update]`</td>
                        <td>Get kurs update date.</td>
                        <td>`[dbanie-kurs-update]`</td>
                        <td>
                            <?php
                            $kursUpdate = do_shortcode('[dbanie-kurs-update]');
                            echo esc_html($kursUpdate);
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
    }
}
