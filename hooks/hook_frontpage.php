<?php

declare(strict_types=1);

namespace SimpleSAML\Module\saml2debug;

use SimpleSAML\Assert\Assert;
use SimpleSAML\Module;

/**
 * Hook to add the simple consenet admin module to the frontpage.
 *
 * @param array &$links  The links on the frontpage, split into sections.
 */
function saml2debug_hook_frontpage(array &$links): void
{
    Assert::keyExists($links, "links");

    $links['config']['saml2debug'] = [
        'href' => Module::getModuleURL('saml2debug/debug.php'),
        'text' => ['en' => 'SAML 2.0 Debugger'],
    ];
}
