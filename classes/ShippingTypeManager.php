<?php namespace MelonCart\Shop\Classes;

use Str;
use File;
use Lang;
use Closure;
use October\Rain\Support\Yaml;
use Illuminate\Container\Container;
use System\Classes\PluginManager;
use System\Classes\SystemException;

/**
 * Widget manager
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class ShippingTypeManager
{
	use \October\Rain\Support\Traits\Singleton;

	/**
	 * @var array An array of menu item types.
	 */
	protected $itemTypes;

	/**
	 * Initialize this singleton.
	 */
	protected function init()
	{
		$this->pluginManager = PluginManager::instance();
	}

	//
	// Form Widgets
	//

	/**
	 * Returns a list of registered form widgets.
	 * @return array Array keys are class names.
	 */
	public function listItemTypes()
	{
		if ($this->itemTypes === null) {
			$this->itemTypes = [];

			/*
			 * Load plugin menu item types
			 */
			$plugins = $this->pluginManager->getPlugins();
			$methodName = 'register_meloncart_shipping_types';

			foreach ($plugins as $plugin) {
				// Plugins doesn't have a register_menu_item_types method
				if ( !method_exists($plugin, $methodName) )
					continue;

				// Plugin didn't register any menu item types
				if ( !is_array($types = $plugin->$methodName()) )
					continue;

				foreach ($types as $className => $typeInfo)
					$this->registerItemType($className, $typeInfo);
			}
		}

		return $this->itemTypes;
	}

	/*
	 * Registers a single form form widget.
	 */
	public function registerItemType($className, $widgetInfo = null)
	{
		$this->itemTypes[$className] = $widgetInfo;
	}

	/**
	 * Manually registers form widget for consideration.
	 */
	public function registerItemTypes(array $itemTypes)
	{
		foreach ( $itemTypes as $className => $widgetInfo )
			$this->registerItemType( $className, $widgetInfo );
	}

	/**
	 * Returns a class name from a form widget alias
	 * Normalizes a class name or converts an alias to it's class name.
	 * @return string The class name resolved, or null.
	 */
	public function resolveItemType($className)
	{
		if ($this->itemTypes === null)
			$this->listItemTypes();

		if (isset($this->itemTypes[$className]))
			return $this->itemTypes[$className];

		return null;
	}
}
