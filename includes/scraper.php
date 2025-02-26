<?php
// includes/Scraper.php
require_once plugin_dir_path(__FILE__) . '../vendor/autoload.php';

class Scraper {
    private $scrapedData = [];
    private $cacheKey = 'scraped_data';
    private $cacheExpiry = 3600; // 1 jam
    private $nickelFile;
    private $kursFile;

    public function __construct() {
        $this->nickelFile = plugin_dir_path(__FILE__) . 'htmlString.nickel';
        $this->kursFile = plugin_dir_path(__FILE__) . 'htmlString.kurs';
        add_action('init', [$this, 'init']);
    }

    public function init() {
        $this->scrapedData = get_transient($this->cacheKey);
        if (false === $this->scrapedData) {
            $this->scrapeData();
            set_transient($this->cacheKey, $this->scrapedData, $this->cacheExpiry);
        }
    }

    private function scrapeData() {
        $client_nickel = new GuzzleHttp\Client(['base_uri' => 'https://www.minerba.esdm.go.id/']);
        $client_kurs = new GuzzleHttp\Client(['base_uri' => 'https://www.bi.go.id/']);

        try {
            $nickelData = $this->fetchAndParseNickel($client_nickel, '/harga_acuan', $this->nickelFile);
            $kursData = $this->fetchAndParseKurs($client_kurs, $this->kursFile);

            if (empty($nickelData) || empty($kursData)) {
                error_log('Scraper: Failed to fetch data from websites or cache files are invalid.');
                return;
            }

            $this->scrapedData = [
                'nickel_theads' => $nickelData[0],
                'nickel_tbodys' => $nickelData[1],
                'kurs_update' => $kursData[0],
                'kurs_theads' => $kursData[1],
                'kurs_tbodys' => $kursData[2],
            ];

            delete_transient($this->cacheKey);
        } catch (GuzzleHttp\Exception\RequestException $e) {
            error_log('Scraper: Request Exception - ' . $e->getMessage());
        } catch (Exception $e) {
            error_log('Scraper: General Exception - ' . $e->getMessage());
        }
    }

    private function fetchAndParseNickel(GuzzleHttp\Client $client, string $url, string $cacheFile = null): array {
        try {
            $response = $client->request('GET', $url, ['http_errors' => false]);
            //error_log('Nickel Response Status: ' . $response->getStatusCode());
            $htmlString = (string) $response->getBody();
            //error_log('Nickel htmlString: ' . $htmlString);
            if ($cacheFile) {
                $this->ensureFileExists($cacheFile);
                file_put_contents($cacheFile, $htmlString);
            }
        } catch (GuzzleHttp\Exception\RequestException $e) {
            error_log('Nickel Scraper: Request Exception - ' . $e->getMessage());
            if ($cacheFile && file_exists($cacheFile) && filesize($cacheFile) > 0) {
                $htmlString = file_get_contents($cacheFile);
                error_log('Nickel Scraper: Using cache file ' . $cacheFile . ' due to request exception.');
            } else {
                return [[], []];
            }
        }

        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML($htmlString);
        $xpath = new DOMXPath($doc);

        $results = [[], []];

        // Extract headers
        $headerElements = $xpath->query('//table[@class="table border-table"]//thead//tr//th');
        error_log('Nickel Header Elements: ' . $headerElements->length);
        foreach ($headerElements as $element) {
            $results[0][] = trim($element->textContent);
        }

        // Extract data rows
        $dataRows = $xpath->query('//table[@class="table border-table"]//tbody//tr');
        error_log('Nickel Data Rows: ' . $dataRows->length);
        foreach ($dataRows as $row) {
            $rowData = [];
            $dataCells = $xpath->query('.//td', $row);
            foreach ($dataCells as $cell) {
                $rowData[] = trim($cell->textContent);
            }
            $results[1][$rowData[0]] = array_slice($rowData, 1); // Use commodity as key
        }

        return $results;
    }

    private function fetchAndParseKurs(GuzzleHttp\Client $client, string $cacheFile = null): array {
        try {
            $response = $client->request('GET', '/id/statistik/informasi-kurs/transaksi-bi/default.aspx', ['http_errors' => false]);
            error_log('Kurs Response Status: ' . $response->getStatusCode());
            $htmlString = (string) $response->getBody();
            error_log('Kurs htmlString: ' . $htmlString);
            if ($cacheFile) {
                $this->ensureFileExists($cacheFile);
                file_put_contents($cacheFile, $htmlString);
            }
        } catch (GuzzleHttp\Exception\RequestException $e) {
            error_log('Kurs Scraper: Request Exception - ' . $e->getMessage());
            if ($cacheFile && file_exists($cacheFile) && filesize($cacheFile) > 0) {
                $htmlString = file_get_contents($cacheFile);
                error_log('Kurs Scraper: Using cache file ' . $cacheFile . ' due to request exception.');
            } else {
                return [[], [], []];
            }
        }

        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML($htmlString);
        $xpath = new DOMXPath($doc);

        $results = [[], [], []];

        // Extract update date
        $updateDateElement = $xpath->query('//div[@class="search-box-wrapper text-left"]/span');
        error_log('Kurs Update Date Elements: ' . $updateDateElement->length);
        if ($updateDateElement->length > 0) {
            $results[0][] = trim($updateDateElement->item(0)->textContent);
        }

        // Extract headers
        $headerElements = $xpath->query('//table[@class="table table-striped table-no-bordered table-lg"]//thead//tr//th');
        error_log('Kurs Header Elements: ' . $headerElements->length);
        foreach ($headerElements as $element) {
            $results[1][] = trim($element->textContent);
        }

        // Extract data rows
        $dataRows = $xpath->query('//table[@class="table table-striped table-no-bordered table-lg"]//tbody//tr');
        error_log('Kurs Data Rows: ' . $dataRows->length);
        foreach ($dataRows as $row) {
            $rowData = [];
            $dataCells = $xpath->query('.//td', $row);
            foreach ($dataCells as $cell) {
                $rowData[] = trim($cell->textContent);
            }
            $results[2][] = $rowData;
        }

        return $results;
    }

    private function ensureFileExists($filePath) {
        if (!file_exists($filePath)) {
            touch($filePath);
        }
    }

    public function getScrapedData() {
        return $this->scrapedData;
    }
}
