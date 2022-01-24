<?php

namespace Drupal\acme_nfl\Repository;

use Drupal\acme_nfl\Model\NflTeamsDto;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

/**
 * The NFL teams data repository.
 */
class Teams {

    protected ClientInterface $httpClient;
    protected ImmutableConfig $config;

    /**
     * Constructor.
     */
    public function __construct(ConfigFactoryInterface $configFactory,
                                ClientInterface        $httpClient
    ) {
        $this->httpClient = $httpClient;
        $this->config = $configFactory->get('acme_nfl.settings');
    }

    /**
     * Fetch the list NFL teams from a remote endpoint.
     *
     * @return NflTeamsDto
     * @throws Exception
     * @throws GuzzleException
     */
    public function list(): NflTeamsDto {
        $dto = new NflTeamsDto();
        $endpoint = $this->config->get('nfl_teams_api_endpoint');

        try {
            $response = $this->httpClient->request('GET', $endpoint, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                $body = json_decode($response->getBody()->getContents());

                // Validate response body
                if (is_object($body)
                    && is_object($body->results)
                    && is_object($body->results->data)
                    && is_array($body->results->data->team)
                    && is_object($body->results->columns)
                    && is_string($body->hash)
                    && !empty($body->hash)) {

                    // Populate DTO
                    $dto->teams = $body->results->data->team;
                    $dto->columns = $body->results->columns;
                    $dto->hash = $body->hash;

                    // Store the hash for caching purposes
                    \Drupal::service('config.factory')
                        ->getEditable('acme_nfl.settings')
                        ->set('nfl_teams_request_hash', $body->hash)
                        ->save();
                }
                else {
                    throw new Exception(
                        'API response contains unexpected NFL teams data format'
                    );
                }
            }
        } catch (GuzzleException $e) {
            \Drupal::logger(__CLASS__)->warning(
                'An error occurred while requesting NFL teams dto.',
                [$e->getCode(), $e->getMessage()],
            );

            throw $e;
        }

        return $dto;
    }
}
