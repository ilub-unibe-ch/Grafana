<?php


use  iLUB\Plugins\Grafana\Helper\GrafanaDBAccess;

class DBAccessTest extends PHPUnit\Framework\TestCase
{
    protected $mockDBInterface;



    protected $mockDIC;
    protected $RunSync;
    protected $mockDBAccess;
    protected $mockDB;

    protected $DBAccess;

    protected function setUp():void
    {

        //$this->mockDBAccess=Mockery::mock(iLUB\Plugins\Grafana\Helper\cleanUpSessionsDBAccess::class);

        $this->mockDIC           = Mockery::mock(Pimple\Container::class);
        $this->mockDB            = Mockery::mock(ilDB::class);

    }

    public function test_logSessionsToDB(){

        $this->mockDB->shouldReceive("query")->with("SELECT count(*) FROM usr_session")->times(1);
        $this->mockDB->shouldReceive("fetchAssoc")->times(4)->andReturn(['count(*)'=> 1, 'count(distinct usr_session.user_id)'=>2]);
        $this->mockDB->shouldReceive("insert")->times(1);
        $this->mockDB->shouldReceive("query")->times(3);



        $this->DBAccess = new GrafanaDBAccess($this->mockDIC, $this->mockDB);
        $this->DBAccess->logSessionsToDB();
    }


    public function test_getAllSessions(){
        $this->mockDB->shouldReceive("query")->with("SELECT count(*) FROM usr_session");
        $this->mockDB->shouldReceive("fetchAssoc")->times(1)->andReturn(['count(*)'=> 1]);


        $this->DBAccess = new GrafanaDBAccess($this->mockDIC, $this->mockDB);
        $this->DBAccess->getAllSessions();
    }

    public function test_getUsersActiveBetween(){
        $this->mockDB->shouldReceive("query")->with("SELECT count(distinct usr_session.user_id) from usr_session where ctime Between '123456'and '654321'");
        $this->mockDB->shouldReceive("fetchAssoc")->times(1)->andReturn(['count(distinct usr_session.user_id)'=> 1]);

        $this->DBAccess = new GrafanaDBAccess($this->mockDIC, $this->mockDB);
        $this->DBAccess->getUsersActiveBetween(123456,654321);
    }
    public function tearDown():void
    {
        //I added this assertion because otherwise the test won't pass with the info "This test did not perform any assertions"
        self::assertTrue(true);
        Mockery::close();
    }
}
