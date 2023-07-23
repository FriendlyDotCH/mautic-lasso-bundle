<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticLassoBundle\Helper;

use Symfony\Component\HttpFoundation\Request;

class RequestHelper
{
    public static function isLasoRequest(Request $request): bool
    {
        $requestUrl = $request->getRequestUri();

        // Check if /lasso
        $isApiRequest = (false !== strpos($requestUrl, '/lasso'));

        defined('MAUTIC_API_REQUEST') or define('MAUTIC_API_REQUEST', $isApiRequest);

        return $isApiRequest;
    }

    
}
