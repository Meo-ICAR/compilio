<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class PracticeChart extends ChartWidget
{
    protected ?string $heading = 'Practice Chart';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
