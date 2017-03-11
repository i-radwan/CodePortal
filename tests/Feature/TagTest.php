<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use App\Models\Tag;
class TagTest extends TestCase
{

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testTag()
    {
        $initialCount = Tag::count();
        // insert valid contest and check for count
        $validTag = $this->insertValidTag();
        $this->assertTrue(Tag::count() == $initialCount + 1);
        $validTag->delete();
        $this->assertTrue(Tag::count() == $initialCount); // test deleting

        // insert invalid models
        try {
            ($this->insertInvalidTagMissingData());
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - missing data");
        } catch (ValidationException $e){
        }
        try {
            ($this->insertInvalidTagName());
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - name too long");
        } catch (ValidationException $e){
        }
        //Duplicate Tags
        $validTag = $this->insertValidTag();
        try {
            ($this->insertValidTag());
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - name duplicate");
        } catch (ValidationException $e){
        }

        $validTag->delete();

        $this->assertTrue(Tag::count() == $initialCount); // not inserted
    }


    public function insertValidTag()
    {
        $tag = new Tag(['name' => 'Tag1']);
        $tag->save();
        return $tag;
    }

    public function insertInvalidTagMissingData()
    {
        $tag = new Tag(['name' => '']);
        $tag->save();
        return $tag;
    }
    public function insertInvalidTagName()
    {
        $tag = new Tag(['name' => 'namenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamename']);
        $tag->save();
        return $tag;
    }

}
