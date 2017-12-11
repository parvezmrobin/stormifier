<?php
/**
 * User: Parvez
 * Date: 12/11/2017
 * Time: 5:32 AM
 */

namespace Stormifier\Console;


use Symfony\Component\Console\Output\ConsoleOutputInterface;

class Console
{
    function __construct(ConsoleOutputInterface $consoleOutput)
    {
        $this->consoleOutput = $consoleOutput;
    }
}