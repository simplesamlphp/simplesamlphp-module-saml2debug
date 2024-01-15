<?php

declare(strict_types=1);

namespace SimpleSAML\Module\saml2debug\Controller;

use SimpleSAML\Configuration;
use SimpleSAML\Error;
use SimpleSAML\Session;
use SimpleSAML\XHTML\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller class for the saml2debug module.
 *
 * This class serves the different views available in the module.
 *
 * @package simplesamlphp/simplesamlphp-module-saml2debug
 */
class Debug
{
    /** @var \SimpleSAML\Configuration */
    protected Configuration $config;

    /** @var \SimpleSAML\Session */
    protected Session $session;


    /**
     * Controller constructor.
     *
     * It initializes the global configuration and session for the controllers implemented here.
     *
     * @param \SimpleSAML\Configuration $config The configuration to use by the controllers.
     * @param \SimpleSAML\Session $session The session to use by the controllers.
     *
     * @throws \Exception
     */
    public function __construct(
        Configuration $config,
        Session $session
    ) {
        $this->config = $config;
        $this->session = $session;
    }


    /**
     * Show SAML2 debugging info.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \SimpleSAML\XHTML\Template
     * @throws \Exception
     */
    public function decode(Request $request): Template
    {
        $decoded = '';
        $encoded = '';

        if ($request->request->has('encoded')) {
            if (!$request->request->has('binding')) {
                throw new Error\BadRequest('Missing binding');
            }

            $decoded = $this->parseEncodedMessage(
                $request->request->get('encoded'),
                $request->request->get('binding'),
            );
        }

        $t = new Template($this->config, 'saml2debug:decode.twig');
        $t->data['encoded'] = $encoded;
        $t->data['decoded'] = $decoded;

        return $t;
    }


    /**
     * Show SAML2 debugging info.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \SimpleSAML\XHTML\Template
     * @throws \Exception
     */
    public function encode(Request $request): Template
    {
        $decoded = '';
        $encoded = 'fZJNT%2BMwEIbvSPwHy%2Fd8tMvHympSdUGISuwS0cCBm%2BtMUwfbk%2FU4zfLvSVMq2Euv45n3fd7xzOb%2FrGE78KTRZXwSp5yBU1hp' .
                   'V2f8ubyLfvJ5fn42I2lNKxZd2Lon%2BNsBBTZMOhLjQ8Y77wRK0iSctEAiKLFa%2FH4Q0zgVrceACg1ny9uMy7rCdaM2%2Bs0BWrtppK2U' .
                   'AdeoVjW2ruq1bevGImcvR6zpHmtJ1MHSUZAuDKU0vY7Si2h6VU5%2BiMuJuLx65az4dPql3SHBKaz1oYnEfVkWUfG4KkeBna7A%2Fxm6M1' .
                   '4j1gZihZazBRH4MODcoKPOgl%2BB32kFz08PGd%2BG0JJIkr7v46%2BhRCaEpod17DCRivYZCkmkd4N28B3wfNyrGKP5bws9DS6PKDz%2F' .
                   'Mpsl36Tyz%2F%2Fax1jeFmi0emcLY7C%2F8SDD0Z7dobcynHbbV3QVbcZW0TlqQemNhoqzJD%2B4%2Fn8Yw7l8AA%3D%3D';

        if ($request->request->has('decoded')) {
            $encoded = $this->parseDecodedMessage($request->request->get('decoded'));
        }

        $t = new Template($this->config, 'saml2debug:encode.twig');
        $t->data['encoded'] = $encoded;
        $t->data['decoded'] = $decoded;

        return $t;
    }


    /**
     * @param string $raw
     * @return string
     */
    private function getValue(string $raw): string
    {
        $val = $raw;

        $url = parse_url($raw, PHP_URL_QUERY);
        if (!empty($url)) {
            $val = $url;
        }

        $arr = [];
        parse_str($val, $arr);

        if (array_key_exists('SAMLResponse', $arr)) {
            return $arr['SAMLResponse'];
        }
        if (array_key_exists('SAMLRequest', $arr)) {
            return $arr['SAMLRequest'];
        }
        if (array_key_exists('LogoutRequest', $arr)) {
            return $arr['LogoutRequest'];
        }
        if (array_key_exists('LogoutResponse', $arr)) {
            return $arr['LogoutResponse'];
        }

        return rawurldecode(stripslashes($val));
    }


    /**
     * @param string $raw
     * @return string
     */
    private function parseDecodedMessage(string $raw): string
    {
        $message = $this->getValue($raw);

        $base64decoded = base64_decode($message);
        $gzinflated = gzinflate($base64decoded);
        if ($gzinflated !== false) {
            $base64decoded = $gzinflated;
        }

        return $base64decoded;
    }


    /**
     * @param string $message
     * @param string $binding
     * @return string
     */
    private function parseEncodedMessage(string $message, string $binding): string
    {
        if ($binding === 'redirect') {
            return urlencode(base64_encode(gzdeflate(stripslashes($message))));
        } else {
            return base64_encode(stripslashes($message));
        }
    }
}
