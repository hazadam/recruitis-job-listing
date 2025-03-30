<?php

declare(strict_types=1);

namespace App\Controller;

use App\RecruitisApi\RecruitisApiClientInterface;
use JsonException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use function is_array;
use function is_string;
use function json_decode;
use function mb_strlen;
use function substr;

final class RecruitisApiProxyController extends AbstractController
{
    private const string PROXY_API_PREFIX = '/api';

    public function __construct(
        private readonly RecruitisApiClientInterface $apiClient,
        private readonly LoggerInterface             $logger,
    ) {
    }

    #[Route(
        path: self::PROXY_API_PREFIX . '/{slug}',
        name: 'forward_api_call',
        requirements: [
            'slug' => '.*',
        ],
        methods: [
            Request::METHOD_GET,
            Request::METHOD_POST,
            Request::METHOD_PUT,
            Request::METHOD_OPTIONS,
        ]
    )]
    public function forwardAction(Request $request): Response
    {
        try {
            $content = $request->getContent();
            $requestHasContent = is_string($content) && $content !== '';
            $body = $requestHasContent
                ? json_decode(
                    json: $content,
                    associative: true,
                    flags: JSON_THROW_ON_ERROR
                )
                : null;

            if (null !== $body && false === is_array($body)) {
                throw new BadRequestException('Body is expected to be a JSON object.');
            }

            $response = $this->apiClient->request(
                $request->getMethod(),
                substr($request->getPathInfo(), mb_strlen(self::PROXY_API_PREFIX)),
                $request->getQueryString(),
                $body,
                $request->headers->all(),
            );

            return new JsonResponse(
                $response->content,
                $response->statusCode,
                $response->headers,
                json: true
            );
        } catch (TransportExceptionInterface $exception) {
            $this->logger->error($exception->getMessage(), [
                'throwable' => $exception,
            ]);

            return new Response(
                'Downstream API is unavailable at this moment, please try again later',
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        } catch (JsonException $exception) {
            $this->logger->error($exception->getMessage(), [
                'throwable' => $exception,
            ]);

            return new Response('Request content is not valid JSON');
        }
    }
}
