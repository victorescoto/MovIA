<?php

namespace App\Service;

use App\Http\Request;

interface ApiServiceInterface
{
    public function searchMovies(string $title): array;
}
