<?php
# scraping Table to scrape: https://www.minerba.esdm.go.id/harga_acuan
require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

$client_nickel = new GuzzleHttp\Client(['base_uri' => 'https://www.minerba.esdm.go.id/']);
$client_kurs = new GuzzleHttp\Client(['base_uri' => 'https://www.bi.go.id/id/statistik/informasi-kurs/transaksi-bi/default.aspx']);

try {
    $res = $client_nickel->request('GET', '/harga_acuan', ['http_errors' => false]);
    if (200 == $res->getStatusCode()) {
        libxml_use_internal_errors(true);
        $htmlString = (string) $res->getBody();
        file_put_contents('htmlString.nickel', $htmlString);
        $doc = new DOMDocument();
        $doc->loadHTML($htmlString);
        $xpath = new DOMXPath($doc);
    }
} catch (\Exception $e) {
    //echo 'Mak Errot: ' . $e->getMessage();
    libxml_use_internal_errors(true);
    $htmlString = file_get_contents('htmlString.nickel');
    $doc = new DOMDocument();
    $doc->loadHTML($htmlString);
    $xpath = new DOMXPath($doc);
}

try {
    $res = $client_kurs->request('GET', '', ['http_errors' => false]);
    if (200 == $res->getStatusCode()) {
        libxml_use_internal_errors(true);
        $htmlString = (string) $res->getBody();
        file_put_contents('htmlString.kurs', $htmlString);
        $doc = new DOMDocument();
        $doc->loadHTML($htmlString);
        $xpath_kurs = new DOMXPath($doc);
    }
} catch (\Exception $e) {
    //echo 'Mak Errot: ' . $e->getMessage();
    libxml_use_internal_errors(true);
    $htmlString = file_get_contents('htmlString.kurs');
    $doc = new DOMDocument();
    $doc->loadHTML($htmlString);
    $xpath_kurs = new DOMXPath($doc);
}

$theads = $xpath->evaluate('//thead//tr//th');
$tbodys = $xpath->evaluate('//tbody//tr//td');

$mata_uang_kurs = $xpath_kurs->evaluate('//table[@class="table table-striped table-no-bordered table-md"]//tbody//tr//td');
$nilai_uang_kurs = $xpath_kurs->evaluate('//table[@class="table table-striped table-no-bordered table-lg"]//tbody//tr//td');
$text_update_kurs = $xpath_kurs->evaluate('//div[@class="search-box-wrapper text-left"]//span');

$extractedTheads = [];
$extractedTbodys = [];

$extractedMU_kurs = [];
$extractedNU_kurs = [];
$extractedTU_kurs = [];

foreach ($theads as $thead) {
    $extractedTheads[] = $thead->textContent . PHP_EOL;
}

foreach ($tbodys as $tbody) {
    $extractedTbodys[] = $tbody->textContent . PHP_EOL;
}

foreach ($mata_uang_kurs as $mata_uang_kurses) {
    $extractedMU_kurs[] = $mata_uang_kurses->textContent . PHP_EOL;
}

foreach ($nilai_uang_kurs as $nilai_uang_kurses) {
    $extractedNU_kurs[] = $nilai_uang_kurses->textContent . PHP_EOL;
}

foreach ($text_update_kurs as $text_update_kurses) {
    $extractedTU_kurs[] = $text_update_kurses->textContent . PHP_EOL;
}

//echo end($extractedTheads);
