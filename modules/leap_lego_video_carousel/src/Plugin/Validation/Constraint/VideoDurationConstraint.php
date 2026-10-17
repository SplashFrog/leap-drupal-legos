<?php

declare(strict_types=1);

namespace Drupal\leap_lego_video_carousel\Plugin\Validation\Constraint;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Validation\Attribute\Constraint;
use Symfony\Component\Validator\Constraint as SymfonyConstraint;

/**
 * Validates a duration typed the way a player shows it.
 */
#[Constraint(
  id: 'LeapVideoDuration',
  label: new TranslatableMarkup('Video duration', [], ['context' => 'Validation']),
  type: ['string']
)]
class VideoDurationConstraint extends SymfonyConstraint {

  public string $message = 'Enter the duration as a player shows it, for example 33:52, or 1:03:05 for a video over an hour.';

}
