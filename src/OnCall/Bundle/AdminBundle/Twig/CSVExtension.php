<?php

namespace OnCall\Bundle\AdminBundle\Twig;

class CSVExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('csv_escape', array($this, 'escapeFilter'))
        );
    }

    public function escapeFilter($string)
    {
        return str_replace('"', '""', $string);
    }

    public function getName()
    {
        return 'csv_extension';
    }
}
