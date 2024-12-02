<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServerFitur extends Model
{
    protected $table = 'serverfitur';

    public function fiturLevel()
    {
        return $this->hasMany(FiturLevel::class, 'KodeFitur', 'KodeFitur');
    }
}
