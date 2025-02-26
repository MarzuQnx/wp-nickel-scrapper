<?php
/**
 * Plugin Name: Nickel Scrapper
 * Plugin URI: https://www.tekindoenergi.co.id
 * Description: Scrapped from HPM (Harga Patokan Mineral) Kementrian Energi dan Sumber Daya Alam
 * Version: 2.9
 * Text Domain: nickel-scrapper
 * Author: dbanie
 * Author URI: https://www.tekindoenergi.co.id
 */

// Include Composer autoload
require_once plugin_dir_path(__FILE__) . 'includes/Scraper.php';
require_once plugin_dir_path(__FILE__) . 'includes/Shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'includes/Admin.php'; // Tambahkan baris ini

// Instantiate Scraper, Shortcodes, and Admin
$scraper = new Scraper();
$shortcodes = new Shortcodes($scraper);
$admin = new Admin($scraper); // Tambahkan baris ini

// Schedule daily scraping
function nickel_scrapper_schedule_scraping() {
    if (!wp_next_scheduled('nickel_scrapper_daily_scraping')) {
        wp_schedule_event(time(), 'daily', 'nickel_scrapper_daily_scraping');
    }
}
add_action('wp', 'nickel_scrapper_schedule_scraping');

// Daily scraping function
function nickel_scrapper_daily_scraping() {
    global $scraper; // Make $scraper instance available
    $scraper->init(); // Trigger scraping and cache update
}
add_action('nickel_scrapper_daily_scraping', 'nickel_scrapper_daily_scraping');

// Deactivation hook to clear the scheduled event
function nickel_scrapper_deactivation() {
    wp_clear_scheduled_hook('nickel_scrapper_daily_scraping');
}
register_deactivation_hook(__FILE__, 'nickel_scrapper_deactivation');
