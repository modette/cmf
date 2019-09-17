<?php declare(strict_types = 1);

namespace Modette\Core\Boot;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Modette\Core\Boot\Helper\CliHelper;
use Modette\Core\DI\Container;
use Modette\ModuleInstaller\Loading\Loader as ModuleLoader;
use Nette\DI\Compiler;
use Nette\DI\Config\Loader;
use Nette\DI\ContainerLoader;
use Nette\DI\Extensions\ExtensionsExtension;
use Nette\Schema\Helpers as ConfigHelpers;
use Nette\SmartObject;
use stdClass;
use Tracy\Bridges\Nette\Bridge;
use Tracy\Debugger;
use Traversable;

/**
 * @method void onCompile(Configurator $configurator, Compiler $compiler)
 */
class Configurator
{

	use SmartObject;

	/** @var callable[] function(Configurator $configurator, Compiler $compiler): void; Occurs after the compiler is created */
	public $onCompile = [];

	/** @var string[] classes which shouldn't be autowired */
	public $autowireExcludedClasses = [ArrayAccess::class, Countable::class, IteratorAggregate::class, stdClass::class, Traversable::class];

	/** @var string */
	private $rootDir;

	/** @var mixed[] */
	private $parameters;

	/** @var mixed[] */
	private $dynamicParameters = [];

	/** @var object[] */
	private $services = [];

	/** @var ModuleLoader */
	private $loader;

	public function __construct(string $rootDir, ModuleLoader $loader)
	{
		$this->rootDir = str_replace('\\', '/', $rootDir);
		$this->parameters = $this->getDefaultParameters();
		$this->loader = $loader;

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

	public function setDebugMode(bool $debugMode): void
	{
		$this->parameters['debugMode'] = $debugMode;
	}

	private function enableDebugger(): void
	{
		Debugger::$strictMode = true;
		Debugger::enable(!$this->parameters['debugMode'], $this->parameters['logDir']);
		Bridge::initialize();
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
			'tempDir' => $this->rootDir . '/var/tmp',
			'vendorDir' => $this->rootDir . '/vendor',
			'debugMode' => false,
			'consoleMode' => CliHelper::isCli(),
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

	/**
	 * @param string[] $configFiles
	 */
	private function generateContainer(Compiler $compiler, array $configFiles): void
	{
		$loader = new Loader();
		$loader->setParameters($this->parameters);

		foreach ($configFiles as $configFile) {
			$compiler->loadConfig($configFile);
		}

		$compiler->addConfig(['parameters' => $this->parameters]);
		$compiler->setDynamicParameterNames(array_keys($this->dynamicParameters));

		$builder = $compiler->getContainerBuilder();
		$builder->addExcludedClasses($this->autowireExcludedClasses);
		$builder->addImportedDefinition('modette.core.boot.configurator')
			->setType(static::class);

		$compiler->addExtension('extensions', new ExtensionsExtension());

		$this->onCompile($this, $compiler);
	}

	/**
	 * @return string[]
	 */
	private function loadConfigFiles(): array
	{
		$files = [];

		foreach ($this->loader->getConfigFiles($this->parameters) as $file) {
			$files[] = $this->rootDir . $file;
		}

		return $files;
	}

	public function loadContainer(): string
	{
		$configFiles = $this->loadConfigFiles();

		$this->parameters['productionMode'] = !$this->parameters['debugMode'];
		$this->parameters['httpMode'] = !$this->parameters['consoleMode'];

		$loader = new ContainerLoader(
			$this->parameters['tempDir'] . '/cache/modette.configurator',
			$this->parameters['debugMode']
		);

		$class = $loader->load(
			function (Compiler $compiler) use ($configFiles): void {
				$this->generateContainer($compiler, $configFiles);
			},
			[$this->parameters, array_keys($this->dynamicParameters), $configFiles, PHP_VERSION_ID - PHP_RELEASE_VERSION]
		);

		return $class;
	}

	public function initializeContainer(): Container
	{
		$this->enableDebugger();

		$containerClass = $this->loadContainer();
		/** @var Container $container */
		$container = new $containerClass($this->dynamicParameters);

		foreach ($this->services as $name => $service) {
			$container->addService($name, $service);
		}

		$container->addService('modette.core.boot.configurator', $this);

		$container->initialize();

		return $container;
	}

}
