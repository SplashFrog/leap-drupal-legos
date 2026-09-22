<?php

declare(strict_types=1);

namespace Drupal\leap_lego_video_carousel\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\leap_lego_video_carousel\Duration;

/**
 * Shows the duration as a player does, and speaks it in full.
 */
#[FieldFormatter(
  id: 'leap_video_duration_accessible',
  label: new TranslatableMarkup('Duration with spoken form'),
  field_types: ['leap_video_duration'],
)]
class VideoDurationAccessibleFormatter extends FormatterBase {

  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];
    foreach ($items as $delta => $item) {
      $duration = Duration::fromString((string) $item->value);
      if ($duration === NULL) {
        continue;
      }
      $elements[$delta] = [
        '#theme' => 'leap_video_duration',
        '#display' => $duration->canonical(),
        '#spoken' => $duration->spoken($langcode),
        '#iso' => $duration->iso8601(),
      ];
    }
    return $elements;
  }

}
