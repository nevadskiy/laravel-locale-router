<?php

namespace Nevadskiy\LocalizationRouter\Repositories;

interface Repository
{
    public function set(string $locale): void;

    public function get(): string;

    public function getDefault(): string;
}
