<?php declare(strict_types = 1);

namespace Modette\Core;

use Modette\Core\Exception\Logic\InvalidArgumentException;
use Modette\Core\Exception\Logic\InvalidStateException;
use Nette\Bridges\CacheDI\CacheExtension;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Nette\DI\Config\Adapters\NeonAdapter;
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

/**
 * @method void onCompile(Configurator $configurator, Compiler $compiler)
 */
final class Configurator
{

	use SmartObject;

	public const COOKIE_SECRET = 'nette-debug';

	/** @var mixed[] */
	private const EXTENSIONS = [
		'php' => PhpExtension::class,
		'constants' => ConstantsExtension::class,
		'extensions' => ExtensionsExtension::class,
		'decorator' => DecoratorExtension::class,
		'cache' => [CacheExtension::class, ['%tempDir%']],
		'di' => [DIExtension::class, ['%debugMode%']],
		'tracy' => [TracyExtension::class, ['%debugMode%', '%consoleMode%']],
		'inject' => InjectExtension::class,
	];

	/** @var callable[] function(Configurator $configurator, Compiler $compiler): void; Occurs after the compiler is created */
	public $onCompile = [];

	/** @var string[] classes which shouldn't be autowired */
	public $autowireExcludedClasses = [
		'stdClass',
	];

	/** @var string */
	private $rootDir;

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
	}

	public function isConsoleMode(): bool
	{
		return $this->parameters['consoleMode'];
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
	}

	private function enableDebugger(): void
	{
		Debugger::$strictMode = true;
		Debugger::enable($this->parameters['productionMode'], $this->parameters['logDir']);
		Bridge::initialize();
	}

	public function loadModules(string $modulesFile): void
	{
		$neon = new NeonAdapter();
		$config = $neon->load($modulesFile);
		foreach ($config as $file) {
			$this->addConfig($this->rootDir . $file);
		}
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
			'server' => [
				'development' => false,
			],
		];
	}

	/**
	 * Adds new parameters.
	 *
	 * @param mixed[] $parameters
	 */
	public function addParameters(array $parameters): self
	{
		$this->parameters = (array) ConfigHelpers::merge($parameters, $this->parameters);
		return $this;
	}

	/**
	 * Adds new dynamic parameters.
	 *
	 * @param mixed[] $parameters
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
		foreach ($this->configs as $configFile) {
			$fileInfo[] = sprintf('// source: %s', $configFile);
			$config = $loader->load($configFile, null);
			$compiler->addConfig($config);
		}
		$compiler->addDependencies($loader->getDependencies());

		$builder = $compiler->getContainerBuilder();
		$builder->addExcludedClasses($this->autowireExcludedClasses);

		foreach (static::EXTENSIONS as $name => $extension) {
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

	public function loadContainer(): string
	{
		$loader = new ContainerLoader(
			$this->parameters['tempDir'] . '/cache/Modette.Configurator',
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

	private function checkParametersIntegrity(): void
	{
		if ((bool) $this->parameters['productionMode'] === (bool) $this->parameters['debugMode']) {
			throw new InvalidStateException(sprintf(
				'Parameters "productionMode" and "debugMode" of %s must be opposite. Set them with "setDebugMode" or "addParameters"',
				static::class
			));
		}
	}

	public function createContainer(): Container
	{
		$this->enableDebugger();

		// Used by cache generator to create containers
		$this->addServices(['configurator' => $this]);

		$this->checkParametersIntegrity();

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

	public function isLocalhost(): bool
	{
		$list = [];
		if (!isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !isset($_SERVER['HTTP_FORWARDED'])) { // Forwarded for BC, X-Forwarded-For is standard
			$list[] = '127.0.0.1';
			$list[] = '::1';
		}
		$address = $_SERVER['REMOTE_ADDR'] ?? php_uname('n');
		return in_array($address, $list, true);
	}

	public function isEnvironmentDebugMode(): bool
	{
		$debug = getenv('NETTE_DEBUG');
		return $debug !== false && (strtolower($debug) === 'true' || $debug === '1');
	}

	/**
	 * Collect environment parameters with NETTE__ prefix
	 *
	 * @return mixed[]
	 */
	public function getEnvironmentParameters(): array
	{
		$map = function (&$array, array $keys, $value) use (&$map) {
			if (count($keys) <= 0) {
				return $value;
			}

			$key = array_shift($keys);

			if (!is_array($array)) {
				throw new InvalidStateException(sprintf('Invalid structure for key "%s" value "%s"', implode($keys), $value));
			}

			if (!array_key_exists($key, $array)) {
				$array[$key] = [];
			}

			// Recursive
			$array[$key] = $map($array[$key], $keys, $value);

			return $array;
		};

		$parameters = [];
		foreach ($_SERVER as $key => $value) {
			// Ensure value
			$value = getenv($key);
			if ($value !== false && strpos($key, 'NETTE__') === 0) {
				// Parse NETTE__{NAME-1}__{NAME-N}
				$keys = explode('__', strtolower(substr($key, 7)));
				// Make array structure
				$map($parameters, $keys, $value);
			}
		}

		return $parameters;
	}

}
