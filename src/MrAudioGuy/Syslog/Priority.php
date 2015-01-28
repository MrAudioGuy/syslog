<?php

	namespace MrAudioGuy\Syslog;


	/**
	 * Description of Prioroty
	 *
	 * Prioroties of Syslog as described in <a href="http://tools.ietf.org/html/rfc5424#section-6.2">RFC 5424 Headers</a>
	 *
	 * @author Talaeezadeh <your.brother.t@hotmail.com>
	 */
	class Prioroty
	{
		// <editor-fold defaultstate="collapsed" desc="Facilities">

		public static $fac_kernel_messages = 0
		public static $fac_user_level_messages = 1
		public static $fac_mail_system = 2
		public static $fac_system_daemons = 3
		public static $fac_security_or_authorization_messages = 4
		public static $fac_messages_generated_internally_by_syslogd = 5
		public static $fac_line_printer_subsystem = 6
		public static $fac_network_news_subsystem = 7
		public static $fac_UUCP_subsystem = 8
		public static $fac_clock_daemon = 9
		public static $fac_security_or_authorization_messages_1 = 10
		public static $fac_FTP_daemon = 11
		public static $fac_NTP_subsystem = 12
		public static $fac_log_audit = 13
		public static $fac_log_alert = 14
		public static $fac_clock_daemon_1 = 15
		public static $fac_local_use_0 = 16
		public static $fac_local_use_1 = 17
		public static $fac_local_use_2 = 18
		public static $fac_local_use_3 = 19
		public static $fac_local_use_4 = 20
		public static $fac_local_use_5 = 21
		public static $fac_local_use_6 = 22
		public static $fac_local_use_7 = 23
		
		public static $FAC_KERNEL_MESSAGES = 0
		public static $FAC_USER_LEVEL_MESSAGES = 1
		public static $FAC_MAIL_SYSTEM = 2
		public static $FAC_SYSTEM_DAEMONS = 3
		public static $FAC_SECURITY_OR_AUTHORIZATION_MESSAGES = 4
		public static $FAC_MESSAGES_GENERATED_INTERNALLY_BY_SYSLOGD = 5
		public static $FAC_LINE_PRINTER_SUBSYSTEM = 6
		public static $FAC_NETWORK_NEWS_SUBSYSTEM = 7
		public static $FAC_UUCP_SUBSYSTEM = 8
		public static $FAC_CLOCK_DAEMON = 9
		public static $FAC_SECURITY_OR_AUTHORIZATION_MESSAGES_1 = 10
		public static $FAC_FTP_DAEMON = 11
		public static $FAC_NTP_SUBSYSTEM = 12
		public static $FAC_LOG_AUDIT = 13
		public static $FAC_LOG_ALERT = 14
		public static $FAC_CLOCK_DAEMON_1 = 15
		public static $FAC_LOCAL_USE_0 = 16
		public static $FAC_LOCAL_USE_1 = 17
		public static $FAC_LOCAL_USE_2 = 18
		public static $FAC_LOCAL_USE_3 = 19
		public static $FAC_LOCAL_USE_4 = 20
		public static $FAC_LOCAL_USE_5 = 21
		public static $FAC_LOCAL_USE_6 = 22
		public static $FAC_LOCAL_USE_7 = 23

		// </editor-fold>

		// <editor-fold defaultstate="collapsed" desc="Severities">

		public static $emergency = 0
		public static $alert = 1
		public static $critical = 2
		public static $error = 3
		public static $warning = 4
		public static $notice = 5
		public static $informational = 6
		public static $debug = 7
		
		public static $EMERGENCY = 0
		public static $ALERT = 1
		public static $CRITICAL = 2
		public static $ERROR = 3
		public static $WARNING = 4
		public static $NOTICE = 5
		public static $INFORMATIONAL = 6
		public static $DEBUG = 7

		// </editor-fold>
	}