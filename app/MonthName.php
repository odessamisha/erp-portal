<?php

namespace App;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum MonthName:string implements HasLabel
{
    case Jan = 'Январь';
    case Feb = 'Февраль';
    case Mar = 'Март';
    case Apr = 'Апрель';
    case May = 'Май';
    case Jun = 'Июнь';
    case Jul = 'Июль';
    case Aug = 'Август';
    case Sep = 'Сентябрь';
    case Oct = 'Октябрь';
    case Nov = 'Ноябрь';
    case Dec = 'Декабрь';
    //
    public function getLabel(): ?string
    {
        // TODO: Implement getLabel() method.
        return $this->name;
    }
}
