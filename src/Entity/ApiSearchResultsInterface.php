<?php

namespace App\Entity;

interface ApiSearchResultsInterface
{
    public function getResults(): ?array;
    public function getTotalResults(): int;
    public function getError(): ?string;
    public function hasErrors(): bool;
}
