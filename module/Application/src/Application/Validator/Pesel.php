<?php
namespace Application\Validator;

use Zend\Validator\AbstractValidator;

class Pesel extends AbstractValidator
{
    const PESEL_VRONG = 'empty';

    protected $messageTemplates = array(
        self::PESEL_VRONG => "'%value%' nie jest poprawnym numerem PESEL"
    );

    public function isValid($value)
    {
        $this->setValue($value);
        $pesel = $value;
        if($this->peselValidate($pesel))
        {
            return true;
        } else {
            $this->error(self::PESEL_VRONG);
        }
    }
    private function peselValidate($pesel)
{
    $sum = 0;
    $weights = array(1, 3, 7, 9, 1, 3, 7, 9, 1, 3, 1); // Wagi dla kolejnych cyfr numeru PESEL

    foreach (str_split($pesel) as $position => $digit) {
        $sum += $digit * $weights[$position];
    }

    if (substr($sum % 10, -1, 1) == 0){
        return true;
    } else {
        return false;
    }
}
}