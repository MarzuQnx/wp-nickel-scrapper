<?php
// includes/Shortcodes.php

class Shortcodes {
    private $scraper;

    public function __construct(Scraper $scraper) {
        $this->scraper = $scraper;
        add_shortcode('dbanie-nickel-code', [$this, 'nickelCode']);
        add_shortcode('dbanie-nickel-month', [$this, 'nickelMonth']);
        add_shortcode('dbanie-kurs-mata', [$this, 'kursMata']);
        add_shortcode('dbanie-kurs-nilai', [$this, 'kursNilai']);
        add_shortcode('dbanie-kurs-update', [$this, 'kursUpdate']);
    }

    public function nickelCode($atts) {
        $atts = shortcode_atts(['mineral' => 'Nikel <small>(USD/dmt)</small>'], $atts);
        $mineral = sanitize_text_field($atts['mineral']);
        $scrapedData = $this->scraper->getScrapedData();
        if (isset($scrapedData['nickel_tbodys'][$mineral])) {
            $values = $scrapedData['nickel_tbodys'][$mineral];
            return esc_html(end($values));
        }
        return 'Data nikel tidak ditemukan.';
    }

    public function nickelMonth() {
        $scrapedData = $this->scraper->getScrapedData();
        if (!empty($scrapedData['nickel_theads'])) {
            return esc_html(end($scrapedData['nickel_theads']));
        }
        return '';
    }

    public function kursMata($atts) {
        $atts = shortcode_atts(['kode' => '0'], $atts);
        $kode = intval($atts['kode']);
        $scrapedData = $this->scraper->getScrapedData();
        if (isset($scrapedData['kurs_tbodys'][$kode][0])) {
            return esc_html($scrapedData['kurs_tbodys'][$kode][0]);
        }
        return '';
    }

    public function kursNilai($atts) {
        $atts = shortcode_atts(['kode' => '0'], $atts);
        $kode = intval($atts['kode']);
        $scrapedData = $this->scraper->getScrapedData();
        if (isset($scrapedData['kurs_tbodys'][$kode][1])) {
            $html = str_replace(".", "", $scrapedData['kurs_tbodys'][$kode][3]);
            return esc_html((double) $html);
        }
        return '';
    }

    public function kursUpdate() {
        $scrapedData = $this->scraper->getScrapedData();
        if (!empty($scrapedData['kurs_update'][0])) {
            return esc_html(substr($scrapedData['kurs_update'][0], 0, -5));
        }
        return '';
    }
}
