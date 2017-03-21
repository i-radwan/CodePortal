<?php

namespace Tests\Feature;

use App\Models\Language;
use Illuminate\Validation\ValidationException;

class LanguageTest extends DatabaseTest
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testLanguage()
    {
        $initialCount = Language::count();
        // insert valid contest and check for count
        $validLanguage = $this->insertLanguage('Lang1');
        $this->assertTrue(Language::count() == $initialCount + 1);
        $validLanguage->delete();
        $this->assertTrue(Language::count() == $initialCount); // test deleting

        // insert invalid models
        try {
            $this->insertLanguage('');
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - missing data");
        } catch (ValidationException $e){
        }
        try {
            $this->insertLanguage('Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1Lang1');
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - name too long");
        } catch (ValidationException $e){
        }

        //Duplicate languages
        $validLanguage = $this->insertLanguage('Lang1');

        try {
            $this->insertLanguage('Lang1');
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - name duplicate");
        } catch (ValidationException $e){
        }

        $validLanguage->delete();

        $this->assertTrue(Language::count() == $initialCount); // not inserted
    }
}
