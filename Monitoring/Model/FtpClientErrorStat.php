<?php

/**
 * @package    3slab/VdmLibraryFtpTransportBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryFtpTransportBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryFtpTransportBundle\Monitoring\Model;

use Vdm\Bundle\LibraryBundle\Monitoring\Model\StatModelInterface;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\StatItems;

class FtpClientErrorStat implements StatModelInterface
{    
    /**
     * @var int
     */
    protected $error;

    /**
     * FtpClientErrorStat constructor.
     *
     * @param int $error
     */
    public function __construct(int $error)
    {
        $this->error = $error;
    }

    /**
     * @return int
     */
    public function getError(): int
    {
        return $this->error;
    }

    public function getStats(): array
    {
        $array[] = new StatItems('increment', 'ftp.error.counter', $this->getError());
        
        return  $array;
    }
}
