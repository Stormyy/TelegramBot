<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Chat
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $user
 * @mixin \Eloquent
 */
class Chat extends Model
{
   protected $table = 'chat';

   public function user(){
      return $this->belongsToMany(User::class);
   }
}
