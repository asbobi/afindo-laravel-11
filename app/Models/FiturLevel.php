<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FiturLevel extends Model
{
    protected $table = 'fiturlevel';

    public function aksesLevel()
    {
        return $this->belongsTo(AksesLevel::class, 'KodeLevel', 'KodeLevel');
    }

    public function serverFitur()
    {
        return $this->belongsTo(ServerFitur::class, 'KodeFitur', 'KodeFitur');
    }
}
