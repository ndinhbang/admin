<?php

use App\Models\Account;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class MigrateDataToJsonColumnOnAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * @throws \Throwable
     */
    public function up()
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn('total');
            $table->json('stats')->nullable()->after('required_code');
        });
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('total');
            $table->json('stats')->nullable()->after('type');
        });
        DB::transaction(
            function () {
                foreach ( Account::withoutGlobalScopes()->cursor() as $account ) {
                    $account[ 'stats->amount' ]          = $account->total_amount;
                    $account[ 'stats->returned_amount' ] = $account->total_return_amount;
                    $account[ 'stats->debt' ]            = $account->total_debt;
                    $account[ 'stats->last_order_at' ]   = $account->last_order_at;
                    if ( $account->type == 'customer' ) {
                        $account[ 'stats->order_count' ] = $account->orders()->count();
                    }
                    $account->save();
                }
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->json('total')->nullable()->after('required_code');
        });
        Schema::table('accounts', function (Blueprint $table) {
            $table->json('total')->nullable()->after('type');
        });
    }
}
