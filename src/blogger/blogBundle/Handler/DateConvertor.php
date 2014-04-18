<?php

namespace Hotel\reserveBundle\Handler;

use CybersExperts\Bundle\JalaliDateBundle\Service\DateConverter;
use CybersExperts\Bundle\JalaliDateBundle\Service\JalaliDateTime;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class DateConvertor
 * @package Hotel\reserveBundle\Handler
 */
class DateConvertor
{

    /**
     * @var \CybersExperts\Bundle\JalaliDateBundle\Service\DateConverter
     */
    private $dateconvertor;
    /**
     * @var \CybersExperts\Bundle\JalaliDateBundle\Service\JalaliDateTime
     */
    private $jalaliDateTime;

    /**
     * @param DateConverter $dateconvertor
     * @param JalaliDateTime $jdatetime
     */
    public function __construct(DateConverter $dateconvertor, JalaliDateTime $jdatetime)
    {
        $this->dateconvertor = $dateconvertor;
        $this->jalaliDateTime = $jdatetime;
    }

    /**
     * @param string $shamsi
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     * @return \DateTime|null
     */
    public function ShamsiToMiladi($shamsi = "")
    {
        if ($shamsi == null || $shamsi == "")
            return null;

        try {
            $shamsiarray = explode("/", $shamsi);

            if(strlen($shamsiarray[0]) != 4 || strlen($shamsiarray[1]) < 1 || strlen($shamsiarray[1]) > 2 || strlen($shamsiarray[2]) < 1 || strlen($shamsiarray[2]) > 2) throw new \Exception("");

            if ($shamsiarray[1] > 12 || $shamsiarray[2] > 31) throw new \Exception();
            if ($shamsiarray[1] > 6 && $shamsiarray[2] > 30) throw new \Exception();
            if ($shamsiarray[1] == 12 && $shamsiarray[2] > 29 && !$this->jalaliDateTime->isLeapYear($shamsiarray[0])) throw new \Exception();

            $miladiarray = $this->dateconvertor->jalaliToGregorian($shamsiarray[0], $shamsiarray[1], $shamsiarray[2]);

            return date_create_from_format("Y/m/d", $miladiarray[0] . "/" . $miladiarray[1] . "/" . $miladiarray[2]);

        } catch (\Exception $e) {
            throw new TransformationFailedException("error in entered date.");
        }
    }

    /**
     * @param \DateTime $miladi
     * @param bool $hasTime
     * @return string
     */
    public function MiladiToShamsi(\DateTime $miladi = null,$hasTime = false)
    {
        if ($miladi == null)
            return "";

        $miladiarray = date_parse(date_format($miladi, "Y/m/d H:i"));
        $shamsiarray = $this->dateconvertor->gregorianToJalali($miladiarray['year'], $miladiarray['month'], $miladiarray['day']);
        return $shamsiarray[0] . "/" . $shamsiarray[1] . "/" . $shamsiarray[2]
        .( $hasTime ? "  " . $this->addZeroBefore($miladiarray['hour']) . ":" .$this->addZeroBefore($miladiarray['minute']) : "" );
    }

    /**
     * @param $year
     * @return bool
     */
    public function isLeap($year)
    {
        return $this->jalaliDateTime->isLeapYear($year);
    }

    /**
     * @param string $shamsi
     * @return int
     */
    public function getWeekDayNumber($shamsi = "")
    {
        $miladiDate = $this->ShamsiToMiladi($shamsi);

        return (date_format($miladiDate, "w") + 1) % 7;
    }

    /**
     * @param integer $number
     * @return string
     */
    private function addZeroBefore($number)
    {
        return $number<10?"0".$number:$number;
    }
}