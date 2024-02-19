<?php

namespace App\Enums;

enum ServiceIdentifier: string
{
    case Backoffice = 'backoffice';
    case Storage = 'storage';
    case Proxy = 'proxy';
    case Speech = 'speech';
}
