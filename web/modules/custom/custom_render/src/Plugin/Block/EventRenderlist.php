<?php

namespace Drupal\custom_render\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

/**
 * Provides a 'Render Event' Block.
 *
 * @Block(
 *   id = "even_list",
 *   admin_label = @Translation("Event List"),
 *   category = @Translation("Event List"),
 * )
 */
class EventRenderlist extends BlockBase
{

    /**
     * {@inheritdoc}
     */
    public function build()
    {

        $timezone = date_default_timezone_get();
        $start = new \DateTime('now', new \DateTimeZone($timezone));
        $start->setTime(16, 0);
        $start->setTimezone(new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE));
        $start = DrupalDateTime::createFromDateTime($start);

        $storage = \Drupal::entityTypeManager()->getStorage('node');
        $query = \Drupal::entityQuery('node');
        $query->condition('type', 'event');

        $and = $query->andConditionGroup()
            ->condition('field_event_date.value',  $start->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT), '>')
            ->notExists('field_event_date.end_value');

        $or = $query->orConditionGroup()
            ->condition($and)
            ->condition('field_event_date.end_value', $start->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT), '>');

        $query->condition($or)->condition('status', 1) ;
        $query->range(0, 2);
        $nids = $query->execute();
        $build = [
            '#theme' => 'item_list',
            '#items' => array(),
            '#title' => 'Event',
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
