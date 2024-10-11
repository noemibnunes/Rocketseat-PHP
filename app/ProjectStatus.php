<?php

namespace App;

enum ProjectStatus: string
{
    case Open = 'open';
    case Closed = 'close';

    public function label():string 
    {
        return match ($this) { // faz funcao de IF
            self::Open => 'Aceitando propostas.',
            self::Closed => 'Encerrado.'
        };
    }
}
