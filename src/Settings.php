<?php

namespace RolfHaug\ModelSettings;

trait Settings
{
    protected $settingsModel;
    protected $settings;

    /**
     * Get the name of the related Settings Model
     *
     * @return string
     */
    protected function getSettingsModel()
   {
       return studly_case( __CLASS__ . "_settings");
   }

    public function scopeWhereSetting($query, $setting, $value)
    {
        return $query->whereIn('id', $this->getSettingsModel()::select('user_id')
            ->where('setting', strtoupper($setting))
            ->where('value', ModelSettings::encodeValue($value))
            ->pluck('user_id')
        );
    }

    public function scopeWhereHasSetting($query, $setting) {
       return $query->whereIn('id', $this->getSettingsModel()::select('user_id')->where('setting', strtoupper($setting))->pluck('user_id'));
    }

    public function scopeWhereDoesntHaveSetting($query, $setting) {
        return $query->whereNotIn('id', $this->getSettingsModel()::select('user_id')->where('setting', strtoupper($setting))->pluck('user_id'));
    }

    protected function settings()
    {
        if($this->settings) {
            return $this->settings;
        }

        $this->settings = new ModelSettings($this, $this->getSettingsModel());

        return $this->settings;
    }

    public function getSettingsAttribute()
    {
        return $this->settings();
    }
}