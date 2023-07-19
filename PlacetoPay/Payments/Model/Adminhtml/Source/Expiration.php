<?php

namespace PlacetoPay\Payments\Model\Adminhtml\Source;

class Expiration
{
    public const EXPIRATION_TIME_MINUTES_LIMIT = 40320;

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [];
        $format = '%d %s';
        $minutes = 10;

        while ($minutes <= self::EXPIRATION_TIME_MINUTES_LIMIT) {
            if ($minutes < 60) {
                $options[$minutes] = sprintf($format, $minutes, __('Minutes'));
                $minutes += 10;
            } elseif ($minutes >= 60 && $minutes < 1440) {
                $options[$minutes] = sprintf($format, $minutes / 60, __('Hour(s)'));
                $minutes += 60;
            } elseif ($minutes >= 1440 && $minutes < 10080) {
                $options[$minutes] = sprintf($format, $minutes / 1440, __('Day(s)'));
                $minutes += 1440;
            } elseif ($minutes >= 10080 && $minutes < 40320) {
                $options[$minutes] = sprintf($format, $minutes / 10080, __('Week(s)'));
                $minutes += 10080;
            } else {
                $options[$minutes] = sprintf($format, $minutes / 40320, __('Month(s)'));
                $minutes += 40320;
            }
        }

        return $options;
    }
}
