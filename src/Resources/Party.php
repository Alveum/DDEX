<?php

namespace Alveum\DDEX\Resources;

class Party
{
    /** @var string */
    public $DPID;

    /** @var string */
    public $DDEXPartyFullName;

    /** @var string */
    public $ContactAdmPostalAddressStreet;

    /** @var string */
    public $ContactAdmPostalAddressStreet1;

    /** @var string */
    public $ContactAdmPostalAddressCity;

    /** @var string */
    public $ContactAdmPostalAddressPostCode;

    /** @var string */
    public $ContactAdmPostalAddressTerritory;

    /**
     * Party constructor.
     *
     * @param \DOMElement $table
     */
    public function __construct(\DOMElement $table)
    {
        $this->transformDomElement($table);
    }

    /**
     * Transform DOM element in object.
     *
     * @param \DOMElement $table
     */
    private function transformDomElement(\DOMElement $table)
    {
        $headers = $this->determineHeaders($table);
        $items = $table->getElementsByTagName('td');

        $allowedKeys = array_keys(get_object_vars($this));

        foreach($headers as $header => $key) {
            if(! in_array($header, $allowedKeys)) {
                continue;
            }

            $this->{$header} = $items->item($key)->nodeValue;
        }
    }

    /**
     * Determine headers of table in DOM element.
     *
     * @param \DOMElement $headersElement
     * @return array
     */
    private function determineHeaders(\DOMElement $headersElement)
    {
        $headers = [];

        foreach($headersElement->getElementsByTagName('th') as $key => $header) {
            $headers[str_replace(' ', '', trim($header->nodeValue))] = $key;
        }

        return $headers;
    }
}