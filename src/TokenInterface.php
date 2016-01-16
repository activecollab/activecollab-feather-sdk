<?php

/*
 * This library is free software, and it is part of the Active Collab SDK project. Check LICENSE for details.
 *
 * (c) A51 doo <info@activecollab.com>
 */

namespace ActiveCollab\SDK;

/**
 * @package ActiveCollab\SDK
 */
interface TokenInterface
{
    /**
     * @return string
     */
    public function getToken();

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @return string
     */
    public function __toString();
}
