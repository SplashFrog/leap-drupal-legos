<?php

declare(strict_types=1);

namespace Drupal\leap_lego_video_carousel\Plugin\Field\FieldType;

use Drupal\Core\Field\Attribute\FieldType;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\leap_lego_video_carousel\Duration;

/**
 * A video's running time, stored in its canonical M:SS or H:MM:SS form.
 */
#[FieldType(
  id: 'leap_video_duration',
  label: new TranslatableMarkup('Video duration'),
  description: new TranslatableMarkup('A running time typed as a player shows it, such as 33:52.'),
  category: 'general',
  default_widget: 'leap_video_duration_textfield',
  default_formatter: 'leap_video_duration_accessible',
)]
class VideoDurationItem extends FieldItemBase {

  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Duration'))
      ->addConstraint('LeapVideoDuration')
      ->setRequired(TRUE);
    return $properties;
  }

  public static function schema(FieldStorageDefinitionInterface $field_definition): array {
    return ['columns' => ['value' => ['type' => 'varchar', 'length' => 11]]];
  }

  public function isEmpty(): bool {
    return trim((string) $this->get('value')->getValue()) === '';
  }

  public function preSave(): void {
    $duration = Duration::fromString((string) $this->get('value')->getValue());
    if ($duration !== NULL) {
      $this->set('value', $duration->canonical());
    }
  }

  public static function generateSampleValue(FieldDefinitionInterface $field_definition): array {
    return ['value' => '3:05'];
  }

}
