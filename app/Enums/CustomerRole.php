<?php

namespace App\Enums;

enum CustomerRole:string
{
    case visitor = 'visitor';
    case customer = 'customer';
}
