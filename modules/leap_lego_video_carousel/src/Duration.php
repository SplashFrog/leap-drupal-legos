<?php

declare(strict_types=1);

namespace Drupal\leap_lego_video_carousel;

/**
 * A video's running time, typed the way a player shows it.
 *
 * Accepts M:SS, MM:SS, MMM:SS and H:MM:SS. Seconds are two digits, 00-59.
 * Minutes may pass 59 without an hour part (65:07); with hours they may not.
 * The canonical form is M:SS below an hour and H:MM:SS from an hour up.
 */
final class Duration {

  private const MAX_SECONDS = 359999;

  private function __construct(private readonly int $seconds) {}

  public static function fromString(string $input): ?self {
    if (!preg_match('/^(?:(\d{1,3}):([0-5]\d)|(\d{1,2}):([0-5]\d):([0-5]\d))$/', trim($input), $m)) {
      return NULL;
    }
    $seconds = isset($m[5])
      ? (int) $m[3] * 3600 + (int) $m[4] * 60 + (int) $m[5]
      : (int) $m[1] * 60 + (int) $m[2];

    return $seconds >= 1 && $seconds <= self::MAX_SECONDS ? new self($seconds) : NULL;
  }

  public static function fromSeconds(int $seconds): self {
    return new self(max(1, min($seconds, self::MAX_SECONDS)));
  }

  public function seconds(): int {
    return $this->seconds;
  }

  public function canonical(): string {
    [$h, $m, $s] = $this->parts();
    return $h > 0 ? sprintf('%d:%02d:%02d', $h, $m, $s) : sprintf('%d:%02d', $m, $s);
  }

  public function iso8601(): string {
    [$h, $m, $s] = $this->parts();
    return 'PT' . ($h ? $h . 'H' : '') . ($m ? $m . 'M' : '') . ($s ? $s . 'S' : '');
  }

  /**
   * The duration as speech should say it, with commas so synthesis pauses.
   *
   * @param string|null $langcode
   *   The language to speak in; NULL uses the current interface language.
   */
  public function spoken(?string $langcode = NULL): string {
    [$h, $m, $s] = $this->parts();
    $t = \Drupal::translation();
    $options = $langcode !== NULL ? ['langcode' => $langcode] : [];
    $words = [];
    if ($h) {
      $words[] = (string) $t->formatPlural($h, '1 hour', '@count hours', [], $options);
    }
    if ($m) {
      $words[] = (string) $t->formatPlural($m, '1 minute', '@count minutes', [], $options);
    }
    if ($s) {
      $words[] = (string) $t->formatPlural($s, '1 second', '@count seconds', [], $options);
    }
    return implode(', ', $words);
  }

  /**
   * @return int[]
   *   Hours, minutes, seconds.
   */
  private function parts(): array {
    return [intdiv($this->seconds, 3600), intdiv($this->seconds % 3600, 60), $this->seconds % 60];
  }

}
