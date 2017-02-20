<?php
namespace Application\Validator;

use Zend\Validator\AbstractValidator;

class Npwz extends AbstractValidator
{
    const NPWZ_VRONG = 'empty';

    protected $messageTemplates = array(
        self::PESEL_VRONG => "'%value%' nie jest poprawnym numerem PESEL"
    );

    public function isValid($value)
    {
        $this->setValue($value);
        if($this->npwzValidate($value))
        {
            return true;
        } else {
            $this->error(self::PESEL_VRONG);
        }
    }
    private function npwzValidate($pesel)
{
    $sum = 0;
    $weights = array(1, 2, 3, 4, 5, 6); // Wagi dla kolejnych cyfr numeru NPWZ

    foreach (str_split($pesel) as $position => $digit) {
        $sum += $digit * $weights[$position];
    }

    if (substr($sum % 11, -1, 1) == 0){
        return true;
    } else {
        return false;
    }
}
}