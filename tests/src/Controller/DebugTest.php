<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Module\saml2debug\Controller;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Configuration;
use SimpleSAML\Error;
use SimpleSAML\Module\saml2debug\Controller;
use SimpleSAML\Session;
use SimpleSAML\XHTML\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Set of tests for the controllers in the "saml2debug" module.
 *
 * @package SimpleSAML\Test
 */
final class DebugTest extends TestCase
{
    /** @var \SimpleSAML\Configuration */
    protected Configuration $config;

    /** @var \SimpleSAML\Session */
    protected Session $session;

    /** @var string */
    protected static string $encoded = 'SAMLRequest=pVJNjxMxDP0ro9yn88FOtRu1lcpWiEoLVNvCgQtKE6eNlHGG2IHl35OZLmLZQy%2BcrNh%2Bz88vXpDq%2FSDXic%2F4CN8TEBdPvUeSU2EpUkQZFDmSqHogyVru1x8eZDur5RADBx28eAG5jlBEENkFFMV2sxTfzO0dmKa11namPuoc39hba%2BfqpqlbM6%2Fb5mZ%2B1LWtj6L4ApEycikyUYYTJdgisULOqbrpyqYt67tD28iulV33VRSbvI1DxRPqzDyQrCrAk0OYUYpWB4QnnqGvVN4fkJ2emitnhoocnjyU5E5YjnrXf6TfB6TUQ9xD%2FOE0fH58%2BEueHbHOv2Yn1w8eRneqPpiU68M5DxjfdIltqTRNWQNWJc8lDaLYPfv71qHJaq5be7w0kXx%2FOOzK3af9QawWI7ecrIqr%2F9HYAyujWL2SuKheDlhcbuljlrbd7IJ3%2BlfxLsRe8XXlY8aZ0k6tkqNCcvkzsuXeh5%2F3ERTDUnBMIKrVZeS%2FF7v6DQ%3D%3D';


    /**
     * Set up for each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->config = Configuration::loadFromArray(
            [
                'module.enable' => ['saml2debug' => true],
            ],
            '[ARRAY]',
            'simplesaml',
        );

        $this->session = Session::getSessionFromRequest();
    }


    /**
     * Test that calling the controller for decoding results in a Template being returned
     */
    public function testEncode(): void
    {
        $request = Request::create(
            '/',
            'POST',
            ['decoded' => 'something'],
        );

        $c = new Controller\Debug($this->config, $this->session);
        $response = $c->decode($request);

        // Validate response
        $this->assertInstanceOf(Template::class, $response);
        $this->assertTrue($response->isSuccessful());
    }


    /**
     * Test that calling the controller for encoding results in a Template being returned
     */
    public function testDecode(): void
    {
        $request = Request::create(
            '/',
            'POST',
            ['encoded' => self::$encoded, 'binding' => 'redirect'],
        );

        $c = new Controller\Debug($this->config, $this->session);
        $response = $c->decode($request);

        // Validate response
        $this->assertInstanceOf(Template::class, $response);
        $this->assertTrue($response->isSuccessful());
    }


    /**
     * Test that calling the controller for encoding without binding results in an exception being raised
     */
    public function testDecodeWithoutBindingThrowsException(): void
    {
        $request = Request::create(
            '/',
            'POST',
            ['encoded' => self::$encoded],
        );

        $c = new Controller\Debug($this->config, $this->session);

        $this->expectException(Error\BadRequest::class);
        $this->expectExceptionMessage('Missing binding');

        $c->decode($request);
    }
}
