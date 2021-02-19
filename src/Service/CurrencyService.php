<?php

namespace App\Service;

use App\Exception\APIException;
use Symfony\Component\HttpFoundation\Response;


class CurrencyService
{
    const SOURCE_URL = 'http://www.cbr.ru/scripts/XML_daily.asp';
    const BASE_CURRENCY = 'RUB';
    private $quotes;

    public function __construct()
    {
        $this->setupQuotes();
    }

    public function convert(string $from, string $to, float $amount): float
    {
        if ($from === $to) {
            throw new APIException('Nothing to do', Response::HTTP_BAD_REQUEST);
        }
        try {
            if ($from === $this::BASE_CURRENCY) {
                $from_rate = 1;
            } else {
                $from_rate = $this->quotes[$from];
            }
            if ($to === $this::BASE_CURRENCY) {
                $to_rate = 1;
            } else {
                $to_rate = $this->quotes[$to];
            }
        } catch (\Throwable $e) {
            $msg = 'Invalid currency.';
            $msg .= ' Available: ' . $this::BASE_CURRENCY . ', ' . implode(', ', array_keys($this->quotes));
            throw new APIException($msg, Response::HTTP_BAD_REQUEST);
        }
        return $amount * $from_rate / $to_rate;
    }

    private function setupQuotes()
    {
        try {
            // @todo $this::SOURCE_URL хорошо бы вынести в конфиги
            // @todo кешировать?
            $xml = file_get_contents($this::SOURCE_URL);
            if (!$xml) {
                throw new \Exception('No data');
            }
            // @note не очень изящное, зато эффективное решение
            $data = json_decode(json_encode((array)simplexml_load_string($xml)), 1);
            $this->quotes = [];
            foreach ($data['Valute'] as $item) {
                $rate = (float)str_replace(',', '.', $item['Value']);
                $this->quotes[$item['CharCode']] = $rate / $item['Nominal'];
            }
        } catch (\Throwable $e) {
            // @todo log to ...
            throw new APIException('Quotes source error', Response::HTTP_BAD_GATEWAY);
        }
    }
}