<?php

namespace Infrastructure\Database\Doctrine;

use stdClass;

interface RepositoryInterface
{
    /**
     * Get model
     *
     * @return stdClass
     */
    public function getModel();
}
