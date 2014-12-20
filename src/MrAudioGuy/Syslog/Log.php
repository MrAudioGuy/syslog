<?php

	namespace MrAudioGuy\Syslog;

	/**
	 * Description of Log
	 *
	 * @author Talaeedeh
	 */
	class Log
	{

		protected static $server;
		protected static $pool;
		protected static $ipType;

		/**
		 *
		 * @param string $ip
		 * @param integer $port
		 * @param int $protocol
		 *
		 * @return boolean
		 */
		public static function setServer ($ip, $port, $protocol = SOL_UDP)
		{

			$IPV4SEG  = "(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])";
			$IPV4ADDR = "($IPV4SEG\.){3,3}$IPV4SEG";
			$IPV6SEG  = "[0-9a-fA-F]{1,4}";
			$IPV6ADDR = <<<"REGEX"
	(($IPV6SEG:){7,7}$IPV6SEG|($IPV6SEG:){1,7}:|($IPV6SEG:){1,6}:$IPV6SEG|($IPV6SEG:){1,5}(:$IPV6SEG){1,2}|($IPV6SEG:){1,4}(:$IPV6SEG){1,3}|($IPV6SEG:){1,3}(:$IPV6SEG){1,4}|($IPV6SEG:){1,2}(:$IPV6SEG){1,5}|$IPV6SEG:((:$IPV6SEG){1,6})|:((:$IPV6SEG){1,7}|:)|fe80:(:$IPV6SEG){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}$IPV4ADDR|($IPV6SEG:){1,4}:$IPV4ADDR)
REGEX;

			static::$ipType = AF_INET;
			$badip = false;
			if (!preg_match("/^$IPV4ADDR$/", $ip))
			{
				if(!preg_match("/^$IPV6ADDR$/", $ip))
				{
					return false;
				}
				static::$ipType = AF_INET6;
			}
			if ($port < 1 || $port > 65535)
			{
				return false;
			}

			if (!in_array($protocol, [SOL_TCP, SOL_UDP, SOL_SOCKET]))
			{
				return false;
			}
			static::$server["ip"]   = $ip;
			static::$server["port"] = (int) $port;
			static::$server["protocol"] = $protocol;

			return true;
		}

		public static function add (Block $message)
		{
			if (empty(static::$pool) || !is_array(static::$pool))
			{
				static::$pool[0] = $message;
			}
			else
			{
				static::$pool[] = $message;
			}

			return $message;
		}

		/**
		 *
		 * @return null
		 */
		public static function & getLast ()
		{
			if (!empty(static::$pool) && is_array(static::$pool))
			{
				return end(static::$pool);
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
		public static function & getByIndex ($index = 0)
		{
			if (!empty(static::$pool[$index]))
			{
				return static::$pool[$index];
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
		public static function getPoolSize ()
		{
			if (!empty(static::$pool) && is_array(static::$pool))
			{
				return count(static::$pool);
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
		public static function flush ()
		{
			if (empty(static::$server['ip']) || empty(static::$server['port']) || empty(static::$server['protocol']))
			{
				return false;
			}
			$socket = socket_create(static::$ipType, static::$server['protocol'] == SOL_TCP ? SOCK_STREAM : SOCK_DGRAM,
									static::$server['protocol']);
			while($element = array_shift(static::$pool))
			{
				if (is_a($element,'MrAudioGuy\Syslog\Block'))
				{
					$message = $element->logBlock();
					if (static::$server['protocol'] == SOL_TCP)
					{
						if (!@socket_connect($socket, static::$server['ip'], static::$server['port']))
						{
							array_unshift(static::$pool, $element);
							socket_close($socket);
							return false;
						}
					}
					if (!socket_sendto($socket, $message, strlen($message), 0, static::$server["ip"], static::$server["port"]))
					{
						array_unshift(static::$pool, $element);
						socket_close($socket);
						return false;
					}
				}
			}
			socket_close($socket);
			static::$pool = null;

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
			if(!static::setServer($ip, $port, $protocol))
			{
				static::setServer('127.0.0.1', 514, SOL_UDP);
			}

		}
	}