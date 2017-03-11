<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use App\Models\Tag;
use Artisan;
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
        $validTag = $this->insertTag("Tag1234");
        $this->assertTrue(Tag::count() == $initialCount + 1);
        $validTag->delete();
        $this->assertTrue(Tag::count() == $initialCount); // test deleting

        // insert invalid models
        try {
            $this->insertTag("");
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - missing data");
        } catch (ValidationException $e) {
        }
        try {
            $this->insertTag("Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1Tag1");
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - name too long");
        } catch (ValidationException $e) {
        }
        //Duplicate Tags
        $validTag = $this->insertTag("Tag2");
        try {
            $this->insertTag("Tag2");
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - name duplicate");
        } catch (ValidationException $e) {
        }

        $validTag->delete();

        $this->assertTrue(Tag::count() == $initialCount); // not inserted
    }


    public function insertTag($name)
    {
        $tag = new Tag(['name' => $name]);
        $tag->store();
        return $tag;
    }

}
