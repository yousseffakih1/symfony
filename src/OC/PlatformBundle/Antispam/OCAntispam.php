<?php
// src/OC/PlatformBundle/Antispam/OCAntispam.php

namespace OC\PlatformBundle\Antispam;

class OCAntispam
{
    public function isSpam($txt){

      return strlen($txt) > 50;
    }
}
