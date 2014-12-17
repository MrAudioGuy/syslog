<?php

	namespace MrAudioGuy\Syslog;

	/**
	 * Description of Element
	 *
	 * Element of Syslog message as described in <a href="http://tools.ietf.org/html/rfc5424#section-6.3.1">RFC 5424 SD-Element</a>
	 *
	 * @author Talaeezadeh <your.brother.t@hotmail.com>
	 */
	class Element
	{

		protected $sdID;
		protected $sdParams;

		/**
		 * Sets the SD-ID parameter
		 *
		 *  mutable
		 *
		 * @param string $ietf
		 * @param string $iana
		 *
		 * @return Element
		 */
		public function & setSdID ($ietf = null, $iana = null)//array $sdID = [])
		{
			if (!isset($ietf))
			{
				$this->sdID['ietf'] = '';

				return $this;
			}
			$ietf               = Helper::standardize($ietf, 'ASCII', -1, '=\s\]\"\@');
			$this->sdID['ietf'] = $ietf;
			if (!empty($iana))
			{
				preg_match_all('/(\d+?)(?:\.|$)|(?:[^\d]+?)(?:\.|$)/', $iana, $matches);
				$iana = '';
				foreach ($matches[1] as $match)
				{
					if (empty($match))
					{
						break;
					}
					$iana .= "$match.";
				}
				$iana               = trim(trim($iana), '.');
				$this->sdID['iana'] = $iana;
			}
			else
			{
				$this->sdID['iana'] = null;
			}

			return $this;
		}

		public function getSdID ()
		{
			return $this->sdID;
		}

		public function getSdParams ()
		{
			return $this->sdParams;
		}

		/**
		 * Add a new or rewite a SD-PARAM parameter
		 *
		 *  mutable
		 *
		 * @param string $name
		 * @param mixed  $value
		 *
		 * @return Element
		 */
		public function & add ($name, $value = null)
		{
			$input = [
				'name'  => Helper::standardize($name, 'ASCII', 32, '=\s\]\"'),
				'value' => isset($value) ? Helper::standardize($value, 'UTF-8', -1, '\\\]\"') : "",
			];

			foreach ($this->sdParams as $key => $param)
			{
				if ($param['name'] === $input['name'])
				{
					$this->sdParams[$key] = $input;

					return $this;
				}
			}
			array_push($this->sdParams, $input);

			return $this;
		}

		/**
		 * Removes an SD-PARAM based on its name
		 *
		 * @param string $name
		 *
		 * @return Element
		 */
		public function & remove ($name)
		{
			$name = Helper::standardize($name, 'ASCII', 32, '=\s\]\"');

			foreach ($this->sdParams as $key => $param)
			{
				if ($param['name'] === $name)
				{
					unset($this->sdParams[$key]);
				}
			}

			return $this;
		}

		public function __sleep ()
		{
			return ['sdID', 'sdParams'];
		}

		public function __wakeup ()
		{
		}

		/**
		 * Makes an array of SD-ELEMENTs based on an input string or array.
		 *
		 * @param mixed $input
		 *
		 * @return array
		 */
		public static function fromString ($input)
		{
			$result = [];
			if (is_string($input))
			{
				preg_match_all('/\[(.*?)\]/', $input, $input);
			}
			foreach ($input[1] as $key => $value)
			{
				$result[$key] = new static;
				$current      = explode(" ", $value);
				///'(\w*)[\@]?(\d*)?$';
				preg_match_all('/^(.*)(?:@)(.*?)$|^(.*)$/', $current[0], $matches);

				if (empty($matches[3]))
				{
					$result[$key]->sdID['ietf'] = $matches[1];
					$result[$key]->sdID['iana'] = $matches[2];
				}
				else
				{
					$result[$key]->sdID['ietf'] = $matches[3];
					$result[$key]->sdID['iana'] = null;
				}
				preg_match_all('/(?:.*?\s)?(.*?)="(.*?)?"|(?<=\s)\G\s(.*?)="(.*?)?"(?:\s|$)/', $value, $matches);
				unset($matches[3]);
				unset($matches[4]);
				for ($i = 0; $i < count($matches[0]); $i++)
				{
					$result[$key]->add($matches[1][$i], $matches[2][$i]);
				}

			}

			return $result;
		}

		public function __toString ()
		{
			$serialized = "[" . $this->sdID['ietf'];

			if (!empty($this->sdID['iana']))
			{
				$serialized .= "@" . $this->sdID['iana'];
			}

			$tmplength = count($this->sdParams) - 1;
			if ($tmplength + 1 > 0)
			{
				$serialized .= " ";
			}
			$i = 0;
			for (; $i < $tmplength; $i++)
			{
				if (isset($this->sdParams[$i]))
				{
					if (is_a($this->sdParams[$i]['value'], 'object'))
					{
						if (!method_exists($this->sdParams[$i]['value'], '__toString'))
						{
							$serialized .= $this->sdParams[$i]['name'] . "=\"\" ";
							continue;
						}
					}
					$serialized .= $this->sdParams[$i]['name'] . "=\"" . $this->sdParams[$i]['value'] . "\" ";
				}
			}
			if (isset($this->sdParams[$i]))
			{
				if (is_a($this->sdParams[$i]['value'], 'object'))
				{
					if (!method_exists($this->sdParams[$i]['value'], '__toString'))
					{
						$serialized .= $this->sdParams[$i]['name'] . "=\"\"";
					}
				}
				$serialized .= $this->sdParams[$i]['name'] . "=\"" . $this->sdParams[$i]['value'] . "\"";
			}

			return $serialized . "]";
		}

		public function __construct ($ietf = null, $iana = null, array $params = [])
		{
			$this->setSdID($ietf, $iana);
			$this->sdParams = [];
			foreach ($params as $param)
			{
				if (is_array($param))
				{
					if (isset($param['name']))
					{
						$name = $param['name'];
					}
					else
					{
						if (isset($param[0]))
						{
							$name = $param[0];
						}
						else
						{
							continue;
						}
					}
					if (isset($param['value']))
					{
						$value = $param['value'];
					}
					else
					{
						if (isset($param[1]))
						{
							$value = $param[1];
						}
						else
						{
							$value = null;
						}
					}
				}
				else
				{
					if (is_string($param))
					{
						$name  = $param;
						$value = null;
					}
					else
					{
						continue;
					}
				}
				$this->add($name, $value);
			}
		}

	}