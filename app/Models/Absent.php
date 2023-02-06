<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absent extends Model
{

    protected $fillable = array('user_id', 'lokasi', 'foto', 'tgl_absen');

    // untuk melakukan update field create_at dan updated_at secara otomatis
    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

}