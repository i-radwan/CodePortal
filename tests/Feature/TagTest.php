<?php

namespace Tests\Feature;

use Illuminate\Validation\ValidationException;
use App\Models\Tag;
use App\Models\Problem;

class TagTest extends DatabaseTest
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

        for ($i = 0; $i < 100; $i++) $this->insertTag('Tag' . $i);
        $tags = Tag::all();
        $this->assertEquals(count(json_decode($tags, true)), 100);
        \Log::info('Tags:: ' . $tags);

        // Get tag problems paginated
        $validTag = $this->insertTag("NewTag");
        $judge = $this->insertJudge('1', 'Codeforces', 'http://www.judge.com');
        for ($i = 0; $i < 100; $i++) {
            $problem = $this->insertProblem('Problem' . $i, 20, $judge, '123' . $i, '312' . $i);
            if ($i % 2 == 0)
                $problem->tags()->sync([$validTag->id], false);
        }
        $problems = Problem::hasTags([$validTag->id])->get();
        $this->assertEquals(count($problems->toArray()), 50);
        \Log::info("Tag's Problems :: " . $problems);

    }

}
