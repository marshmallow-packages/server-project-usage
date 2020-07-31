<?php

namespace Marshmallow\Server\ProjectUsage;

use Exception;
use RuntimeException;
use FilesystemIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DataGenerator
{
	protected $data;

	public function __construct()
	{
		$this->prepareDataStructureWithDefault();
	}

	public function generate()
	{
		return $this
					->setDatabaseInformation()
					->setPackages()
					->setStorage('root', base_path())
					->setStorage('storage', storage_path())
					;
	}

	public function publishPackages()
	{
		/**
		 * Only keep packages in the data object.
		 * @var [type]
		 */
		foreach ($this->data['data'] as $key => $data) {
			if ($key != 'packages') {
				unset($this->data['data'][$key]);
			}
		}

		return $this->setPackages()
			 	    ->publish();
	}

	public function publish()
	{
		if (!class_exists(Http::class)) {
			/**
			 * Fallback for Laravel 6.x
			 */
			$client = new \GuzzleHttp\Client;
			$client->request('POST', config('project-usage.api_endpoint'), [
			    'json' => $this->data,
			]);
		} else {
			Http::withHeaders([
				'Accept' => 'application/json',
				'Content-Type' => 'application/json',
			])->post(config('project-usage.api_endpoint'), $this->data);
		}

		return $this;
	}

	protected function setDatabaseInformation()
	{
		$tables = DB::select("show table status");

		$size = 0;
		foreach ($tables as $table) {
			$size += $table->Data_length + $table->Index_length;
		}
		$this->data['data']['database']['size'] = $size;
		$this->data['data']['database']['table_count'] = count($tables);

		return $this;
	}

	protected function setPackages()
	{
		$composer_file = base_path() . '/composer.json';
		if (!file_exists($composer_file)) {
			throw new Exception('No composer.json file found');
		}

		$composer_lock_file = base_path() . '/composer.lock';
		if (!file_exists($composer_lock_file)) {
			throw new Exception('No composer.lock file found');
		}

		$all_packages = [];
		$composer_lock = json_decode(file_get_contents($composer_lock_file));
		foreach ($composer_lock->packages as $package) {
			$all_packages[$package->name] = $package->version;
		}

		$composer_packages = [];
		$composer = json_decode(file_get_contents($composer_file));
		foreach ($composer->require as $package => $version) {
			if (!isset($all_packages[$package])) {
				continue;
			}

			$composer_packages[$package] = $all_packages[$package];
			unset($all_packages[$package]);
		}


		$this->data['data']['packages']['dependencies'] = $all_packages;
		$this->data['data']['packages']['composer'] = $composer_packages;

		return $this;
	}

	protected function getDirectorySize($path)
	{
	    $bytestotal = 0;
	    $path = realpath($path);
	    if ($path!==false && $path!='' && file_exists($path)) {
	        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object) {
	            try {
					$bytestotal += $object->getSize();
				} catch (RuntimeException $e) {
					//
				}
	        }
	    }
	    return $bytestotal;
	}

	public function output()
	{
		return json_encode($this->data);
	}

	protected function setStorage($key, $path)
	{
		$size = $this->getDirectorySize($path);
		$this->data['data']['storage'][$key] = $size;
		return $this;
	}

	public static function shouldRun()
	{
		if (!config('project-usage.customer_id')) {
			return false;
		}
		if (!config('project-usage.project_id')) {
			return false;
		}
		if (!config('project-usage.api_endpoint')) {
			return false;
		}
		return true;
	}

	protected function prepareDataStructureWithDefault()
	{
		$this->data = [
			'customer_id' => config('project-usage.customer_id'),
			'project_id' => config('project-usage.project_id'),
			'data' => [
				'server' => [
					'php_version' => phpversion(),
				],
				'database' => [
					'size' => 0, // bytes
				],
				'storage' => [
					'root' => 0, // bytes
					'storage' => 0, // bytes
				],
				'packages' => [
					'composer' => [],
					'dependencies' => [],
				],
			],
		];
	}
}
