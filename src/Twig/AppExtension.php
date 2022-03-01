<?php

namespace App\Twig;

use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

class AppExtension extends AbstractExtension
{

    private $noteTravailRepository;

    public function getFilters()
    {
        return [
            new TwigFilter('extractText', [$this, 'extractTextFromBD'])
        ];
    }

    public function extractTextFromBD($content, $length = 10)
    {

        $content = $content;
        if (strlen($content) > $length) {
            $content = substr($content, 0, $length);
            $content = substr($content, 0, strrpos($content, ' ')) . ' ...';
        }


        return str_replace('<p>', '', $content);
    }
}
