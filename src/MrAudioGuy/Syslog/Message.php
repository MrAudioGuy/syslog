<?php

	namespace MrAudioGuy\Syslog;

	/**
	 * Description of Message
	 *
	 * Message of Syslog message as described in <a href="http://tools.ietf.org/html/rfc5424#section-6.4">RFC 5424 Msg</a>
	 *
	 * @author Talaeezadeh <your.brother.t@hotmail.com>
	 */
	class Message
	{

		protected static $bom = "\xEF\xBB\xBF";

		protected $message;

		/**
		 * Normalizes the input to utf8 if possible, then sets the message to this value; without BOM.
		 *
		 * @param string $message
		 *
		 * @return \Message
		 */
		public function & setMessage ($message)
		{
			$message = iconv(mb_detect_encoding($message), 'UTF-8', $message);
			if (substr($message, 0, 3) == "\xEF\xBB\xBF")
			{
				$message = substr($message, 3);
			}
			$this->message = $message;

			return $this;
		}

		/**
		 * Returns the utf8 string of the message without BOM
		 *
		 * @return string
		 */
		public function getMessage ()
		{
			return $this->message;
		}

		/**
		 * Returns the BOM.
		 *
		 * It is statically always set to utf8 BOM (\xEF\xBB\xBF).
		 *
		 * @return type
		 */
		public static function getBom ()
		{
			return self::$bom;
		}

		/**
		 * Creates a message based on input string.
		 *
		 * @param string $input
		 *
		 * @return \static
		 */
		public static function fromString ($input)
		{
			$result = new static;
			$result->setMessage($input);

			return $result;
		}

		public function __sleep ()
		{
			return ['message'];
		}

		public function __wakeup ()
		{
		}

		/**
		 * @param string $message
		 */
		public function __construct($message = null)
		{
			if (isset($message))
			{
				$this->setMessage($message);
			}
		}
	}
