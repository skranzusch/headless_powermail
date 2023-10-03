<?php

declare(strict_types=1);

namespace FriendsOfTYPO3\HeadlessPowermail\ViewHelpers\Form;

/**
 * Class CountriesViewHelper
 */
class CountriesViewHelper extends \In2code\Powermail\ViewHelpers\Form\CountriesViewHelper
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

        // EXT:powermail createse a simple one-dimensional array like this:
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
