<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthLog extends Model
{
    use HasFactory;
    protected $table = 'auth_logs';
    protected $primaryKey = 'log_id';
    public $incrementing = true;
    public $timestamps = false;
    
    protected $fillable = [
        'log_id',
        'employee_id',
        'status',
        'timestamp'
    ];

    // ความสัมพันธ์กับตาราง Employees
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

}
