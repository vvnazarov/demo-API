<?php

namespace App\Controller;

use App\Exception\APIException;
use App\Service\CurrencyService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class Controller
{
    private $service;

    public function __construct(CurrencyService $service)
    {
        $this->service = $service;
    }

    /**
     * @Route("/{from}/{to}/{amount}", name="index", methods={"GET"})
     */
    public function index(string $from, string $to, float $amount): JsonResponse
    {
        $code = Response::HTTP_OK;
        return new JsonResponse([
            'success' => true,
            'status' => $code,
            'result' => $this->service->convert($from, $to, $amount),
        ], Response::HTTP_OK);
    }
}