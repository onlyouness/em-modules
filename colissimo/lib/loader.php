<?php
/**
 * 2007-2024 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2024 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/ColissimoClient.php';
require_once dirname(__FILE__) . '/Request/AbstractColissimoRequest.php';
require_once dirname(__FILE__) . '/Request/ColissimoGenerateLabelRequest.php';
require_once dirname(__FILE__) . '/Request/ColissimoGenerateBordereauRequest.php';
require_once dirname(__FILE__) . '/Request/ColissimoWidgetAuthenticationRequest.php';
require_once dirname(__FILE__) . '/Request/ColissimoMailboxDetailsRequest.php';
require_once dirname(__FILE__) . '/Request/ColissimoTrackingEnrichiRequest.php';
require_once dirname(__FILE__) . '/Request/ColissimoTrackingSimpleRequest.php';
require_once dirname(__FILE__) . '/Request/ColissimoCheckGenerateLabelRequest.php';
require_once dirname(__FILE__) . '/Request/ColissimoPlanPickupRequest.php';
require_once dirname(__FILE__) . '/Request/ColissimoTrackingTimelineRequest.php';
require_once dirname(__FILE__) . '/Request/ColissimoCreateCustomDocumentRequest.php';
require_once dirname(__FILE__) . '/Request/ColissimoUpdateCustomDocumentRequest.php';
require_once dirname(__FILE__) . '/Request/ColissimoBoxServicesRequest.php';
require_once dirname(__FILE__) . '/Request/ColissimoboxConnectRequest.php';
require_once dirname(__FILE__) . '/Request/ColissimoGenerateTokenRequest.php';
require_once dirname(__FILE__) . '/Response/ColissimoReturnedResponseInterface.php';
require_once dirname(__FILE__) . '/Response/AbstractColissimoResponse.php';
require_once dirname(__FILE__) . '/Response/ColissimoResponseParser.php';
require_once dirname(__FILE__) . '/Response/ColissimoGenerateLabelResponse.php';
require_once dirname(__FILE__) . '/Response/ColissimoGenerateBordereauResponse.php';
require_once dirname(__FILE__) . '/Response/ColissimoWidgetAuthenticationResponse.php';
require_once dirname(__FILE__) . '/Response/ColissimoMailboxDetailsResponse.php';
require_once dirname(__FILE__) . '/Response/ColissimoTrackingEnrichiResponse.php';
require_once dirname(__FILE__) . '/Response/ColissimoTrackingSimpleResponse.php';
require_once dirname(__FILE__) . '/Response/ColissimoCheckGenerateLabelResponse.php';
require_once dirname(__FILE__) . '/Response/ColissimoPlanPickupResponse.php';
require_once dirname(__FILE__) . '/Response/ColissimoTrackingTimelineResponse.php';
require_once dirname(__FILE__) . '/Response/ColissimoCreateCustomDocumentResponse.php';
require_once dirname(__FILE__) . '/Response/ColissimoUpdateCustomDocumentResponse.php';
require_once dirname(__FILE__) . '/Response/ColissimoBoxServicesResponse.php';
require_once dirname(__FILE__) . '/Response/ColissimoboxConnectResponse.php';
