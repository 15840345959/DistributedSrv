<?php

/**
 * Created by PhpStorm.
 * User: HappyQi
 * Date: 2017/9/28
 * Time: 10:19
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AD extends Model
{
    use SoftDeletes;    //使用软删除
    protected $table = 't_ad_info';
    public $timestamps = true;
    protected $dates = ['deleted_at'];  //软删除
}


