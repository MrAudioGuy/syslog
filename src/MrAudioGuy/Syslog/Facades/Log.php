<?php

	namespace MrAudioGuy\Syslog;

	/**
	 * Description of Log
	 *
	 * @author Talaeedeh
	 */
	class Log
	{

		protected $server;
		protected $pool;
		protected $ipType;

		/**
		 *
		 * @param string $ip
		 * @param integer $port
		 * @param int $protocol
		 *
		 * @return boolean
		 */
		public function setServer ($ip, $port, $protocol = SOL_UDP)
		{

			$IPV4SEG  = "(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])";
			$IPV4ADDR = "($IPV4SEG\.){3,3}$IPV4SEG";
			$IPV6SEG  = "[0-9a-fA-F]{1,4}";
			$IPV6ADDR = <<<"REGEX"
	(($IPV6SEG:){7,7}$IPV6SEG|($IPV6SEG:){1,7}:|($IPV6SEG:){1,6}:$IPV6SEG|($IPV6SEG:){1,5}(:$IPV6SEG){1,2}|($IPV6SEG:){1,4}(:$IPV6SEG){1,3}|($IPV6SEG:){1,3}(:$IPV6SEG){1,4}|($IPV6SEG:){1,2}(:$IPV6SEG){1,5}|$IPV6SEG:((:$IPV6SEG){1,6})|:((:$IPV6SEG){1,7}|:)|fe80:(:$IPV6SEG){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}$IPV4ADDR|($IPV6SEG:){1,4}:$IPV4ADDR)
REGEX;

			$this->ipType = AF_INET;
			$badip = false;
			if (!preg_match("/^$IPV4ADDR$/", $ip))
			{
				if(!preg_match("/^$IPV6ADDR$/", $ip))
				{
					return false;
				}
				$this->ipType = AF_INET6;
			}
			if ($port < 1 || $port > 65535)
			{
				return false;
			}

			if (!in_array($protocol, [SOL_TCP, SOL_UDP, SOL_SOCKET]))
			{
				return false;
			}
			$this->server["ip"]   = $ip;
			$this->server["port"] = (int) $port;
			$this->server["protocol"] = $protocol;

			return true;
		}

		public function & add (Block $message)
		{
			if (empty($this->pool) || !is_array($this->pool))
			{
				$this->pool[0] = $message;
			}
			else
			{
				$this->pool[] = $message;
			}

			return $this;
		}

		/**
		 *
		 * @return null
		 */
		public function & getLast ()
		{
			if (!empty($this->pool) && is_array($this->pool))
			{
				return end($this->pool);
			}
			else
			{
				return null;
			}
		}

		/**
		 *
		 * @param integer $index
		 *
		 * @return null
		 */
		public function & getByIndex ($index = 0)
		{
			if (!empty($this->pool[$index]))
			{
				return $this->pool[$index];
			}
			else
			{
				return null;
			}
		}

		/**
		 *
		 * @return int
		 */
		public function getPoolSize ()
		{
			if (!empty($this->pool) && is_array($this->pool))
			{
				return count($this->pool);
			}
			else
			{
				return 0;
			}
		}


		/**
		 *
		 * @return boolean
		 */
		public function flush ()
		{
			if (empty($this->server['ip']) || empty($this->server['port']) || empty($this->server['protocol']))
			{
				return false;
			}
			$socket = socket_create($this->ipType, $this->server['protocol'] == SOL_TCP ? SOCK_STREAM : SOCK_DGRAM,
									$this->server['protocol']);
			while($element = array_shift($this->pool))
			{
				if (is_a($element,'MrAudioGuy\Syslog\Block'))
				{
					$message = $element->logBlock();
					if ($this->server['protocol'] == SOL_TCP)
					{
						if (!@socket_connect($socket, $this->server['ip'], $this->server['port']))
						{
							array_unshift($this->pool, $element);
							socket_close($socket);
							return false;
						}
					}
					if (!socket_sendto($socket, $message, strlen($message), 0, $this->server["ip"], $this->server["port"]))
					{
						array_unshift($this->pool, $element);
						socket_close($socket);
						return false;
					}
				}
			}
			socket_close($socket);
			$this->pool = null;

			return true;
		}

		/**
		 *
		 * @param string $ip
		 * @param integer $port
		 * @param integer $protocol
		 */
		public function __construct ($ip = '127.0.0.1', $port = 514, $protocol = SOL_UDP)
		{
			if(!$this->setServer($ip, $port, $protocol))
			{
				$this->setServer('127.0.0.1', 514, SOL_UDP);
			}

		}
	}