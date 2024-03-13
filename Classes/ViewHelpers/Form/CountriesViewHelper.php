<?php

declare(strict_types=1);

namespace FriendsOfTYPO3\HeadlessPowermail\ViewHelpers\Form;

use \In2code\Powermail\ViewHelpers\Form\CountriesViewHelper as Powermail_CountriesViewHelper;

class CountriesViewHelper extends Powermail_CountriesViewHelper
{
    /**
     * Get array with countries
     *
     * @return array
     * @throws PropertyNotAccessibleException
     */
    public function render(): array
    {
        $countries = [];

        // EXT:powermail creates a simple one-dimensional array like this:
        // [
        //    'AND' => 'Andorra',
        //    'ARE' => 'الإمارات العربيّة المتّحدة',
        //    'AFG' => 'افغانستان',
        //    ...
        // ]
        $rawCountries = parent::render();

        // We want to return a multi-dimensional array though!
        foreach ($rawCountries as $value => $label) {
            $countries[] = [
                'value' => $value,
                'label' => $label,
                'selected' => false,
            ];
        }

        return $countries;
    }
}
