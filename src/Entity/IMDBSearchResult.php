<?php

namespace App\Entity;

use App\Entity\ApiSearchResultsInterface;
use Psr\Http\Message\ResponseInterface;

class IMDBSearchResult implements ApiSearchResultsInterface
{
    protected $results;
    protected $error;

    public function __construct(ResponseInterface $response)
    {
        $searchResult = json_decode((string) $response->getBody(), true);

        $this->error = $searchResult['Error'] ?? null;

        $this->results = $searchResult['Search'] ?? [];
        $this->removeDuplicatedMovies();
    }

    protected function removeDuplicatedMovies()
    {
        $filteredMovies = [];
        $imdbIds = [];

        foreach ($this->results as $result) {
            if (in_array($result['imdbID'], $imdbIds)) {
                continue;
            }

            $imdbIds[] = $result['imdbID'];
            $filteredMovies[] = $result;
        }

        $this->results = $filteredMovies;
    }

    public function getResults(): array
    {
        return $this->results;
    }

    public function getTotalResults(): int
    {
        return count($this->results);
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function hasErrors(): bool
    {
        return !is_null($this->error);
    }
}
