<?php

namespace Nevadskiy\LocalizationRouter\Repositories;

interface UserLocaleRepository
{
    /**
     * Set the locale to the repository.
     *
     * @param string $locale
     */
    public function set(string $locale): void;

    /**
     * Get the locale from the repository.
     *
     * @return string
     */
    public function get(): string;
}
