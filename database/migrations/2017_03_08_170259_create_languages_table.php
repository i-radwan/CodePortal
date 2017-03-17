 <?php

 use App\Utilities\Constants;
 use Illuminate\Support\Facades\Schema;
 use Illuminate\Database\Schema\Blueprint;
 use Illuminate\Database\Migrations\Migration;

class CreateLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Constants::TBL_LANGUAGES, function (Blueprint $table) {
            $table->increments(Constants::FLD_LANGUAGES_ID);
            $table->string(Constants::FLD_LANGUAGES_NAME, 50)->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(Constants::TBL_LANGUAGES);
    }
}
