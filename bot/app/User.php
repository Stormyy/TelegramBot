<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\User
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Chat[] $chat
 * @mixin \Eloquent
 */
class User extends Model
{
   protected $table = 'user';

   public function chat(){
      return $this->belongsToMany(Chat::class);
   }

   public function getName(){
      if($this->username){
         return "@".$this->username;
      } else {
         return $this->firstname;
      }
   }
}
