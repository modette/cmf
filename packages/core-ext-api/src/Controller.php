<?php declare(strict_types = 1);

namespace Modette\Api;

use Apitte\Core\Annotation\Controller\Controller as ControllerAnnotation;
use Apitte\Core\Annotation\Controller\ControllerPath;
use Apitte\Core\UI\Controller\IController;

/**
 * @ControllerAnnotation()
 * @ControllerPath("/api")
 */
abstract class Controller implements IController
{

}
