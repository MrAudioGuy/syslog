<?php

	namespace MrAudioGuy\Syslog;
    
    /**
     * Class Block
     *
     * Block of Syslog based on <a href="http://tools.ietf.org/html/rfc5424">RFC 5424</a>
     *      
     * @author Talaeezadeh <your.brother.t@hotmail.com>
     */
    class Block
	{

		public $header;

		protected $structuredData;

		public $message;

		/**
		 * Gets an element with given ID, NULL otherwise.
		 *
		 * @param array $sd_id an associative array for sd_id
		 * 	<ul>
		 * 	<li>'ietf'	=>	string: The name specified in IETF RFC5424</li>
		 * 	<li>'iana'	=>	int: The PEN number assigned by iana</li>
		 * 	</ul>
		 *
		 * @return null|Element returns the reference to the element, NULL otherwise.
		 */
		public function & getElement ($ietf = null, $iana = null)
		{
			$result = null;
			if (isset($ietf))
			{
				$tmp = new Element();
				$tmp->setSdID($ietf, $iana);
				if (!empty($this->structuredData))
				{
					foreach ($this->structuredData as $key => $value)
					{
						if ($value->getSdID() == $tmp->getSdID())
						{
							return $value;
						}
					}
				}
			}

			return $result;
		}

		/**
		 * Adds new element to the structuredData
		 * Adds new elemet by the $sd_id into the structuredData array, or returns the existing one.
		 *
		 * @param array $sd_id
		 *
		 * @return array The element by ref
		 */
		public function & addElement ($ietf = null, $iana = null)
		{
			$result = & $this->getElement($ietf, $iana);
			if (empty($result))
			{
				$this->structuredData[] = new Element();
				$result = end($this->structuredData)->setSdID($ietf, $iana);
			}

			return $result;
		}

		/**
		 * Merges an array of elements into the structuredData
		 *
		 * @param array $array
		 *
		 * @return Block mutable
		 */
		public function & mergeElement (array $array)
		{
			foreach ($array as $key => $value)
			{
				$sd_id = $value->getSdID();
				$current = & $this->addElement($sd_id['ietf'],isset($sd_id['iana']) ? $sd_id['iana'] : null);
				foreach ($value->getSdParams() as $param)
				{
					$current->add($param['name'], $param['value']);
				}
			}

			return $this;
		}

		public function logBlock ()
		{
			$serialized = $this->header->logHeader() . " ";
			if (!empty($this->structuredData))
			{
				foreach ($this->structuredData as $element)
				{
					$serialized .= $element;
				}
			}
			else
			{
				$serialized .= "-";
			}
			// Compatibility Problem < php 5.5
			$tmp = $this->message->getMessage();
			if (!empty($tmp))
			{
				$serialized .= " " . Message::getBOM() . $this->message->getMessage();
			}

			return $serialized;
		}


		// <editor-fold defaultstate="collapsed" desc="Magic functions">

		/**
		 *
		 * @return array
		 */
		public function __sleep ()
		{
			return ['header', 'structuredData', 'message'];
		}

		/**
		 * Creates a new Syslog block from an input string.
		 *
		 * @param string $input
		 *
		 * @return \static
		 */
		public static function fromString ($input)
		{
			$result   = new static;
			$exploded = explode(" ", $input);
			preg_match_all('/\[(.*?)\]/', $input, $elements);
			preg_match_all('/\] (.*?)$/', $input, $message);
			$header = array_slice($exploded, 0, 7);
			if (!empty($header))
			{
				$result->header = Header::fromString($header);
			}
			if (!empty($elements[1]))
			{
				$result->structuredData = Element::fromString($elements);
			}
			else
			{
				if ($exploded[7] == '-')
				{
					$body          = $header = array_slice($exploded, 8);
					$message[1][0] = '';
					foreach ($body as $elem)
					{
						$message[1][0] .= $elem . " ";
					}
					$message[1][0] = trim($message[1][0], " ");
				}
			}
			if (!empty($message[1]))
			{
				$result->message = Message::fromString($message[1][0]);
			}

			return $result;
		}

		/**
		 *
		 * @return string
		 */
		public function __toString ()
		{
			$serialized = $this->header . " ";
			if (!empty($this->structuredData))
			{
				foreach ($this->structuredData as $element)
				{
					$serialized .= $element;
				}
			}
			else
			{
				$serialized .= "-";
			}
			$tmp = $this->message->getMessage();
			if (!empty($tmp))
			{
				$serialized .= " " . Message::getBOM() . $this->message->getMessage();
			}

			return $serialized;
		}

		/**
		 *
		 * @param Header  $header
		 * @param array   $structuredData
		 * @param Message $message
		 */
		public function __construct (Header $header = null, array $structuredData = null, Message $message = null)
		{
			// <editor-fold defaultstate="collapsed" desc="Setting header">

			// If header is not provided:
			if (empty($header))
			{
				$this->header = new Header();
			}
			// Header is Partially or fully provided.
			else
			{
				$this->header = $header;
			}

			// </editor-fold>

			// <editor-fold defaultstate="collapsed" desc="Setting structuredData">

			// If structuredData is not provided:
			if (empty($structuredData))
			{
				$this->structuredData = [];
			}
			// structuredData is Partially or fully provided.
			else
			{
				$this->structuredData = $structuredData;
			}

			// </editor-fold>

			// <editor-fold defaultstate="collapsed" desc="Setting message">

			if (empty($message))
			{
				$this->message = new Message();
			}
			else
			{
				$this->message = $message;
			}

			// </editor-fold>
		}
	}
