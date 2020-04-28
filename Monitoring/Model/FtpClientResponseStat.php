<?php

/**
 * @package    3slab/VdmLibraryFtpTransportBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryFtpTransportBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryFtpTransportBundle\Monitoring\Model;

use Vdm\Bundle\LibraryBundle\Monitoring\Model\StatModelInterface;
use Vdm\Bundle\LibraryBundle\Monitoring\Model\StatItems;

class FtpClientResponseStat implements StatModelInterface
{
    /**
     * @var int
     */
    protected $size;

    /**
     * FtpClientResponseStat constructor.
     *
     * @param int|null $size
     */
    public function __construct(?int $size = null)
    {
        $this->size = $size;
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    public function getStats(): array
    {
        $array = [];

        if ($this->getSize() !== null) {
            $array[] = new StatItems('gauge', 'ftp.size', $this->getSize());
        }

        return  $array;
    }
}
