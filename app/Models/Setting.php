<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'type', 'group'];

    /**
     * Get a setting by key
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        $cacheKey = 'setting_' . $key;
        
        // Try to get from cache first
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        // If not in cache, get from database
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }
        
        $value = self::formatValue($setting->value, $setting->type);
        
        // Cache the result for future use
        Cache::put($cacheKey, $value, now()->addDay());
        
        return $value;
    }
    
    /**
     * Set a setting value
     * 
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @param string $group
     * @return bool
     */
    public static function set($key, $value, $type = 'string', $group = 'general')
    {
        $setting = self::firstOrNew(['key' => $key]);
        
        $setting->value = $value;
        $setting->type = $type;
        $setting->group = $group;
        
        $result = $setting->save();
        
        if ($result) {
            // Update cache
            Cache::put('setting_' . $key, self::formatValue($value, $type), now()->addDay());
        }
        
        return $result;
    }
    
    /**
     * Format value based on type
     * 
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    private static function formatValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return (bool) $value;
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'array':
            case 'object':
                return json_decode($value, true);
            case 'file':
            case 'image':
                return asset('storage/' . $value);
            default:
                return $value;
        }
    }
    
    /**
     * Clear settings cache
     * 
     * @param string|null $key
     * @return void
     */
    public static function clearCache($key = null)
    {
        if ($key) {
            Cache::forget('setting_' . $key);
        } else {
            $keys = self::all()->map(function($setting) {
                return 'setting_' . $setting->key;
            });
            
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }
}
