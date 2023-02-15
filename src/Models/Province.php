<?php

namespace Nttps\Thailand\Models;

use Nttps\Countries\Models\Country;
use Nttps\Laravel\Model;
use Nttps\Thailand\Models\Geography;
use Illuminate\Support\Facades\App;

class Province extends Model
{
    // use \Nttps\Laravel\Traits\Moderatable;
    // use \Nttps\Laravel\Traits\Ownable;
    // use \Illuminate\Database\Eloquent\Concerns\HasUuids;
    use \Illuminate\Database\Eloquent\SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'provinces';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (is_null($model->id)) {
                $model->id = Province::max('id') + 1;
            }
        });
    }

    /**
     * The data type of the auto-incrementing ID.
     * NB: PostgreSQL is strict and does not do any magic typecasting for you
     * NB2: dim_audits.auditable_id exepects string
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        //
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        //
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        //
    ];

    /**
     * The attributes that should be saved as file in the storage
     */
    protected $files = [
        //
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'country_id',
        'geography_id',
        'id',
        'name_english',
        'name_thai',
    ];

    /**
     * The attributes that should be hashed on save.
     *
     * @var array
     */
    protected $hashed = [
        //
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        //
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
        // 'country',
        // 'geography',
    ];

    /**
     * Returns the string representation of the Reflection method object
     */
    public function __toString()
    {
        if (App::isLocale('th')) {
            return 'จังหวัด' . $this->name_thai;
        }
        return $this->name_english ?? '';
    }

    /**
     * Attribute Getters
     */
    // public function getExampleAttribute()
    // {
    //     return $this->attributes['example'] / 2;
    // }

    /**
     * Attribute Setters
     */
    // public function setExampleAttribute($newExample)
    // {
    //     $this->attributes['example'] = $newExample * 2;
    // }

    /**
     * Relationships
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function districts()
    {
        return $this->hasMany(District::class);
    }

    public function geography()
    {
        return $this->belongsTo(Geography::class);
    }

    /**
     * Scopes
     */
    // public function scopeExample(Builder $query, $q) {
    //     return $query->where('example', '=', $q);
    // }
}
