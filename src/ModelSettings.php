<?php

namespace RolfHaug\ModelSettings;

use Illuminate\Database\Eloquent\Model;

class ModelSettings
{
    protected $model;
    protected $driver;
    protected $settings = [];

    /**
     * ModelSettings constructor.
     *
     * @param Model $model
     * @param $driver String. Name of the Eloquent model that stores the settings
     */
    public function __construct(Model $model, $driver)
    {
        $this->model = $model;
        $this->driver = studly_case($driver);
    }

    public static function decodeValue($value)
    {
        if($value === 'bool:true') {
            $value = true;
        }elseif($value === 'bool:false') {
            $value = false;
        }

        elseif(substr($value, 0, 4) == 'int:') {
            $value = (int) substr($value,4);
        }

        return $value;
    }

    public static function encodeValue($value)
    {
        // boolean
        if(is_bool($value)) $value = $value ? 'bool:true' : 'bool:false';

        // Laravel request transform bool to string. Cast to bool
        elseif($value === "true") $value = self::encodeValue(true);

        elseif($value === "false") $value = self::encodeValue(false);

        // Integers
        elseif(is_int($value)) $value = 'int:' . $value;

        return $value;
    }

    /**
     * Load settings from database
     */
    protected function loadSettings()
    {
        $settings = $this->driver::where($this->model->getForeignKey(), $this->model->getKey())->get();

        foreach($settings as $row)
        {
            $this->registerSetting($row->setting, $row->value);
        }
    }


    /**
     * Register and normalize a Setting
     *
     * @param $setting
     * @param $value
     */
    protected function registerSetting($setting, $value)
    {
        $this->settings[strtolower($setting)] = self::decodeValue($value);
    }

    /**
     * Return all of the model's settings
     *
     * @return array
     */
    public function all()
    {
        if( ! $this->settings)
        {
            $this->loadSettings();
        }

        return $this->settings;
    }

    /**
     * Accessor to get a model setting
     *
     * @param $setting
     * @return mixed|null
     */
    public function __get($setting)
    {
        if( ! $this->settings)
        {
            $this->loadSettings();
        }

        if(key_exists($setting, $this->settings))
        {
            return $this->settings[$setting];
        }

        return NULL;
    }

    /**
     * Set and persist a model setting
     *
     * @param $setting
     * @param mixed $value
     * @throws \Exception
     */
    public function set($setting, $value = null)
    {
        if(is_array($setting))
        {
            foreach($setting as $key => $value)
            {
                if(is_array($value) or is_null($value)) {
                    continue;
                }

                $this->set($key, $value);
            }
            return;
        } elseif(is_null($value)) {
            throw new \Exception("Value of setting \"" . $setting . "\" cannot be NULL");
        }

        $setting = strtoupper($setting);

        $settings = $this->driver::where($this->model->getForeignKey(), $this->model->getKey())->where('setting', $setting)->first();

        if($settings)
        {
            // Return if setting is already set with the same value
            if($value === self::decodeValue($settings->value)) {
                return;
            }

            $settings->value = self::encodeValue($value);
        } else {

            // forceFill to avoid to set the foreign key in the driver's fillable array
            $settings = (new $this->driver)->forceFill([
                $this->model->getForeignKey() => $this->model->getKey(),
                'setting' => $setting,
                'value' => self::encodeValue($value)
            ]);
        }

        $settings->save();

        $this->registerSetting($setting, $value);
    }

    public function delete($setting)
    {
        return $this->driver::where($this->model->getForeignKey(), $this->model->getKey())->where('setting', strtoupper($setting))->delete();
    }
}