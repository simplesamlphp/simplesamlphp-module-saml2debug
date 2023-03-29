<?php

declare(strict_types=1);

use SimpleSAML\Locale\Translate;
use SimpleSAML\Module;
use SimpleSAML\XHTML\Template;

/**
 * Hook to add the saml2debug module to the config page.
 *
 * @param \SimpleSAML\XHTML\Template &$template The template that we should alter in this hook.
 */
function saml2debug_hook_configpage(Template &$template): void
{
    $template->data['links'][] = [
        'href' => Module::getModuleURL('saml2debug/decode'),
        'text' => Translate::noop('SAML 2.0 Debugger'),
    ];

    $template->getLocalization()->addModuleDomain('saml2debug');
}
