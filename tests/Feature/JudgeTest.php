<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Judge;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;

class JudgeTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testJudge()
    {
        $initialCount = Judge::count();
        // insert valid contest and check for count
        $validJudge = $this->insertValidJudge();
        $this->assertTrue(Judge::count() == $initialCount + 1);
        $validJudge->delete();
        $this->assertTrue(Judge::count() == $initialCount); // test deleting


        // insert invalid models
        try {
            ($this->insertInvalidJudgeWithMissingData());
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - missing data");
        } catch (ValidationException $e){
        }
        try {
            ($this->insertInvalidJudgeLink());
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - invalid link");
        } catch (ValidationException $e){
        }
        try {
            ($this->insertInvalidJudgeAPILink());
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - invalid api link");
        } catch (ValidationException $e){
        }
        $validJudge = $this->insertValidJudge();
        try {
            ($this->insertDuplicateNameJudge());
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - name duplicate");
        } catch (ValidationException $e){
        }
        try {
            ($this->insertDuplicateLinkJudge());
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - link duplicate");
        } catch (ValidationException $e){
        }

        $validJudge->delete();

        $this->assertTrue(Judge::count() == $initialCount); // not inserted
    }

    public function insertValidJudge()
    {
        $judge = new Judge(['name' => 'Judge1', 'link' => 'http://www.link.com', 'api_link' => 'http://www.apilink.com']);
        $judge->save();
        return $judge;
    }

    public function insertInvalidJudgeWithMissingData()
    {
        $judge = new Judge(['name' => '', 'link' => 'http://www.link.com', 'api_link' => 'http://www.apilink.com']);
        $judge->save();
        return $judge;
    }
    public function insertInvalidJudgeLink()
    {
        $judge = new Judge(['name' => 'Judge14', 'link' => 'www.link.com', 'api_link' => 'http://www.apilink.com']);
        $judge->save();
        return $judge;
    }
    public function insertInvalidJudgeAPILink()
    {
        $judge = new Judge(['name' => 'Judge3', 'link' => 'http://www.link.com', 'api_link' => 'ask;lfadkls;']);
        $judge->save();
        return $judge;
    }
    public function insertDuplicateNameJudge()
    {
        $judge = new Judge(['name' => 'Judge1', 'link' => 'http://www.link.com', 'api_link' => 'http://www.apilink.com']);
        $judge->save();
        return $judge;
    }
    public function insertDuplicateLinkJudge()
    {
        $judge = new Judge(['name' => 'Judge2', 'link' => 'http://www.link.com', 'api_link' => 'http://www.apilink.com']);
        $judge->save();
        return $judge;
    }
}
