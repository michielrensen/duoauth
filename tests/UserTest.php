<?php

namespace DuoAuth;

require_once 'MockClient.php';
require_once 'MockResponse.php';

class UserTest extends \PHPUnit_Framework_TestCase
{
    private function buildMockRequest($data)
    {
        $mockClient = new MockClient();
        $response = new \DuoAuth\Response();

        $r = new MockResponse();
        $r->setBody(json_encode($data));
        $response->setData($r);

        $request = $this->getMock('\DuoAuth\Request', array('send'), array($mockClient));

        $request->expects($this->once())
            ->method('send')
            ->will($this->returnValue($response));

        return $request;
    }

    private function buildMockUser($request)
    {
        $user = $this->getMock('\DuoAuth\User', array('getRequest'));

        $user->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request));

        return $user;
    }

    /**
     * Test that the "find all" returns results and that they're the right type
     * @covers \DuoAuth\User::findAll
     */
    public function testFindAllValidResults()
    {

        $user = new \DuoAuth\User();
        $results = array(
            "response" => array(
                array("username" => "testuser1"),
                array("username" => "testuser2")
            )
        );
        $request = $this->buildMockRequest($results);
        $user = $this->buildMockUser($request);

        $result = $user->findAll();
        $this->assertEquals(2, count($result));
        foreach ($result as $r) {
            if (!($r instanceof \DuoAuth\User)) {
                $this->fail('Invalid object type returned - not a User');
            }
        }
    }

    /**
     * Validate code from the user (a valid response)
     * @covers \DuoAuth\User::validateCode
     */
    public function testValidateCodeValid()
    {
        $code = 'testing1234';
        $results = array('response' => array('result' => 'allow'));

        $request = $this->buildMockRequest($results);
        $user = $this->buildMockUser($request);

        $v = $user->validateCode($code, 'ccornutt');
        $this->assertTrue($v);
    }

    /**
     * Try to request too many auth codes (max 10)
     * @covers \DuoAuth\User::generateBypassCodes
     * @expectedException \InvalidArgumentException
     */
    public function testGenerateCodesTooMany()
    {
        $user = new \DuoAuth\User();
        $user->generateBypassCodes('testuser1', 20);
    }

    /**
     * Try to request too many auth codes (max 10)
     * @covers \DuoAuth\User::generateBypassCodes
     * @expectedException \InvalidArgumentException
     */
    public function testGenerateCodesNaN()
    {
        $user = new \DuoAuth\User();
        $user->generateBypassCodes('testuser1', '10');
    }

    /**
     * If phones are already set, return them right away
     * @covers \DuoAuth\User::getphones
     */
    public function testGetSetPhones()
    {
        $phones = array(
            array('name' => 'Phone #1')
        );
        $user = new \DuoAuth\User();
        $user->phones = $phones;

        $this->assertEquals($phones, $user->getPhones());
    }
}

?>