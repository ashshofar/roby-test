<?php

namespace Drupal\custom_render\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'NewsResnderlist' Block.
 *
 * @Block(
 *   id = "NewsResnderlist",
 *   admin_label = @Translation("List NEws"),
 *   category = @Translation("List News"),
 * )
 */
class NewsResnderlist extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $query = \Drupal::entityQuery('node');
    $query->condition('type', 'news');
    $query->condition('status', 1)->sort('field_publish_date' , 'DESC');
    $query->range(0, 1);
    $nids = $query->execute();
    $build = [
        '#theme' => 'item_list',
        '#items' => array(),
        '#title' => 'News',
    ];
    $nodes = $storage->loadMultiple($nids);

    $view_builder = \Drupal::entityTypeManager()->getViewBuilder('node');
    $view_mode = 'grid';

    foreach ($nodes as $node) {
        $build['#items'][] = $view_builder->view($node, $view_mode);
    }

    return $build;
  }

}
