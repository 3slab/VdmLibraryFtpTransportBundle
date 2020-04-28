<?php

/**
 * @package    3slab/VdmLibraryFtpTransportBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryFtpTransportBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryFtpTransportBundle\Executor;

use Symfony\Component\Messenger\Envelope;
use Vdm\Bundle\LibraryBundle\Model\Message;
use Vdm\Bundle\LibraryBundle\Stamp\StopAfterHandleStamp;

class DefaultFtpExecutor extends AbstractFtpExecutor
{
    public function execute(array $files): iterable
    {
        $files = $this->filterFiles($files);
        
        foreach ($files as $key => $file) {
            $file = $this->ftpClient->get($file);
            $message = new Message($file);

            yield $this->getEnvelope($files, $key, $message);
        }

        yield new Envelope(new Message(""), [new StopAfterHandleStamp()]);
    }

    protected function filterFiles(array $files)
    {
        return array_filter($files, function($file) {
            return (isset($file['type']) && $file['type'] === 'file');
        });
    }
    
    private function getEnvelope(array $files, int $key, Message $message): Envelope
    {
        $stamps = [];

        // Put the stop stamp on the last file
        if (array_key_last($files) === $key) {
            $stamps = [new StopAfterHandleStamp()];
        }

        return new Envelope($message, $stamps);
    }
}
