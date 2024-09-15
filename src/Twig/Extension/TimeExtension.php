<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Extension Twig pour filtrer l'affichage des dates en fonction de la date actuelle
 */
class TimeExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('time_diff', [$this, 'getTimeDiff']),
        ];
    }

    public function getTimeDiff(\DateTimeInterface $date): string
    {
        $now = new \DateTimeImmutable();
        $diff = $date->diff($now);
    
        if ($diff->y > 0) {
            return $diff->y . ' year' . ($diff->d > 1 ? 's' : '');
        }
    
        if ($diff->m > 0) {
            return $diff->m . ' month' . ($diff->d > 1 ? 's' : '');
        }
    
        if ($diff->d > 0) {
            return $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
        }
    
        if ($diff->h > 0) {
            return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
        }
    
        return $diff->i . ' min' . ($diff->i > 1 ? 's' : '');
    }
}