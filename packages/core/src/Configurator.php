<?php declare(strict_types = 1);

namespace Modette\Core;

use Contributte\PhpDoc\DI\PhpDocExtension;
use Modette\Admin\DI\AdminExtension;
use Modette\Api\DI\ApiExtension;
use Modette\Core\DI\CoreExtension;
use Modette\Core\Exception\Logic\InvalidArgumentException;
use Modette\Front\DI\FrontExtension;
use Modette\Http\DI\HttpExtension;
use Modette\Mail\DI\MailExtension;
use Modette\Orm\DI\OrmExtension;
use Modette\Sql\DI\SqlExtension;
use Modette\Templates\DI\TemplatesExtension;
use Modette\UI\DI\UIExtension;
use Nette\Bridges\CacheDI\CacheExtension;
use Nette\Bridges\SecurityDI\SecurityExtension;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Nette\DI\Config\Helpers as ConfigHelpers;
use Nette\DI\Config\Loader;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Nette\DI\Extensions\ConstantsExtension;
use Nette\DI\Extensions\DecoratorExtension;
use Nette\DI\Extensions\DIExtension;
use Nette\DI\Extensions\ExtensionsExtension;
use Nette\DI\Extensions\InjectExtension;
use Nette\DI\Extensions\PhpExtension;
use Nette\DI\Helpers;
use Nette\SmartObject;
use ReflectionClass;
use Tracy\Bridges\Nette\Bridge;
use Tracy\Bridges\Nette\TracyExtension;
use Tracy\Debugger;

final class Configurator
{

	use SmartObject;

	public const COOKIE_SECRET = 'nette-debug';

	/** @var callable[] function(Configurator $sender, Compiler $compiler): void; Occurs after the compiler is created */
	public $onCompile;

	/** @var string[] @internal classes which shouldn't be autowired */
	public $autowireExcludedClasses = [
		'stdClass',
	];

	/** @var mixed[] */
	private $extensions = [
		// core
		'php' => PhpExtension::class,
		'constants' => ConstantsExtension::class,
		'extensions' => ExtensionsExtension::class,
		'decorator' => DecoratorExtension::class,
		'cache' => [CacheExtension::class, ['%tempDir%']],
		'di' => [DIExtension::class, ['%debugMode%']],
		// core and its extensions
		'modette.core' => CoreExtension::class,
		'modette.admin' => AdminExtension::class,
		'modette.api' => ApiExtension::class,
		'modette.front' => FrontExtension::class,
		'modette.http' => HttpExtension::class,
		'modette.mail' => MailExtension::class,
		'modette.orm' => OrmExtension::class,
		'modette.sql' => SqlExtension::class,
		'modette.templates' => TemplatesExtension::class,
		'modette.ui' => UIExtension::class,
		// ui (not in UIExtension because could be installed without UI layer)
		'security' => [SecurityExtension::class, ['%debugMode%']],
		// api (not in ApiExtension because could be installed without API layer)
		'phpdoc' => PhpDocExtension::class,
		// core
		'tracy' => [TracyExtension::class, ['%debugMode%', '%consoleMode%']],
		'inject' => InjectExtension::class,
	];

	/** @var string */
	private $rootDir;

	/** @var bool */
	private $debugModeSetByUser = false;

	/** @var string[] */
	private $configs = [];

	/** @var mixed[] */
	private $parameters;

	/** @var mixed[] */
	private $dynamicParameters = [];

	/** @var object[] */
	private $services = [];

	public function __construct(string $rootDir)
	{
		// Set parameters
		$this->rootDir = str_replace('\\', '/', $rootDir);
		$this->parameters = $this->getDefaultParameters();

		// Set timezone to UTC
		date_default_timezone_set('UTC');
		@ini_set('date.timezone', 'UTC'); // @ - function may be disabled

		// todo - temporary solution
		$this->addConfig(CoreExtension::provideConfig());
		// admin
		if (class_exists(AdminExtension::class)) {
			$this->addConfig(AdminExtension::provideConfig());
		}
		// api
		if (class_exists(ApiExtension::class)) {
			$this->addConfig(ApiExtension::provideConfig());
		}
		// front
		if (class_exists(FrontExtension::class)) {
			$this->addConfig(FrontExtension::provideConfig());
		}
		// http
		if (class_exists(HttpExtension::class)) {
			$this->addConfig(HttpExtension::provideConfig());
		}
		// mail
		if (class_exists(MailExtension::class)) {
			$this->addConfig(MailExtension::provideConfig());
		}
		// orm
		if (class_exists(OrmExtension::class)) {
			$this->addConfig(OrmExtension::provideConfig());
		}
		// sql
		if (class_exists(SqlExtension::class)) {
			$this->addConfig(SqlExtension::provideConfig());
		}
		// templates
		if (class_exists(TemplatesExtension::class)) {
			$this->addConfig(TemplatesExtension::provideConfig());
		}
		// ui
		if (class_exists(UIExtension::class)) {
			$this->addConfig(UIExtension::provideConfig());
		}
	}

	public function isDebugMode(): bool
	{
		return $this->parameters['debugMode'];
	}

	/**
	 * Set debug mode for HTTP (debug is always enabled for CLI)
	 */
	public function setDebugMode(bool $debugMode): void
	{
		$this->parameters['debugMode'] = $debugMode;
		$this->parameters['productionMode'] = !$debugMode;
		$this->debugModeSetByUser = true;
	}

	private function enableDebugger(): void
	{
		Debugger::$strictMode = true;
		Debugger::enable($this->parameters['productionMode']);
		Bridge::initialize();
	}

	public function addConfig(string $config): self
	{
		$this->configs[] = $config;
		return $this;
	}

	/**
	 * @return mixed[]
	 */
	private function getDefaultParameters(): array
	{
		return [
			'rootDir' => $this->rootDir,
			'appDir' => $this->rootDir . '/src',
			'configDir' => $this->rootDir . '/config',
			'logDir' => $this->rootDir . '/var/log',
			'tempDir' => $this->rootDir . '/var/temp',
			'storage' => [
				'internalDir' => $this->rootDir . '/var/storage',
			],
			'vendorDir' => $this->rootDir . '/vendor',
			'debugMode' => false,
			'productionMode' => true,
			'consoleMode' => PHP_SAPI === 'cli',
		];
	}

	/**
	 * Adds new parameters.
	 *
	 * @param mixed[] $parameters
	 * @internal
	 */
	public function addParameters(array $parameters): self
	{
		if (isset($parameters['debugMode']) || isset($parameters['productionMode'])) {
			throw new InvalidArgumentException(sprintf('Set debug mode with %s::setDebugMode', static::class));
		}

		$this->parameters = (array) ConfigHelpers::merge($parameters, $this->parameters);
		return $this;
	}

	/**
	 * Adds new dynamic parameters.
	 *
	 * @param mixed[] $parameters
	 * @internal
	 */
	public function addDynamicParameters(array $parameters): self
	{
		$this->dynamicParameters = $parameters + $this->dynamicParameters;
		return $this;
	}

	/**
	 * Add instances of services.
	 *
	 * @param object[] $services
	 * @internal
	 */
	public function addServices(array $services): self
	{
		$this->services = $services + $this->services;
		return $this;
	}

	private function generateContainer(Compiler $compiler): string
	{
		$compiler->addConfig(['parameters' => $this->parameters]);
		$compiler->setDynamicParameterNames(array_keys($this->dynamicParameters));

		$loader = new Loader();
		$fileInfo = [];
		foreach ($this->configs as $info) {
			$fileInfo[] = sprintf('// source: %s', $info);
			$info = $loader->load($info, null);
			$compiler->addConfig($info);
		}
		$compiler->addDependencies($loader->getDependencies());

		$builder = $compiler->getContainerBuilder();
		$builder->addExcludedClasses($this->autowireExcludedClasses);

		foreach ($this->extensions as $name => $extension) {
			[$class, $args] = is_string($extension) ? [$extension, []] : $extension;
			if (class_exists($class)) {
				$args = Helpers::expand($args, $this->parameters, true);
				$classInst = (new ReflectionClass($class))->newInstanceArgs($args);
				if (!$classInst instanceof CompilerExtension) {
					throw new InvalidArgumentException(sprintf(
						'Extension "%s" must be instance of "%s", "%s given."',
						$name,
						CompilerExtension::class,
						get_class($classInst)
					));
				}
				$compiler->addExtension($name, $classInst);
			}
		}

		$this->onCompile($this, $compiler);

		$classes = $compiler->compile();
		return implode("\n", $fileInfo) . "\n\n" . $classes;
	}

	private function loadContainer(): string
	{
		$loader = new ContainerLoader(
			$this->parameters['tempDir'] . '/Modette.Configurator',
			$this->parameters['debugMode']
		);
		$class = $loader->load(
			function (Compiler $compiler): string {
				return $this->generateContainer($compiler);
			},
			[$this->parameters, array_keys($this->dynamicParameters), $this->configs, PHP_VERSION_ID - PHP_RELEASE_VERSION]
		);
		return $class;
	}

	public function createContainer(): Container
	{
		if ($this->parameters['consoleMode'] === true || // Always enable debug in console
			(!$this->debugModeSetByUser && $this->isLocalhost()) // Enable debug mode if user is at localhost and not set debug mode itself
		) {
			$this->setDebugMode(true);
		}
		$this->enableDebugger();

		// Used by cache generator to create containers
		$this->addServices(['configurator' => $this]);

		$class = $this->loadContainer();
		/** @var Container $container */
		$container = new $class($this->dynamicParameters);
		foreach ($this->services as $name => $service) {
			$container->addService($name, $service);
		}
		$container->initialize();
		return $container;
	}

	/**
	 * @param string[] $tokenList
	 */
	public function haveDebugToken(array $tokenList = []): bool
	{
		$token = is_string($_COOKIE[self::COOKIE_SECRET] ?? null)
			? $_COOKIE[self::COOKIE_SECRET]
			: null;

		if ($token === null) {
			return false;
		}
		if (in_array($token, $tokenList, true)) {
			return true;
		}
		return false;
	}

	private function isLocalhost(): bool
	{
		$list = [];
		if (!isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !isset($_SERVER['HTTP_FORWARDED'])) { // Forwarded for BC, X-Forwarded-For is standard
			$list[] = '127.0.0.1';
			$list[] = '::1';
		}
		$address = $_SERVER['REMOTE_ADDR'] ?? php_uname('n');
		return in_array($address, $list, true);
	}

}
