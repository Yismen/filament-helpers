<?php

namespace Dainsys\FilamentHelpers\Traits;

trait HasFilamentPanels
{
    protected function getFilamentPanels(): array
    {
        $panels = [];

        foreach (\Filament\Facades\Filament::getPanels() as $this->panel) {
            $panels[] = $this->panel->getId();
        }

        if (empty($panels)) {
            $panels[] = 'admin';
        }

        return $panels;
    }
}
