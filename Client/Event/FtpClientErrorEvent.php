<?php

/**
 * @package    3slab/VdmLibraryFtpTransportBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryFtpTransportBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryFtpTransportBundle\Client\Event;

use Symfony\Contracts\EventDispatcher\Event;

class FtpClientErrorEvent extends Event
{
    /**
     * FtpClientErrorEvent constructor
     */
    public function __construct()
    {
    }

    public function getError()
    {
        return 1;
    }
}
