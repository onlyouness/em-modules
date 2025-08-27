<?php
namespace Hp\Faq\Controller;

use Hp\Faq\Entity\Faq;
use Hp\Faq\Entity\FaqLang;
use Language;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Entity\Lang;
use Symfony\Component\HttpFoundation\Request;

class AdminFaq extends FrameworkBundleAdminController
{
    public function indexAction()
    {
        $em         = $this->getDoctrine()->getManager();
        $languageId = \Context::getContext()->language->id;
        $groups     = $this->getGroups($languageId, false); // Get groups for the language

        // Fetch FAQs using the repository method
        $faqsA = $em->getRepository(Faq::class)->findFaqsByLang($languageId);

        // Organize FAQs by group ID
        $groupedFaqs = [];
        foreach ($faqsA as $faq) {
            $groupId = $faq['group']; // Get group ID

            // Initialize the group entry if it doesn't exist
            if (! isset($groupedFaqs[$groupId])) {
                $groupedFaqs[$groupId] = [];
            }

            // Add FAQ to the corresponding group
            $groupedFaqs[$groupId][] = $faq;
        }

        // \Tools::dieObject($groupedFaqs); // You can remove this line later for debugging

        return $this->render('@Modules/faq/views/templates/admin/index.html.twig', [
            'enableSidebar' => true,
            'layoutTitle'   => $this->trans('FAQs', 'Modules.Faq.Admin'),
            'groups'        => $groups,      // Pass groups in case you need them
            'groupedFaqs'   => $groupedFaqs, // FAQs grouped by their respective groups
        ]);
    }

    public function createAction(Request $request)
    {
        $em         = $this->getDoctrine()->getManager();
        $faq        = new Faq();
        $languageId = \Context::getContext()->language->id;
        $groups     = $this->getGroups($languageId, true);
        $languages  = \Language::getLanguages(true);

        // $form = $this->createForm(FaqType::class, $faq, [
        //     'groups' => $groups,
        // ]);
        $formBuilder = $this->get('faq.form.identifiable_object.builder');

        $form = $formBuilder->getForm(['groups' => $groups]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // \Tools::dieObject($form->getData());
            $faq->setGroup($form->get('group')->getData());
            $faq->setSection($form->get('section')->getData());
            $active = $form->get('active')->getData();
            if ($active) {
                $faq->setActive($active);
            } else {
                $faq->setActive($active);
            }

            /** @var Lang $language */
            foreach ($languages as $language) {
                $faqLang  = new FaqLang();
                $id_lang  = $language['id_lang'];
                $question = $form->get('question')->getData();
                $response = $form->get('response')->getData();

                $langEntity = $em->getRepository(Lang::class)->find($id_lang);
                if (! $langEntity) {
                    // Handle the case where the Lang entity is not found, perhaps throw an error
                    throw new \Exception('Language not found for ID ' . $id_lang);
                }

                // Set the Lang entity (not the ID) to the FaqLang object
                $faqLang->setLang($langEntity);

                if (isset($question[$id_lang])) {
                    $faqLang->setQuestion($question[$id_lang]);
                } else {
                    $faqLang->setQuestion($question[2]);
                }
                if (isset($response[$id_lang])) {
                    $faqLang->setResponse($response[$id_lang]);
                } else {
                    $faqLang->setResponse($response[2]);
                }
                $faq->addFaqLang($faqLang);
                dump($faqLang);
            }
            // \Tools::dieObject($faq);

            $em->persist($faq);
            $em->flush();
            $this->addFlash('success', $this->trans('FAQ created', 'Modules.Faq.Admin'));
            return $this->redirectToRoute('mm_faq_index');
        }
        return $this->render('@Modules/faq/views/templates/admin/create_faq.html.twig', [
            'enableSidebar' => true,
            'layoutTitle'   => $this->trans('FAQ', 'Modules.FAQ.Admin'),
            'form'          => $form->createView(),

        ]);
    }
    public function editAction(Request $request, Faq $faq)
    {
        $em         = $this->getDoctrine()->getManager();
        $languageId = \Context::getContext()->language->id;
        $groups     = $this->getGroups($languageId, true);
        dump($groups);
        $languages = \Language::getLanguages(true);

        $formBuilder = $this->get('faq.form.identifiable_object.builder');
        $form        = $formBuilder->getFormFor((int) $faq->getId(), ["groups" => $groups]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle group, section, and active status
            $faq->setGroup($form->get('group')->getData());
            $faq->setSection($form->get('section')->getData());
            $active = $form->get('active')->getData();
            $faq->setActive($active ? 1 : 0);

            foreach ($languages as $language) {
                $id_lang = $language['id_lang'];
                $faqLang = $em->getRepository(FaqLang::class)->findOneBy([
                    'Faq'  => $faq,
                    'lang' => $id_lang,
                ]);

                // If no existing FaqLang entry, create a new one
                if (! $faqLang) {
                    $faqLang = new FaqLang();
                    $faqLang->setLang($em->getRepository(Lang::class)->find($id_lang));
                    $faqLang->setFaq($faq);
                }

                // Get the question and response data from the form for this language
                $question = $form->get('question')->getData();
                $response = $form->get('response')->getData();

                                                                            // Set the question and response for the current language, with a fallback to language 2 if not available
                $faqLang->setQuestion($question[$id_lang] ?? $question[2]); // Fallback to language 2 if missing
                $faqLang->setResponse($response[$id_lang] ?? $response[2]); // Fallback to language 2 if missing

                $em->persist($faqLang); 
            }

            // Flush the changes to the database
            $em->flush();

            // Flash success message and redirect
            $this->addFlash('success', $this->trans('FAQ Updated', 'Modules.Faq.Admin'));
            return $this->redirectToRoute('mm_faq_index');
        }

        return $this->render('@Modules/faq/views/templates/admin/create_faq.html.twig', [
            'enableSidebar' => true,
            'layoutTitle'   => $this->trans('FAQ', 'Modules.FAQ.Admin'),
            'form'          => $form->createView(),
        ]);
    }

    public function deleteAction(Faq $faq)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($faq);
        $em->flush();

        $this->addFlash('success', $this->trans('Faq deleted', 'Modules.Faq.Admin'));
        return $this->redirectToRoute('mm_faq_index');
    }

    public function activeAction(Faq $faq)
    {
        $em     = $this->getDoctrine()->getManager();
        $active = $faq->getActive();
        // \Tools::dieObject($active);
        if ($active == 0) {
            $faq->setActive(1);
        } else {
            $faq->setActive(0);

        }
        $em->flush();
        $this->addFlash('success', $this->trans('Faq Activation Updated', 'Modules.Faq.Admin'));
        return $this->redirectToRoute('mm_faq_index');
    }

    public function getGroups($id_lang, $creation = false)
    {
        $results = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT DISTINCT g.`id_group` AS id, gl.`name`
		FROM `' . _DB_PREFIX_ . 'group` g
		LEFT JOIN `' . _DB_PREFIX_ . 'group_lang` AS gl ON (g.`id_group` = gl.`id_group` AND gl.`id_lang` = ' . (int) $id_lang . ')
		ORDER BY g.`id_group` ASC');
        $groups = [];
        if (! $creation) {
            $groups = $results;
        } else {
            foreach ($results as $res) {
                $groups[$res['name']] = $res['id'];
            }
        }
        return $groups;
    }

}
