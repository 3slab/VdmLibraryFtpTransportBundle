# VdmLibraryFtpTransportBundle

**Not maintained anymore : use 
[VdmLibraryFlysystemTransportBundle](https://github.com/3slab/VdmLibraryFlysystemTransportBundle) instead with 
[VdmLibraryBundle v3.x](https://github.com/3slab/VdmLibraryBundle)**

This source can collect data from a ftp server.

## Configuration reference

```
framework:
    messenger:
        transports:
            consumer:
                dsn: "sftp://user:password@sftp.fr:2222"
                retry_strategy:
                    max_retries: 0
                options:
                    monitoring:
                        enabled: true
                    mode: move
                    ftp_options:
                        dirpath: path/to/your/files/
                        storage: path/to/your/storage/
```

Configuration | Description
--- | ---
dsn | the url you want to collect (needs to start by ftp or sftp)
retry_strategy.max_retries | needs to be 0 because ftp transport does not support this feature
options.mode | two mode available (move|delete), `move` to deplace the file in other folder when it is treated, `delete` to remove it.
options.ftp_options | options to manage your ftp actions
options.ftp_options.dirpath | path to your directory
options.ftp_options.storage | If you choose option `move` you have to configure this path.
options.monitoring.enabled | if true, hook up in the vdm library bundle monitoring system to send information about the FTP response

## Custom ftp executor

A custom ftp executor allows you to customize how you call the ftp server. It's necessary if you have differents action to make on files.

Just create a class in your project that extends `Vdm\Bundle\LibraryFtpTransportBundle\Executor\AbstractFtpExecutor`. It will
automatically replace the default executor.

**If you have 2 custom executor. Only a single one will be used, the second is ignored.**

```
namespace App\FtpExecutor;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Vdm\Bundle\LibraryBundle\Model\Message;
use Vdm\Bundle\LibraryFtpTransportBundle\Executor\AbstractFtpExecutor;
use Vdm\Bundle\LibraryBundle\Stamp\StopAfterHandleStamp;

class CustomFtpExecutor implements AbstractFtpExecutor
{
    /** 
     * @var LoggerInterface 
    */
    private $logger;

    public function __construct(LoggerInterface $logger) 
    {
        parent::__construct();
        $this->logger = $logger;
    }

    public function execute(array $files): iterable
    {
        $files = array_filter($files, function($file) {
            return (isset($file['type']) && $file['type'] === 'file');
        });

        foreach ($files as $key => $file) {
            $file = $this->ftpClient->get($file);
            $message = new Message($file);

            yield $this->getEnvelope($files, $key, $message);
        }

        yield new Envelope(new Message(""), [new StopAfterHandleStamp()]);
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
```

There are 2 important things your custom executor needs to do :

* `yield` a new envelope with a VDM Message instance
* Add a `StopAfterHandleStamp` stamp to the yielded envelope if you want to stop after handling the last file (if not,
  the messenger worker loop over and will execute it once again).

*Note : thanks to the yield system, you can implement a loop in your execute function and return items once at a time*

*Note : you can keep state in your custom executor so if it is executed again, adapt your ftp call*

## Monitoring

If you enable monitoring, it will track the following metrics :

* Size of the Ftp file body
* Counter the ftp error