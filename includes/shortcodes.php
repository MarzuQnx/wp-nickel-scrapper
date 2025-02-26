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
        $atts = shortcode_atts(['mineral' => '#'], $atts);
        $mineral = sanitize_text_field($atts['mineral']);
        $scrapedData = $this->scraper->getScrapedData();
        if (isset($scrapedData['nickel_tbodys'][$mineral])) {
            return (double) $scrapedData['nickel_tbodys'][$mineral];
        }
        return '';
    }

    public function nickelMonth() {
        $scrapedData = $this->scraper->getScrapedData();
        if (!empty($scrapedData['nickel_theads'])) {
            return end($scrapedData['nickel_theads']);
        }
        return '';
    }

    public function kursMata($atts) {
        $atts = shortcode_atts(['kode' => '0'], $atts);
        $kode = sanitize_text_field($atts['kode']);
        $scrapedData = $this->scraper->getScrapedData();
        if (isset($scrapedData['kurs_mu'][$kode])) {
            return $scrapedData['kurs_mu'][$kode];
        }
        return '';
    }

    public function kursNilai($atts) {
        $atts = shortcode_atts(['kode' => '0'], $atts);
        $kode = sanitize_text_field($atts['kode']);
        $scrapedData = $this->scraper->getScrapedData();
        if (isset($scrapedData['kurs_nu'][$kode])) {
            $html = str_replace(".", "", $scrapedData['kurs_nu'][$kode]);
            return (double) $html;
        }
        return '';
    }

    public function kursUpdate() {
        $scrapedData = $this->scraper->getScrapedData();
        if (!empty($scrapedData['kurs_tu'])) {
            return substr($scrapedData['kurs_tu'][0], 0, -5);
        }
        return '';
    }
}
