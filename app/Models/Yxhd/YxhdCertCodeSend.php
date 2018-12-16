<?php

/**
 * Created by PhpStorm.
 * User: HappyQi
 * Date: 2017/9/28
 * Time: 10:19
 */

namespace App\Models\Yxhd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class YxhdCertCodeSend extends Model
{
    use SoftDeletes;    //使用软删除
    protected $table = 'yxhd_certCode_send';
    public $timestamps = true;
    protected $dates = ['deleted_at'];  //软删除
}


