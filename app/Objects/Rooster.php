<?php
/**
 * @package zsm-rooster
 * @author     Jeroen Nijhuis <j.nijhuis@epartment.nl>
 * @copyright  2016-2017 Epartment Ecommerce B.V.
 */

namespace App\Objects;

class Rooster
{
    public $naam;
    public $voornaam;
    public $personeelsNummer;
    public $functie;

    public $roosterPerDag = [];
}