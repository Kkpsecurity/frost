<?php
declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\ChatLog;
use App\Models\User;


class GetChatLogs extends Command
{

    protected $signature   = 'command:get_chat_logs {user_id}';
    protected $description = 'Get Student ChatLogs';


    public function handle() : int
    {


        $User = User::findOrFail( $this->argument( 'user_id' ) );

        $this->line( "Chat Logs for {$User}\n" );

        $ChatLogs = ChatLog::where( 'student_id', $User->id )
                         ->orderBy( 'created_at' )
                         ->get();

        if ( ! $ChatLogs )
        {
            print "No records.\n";
            return 0;
        }


        $records = [];
        foreach ( $ChatLogs as $ChatLog )
        {
            array_push( $records, [
                $ChatLog->CreatedAt(), $ChatLog->body
            ]);
        }

        $this->table(
            [ 'Date', 'Message'],
            $records
        );


        return 0;

    }

}
