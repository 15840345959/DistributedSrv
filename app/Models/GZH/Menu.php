<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/9
 * Time: 15:52
 */

namespace App\Models\GZH;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;    //使用软删除
    protected $table = 't_menu_info';
    public $timestamps = true;
    protected $dates = ['deleted_at'];  //软删除
}