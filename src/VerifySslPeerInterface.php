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
interface VerifySslPeerInterface
{
    /**
     * Return true if SSL peer will be verified.
     *
     * @return bool
     */
    public function getSslVerifyPeer();

    /**
     * Set if we should verify SSL peer (true by default).
     *
     * @param  bool  $value
     * @return $this
     */
    public function &setSslVerifyPeer($value);
}
