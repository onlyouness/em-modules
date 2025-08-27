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
 * Class ColissimoResponseParser
 */
class ColissimoResponseParser
{
    /** Regex for separator */
    const UUID = '/--uuid:/';

    /** New line character in Response */
    const NEW_LINE_CHAR = "\r\n";

    /** @var string */
    private $uuid;

    /**
     * @param string $rawHeaders
     * @return array
     */
    public function parseHeaders($rawHeaders)
    {
        $headers = [];
        $key = '';

        foreach (explode("\n", $rawHeaders) as $i => $h) {
            $h = explode(':', $h, 2);
            if (isset($h[1])) {
                if (!isset($headers[$h[0]])) {
                    $headers[$h[0]] = trim($h[1]);
                } elseif (is_array($headers[$h[0]])) {
                    $headers[$h[0]] = array_merge($headers[$h[0]], [trim($h[1])]);
                } else {
                    $headers[$h[0]] = array_merge([$headers[$h[0]]], [trim($h[1])]);
                }

                $key = $h[0];
            } else {
                if (substr($h[0], 0, 1) == "\t") {
                    $headers[$key] .= "\r\n\t" . trim($h[0]);
                } elseif (!$key) {
                    $headers[0] = trim($h[0]);
                }
            }
        }

        return $headers;
    }

    /**
     * @param string $body
     * @return array
     * @throws Exception
     */
    public function parseBody($body)
    {
        $contents = $this->splitContent($body);
        if (!is_array($contents) || empty($contents)) {
            throw new Exception('Empty response.');
        }
        $parsedResponse = [];
        foreach ($contents as $content) {
            if ($this->uuid == null) {
                $uuidStart = strpos($content, self::UUID, 0) + strlen(self::UUID);
                $uuidEnd = strpos($content, self::NEW_LINE_CHAR, $uuidStart);
                $this->uuid = substr($content, $uuidStart, $uuidEnd - $uuidStart);
            }
            $headers = $this->extractHeader($content);
            if (count($headers) > 0) {
                if (isset($headers['Content-ID'])) {
                    $parsedResponse[$headers['Content-ID']] = trim(substr($content, $headers['offsetEnd']));
                }
            }
        }
        if (empty($parsedResponse)) {
            throw new Exception('Response cannot be parsed.');
        }

        return $parsedResponse;
    }

    /**
     * @param array $parsedHeaders
     * @return bool|string
     */
    public function parseContentType($parsedHeaders)
    {
        if (isset($parsedHeaders['content-type'])) {
            $contentType = $parsedHeaders['content-type'];
        } else {
            $contentType = $parsedHeaders['Content-Type'];
        }
        if (!$contentType) {
            return false;
        }
        $contentTypes = explode(';', $contentType);
        foreach ($contentTypes as $contentType) {
            if (strpos($contentType, 'start=') !== false) {
                return substr($contentType, 8, -1);
            }
        }

        return false;
    }

    /**
     * @param string $response
     * @return array
     */
    private static function splitContent($response)
    {
        $contents = [];
        $matches = [];
        preg_match_all(self::UUID, $response, $matches, PREG_OFFSET_CAPTURE);
        for ($i = 0; $i < count($matches[0]) - 1; $i++) {
            if ($i + 1 < count($matches[0])) {
                $contents[$i] = substr(
                    $response,
                    $matches[0][$i][1],
                    $matches[0][$i + 1][1] - $matches[0][$i][1]
                );
            } else {
                $contents[$i] = substr(
                    $response,
                    $matches[0][$i][1],
                    strlen($response)
                );
            }
        }

        return $contents;
    }

    /**
     * @param string $part
     * @return array
     */
    private function extractHeader($part)
    {
        $header = [];
        $headerLineStart = strpos($part, 'Content-', 0);
        $endLine = 0;
        while ($headerLineStart !== false) {
            $header['offsetStart'] = $headerLineStart;
            $endLine = strpos($part, self::NEW_LINE_CHAR, $headerLineStart);
            $headerLine = explode(': ', substr($part, $headerLineStart, $endLine - $headerLineStart));
            $header[$headerLine[0]] = $headerLine[1];
            $headerLineStart = strpos($part, 'Content-', $endLine);
        }
        $header['offsetEnd'] = $endLine;

        return $header;
    }
}
