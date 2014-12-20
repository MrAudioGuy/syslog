<?php namespace MrAudioGuy\Syslog;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class SyslogServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('mr-audio-guy/syslog');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->booting(function()
		{
			$loader = \Illuminate\Foundation\AliasLoader::getInstance();
			$loader->alias('Syslog', 'MrAudioGuy\Syslog\Facades\Syslog');
			$loader->alias('Block', 'MrAudioGuy\Syslog\Facades\Block');
			$loader->alias('Header', 'MrAudioGuy\Syslog\Facades\Header');
			$loader->alias('Element', 'MrAudioGuy\Syslog\Facades\Element');
			$loader->alias('Message', 'MrAudioGuy\Syslog\Facades\Message');
		});
		$this->app['syslog'] = $this->app->share(function($app)
		{
			return new Log(Config::get('Syslog::connection.ip'), Config::get('Syslog::connection.port'),
						   Config::get('Syslog::connection.protocol'));
		});

		$this->app['logblock'] = $this->app->share(function($app)
		{
			return new Block();
		});

		$this->app['logheader'] = $this->app->share(function($app)
		{
			return new Header();
		});

		$this->app['logelement'] = $this->app->share(function($app)
		{
			return new Element();
		});

		$this->app['logmessage'] = $this->app->share(function($app)
		{
			return new Message();
		});

	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return ['syslog', 'logblock', 'logheader', 'logelement', 'logmessage',];
	}

}
