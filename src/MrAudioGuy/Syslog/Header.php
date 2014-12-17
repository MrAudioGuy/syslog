<?php

	namespace MrAudioGuy\Syslog;


	// <editor-fold defaultstate="collapsed" desc="Facilities">

	use Illuminate\Support\Facades\Config;

	!defined('fac_kernel_messages') ? define('fac_kernel_messages', 0, true) : null;
	!defined('fac_user_level_messages') ? define('fac_user_level_messages', 1, true) : null;
	!defined('fac_mail_system') ? define('fac_mail_system', 2, true) : null;
	!defined('fac_system_daemons') ? define('fac_system_daemons', 3, true) : null;
	!defined('fac_security_or_authorization_messages') ? define('fac_security_or_authorization_messages', 4, true) : null;
	!defined('fac_messages_generated_internally_by_syslogd') ? define('fac_messages_generated_internally_by_syslogd', 5, true) : null;
	!defined('fac_line_printer_subsystem') ? define('fac_line_printer_subsystem', 6, true) : null;
	!defined('fac_network_news_subsystem') ? define('fac_network_news_subsystem', 7, true) : null;
	!defined('fac_UUCP_subsystem') ? define('fac_UUCP_subsystem', 8, true) : null;
	!defined('fac_clock_daemon') ? define('fac_clock_daemon', 9, true) : null;
	!defined('fac_security_or_authorization_messages_1') ? define('fac_security_or_authorization_messages_1', 10, true) : null;
	!defined('fac_FTP_daemon') ? define('fac_FTP_daemon', 11, true) : null;
	!defined('fac_NTP_subsystem') ? define('fac_NTP_subsystem', 12, true) : null;
	!defined('fac_log_audit') ? define('fac_log_audit', 13, true) : null;
	!defined('fac_log_alert') ? define('fac_log_alert', 14, true) : null;
	!defined('fac_clock_daemon_1') ? define('fac_clock_daemon_1', 15, true) : null;
	!defined('fac_local_use_0') ? define('fac_local_use_0', 16, true) : null;
	!defined('fac_local_use_1') ? define('fac_local_use_1', 17, true) : null;
	!defined('fac_local_use_2') ? define('fac_local_use_2', 18, true) : null;
	!defined('fac_local_use_3') ? define('fac_local_use_3', 19, true) : null;
	!defined('fac_local_use_4') ? define('fac_local_use_4', 20, true) : null;
	!defined('fac_local_use_5') ? define('fac_local_use_5', 21, true) : null;
	!defined('fac_local_use_6') ? define('fac_local_use_6', 22, true) : null;
	!defined('fac_local_use_7') ? define('fac_local_use_7', 23, true) : null;

	// </editor-fold>

	// <editor-fold defaultstate="collapsed" desc="Severities">

	!defined('emergency') ? define('emergency', 0, true) : null;
	!defined('alert') ? define('alert', 1, true) : null;
	!defined('critical') ? define('critical', 2, true) : null;
	!defined('error') ? define('error', 3, true) : null;
	!defined('warning') ? define('warning', 4, true) : null;
	!defined('notice') ? define('notice', 5, true) : null;
	!defined('informational') ? define('informational', 6, true) : null;
	!defined('debug') ? define('debug', 7, true) : null;

	// </editor-fold>


	/**
	 * Description of Header
	 *
	 * Header of Syslog message as described in <a href="http://tools.ietf.org/html/rfc5424#section-6.2">RFC 5424 Headers</a>
	 *
	 * @author Talaeezadeh <your.brother.t@hotmail.com>
	 */
	class Header
	{

		public    $uuid;
		protected $priority;
		protected $version;
		protected $timestamp;
		protected $hostname;
		protected $appName;
		protected $processID;
		protected $messageID;

		/**
		 * Calculates and sets the priority based on severity and facility
		 *
		 * (see <a href="http://tools.ietf.org/html/rfc5424#section-6.2.1">priority</a>)
		 *
		 * @param int $severity Severity number based on RFC5424:
		 *                      <ol start="0">
		 *                      <li>Emergency</li>
		 *                      <li>Alert</li>
		 *                      <li>Critical</li>
		 *                      <li>Error</li>
		 *                      <li>Warning</li>
		 *                      <li>Notice</li>
		 *                      <li>Informational</li>
		 *                      <li>Debug</li>
		 *                      </ol>
		 * @param int $facility Facility number based on OFC5424:
		 *                      <ol start="0">
		 *                      <li>fac_kernel_messages</li>
		 *                      <li>fac_user_level_messages</li>
		 *                      <li>fac_mail_system</li>
		 *                      <li>fac_system_daemons</li>
		 *                      <li>fac_security_or_authorization_messages</li>
		 *                      <li>fac_messages_generated_internally_by_syslogd</li>
		 *                      <li>fac_line_prioritynter_subsystem</li>
		 *                      <li>fac_network_news_subsystem</li>
		 *                      <li>fac_UUCP_subsystem</li>
		 *                      <li>fac_clock_daemon</li>
		 *                      <li>fac_security_or_authorization_messages_1</li>
		 *                      <li>fac_FTP_daemon</li>
		 *                      <li>fac_NTP_subsystem</li>
		 *                      <li>fac_log_audit</li>
		 *                      <li>fac_log_alert</li>
		 *                      <li>fac_clock_daemon_1</li>
		 *                      <li>fac_local_use_0</li>
		 *                      <li>fac_local_use_1</li>
		 *                      <li>fac_local_use_2</li>
		 *                      <li>fac_local_use_3</li>
		 *                      <li>fac_local_use_4</li>
		 *                      <li>fac_local_use_5</li>
		 *                      <li>fac_local_use_6</li>
		 *                      <li>fac_local_use_7</li>
		 *                      </ol>
		 *
		 * @return Header mutable
		 */
		public function & setPriority ($severity, $facility)
		{
			$this->priority = (int)$severity | ($facility << 3);
			if ($this->priority > 191)
			{
				$this->priority = 191;
			}
			elseif ($this->priority < 0)
			{
				$this->priority = 0;
			}

			return $this;
		}

		/**
		 * Sets the version of Syslog message based on RFC5424 (see <a href="http://tools.ietf.org/html/rfc5424#section-6.2.2">version</a>). If the value exceeds the standard, it will be set to boundry values.
		 *
		 * @param int $input The desired version value.
		 *
		 * @return Header mutable
		 */
		public function & setVersion ($input = 1)
		{
			if (is_numeric($input))
			{
				if ($input < 1000 && $input >= 1)
				{
					$this->version = (int)$input;
				}
				elseif ($input > 999)
				{
					$this->version = 999;
				}
				elseif ($input < 1)
				{
					$this->version = 1;
				}
			}

			return $this;
		}

		/**
		 * Sets the timestamp to a specified value or now as default.
		 *
		 * (see <a href="http://tools.ietf.org/html/rfc5424#section-6.2.3">timestamp</a>)
		 *
		 * @param string       $sTime The desired time.
		 * @param \DateTimeZone $oTimeZone
		 *
		 * @return Header mutable
		 */
		public function & setTimestamp ($sTime = 'now', \DateTimeZone $oTimeZone = null)
		{
			$this->timestamp = new ExtendedDateTime($sTime, $oTimeZone);

			return $this;
		}

		/**
		 * Sets the hostname based on RFC5424 values (see <a href="http://tools.ietf.org/html/rfc5424#section-6.2.4">hostname</a>) MUST be one of the following based on availability:
		 *      <ol>
		 *          <li>FQDN</li>
		 *          <li>Static IP</li>
		 *          <li>Hostname</li>
		 *          <li>Dynamic IP</li>
		 *          <li>NILVALUE (NULL)</li>
		 *      </ol>
		 *
		 * @param string $input The desired qualified hostname
		 *
		 * @return Header mutable
		 */
		public function & setHostname ($input)
		{
			if (!empty($input))
			{
				$this->hostname = Helper::standardize($input, $encoding = 'ASCII', 255, '\x00-\x20\x7F-\xFF\s');
			}
			else
			{
				$this->hostname = null;
			}
			return $this;
		}

		/**
		 * Sets the APP-NAME based on RFC5424 specifications on <a href="http://tools.ietf.org/html/rfc5424#section-6.2.5">App-NAME</a>.
		 *
		 * @param string $input The desired appname
		 *
		 * @return Header mutable
		 */
		public function & setAppName ($input)
		{
			if (!empty($input))
			{
				$this->appName = Helper::standardize($input, $encoding = 'ASCII', 48, '\x00-\x20\x7F-\xFF\s');
			}
			else
			{
				$this->appName = null;
			}
			return $this;
		}

		/**
		 * Sets the processID based on RFC5424 specifications on <a href="http://tools.ietf.org/html/rfc5424#section-6.2.6">processID</a>.
		 *
		 * @param string $input The desired ProcID
		 *
		 * @return Header mutable
		 */
		public function & setProcessID ($input)
		{
			if (!empty($input))
			{
				$this->processID = Helper::standardize($input, $encoding = 'ASCII', 128, '\x00-\x20\x7F-\xFF\s');
			}
			else
			{
				$this->processID = null;
			}
			return $this;
		}

		/**
		 * Sets the messageID based on RFC5424 specifications on <a href="http://tools.ietf.org/html/rfc5424#section-6.2.7">messageID</a>.
		 *
		 * @param string $input The desired MsgID
		 *
		 * @return Header mutable
		 */
		public function & setMessageID ($input)
		{
			if (!empty($input))
			{
				$this->messageID = Helper::standardize($input, $encoding = 'ASCII', 32, '\x00-\x20\x7F-\xFF\s');
			}
			else
			{
				$this->messageID = null;
			}
			return $this;
		}

		/**
		 * Returns an array consisting of FACILITY and SEVERITY of log.
		 *
		 * {@see Header::setPriorityority()}
		 *
		 * @return array array of priority 'SEVERITY' and 'FACILITY'
		 */
		public function getPriority()
		{
			return [
				'SEVERITY' => $this->priority % 8,
				'FACILITY' => $this->priority >> 3
			];
		}

		/**
		 * Returns the messages version
		 *
		 * {@see Header::setVersion()}
		 *
		 * @return int Message's version
		 */
		public function getVersion()
		{
			return $this->version;
		}

		/**
		 * Returns the timestamp of log
		 *
		 * {@see Header::setTimestamp()}
		 *
		 * @return ExtendedDateTime The timestamp
		 */
		public function getTimestamp()
		{
			return $this->timestamp;
		}

		/**
		 * Returns a the message's hostname.
		 *
		 * {@see Header::setHostname()}
		 *
		 * @return string Hostname
		 */
		public function getHostname()
		{
			return $this->hostname;
		}

		/**
		 * Returns a the message's APP-NAME.
		 *
		 * {@see Header::setAppname()}
		 *
		 * @return string The appname
		 */
		public function getAppName()
		{
			return $this->appName;
		}

		/**
		 * Returns a the message's processID.
		 *
		 * {@see Header::setProcID()}
		 *
		 * @return string The ProcID
		 */
		public function getProcessID()
		{
			return $this->processID;
		}

		/**
		 * Returns a the message's messageID.
		 *
		 * {@see Header::setMsgID()}
		 *
		 * @return string The MsgID
		 */
		public function getMessageID()
		{
			return $this->messageID;
		}

		public function logHeader ()
		{
			$serialized = '';

			// <editor-fold defaultstate="collapsed" desc="priority">
			if (empty($this->priority))
			{
				if ($this->priority == 0)
				{
					$serialized .= "<0>";
				}
				else
				{
					$serialized .= "-";
				}
			}
			else
			{
				$serialized .= "<" . $this->priority . ">";
			}
			// </editor-fold>

			// <editor-fold defaultstate="collapsed" desc="version">
			if (empty($this->version))
			{
				$serialized .= "- ";
			}
			else
			{
				$serialized .= $this->version . " ";
			}
			// </editor-fold>

			// <editor-fold defaultstate="collapsed" desc="timestamp">
			if (empty($this->timestamp))
			{
				$serialized .= "- ";
			}
			else
			{
				$serialized .= $this->timestamp->format("Y-m-d\TH:i:s.uP ");
			}
			// </editor-fold>

			// <editor-fold defaultstate="collapsed" desc="hostname">
			if (empty($this->hostname))
			{
				$serialized .= "- ";
			}
			else
			{
				$serialized .= $this->hostname . " ";
			}
			// </editor-fold>

			// <editor-fold defaultstate="collapsed" desc="APP-NAME">
			if (empty($this->appName))
			{
				$serialized .= "- ";
			}
			else
			{
				$serialized .= $this->appName . " ";
			}
			// </editor-fold>

			// <editor-fold defaultstate="collapsed" desc="processID">
			if (empty($this->processID))
			{
				$serialized .= "- ";
			}
			else
			{
				$serialized .= $this->processID . " ";
			}
			// </editor-fold>

			// <editor-fold defaultstate="collapsed" desc="messageID">
			if (empty($this->messageID))
			{
				$serialized .= "-";
			}
			else
			{
				$serialized .= $this->messageID;
			}

			// </editor-fold>

			return $serialized;
		}


		/**
		 *
		 * @return array
		 */
		public function __sleep ()
		{
			return ['uuid', 'priority', 'version', 'timestamp', 'hostname', 'appName', 'processID', 'messageID'];
		}

		/**
		 *
		 */
		public function __wakeup ()
		{
		}

		/**
		 * Makes a header based on an input string or an array.
		 *
		 * @param mixed $input
		 *
		 * @return \static
		 */
		public static function fromString($input)
		{
			$result = new static;
			if (is_string($input))
			{
				$input = explode(" ", $input);
				$input = array_slice($input, 0, 7);
			}
			preg_match_all('/<(.*?)>(.*?)$/', $input[1], $matches);
			if (isset($matches[1][0]))
			{
				$result->priority = (int)$matches[1][0];
			}
			if (isset($matches[2][0]))
			{
				$result->version = (int)$matches[2][0];
			}
			$result->uuid = $input[0];
			$result->setTime($input[2]);
			$result->hostname  = $input[3];
			$result->appName   = $input[4];
			$result->processID = $input[5];
			$result->messageID = $input[6];

			return $result;
		}

		/**
		 *
		 * @return string
		 */
		public function __toString()
		{
			$serialized = $this->uuid . " ";

			// <editor-fold defaultstate="collapsed" desc="priority">
			if (empty($this->priority))
			{
				if ($this->priority == 0)
				{
					$serialized .= "<" . $this->priority . ">";
				}
				else
				{
					$serialized .= "-";
				}
			}
			else
			{
				$serialized .= "<" . $this->priority . ">";
			}
			// </editor-fold>

			// <editor-fold defaultstate="collapsed" desc="version">
			if (empty($this->version))
			{
				$serialized .= "- ";
			}
			else
			{
				$serialized .= $this->version . " ";
			}
			// </editor-fold>

			// <editor-fold defaultstate="collapsed" desc="timestamp">
			if (empty($this->timestamp))
			{
				$serialized .= "- ";
			}
			else
			{
				$serialized .= $this->timestamp->format("Y-m-d\TH:i:s.uP ");
			}
			// </editor-fold>

			// <editor-fold defaultstate="collapsed" desc="hostname">
			if (empty($this->hostname))
			{
				$serialized .= "- ";
			}
			else
			{
				$serialized .= $this->hostname . " ";
			}
			// </editor-fold>

			// <editor-fold defaultstate="collapsed" desc="APP-NAME">
			if (empty($this->appName))
			{
				$serialized .= "- ";
			}
			else
			{
				$serialized .= $this->appName . " ";
			}
			// </editor-fold>

			// <editor-fold defaultstate="collapsed" desc="processID">
			if (empty($this->processID))
			{
				$serialized .= "- ";
			}
			else
			{
				$serialized .= $this->processID . " ";
			}
			// </editor-fold>

			// <editor-fold defaultstate="collapsed" desc="messageID">
			if (empty($this->messageID))
			{
				$serialized .= "-";
			}
			else
			{
				$serialized .= $this->messageID;
			}

			// </editor-fold>

			return $serialized;
		}

		/**
		 * @param string $uuid
		 * @param string $severity
		 * @param integer $facility
		 * @param integer $oTime
		 * @param string $hostname
		 * @param string $appName
		 * @param string $processID
		 * @param string $messageID
		 * @param integer $version
		 */
		public function __construct ($severity = emergency, $facility = fac_user_level_messages, $appName = null ,
									 $oTime = 'now', $uuid = null, $hostname = null, $processID = null,
									 $messageID = null, $version = null)
		{
			if (empty($uuid))
			{
				$this->uuid = Helper::guid();
			}
			if (empty($severity))
			{
				$severity = emergency;
			}
			if (empty($facility))
			{
				$facility = fac_user_level_messages;
			}
			if (empty($oTime))
			{
				$oTime = 'now';
			}
			if (empty($hostname))
			{
				$hostname = Config::get('syslog::app.url');
			}
			if (empty($appName))
			{
				$appName = preg_replace('/https?\:\/\//', '', Config::get('app.hostname'));
			}
			if (empty($processID))
			{
				$pid = getmypid();
				$processID = getmypid() !== false ? $pid : 0;
			}
			if (empty($version))
			{
				$version = 1;
			}

			$this->uuid = $uuid;
			$this->setPriority($severity, $facility);
			$dtz = new \DateTimeZone(Config::get('app.timezone'));
			$this->setTimestamp($oTime, $dtz);
			$this->setHostname($hostname);
			$this->setAppName($appName);
			$this->setProcessID($processID);
			$this->setMessageID($messageID);
			$this->setVersion($version);
		}
	}