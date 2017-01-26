<?php

namespace Alveum\DDEX;

use Alveum\DDEX\Exceptions\InvalidCredentialsException;
use Alveum\DDEX\Resources\Party;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

class DDEX {

    /** @var string */
    private $dpid_url;

    /** @var Client */
    private $guzzle;

    /** @var CookieJar */
    private $cookieJar;

    /**
     * DDEX constructor.
     *
     * @param $username
     * @param $password
     * @param string $dpid_url
     */
    public function __construct($username, $password, $dpid_url = 'http://dpid.ddex.net/')
    {
        $this->dpid_url = $dpid_url;

        $this->cookieJar = new CookieJar();
        $this->initializeGuzzle();
        $this->authenticateDdex($username, $password);
    }

    /**
     * Get Party by DDEX DPID.
     *
     * @param $dpid
     * @return Party|bool
     */
    public function getPartyById($dpid)
    {
        $searchRequest = $this->guzzle->post('search.php', [
            'form_params' => [
                'find' => $dpid,
                'selectField' => 'DPID',
                'search' => 'Search',
            ]
        ]);

        $content = $searchRequest->getBody()->getContents();

        if(strpos($content, "did not match any records") !== false) {
            return false;
        }

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($content);
        $row = $dom->getElementsByTagName('table')->item(0);

        return new Party($row, $row);
    }

    /**
     * Initialize Guzzle client.
     */
    private function initializeGuzzle()
    {
        $this->guzzle = new Client([
            'base_uri' => $this->dpid_url,
            'cookies' => $this->cookieJar,
        ]);
    }

    /**
     * Authenticate DDEX on Guzzle client.
     *
     * @param $username
     * @param $password
     * @return bool
     * @throws InvalidCredentialsException
     */
    public function authenticateDdex($username, $password)
    {
        $loginRequest = $this->guzzle->post('login.php', [
            'form_params' => [
                'username' => $username,
                'password' => $password,
                'submit' => 'Log In',
            ]
        ]);

        $content = $loginRequest->getBody()->getContents();

        if(strpos($content, "<p class='error'>") !== false) {
            throw new InvalidCredentialsException();

            return false;
        }

        if(strpos($content, "DDEX Party Registry") !== false) {
            // Probably companies.php, logged in successfully.
            return true;
        }

        return false; // Unknown state.
    }

    /**
     * Get DPID URL.
     *
     * @return string
     */
    public function getDpidUrl()
    {
        return $this->dpid_url;
    }

    /**
     * Set DPID URL.
     *
     * @param string $dpid_url
     */
    public function setDpidUrl($dpid_url)
    {
        $this->dpid_url = $dpid_url;
        $this->initializeGuzzle();
    }
}
