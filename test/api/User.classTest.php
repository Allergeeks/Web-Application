<?php

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-04-02 at 11:13:13.
 */
class UserTest extends PHPUnit_Framework_TestCase {

    /**
     * @var User
     */
    protected $test_user;
    protected $test_user2;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->test_user = new User(1, 'marco.heumann@web.de', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8');
        $this->test_user2 = new User(3, 'marco-polo93@web.de', '91dfd9ddb4198affc5c194cd8ce6d338fde470e2');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        $sql = 'DELETE FROM user
                WHERE id <> 1 AND id <> 2';
        $stmt = DB::con()->prepare($sql);
        if (!$stmt) {
            throw new InternalError('Konnte Query nicht vorbereiten: '.DB::con()->error);
        }

        if (!$stmt->execute()) {
            throw new InternalError('Konnte Query nicht ausführen: '.$stmt->error);
        }
        
        $sql = 'ALTER TABLE user AUTO_INCREMENT=2';
        $stmt = DB::con()->prepare($sql);
        if (!$stmt) {
            throw new InternalError('Konnte Query nicht vorbereiten: '.DB::con()->error);
        }

        if (!$stmt->execute()) {
            throw new InternalError('Konnte Query nicht ausführen: '.$stmt->error);
        }
        $stmt->close();
    }

    /**
     * @covers User::create
     * @todo   Implement testCreate().
     */
    public function testCreate() {
        $new_user = User::create('marco-polo93@web.de', 'mypassword');
        
        $this->assertEquals($this->test_user2, $new_user);
    }

    /**
     * @covers User::get_by_id
     */
    public function testGet_by_id() {
        $this->assertEquals($this->test_user, User::get_by_id(1));
    }

    /**
     * @covers User::get_by_email
     */
    public function testGet_by_email() {
        $this->assertEquals($this->test_user, User::get_by_email('marco.heumann@web.de'));
    }

    /**
     * @covers User::get_id
     */
    public function testGet_id() {
        $this->assertEquals(1, $this->test_user->get_id());
    }

    /**
     * @covers User::get_email
     */
    public function testGet_email() {
        $this->assertEquals('marco.heumann@web.de', $this->test_user->get_email());
    }

    /**
     * @covers User::check_password
     */
    public function testCheck_password() {
        $this->assertTrue($this->test_user->check_password('password'));
    }

    /**
     * @covers User::set_email
     * @todo   Implement testSet_email().
     */
    public function testSet_email() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers User::set_password
     * @todo   Implement testSet_password().
     */
    public function testSet_password() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers User::get_blacklist
     * @todo   Implement testGet_blacklist().
     */
    public function testGet_blacklist() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers User::add_to_blacklist
     * @todo   Implement testAdd_to_blacklist().
     */
    public function testAdd_to_blacklist() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers User::rm_from_blacklist
     * @todo   Implement testRm_from_blacklist().
     */
    public function testRm_from_blacklist() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

}
