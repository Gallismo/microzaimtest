<?php

declare(strict_types=1);

namespace Codenixsv\CoinGeckoApi\Api;

use Exception;

class Search extends Api
{
    /**
     * @param string $query
     * @param array $params
     * @return object
     * @throws Exception
     */
    public function getSearch(string $query, array $params = []): array
    {
        $params['query'] = $query;

        return $this->get('/search', $params);
    }
}
