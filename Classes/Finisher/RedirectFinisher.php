<?php

declare(strict_types=1);
namespace FriendsOfTYPO3\HeadlessPowermail\Finisher;

use In2code\Powermail\Domain\Service\RedirectUriService;
use In2code\Powermail\Utility\FrontendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RedirectFinisher extends \In2code\Powermail\Finisher\RedirectFinisher
{
    /**
     * Redirect user after form submit
     *
     * @return void
     */
    public function redirectToUriFinisher(): void
    {
        $redirectService = GeneralUtility::makeInstance(RedirectUriService::class, $this->contentObject);
        $uri = $redirectService->getRedirectUri();
        if (!empty($uri) && $this->isRedirectEnabled()) {
            header('Content-Type: application/json');
            echo json_encode([
                'redirectUrl' => $uri,
                'statusCode' => 303,
            ]);
            die;
        }
    }
}
