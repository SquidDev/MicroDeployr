<?php
namespace SquidDev\Deploy\Visitors;

/**
 * A {@link IVisitor} that uploads to FTP
 */
class FTPVisitor implements IVisitor {
	/**
	 * The connection handle to use
	 * @var resource $connection
	 */
	protected $connection;

	/**
	 * The local directory to upload from
	 * @var string $local
	 */
	protected $local;

	/**
	 * The remote directory to upload to
	 * @var string $remote
	 */
	protected $remote;

	/**
	 * Directories we have created
	 * @var array $dirs
	 */
	protected $dirs = array();

	/**
	 * Create a new {@link FTPVisitor}
	 * @param resource $connection The FTP connection to use
	 * @param string   $local      The local directory
	 * @param string   $remote     The remote directory
	 */
	public function __construct($connection, $local, $remote) {
		$this->connection = $connection;

		if(substr($local, -1) != '/' && $local != '') $local .= '/';
		$this->local = $local;

		if(substr($remote, -1) != '/' && $remote != '') $remote .= '/';
		$this->remote = $remote;
	}

	/**
	 * Create a new {@link FTPVisitor} from login details
	 * @param  string  $host     Host name
	 * @param  string  $username Username
	 * @param  string  $password Password
	 * @param  string  $local    The local directory
	 * @param  string  $remote   The remote directory
	 * @param  integer $port     The port to connect with
	 * @param  boolean $passive  Use passive mode
	 * @return FTPVisitor        The created vistor
	 */
	public static function fromDetails($host, $username, $password, $local, $remote = '', $port = 21, $passive = true) {
		$connection = ftp_connect($host, $port);
		if(!$connection) throw new RuntimeException("Cannot connect to $host:$port");

		if(!ftp_login($connection, $username, $password)) throw new RuntimeException("Cannot connect to $username@$host:$port");

		if(!ftp_pasv($connection, $passive)) throw new RuntimeException("Cannot set passive mode to " . ($passive ? "true" : "false"));

		return new FTPVisitor($connection, $local, $remote);
	}

	/**
	 * {@inheritdoc}
	 */
	public function visitDirectory($path) {
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function visitFile($path) {
		$directory = dirname($path);
		if(!isset($this->dirs[$directory])) {
			$dir = '';
			foreach(explode('/', $directory) as $subDir) {
				$dir = trim($dir . '/' . $subDir, '/');
				if(!isset($this->dirs[$dir])) {
					@ftp_mkdir($this->connection, $this->remote . $dir);
					$this->dirs[$dir] = true;
				}
			}
		}

		if(!ftp_put($this->connection, $this->remote . $path, $this->local . $path, $this->guessType($path))) {
			$this->onError($path);
		}
	}

	/**
	 * Guess the type of the file
	 * @param string $path Path to the file
	 */
	public function guessType($path) {
		return FTP_ASCII;
	}

	/**
	 * Report an error.
	 * @param string $path The path to the file that failed
	 */
	public function onError($path) {
		throw new Exception("Cannot put " . $path);
	}
}
