<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
declare(strict_types=1);

namespace Hp\Faq\Entity;

use Doctrine\ORM\Mapping as ORM;
use PrestaShopBundle\Entity\Lang;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class FaqLang
{
    /**
     * @var Faq
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Hp\Faq\Entity\Faq", inversedBy="faqLangs")
     * @ORM\JoinColumn(name="id_faq", referencedColumnName="id", nullable=false)
     */
    private $Faq;

    /**
     * @var Lang
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\Lang")
     * @ORM\JoinColumn(name="id_lang", referencedColumnName="id_lang", nullable=false, onDelete="CASCADE")
     */
    private $lang;

    /**
     * @var string
     * @ORM\Column(name="question", type="string", nullable=false)
     */
    private $question;
    /**
     * @var string
     * @ORM\Column(name="response", type="string", nullable=false)
     */
    private $response;

    /**
     * @return Faq
     */
    public function getFaq() : Faq
    {
        return $this->Faq;
    }

    /**
     * @param Faq $Faq
     * @return $this
     */
    public function setFaq(Faq $Faq): self
    {
        $this->Faq = $Faq;

        return $this;
    }

    /**
     * @return Lang
     */
    public function getLang(): Lang
    {
        return $this->lang;
    }

    /**
     * @param Lang $lang
     * @return $this
     */
    public function setLang($lang): self
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * @return string
     */
    public function getQuestion(): string
    {
        return $this->question;
    }

    /**
     * @param string $question
     * @return $this
     */
    public function setQuestion(string $question): self
    {
        $this->question = $question;
        return $this;
    }
    /**
     * @return string
     */
    public function getResponse(): string
    {
        return $this->response;
    }

    /**
     * @param string $response
     * @return $this
     */
    public function setResponse(string $response): self
    {
        $this->response = $response;
        return $this;
    }
}
