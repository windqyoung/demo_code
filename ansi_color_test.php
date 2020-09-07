<?php

namespace ansi_color_test;

function ansi7($sleep_micro = 0)
{
    echo "\n默认7位\n";
    for ($i = 0; $i < 128; $i++) {
        echo "\e[{$i}m" . "color[{$i}m" . "\e[m ";
        if ($sleep_micro > 0) {
            usleep($sleep_micro);
        }
    }
}

function ansi256fg($sleep_micro = 0)
{
    echo "\n256色字体\n";
    for ($i = 0; $i < 256; $i++) {
        echo "\e[38;5;{$i}m" . "color[38;5;{$i}m" . "\e[m ";
        if ($sleep_micro > 0) {
            usleep($sleep_micro);
        }
    }
}

function ansi256bg($sleep_micro = 0)
{
    echo "\n256色背景\n";
    for ($i = 0; $i < 256; $i++) {
        echo "\e[48;5;{$i}m" . "color[48;5;{$i}m" . "\e[m ";
        if ($sleep_micro > 0) {
            usleep($sleep_micro);
        }
    }
}

function ansi24fg($sleep_micro = 0)
{
    echo "\n24位字体\n";
    $step = 15;
    for ($r = 0; $r < 256; $r += $step) {
        for ($g = 0; $g < 256; $g += $step) {
            for ($b = 0; $b < 256; $b += $step) {
                echo "\e[38;2;{$r};{$g};{$b}m" . "color[38;2;{$r};{$g};{$b}m" . "\e[m ";
                if ($sleep_micro > 0) {
                    usleep($sleep_micro);
                }
            }
        }
    }
}

function ansi24bg($sleep_micro = 0)
{
    echo "\n24位背景\n";
    $step = 15;
    for ($r = 0; $r < 256; $r += $step) {
        for ($g = 0; $g < 256; $g += $step) {
            for ($b = 0; $b < 256; $b += $step) {
                echo "\e[48;2;{$r};{$g};{$b}m" . "color[48;2;{$r};{$g};{$b}m" . "\e[m ";
                if ($sleep_micro > 0) {
                    usleep($sleep_micro);
                }
            }
        }
    }
}


function ansi_date()
{
    $step = 15;
    $star = 0;
    for ($r = 0; $r < 256; $r += $step) {
        for ($g = 0; $g < 256; $g += $step) {
            for ($b = 0; $b < 256; $b += $step) {
                echo "\e[2K\r\e[38;2;{$r};{$g};{$b}m"
                    . date('c')
                    . "\e[m"
                    . str_repeat('*', $star % 20);

                $star++;
                usleep(100000);
            }
        }
    }
}

$sleep_micro = isset($argv[1]) ? $argv[1] : 0;
if ($sleep_micro === 'date') {
    while (true) {
        ansi_date();
    }
    exit;
}

ansi7($sleep_micro);
ansi256fg($sleep_micro);
ansi256fg($sleep_micro);
ansi24fg($sleep_micro);
ansi24bg($sleep_micro);

