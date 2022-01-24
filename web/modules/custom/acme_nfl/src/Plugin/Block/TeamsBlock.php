<?php

namespace Drupal\acme_nfl\Plugin\Block;

use Drupal\acme_nfl\Model\NflTeamsDto;
use Drupal\acme_nfl\Repository\Teams;
use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'TeamsBlock' block.
 *
 * @Block(
 *  id = "nfl_teams_block",
 *  admin_label = @Translation("NFL Teams"),
 * )
 */
class TeamsBlock extends BlockBase implements ContainerFactoryPluginInterface {

    /**
     * @var \Drupal\acme_nfl\Repository\Teams
     */
    protected Teams $teamsRepository;

    /**
     * Class constructor.
     */
    public function __construct(array $configuration,
                                      $plugin_id,
                                      $plugin_definition,
                                Teams $teamsRepository
    ) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->teamsRepository = $teamsRepository;
    }

    /**
     * {@inheritdoc}
     *
     * A factory method that loads the resources required to construct this class.
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('acme_nfl.teams_repository')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function defaultConfiguration() {
        return [] + parent::defaultConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function build() {
        $block = [
            '#theme' => 'nfl_teams_block',
            '#attached' => [
                'library' => ['acme_nfl/teams']
            ],
            '#cache' => [
                'tags' => [
                    'config:acme_nfl.settings.nfl_teams_request_hash',
                ],
            ],
        ];

        try {
            $dto = $this->teamsRepository->list();
            $block['#data'] = $this->createBlockData($dto);
        } catch (\Exception $e) {
            return [];
        }

        return $block;
    }

    /**
     * Build the block data from the given DTO.
     *
     * @param NflTeamsDto $dto
     * @return array
     */
    private function createBlockData(NflTeamsDto $dto): array {
        $result = [
            'teams' => [],
            'divisions' => [],
            'columns' => $dto->columns,
        ];

        foreach ($dto->teams as $team) {
            if (!is_object($team)) {
                continue;
            }

            // Group teams by `conference`
            if (!empty($team->conference)) {
                $result['teams'][$team->conference][] = $team;
            }

            // Add division to result
            if (!empty($team->division) && !in_array($team->division, $result['divisions'])) {
                $result['divisions'][] = $team->division;
            }
        }

        return $result;
    }

    /**
     * Group items in a list by a property of its items.
     *
     * @param \stdClass[] $list
     * @param string $property
     * @return array An associative array of one more item groups classified by $property.
     */
    private function groupBy(array $list, string $property): array {
        $result = [];

        foreach ($list as $item) {
            if (!is_object($item)
                || empty($property)
                || !isset($item->$property)) {
                continue;
            }

            $result[$item->$property][] = $item;
        }

        return $result;
    }
}
