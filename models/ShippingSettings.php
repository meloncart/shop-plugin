<?php namespace MelonCart\Shop\Models;

use Model;
use RainLab\Location\Models\Country;
use RainLab\Location\Models\State;

class ShippingSettings extends Model
{
	public $implement = ['System.Behaviors.SettingsModel'];

	// A unique code
	public $settingsCode = 'mc_shop_shippingsettings';

	// Reference to field configuration
	public $settingsFields = 'fields.yaml';

	protected $cache = [];

	public function getOriginCountryIdOptions()
	{
		return Country::getNameList();
	}

	public function getOriginStateIdOptions()
	{
		return State::getNameList($this->origin_country_id);
	}

	public function getDefaultCountryIdOptions()
	{
		return Country::getNameList();
	}

	public function getDefaultStateIdOptions()
	{
		return State::getNameList($this->origin_country_id);
	}

	public function getWeightUnitOptions()
	{
		return [
			'LBS' => 'Pounds',
			'KGS' => 'Kilograms',
		];
	}

	public function getDimensionUnitOptions()
	{
		return [
			'IN' => 'Inches',
			'CM' => 'Centimeters',
		];
	}
}