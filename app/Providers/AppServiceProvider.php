<?php

namespace App\Providers;

use App\Components\UserManager;
use App\Components\Yxhd\YxhdActivityManager;
use App\Models\User;
use App\Models\Yxhd\YxhdActivity;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        User::saved(function($info){
            $class = new UserManager();

            $class_name = substr(explode('\\', get_class($class))[count(explode('\\', get_class($class))) - 1],0, -7);

            $cacheKey = "$class_name:$info->id";

            $cacheData = Cache::get($cacheKey);

            if(!$cacheData){
                Cache::add($cacheKey, $info,60*24*7);
            }else{
                Cache::put($cacheKey, $info,60*24*7);
            }
        });

        User::deleted(function($info){
            $class = new UserManager();

            $class_name = substr(explode('\\', get_class($class))[count(explode('\\', get_class($class))) - 1],0, -7);

            $cacheKey = "$class_name:$info->id";

            $cacheData = Cache::get($cacheKey);

            if($cacheData){
                Cache::forget($cacheKey);
            }
        });

        YxhdActivity::saved(function($info){
            $class = new YxhdActivityManager();

            $class_name = substr(explode('\\', get_class($class))[count(explode('\\', get_class($class))) - 1],0, -7);

            $cacheKey = "$class_name:$info->id";

            $cacheData = Cache::get($cacheKey);

            if(!$cacheData){
                Cache::add($cacheKey, $info,60*24*7);
            }else{
                Cache::put($cacheKey, $info,60*24*7);
            }
        });

        YxhdActivity::deleted(function($info){
            $class = new YxhdActivityManager();

            $class_name = substr(explode('\\', get_class($class))[count(explode('\\', get_class($class))) - 1],0, -7);

            $cacheKey = "$class_name:$info->id";

            $cacheData = Cache::get($cacheKey);

            if($cacheData){
                Cache::forget($cacheKey);
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
