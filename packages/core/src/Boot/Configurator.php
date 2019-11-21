<?php declare(strict_types = 1);

namespace Modette\Core\Boot;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Modette\Core\Boot\Helper\CliHelper;
use Modette\Core\DI\Container;
use Modette\ModuleInstaller\Loading\BaseLoader as ModuleLoader;
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

	private const SERVICE_NAME = 'modette.core.boot.configurator';

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
		$this->loader = $loader;
		$this->parameters = [
			'rootDir' => $this->rootDir,
			'appDir' => $this->rootDir . '/src',
			'logDir' => $this->rootDir . '/var/log',
			'tempDir' => $this->rootDir . '/var/tmp',
			'vendorDir' => $this->rootDir . '/vendor',
			'debugMode' => false,
			'consoleMode' => CliHelper::isCli(),
			'modules' => $this->loader->loadModulesMeta($this->rootDir),
		];
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

	public function enableDebugger(): void
	{
		Debugger::$strictMode = true;
		Debugger::enable(!$this->parameters['debugMode'], $this->parameters['logDir']);
		Bridge::initialize();
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
			$compiler->loadConfig($configFile, $loader);
		}

		$compiler->addConfig(['parameters' => $this->parameters]);
		$compiler->setDynamicParameterNames(array_keys($this->dynamicParameters));

		$builder = $compiler->getContainerBuilder();
		$builder->addExcludedClasses($this->autowireExcludedClasses);
		$builder->addImportedDefinition(self::SERVICE_NAME)
			->setType(static::class);

		$compiler->addExtension('extensions', new ExtensionsExtension());

		$this->onCompile($this, $compiler);
	}

	public function loadContainer(): string
	{
		$this->loader->configureSwitch('consoleMode', $this->parameters['consoleMode']);
		$this->loader->configureSwitch('debugMode', $this->parameters['debugMode']);

		$configFiles = $this->loader->loadConfigFiles($this->rootDir);

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
		$containerClass = $this->loadContainer();
		$container = new $containerClass($this->dynamicParameters);
		assert($container instanceof Container);

		foreach ($this->services as $name => $service) {
			$container->addService($name, $service);
		}

		$container->addService(self::SERVICE_NAME, $this);

		$container->initialize();

		return $container;
	}

}
