<?php

namespace Turiac\SkuChange\Model\ProductApiClient;

interface ProductApiClientInterface
{
    /**
     * Sends product change data to the ProductDomain.
     *
     * @param array $data The product change data.
     * @return mixed
     */
    public function sendProductChange(array $data);
}
