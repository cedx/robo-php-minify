<?php declare(strict_types=1);
namespace Robo\PhpMinify;

use GuzzleHttp\{Client};
use Symfony\Component\Process\{Process};

/** Removes comments and whitespace from a PHP script, by calling a Web service. */
class FastTransformer implements Transformer {

  /** @var string The address that the server is listening on. */
  const address = '127.0.0.1';

  /** @var string The path to the PHP executable. */
  private string $executable;

  /** @var int The port that the PHP process is listening on. */
  private int $port = -1;

  /** @var Process|null The underlying PHP process. */
  private ?Process $process = null;

  /**
   * Creates a new safe transformer.
   * @param string $executable The path to the PHP executable.
   */
  function __construct(string $executable = 'php') {
    assert(mb_strlen($executable) > 0);
    $this->executable = $executable;
  }

  /** Closes this transformer and releases any resources associated with it. */
  function close(): void {
    if (!$this->isListening()) return;

    /** @var Process $process */
    $process = $this->process;
    $process->stop();
    $this->process = null;
  }

  /**
   * Gets a value indicating whether the PHP process is currently listening.
   * @return bool `true` if the PHP process is currently listening, otherwise `false`.
   */
  function isListening(): bool {
    return (bool) $this->process;
  }

  /**
   * Starts the underlying PHP process: begins accepting connections.
   * @return int The port used by the PHP process.
   */
  function listen(): int {
    if (!$this->isListening()) {
      $this->port = $this->getPort();
      $this->process = new Process([$this->executable, '-S', static::address.":{$this->port}", '-t', __DIR__]);
      $this->process->start();
      sleep(1);
    }

    return $this->port;
  }

  /**
   * Processes a PHP script.
   * @param string $script The path to the PHP script.
   * @return string The transformed script.
   */
  function transform(string $script): string {
    assert(mb_strlen($script) > 0);
    $address = static::address;
    $file = rawurlencode((string) realpath($script));
    $port = $this->listen();
    return (string) (new Client)->get("http://$address:$port/Server.php?file=$file")->getBody();
  }

  /**
   * Gets an ephemeral port chosen by the system.
   * @return int A port that the server can listen on.
   * @throws \RuntimeException The socket could not be created.
   */
  private function getPort(): int {
    $address = static::address;
    $socket = stream_socket_server("tcp://$address:0");
    if (!$socket) throw new \RuntimeException('The socket could not be created.');

    $parts = explode(':', stream_socket_get_name($socket, false));
    fclose($socket);
    return (int) end($parts);
  }
}
