<?php


class ZendValidatePesel extends Zend\Validator\AbstractValidator
{
    const PESEL_VRONG = 'empty';

    protected $messageTemplates = array(
        self::PESEL_VRONG => "'%value%' nie jest poprawnym numerem PESEL"
    );

    public function isValid($value)
    {
        $wagi = [9,7,3,1,9,7,3,1,9,7];
        $this->setValue($value);

        if (!is_float($value)) {
            $this->error(self::FLOAT);
            return false;
        }

        return true;
    }
}