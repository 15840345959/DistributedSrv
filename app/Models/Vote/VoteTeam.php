<?php

/**
 * Created by PhpStorm.
 * User: HappyQi
 * Date: 2017/9/28
 * Time: 10:19
 */

namespace App\Models\Vote;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoteTeam extends Model
{
    use SoftDeletes;    //使用软删除
    protected $table = 'vote_out_team';
    public $timestamps = true;
    protected $dates = ['deleted_at'];  //软删除

    public function voteActivity () {
        return $this->hasMany(VoteActivity::class, 'vote_team_id', 'id');
    }


}


