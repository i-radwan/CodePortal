<?php

namespace Tests\Feature;

use Illuminate\Validation\ValidationException;
use App\Models\Tag;

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
        $tags = Tag::index(20);
        $this->assertEquals(count(json_decode($tags, true)), 20);
        \Log::info('Tags:: ' . $tags);

        // Get tag problems paginated
        $validTag = $this->insertTag("NewTag");
        $judge = $this->insertJudge('Codeforces', 'http://www.judge.com', 'http://www.judge.com');
        for ($i = 0; $i < 100; $i++) {
            $problem = $this->insertProblem('Problem' . $i, 10, 20, $judge);
            if ($i % 2 == 0)
                $problem->tags()->sync([$validTag->id], false);
        }
        $problems = Tag::getTagProblems($validTag->id);
        $this->assertEquals(json_decode($problems, true)['problems']['total'], 50);
        \Log::info("Tag's Problems :: " . $problems);

    }

}
