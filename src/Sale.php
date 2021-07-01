<?php
namespace khaller\fakturowniasdk;

use Collections\Vector;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use khaller\fakturowniasdk\models\SaleModel;

class Sale
{
    /**
     * @var $HTTPClient
     */
    private $HTTPClient;

    /**
     * @var $authToken
     */
    private $authToken;

    /**
     * Sale constructor.
     */
    function __construct($authToken)
    {
        $this->HTTPClient = new Client(["base_uri" => "https://app.fakturomania.pl/api/v1/"]);
        $this->authToken = $authToken;
    }

    /**
     * @param $invoiceData
     * @return SaleModel
     * @throws Exception
     */
    public function createInvoice($invoiceData): SaleModel
    {
        if(!isset($invoiceData))
            throw new Exception("[ Fakturomania SDK ] InvoiceData is required");

        try {
            $APIOptions = [
                "headers" => [
                    "Accept" => "application/json",
                    "Auth-Token" => $this->authToken,
                    "Content-Type" => "application/json"
                ],
                "json" => $invoiceData
            ];
            $APIRequest = $this->HTTPClient->request("POST", "sale", $APIOptions);
            $APIResponse = json_decode($APIRequest->getBody()->getContents(), true);
            print_r($APIResponse);
            return new SaleModel($APIResponse);
        } catch (GuzzleException $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     *
     * @throws Exception
     */
    public function getInvoices($from, $to, $sortDir, $sortPar): Vector
    {
        if(!isset($from) || !isset($to) || !isset($sortDir) || !isset($sortPar))
            throw new Exception("[ Fakturomania SDK ] InvoiceData is required");

        try {
            $coll = new Vector();
            $APIOptions = [
                "headers" => [
                    "Accept" => "application/json",
                    "Auth-Token" => $this->authToken,
                ],
                "query" => [
                    "fromMoment" => $from,
                    "toMoment" => $to,
                    "sortDirection" => $sortDir,
                    "sortParameter" => $sortPar
                ]
            ];
            $APIRequest = $this->HTTPClient->request("GET", "sale", $APIOptions);
            $APIResponse = json_decode($APIRequest->getBody()->getContents(), true);
            foreach ($APIResponse["data"] as $invoice)
            {
                $coll->add(new SaleModel($invoice));
            }
            return $coll;
        } catch (GuzzleException $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param integer $invoiceId
     * @return bool
     * @throws Exception
     */
    public function deleteInvoice(int $invoiceId): bool
    {
        if(!isset($invoiceId))
            throw new Exception("[ Fakturomania SDK ] invoiceId is required");

        try {
            $APIOptions = [
                "headers" => [
                    "Accept" => "application/json",
                    "Auth-Token" => $this->authToken,
                ]
            ];
            $APIRequest = $this->HTTPClient->request("GET", "sale/". $invoiceId, $APIOptions);
            return $APIRequest->getStatusCode() == 200;
        } catch (GuzzleException $e) {
            throw new Exception($e->getMessage());
        }
    }
}