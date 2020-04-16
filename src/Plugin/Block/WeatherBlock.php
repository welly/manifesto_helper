<?php

namespace Drupal\manifesto_helper\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Current weather in...' block.
 *
 * @Block(
 *   id = "manifesto_weather_block",
 *   admin_label = @Translation("Current weather"),
 *   category = @Translation("Manifesto")
 * )
 */
class WeatherBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Current route object.
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $routeMatch;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, CurrentRouteMatch $route_match) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration, $plugin_id, $plugin_definition,
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = $this->routeMatch->getParameter('node');
    $postcode = $node->get('field_postcode')->value;
    $data = manifesto_helper_get_location_data($postcode);

    $build = [
      '#markup' => $this->t('The weather in %borough is %weather', ['%borough' => $data['borough'], '%weather' => $data['weather']]),
    ];

    $cacheableMetadata = new CacheableMetadata();
    $cacheableMetadata->setCacheContexts(['url']);
    $cacheableMetadata->setCacheTags(['weather_block', 'weather_block:' . $data['id']]);
    $cacheableMetadata->applyTo($build);

    return $build;
  }

}
