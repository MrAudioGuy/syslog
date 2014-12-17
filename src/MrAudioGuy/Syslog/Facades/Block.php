<?php

	namespace MrAudioGuy\Syslog\Facades;

	use Illuminate\Support\Facades\Facade;

	class Block extends Facade {

		/**
		 * Get the registered name of the component.
		 *
		 * @return string
		 */
		protected static function getFacadeAccessor() { return 'logblock'; }

	}