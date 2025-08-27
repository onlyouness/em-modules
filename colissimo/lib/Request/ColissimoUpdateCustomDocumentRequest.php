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

/**
 * Class ColissimoUpdateCustomDocumentRequest
 */
class ColissimoUpdateCustomDocumentRequest extends AbstractColissimoRequest
{
    const WS_TYPE = 'CURL';
    const WS_PATH = '/api-document/rest/updatedocument?';
    const WS_CONTENT_TYPE = 'multipart/form-data';
    const WS_HEADER = 1;
    const WS_HEADER_DOCUMENT = 1;

    /** @var string */
    protected $accountNumber;

    /** @var int */
    protected $parcelNumber;

    /** @var string */
    protected $documentType;

    /** @var binary */
    protected $file;

    /** @var string */
    protected $filename;

    /** @var array */
    protected $parcelNumberList;

    /**
     * @param string $accountNumber
     * @return ColissimoCreateCustomDocumentRequest
     */
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    /**
     * @param string $parcelNumber
     * @return ColissimoCreateCustomDocumentRequest
     */
    public function setParcelNumber($parcelNumber)
    {
        $this->parcelNumber = $parcelNumber;

        return $this;
    }

    /**
     * @param string $documentType
     * @return ColissimoCreateCustomDocumentRequest
     */
    public function setDocumentType($documentType)
    {
        $this->documentType = $documentType;

        return $this;
    }

    /**
     * @param array $file
     * @return ColissimoCreateCustomDocumentRequest
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @param string $filename
     * @return ColissimoCreateCustomDocumentRequest
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @param array $parcelNumberList
     * @return ColissimoCreateCustomDocumentRequest
     */
    public function setParcelNumberList($parcelNumberList)
    {
        $this->parcelNumberList = $parcelNumberList;

        return $this;
    }

    /**
     * @return mixed|void
     */
    public function buildRequest()
    {
        $this->request['accountNumber'] = $this->accountNumber;
        $this->request['parcelNumber'] = $this->parcelNumber;
        $this->request['documentType'] = $this->documentType;
        $this->request['file'] = $this->file;
        $this->request['filename'] = $this->filename;
        if (!empty($this->parcelNumberList)) {
            $this->request['parcelNumberList'] = $this->parcelNumberList;
        }
    }

    /**
     * @param mixed $responseHeader
     * @param mixed $responseBody
     * @return mixed
     * @throws Exception
     */
    public function buildResponse($responseHeader, $responseBody)
    {
        return ColissimoUpdateCustomDocumentResponse::buildFromResponse($responseHeader, $responseBody);
    }
}
